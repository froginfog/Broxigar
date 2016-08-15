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

    public function read($key, $lifeTime=null){
        $filename = $this->getFilename($key);
        if($data = @file_get_contents($filename)){
            $res = unserialize($data);
            $lt = $res['createtime'] + $lifeTime;
            if($lt > time() || $lifeTime == null){
                return $res['data'];
            }
        }
        return false;
    }

    public function write($key, $data){
        $filename = $this->getFilename($key);
        if($handle = fopen($filename, 'w+')){
            $datas = serialize(array('data'=>$data, 'createtime'=>time()));
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
        $handle = opendir($this->dir);
        while(($file = readdir($handle)) !== false){
            if($key == null) {
                if ($file != '.' && $file != '..') {
                    $fullPath = $this->dir . '/' . $file;
                    if (is_dir($fullPath)) {
                        $this->delete($key);
                    } else {
                        unlink($fullPath);
                    }
                }
            }else{
                unlink($this->dir.'/'.$this->getFilename($key));
            }
        }
    }
}