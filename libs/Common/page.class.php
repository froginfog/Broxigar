<?php
class page {
    private $records;  //总记录数
    private $pageSize; //每页记录数
    private $listOneSide; //页码列表中 当前页一边保持带链接页码的数量
    private $totalPage; //总页数
    private $thisPage;  //当前页
    private $url;
    private $left; //页码列表 起始页
    private $right; //页码列表结束页

    public function __construct($records=1, $pageSize=1, $listOneSide=3){
        $this->records = intval($records) > 0 ? intval($records) : 1;
        $this->pageSize = intval($pageSize) > 0 ? intval($pageSize) : 1;
        $this->listOneSide = intval($listOneSide);
        $this->totalPage = ceil($this->records / $this->pageSize);
        $this->thisPage = isset($_GET['page']) ? $_GET['page'] : 1;
        if($this->thisPage < 1){
            $this->thisPage = 1;
        }
        if($this->thisPage > $this->totalPage){
            $this->thisPage = $this->totalPage;
        }
        $this->url = $this->getUrl();
        $this->left = $this->thisPage - $this->listOneSide;
        $this->right = $this->thisPage + $this->listOneSide;
        if($this->left < 1){
            $this->right =  $this->right + 1 + abs($this->left);
            $this->left = 1;
        }
        if($this->right > $this->totalPage){
            $this->left = $this->left - ($this->right - $this->totalPage);
            $this->right = $this->totalPage;
        }

    }

    private function getUrl(){
        $uri = parse_url($_SERVER['REQUEST_URI']);
        $query = $uri['query'];
        parse_str($query, $arr);
        unset($arr['page']);
        if(empty($arr)){
            $_url = $uri['path'] . '?';
        }else{
            $_url = $uri['path'] . '?' . http_build_query($arr).'&';
        }
        return $_url;
    }
    
    private function home(){
        if($this->thisPage != 1){
            return '<a href='.$this->url.'page=1 title="首页">首页</a>';
        }else{
            return '<a>首页</a>';
        }
    }

    private function end(){
        if($this->thisPage != $this->totalPage){
            return '<a href='.$this->url.'page='.$this->totalPage.' title="尾页">尾页</a>';
        }else{
            return '<a>尾页</a>';
        }
    }

    private function prev(){
        if($this->thisPage != 1){
            return '<a href='.$this->url.'page='.($this->thisPage-1).' title="上一页">上一页</a>';
        }else{
            return '<a>上一页</a>';
        }
    }

    private function next(){
        if($this->thisPage != $this->totalPage){
            return '<a href='.$this->url.'page='.($this->thisPage+1).' title="下一页">下一页</a>';
        }else{
            return '<a>下一页</a>';
        }
    }

    private function pageList(){
        $str = '';
        if($this->left < 1){
            $this->left = 1;
        }
        if($this->thisPage - $this->listOneSide > 1){
            $str .= '<p class="pageEllipsis">...</p>';
        }
        for($i = $this->left; $i <= $this->right; $i++){
            if($i == $this->thisPage) {
                $str .= '<a title=第'.$i.'页 class="currentPage">'.$i.'</a>';
            }else{
                $str .='<a href=' . $this->url . 'page=' . $i . ' title=第'.$i.'页>'.$i.'</a>';
            }
        }
        if($this->right < $this->totalPage){
            $str .= '<p class="pageEllipsis">...</p>';
        }
        return $str;
    }
    private function start(){
         $str = '<div class="pageList">';
         return $str;
    }

    private function finish(){
        $str = '</div>';
        return $str;
    }

    public function outPut(){
        $str = $this->start();
        $str .= $this->home();
        $str .= $this->prev();
        $str .=  $this->pageList();
        $str .=  $this->next();
        $str .=  $this->end();
        $str .= $this->finish();
        echo $str;
    }

    public function limit(){
        $str = ' limit '.($this->thisPage - 1) * $this->pageSize.','.$this->pageSize;
        return $str;
    }
}