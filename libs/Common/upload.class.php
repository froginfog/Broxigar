<?php
class upload {
    private $uploadConfig = array(
        'path'         => './uploads',
        'allowtype'    => ['jpg', 'gif', 'png', 'bmp'],
        'maxsize'      => 5*1024*1024,
        'israndomname' => true,
        'isthumb'      => false,
        'iswatermark'  => false,
        'thumb'        => [
            /**
             * 缩略图放置文件夹名 => 缩略图宽、高
             */
            '50x50'           => [50, 50],
            '100x100'         => [100, 100]
        ]
    );
    private $errMsg = array();
    private $finalFileName;
    private $final; //文件名，通过getFinal()方法调用，用于插入数据库，多文件上传时每个文件名用逗号分隔。
    private $oriName;

    public function set($arr){
        foreach($arr as $key=>$value){
            if(array_key_exists($key, $this->uploadConfig)){
                $this->uploadConfig[$key] = $value;
            }
        }
    }

    public function getFinal(){
        return $this->final;
    }

    private function thumb($imageName){
        $image = $this->uploadConfig['path'].'/'.$imageName;
        if(!list($src_w, $src_h, $type) = getimagesize($image)){
            $this->errMsg[] = $this->getError(100);
            return false;
        }
        $createFunc = $this->createImageFunc($type);
        $outFunc = $this->outPutImageFunc($type);
        $src_img = $createFunc($image);
        foreach($this->uploadConfig['thumb'] as $folder=>$size){
            $dst_img = imagecreatetruecolor(intval($size[0]), intval($size[1]));
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0,intval($size[0]), intval($size[1]), $src_w, $src_h);
            $destination_folder = $this->uploadConfig['path'].'/'.$folder;
            if(!file_exists($destination_folder) || !is_writable($destination_folder)){
                mkdir($destination_folder, 0777, true);
            }
            $destination = $destination_folder.'/'.$imageName;
            $outFunc($dst_img, $destination);
            imagedestroy($dst_img);
        }
        imagedestroy($src_img);
    }

    /**
     * imagecreatefromgif()
     * @param int $type from getimagesize()
     * @return string
     */
    private function createImageFunc($type){
        $mime = image_type_to_mime_type($type);
        $res = str_replace('/', 'createfrom', $mime);
        return $res;
    }

    /**
     * imagegif()
     * @param int $type from getimagesize()
     * @return string
     */
    private function outPutImageFunc($type){
        $mime = image_type_to_mime_type($type);
        $res = str_replace('/', null, $mime);
        return $res;
    }

    private function waterMark($imageName){
        global $config;
        $watermark = $config['watermark'];
        $dstImg = $this->uploadConfig['path'].'/'.$imageName;
        list($watermark_w, $watermark_h, $watermark_type) = getimagesize($watermark);
        list($dstImg_w, $dstImg_h, $dstImg_type) = getimagesize($dstImg);

        //分别创建水印图片资源和目标图片资源
        $createwater = $this->createImageFunc($watermark_type);
        $watermark = $createwater($watermark);
        $createDstImg = $this->createImageFunc($dstImg_type);
        $dstImg = $createDstImg($dstImg);

        //水印放在图片左下角
        $x = $dstImg_w - $watermark_w;
        $y = $dstImg_h - $watermark_h;

        imagecopy($dstImg, $watermark, $x, $y, 0, 0, $watermark_w, $watermark_h);
        $outputFunc = $this->outPutImageFunc($dstImg_type);
        
        $watermark_folder = $this->uploadConfig['path'].'/watermarked';
        if(!file_exists($watermark_folder) || !is_writable($watermark_folder)){
            mkdir($watermark_folder, 0777, true);
        }
        $outputFunc($dstImg, $watermark_folder.'/'.$imageName);
    }

    private function getError($errNum){
        $str = "文件 {$this->oriName} 上传出错：";
        switch ($errNum){
            case 1:
                $str .= "上传的文件超过了php.ini中upload_max_filesize选项限制的值";
                break;
            case 2:
                $str .= "上传文件的大小超过了HTML表单中MAX_FILE_SIZE选项指定的值";
                break;
            case 3:
                $str .= "文件只有部分被上传";
                break;
            case 4:
                $str .= "没有文件被上传";
                break;
            case 6:
                $str .= "找不到临时文件夹";
                break;
            case 7:
                $str .= "文件写入失败";
                break;
            case 88:
                $str .= "不允许的文件类型";
                break;
            case 99:
                $str .= "文件大小不能超过 {$this->uploadConfig['maxsize']}";
                break;
            case 100:
                $str .= "无法生成缩略图的文件类型";
                break;
            case 101:
                $str .= "非POST方式上传文件";
                break;
            case 102:
                $str .= "上传失败";
                break;
            default:
                $str .= "未知错误";
        }
        return $str;
    }

    public function doUpload($fieldName){
        $this->final = "";
        $res = true;
        if(!file_exists($this->uploadConfig['path']) || !is_writable($this->uploadConfig['path'])){
            $this->errMsg[] = $this->uploadConfig['path'].'文件夹不存在。';
            $res = false;
        }
        $name = $_FILES[$fieldName]['name'];
        $tmp_name = $_FILES[$fieldName]['tmp_name'];
        $error = $_FILES[$fieldName]['error'];
        $size = $_FILES[$fieldName]['size'];
        if(is_array($name)){
            for($i = 0; $i < count($name); $i++){
                $this->oriName = $name[$i];
                if($error[$i] !== UPLOAD_ERR_OK){
                    $this->errMsg[] = $this->getError($error[$i]);
                    $res = false;
                }
                if($size[$i] > $this->uploadConfig['maxsize']){
                    $this->errMsg[] = $this->getError(99);
                    $res = false;
                }
                if(!in_array($this->getExt($this->oriName), $this->uploadConfig['allowtype'])){
                    $this->errMsg[] = $this->getError(88);
                    $res = false;
                }
                if(is_uploaded_file($tmp_name[$i]) && $res == true) {
                    if ($this->uploadConfig['israndomname']) {
                        $this->finalFileName = $this->getRandomName($this->oriName);
                    } else {
                        $this->finalFileName = $this->oriName;
                    }
                    if(move_uploaded_file($tmp_name[$i], $this->uploadConfig['path'].'/'.$this->finalFileName)){
                        if($this->final == ""){
                            $sep = "";
                        }else{
                            $sep = ",";
                        }
                        $this->final .= $sep.$this->finalFileName;
                        if($this->uploadConfig['isthumb']){
                            $this->thumb($this->finalFileName);
                        }
                        if($this->uploadConfig['iswatermark']){
                            $this->waterMark($this->finalFileName);
                        }
                    }else{
                        $this->errMsg[] = $this->getError(102);
                        $res = false;
                    }
                }else{
                    $this->errMsg[] = $this->getError(101);
                    $res = false;
                }
            }
        }else{
            $this->oriName = $name;
            if($error !== UPLOAD_ERR_OK){
                $this->errMsg[] = $this->getError($error);
                $res = false;
            }
            if($size > $this->uploadConfig['maxsize']){
                $this->errMsg[] = $this->getError(99);
            }
            if(!in_array($this->getExt($this->oriName), $this->uploadConfig['allowtype'])){
                $this->errMsg[] = $this->getError(88);
            }
            if(is_uploaded_file($tmp_name)){
                if ($this->uploadConfig['israndomname']) {
                    $this->finalFileName = $this->getRandomName($this->oriName);
                } else {
                    $this->finalFileName = $this->oriName;
                }
                if(move_uploaded_file($tmp_name, $this->uploadConfig['path'])){
                    $this->final = $this->finalFileName;
                    if($this->uploadConfig['isthumb']){
                        $this->thumb($this->final);
                    }
                    if($this->uploadConfig['iswatermark']){
                        $this->waterMark($this->final);
                    }
                }else{
                    $this->errMsg[] = $this->getError(102);
                    $res = false;
                }
            }
        }
        return $res;
    }

    private function getExt($fileName){
        $name = explode('.',$fileName);
        return strtolower(end($name));
}

    private function getRandomName($fileName){
        $ext = $this->getExt($fileName);
        $name = md5(uniqid(microtime(), true));
        return $name.'.'.$ext;
    }

    public function printError(){
        return $this->errMsg;
    }
}