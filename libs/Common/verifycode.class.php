<?php
class verifycode {
    private $width;
    private $height;
    private $length;
    private $snow;
    private $line;
    private $font = ['./static/font/Elephant.ttf'];
    private $chars = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
    private $img;
    public $code; //外部调用获取验证码字符

    /**
     * verifycode constructor.实例化时必须传入图片宽高，长度默认4，雪花、干扰线默认0
     * @param int $width
     * @param int $height
     * @param int $length
     * @param int $snow
     * @param int $line
     */
    public function __construct($width, $height, $length=4, $snow=0, $line=0){
        $this->width = $width;
        $this->height = $height;
        $this->length = $length;
        $this->snow = $snow;
        $this->line = $line;
        $this->code = $this->getCode();
    }

    private function getCode(){
        $res = "";
        $len = strlen($this->chars) - 1;
        for($i = 0; $i < $len; $i++){
            $res .= $this->chars[mt_rand(0, $len)];
        }
        return strtolower($res);
    }

    private function createBg(){
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $bgColor = imagecolorallocate($this->img, mt_rand(160, 255), mt_rand(160, 255), mt_rand(160, 255));
        imagefill($this->img, 0, 0, $bgColor);
    }

    private function drawDistrub(){
        if($this->line){
            for($i = 0; $i < $this->line; $i++){
                $color = imagecolorallocate($this->img, mt_rand(0, 200), mt_rand(0, 200), mt_rand(0, 200));
                imageline($this->img, mt_rand(1, $this->width), mt_rand(1, $this->height), mt_rand(1, $this->width), mt_rand(1, $this->height), $color);
            }
        }
        if($this->snow){
            for($i = 0; $i < $this->snow; $i++){
                $color = imagecolorallocate($this->img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
                imagestring($this->img, mt_rand(1, 5), mt_rand(0,$this->width),mt_rand(0,$this->height),'*',$color);
            }
        }
    }

    private function writeCode(){
        for($i = 0; $i < $this->length; $i++) {
            $fontSize = mt_rand(15, 20);
            $angle = mt_rand(-30, 30);
            $x = mt_rand(2, 6) + $i * 100 / $this->length;
            $y = mt_rand(20, 25);
            $color = imagecolorallocate($this->img, mt_rand(20, 120), mt_rand(20, 120), mt_rand(20, 120));
            $font = array_rand($this->font);
            $str = substr($this->code, $i, 1);
            imagettftext($this->img, $fontSize, $angle, $x, $y, $color, $font, $str);
        }
    }

    private function outPut(){
        header("content-type:image/gif");
        imagegif($this->img);
        imagedestroy($this->img);
    }

    /**
     * 外部调用生成验证图片
     */
    public function doIt(){
        $this->createBg();
        $this->drawDistrub();
        $this->writeCode();
        $this->outPut();
    }
}