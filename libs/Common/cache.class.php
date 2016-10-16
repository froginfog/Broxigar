<?php
class cache {
    private $dir;
    private $key = 'vurtne';

    private function makeDir($path){
        if(!file_exists($path)){
            if(!mkdir($path, 0777)){
                die('文件夹创建失败');
            }
        }
    }

    private function getFilename($key){
        return $this->dir.'/'.$key.'_'.md5($key.$this->key);
    }

    public function setDir($path){
        $this->dir = $path;
        $this->makeDir($path);
    }

    public function read($key){
        $filename = $this->getFilename($key);
        if($data = @file_get_contents($filename)){
            $res = unserialize($data);
            $lt = $res['createtime'] + $res['lifetime'];
            if($lt > time() || is_null($res['lifetime'])){
                return $res['data'];
            }
        }
        return false;
    }

    public function write($key, $data, $lifetime=null){
        $filename = $this->getFilename($key);
        if($handle = fopen($filename, 'w+')){
            $datas = serialize(array('data'=>$data, 'createtime'=>time(), 'lifetime'=>$lifetime));
            flock($handle, LOCK_EX);
            $res = fwrite($handle, $datas);
            flock($handle, LOCK_UN);
            fclose($handle);
            if($res !== false){
                return true;
            }
        }
        return false;
    }

    public function delete($key=null){
        if($key == null) {
            $handle = opendir($this->dir);
            while (($file = readdir($handle)) !== false) {
                if ($file != '.' && $file != '..') {
                    $fullPath = $this->dir . '/' . $file;
                    if (is_dir($fullPath)) {
                        $this->delete($key);
                    } else {
                        unlink($fullPath);
                    }
                }
            }
            closedir($handle);
        }else{
            unlink($this->getFilename($key));
        }
    }
}
