<?php
class visitor {
    private $url = 'http://ip-api.com/json/%s?lang=zh-CN';
    private $ip;
    private $visitdate;
    private $referer;

    //数据库对应字段名
    private $db_table;
    private $db_vid;
    private $db_ip;
    private $db_location;
    private $db_visitdate;
    private $db_referer;

    public function __construct($db_table=null, $db_vid=null, $db_ip=null, $db_location=null, $db_visitdate=null, $db_referer=null){
        $this->db_table = $db_table ? $db_table : 'visitor';
        $this->db_vid = $db_vid ? $db_vid : 'vid';
        $this->db_ip = $db_ip ? $db_ip : 'ip';
        $this->db_location = $db_location ? $db_location : 'location';
        $this->db_visitdate = $db_visitdate ? $db_visitdate : 'visitdate';
        $this->db_referer = $db_referer ? $db_referer : 'referer';

        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->visitdate = $_SERVER['REQUEST_TIME'];
        $this->referer = $_SERVER['HTTP_REFERER'];
    }

    public function record(){
        $sql = 'insert into '.$this->db_table.'('.$this->db_ip.','.$this->db_location.','.$this->db_visitdate.','.$this->db_referer.') values(?,?,?,?)';
        $sql2 = "select ".$this->db_visitdate." from ".$this->db_table." where ".$this->db_ip."=? order by ".$this->db_visitdate." desc limit 1";//TODO
        $arr = array(
            array('value'=>$this->ip, 'type'=>PDO::PARAM_STR)
        );
        try{
            $db = DB::getInstance();
            $date = $db->prepare($sql2)->bindValue($arr)->execute()->getOne();
            if($this->visitdate - $date[$this->db_visitdate] > 3600) {
                $arr[] = array('value'=>$this->getLocation(), 'type'=>PDO::PARAM_STR);
                $arr[] = array('value'=>$this->visitdate, 'type'=>PDO::PARAM_INT);
                $arr[] = array('value'=>$this->referer, 'type'=>PDO::PARAM_STR);
                $db->prepare($sql)->bindValue($arr)->execute();
            }
        }catch(PDOException $e){
            exit($e->getMessage());
        }
    }

    public function getTodayRecord(){
        $today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $sql = 'select count('.$this->db_vid.') from '.$this->db_table.' where '.$this->db_visitdate.'>'.$today;
        try{
            $db = DB::getInstance();
            $res = $db->query($sql)->count();
        }catch(PDOException $e){
            exit($e->getMessage());
        }
        return $res;
    }

    public function getYesterdayRecord(){
        $begin = mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));
        $end = mktime(23, 59, 59, date('m'), date('d')-1, date('Y'));
        $sql = 'select count('.$this->db_vid.') from '.$this->db_table.' where '.$this->db_visitdate.'>'.$begin.' and '.$this->db_visitdate.'<'.$end;
        try{
            $db = DB::getInstance();
            $res = $db->query($sql)->count();
        }catch(PDOException $e){
            exit($e->getMessage());
        }
        return $res;
    }

    public function getAllRecord(){
        $sql = 'select count('.$this->db_vid.') from '.$this->db_table;
        try{
            $db = DB::getInstance();
            $res = $db->query($sql)->count();
        }catch(PDOException $e){
            exit($e->getMessage());
        }
        return $res;
    }

    private function getLocation(){
        $url = sprintf($this->url, $this->ip);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($res, true);
        if($res['status'] == 'success') {
            $location = $res['country'] . ' ' . $res['regionName'] . ' ' . $res['city'];
        }else{
            $location = '';
        }
        return $location;
    }

}