<?php
namespace Kernel\Files;

class TextToImage
{
    private $h_image;

    public function __construct(){
    }

    public function createImagePNG($text, $outputPath) : string|NULL
    {
        $img_name = $this->createImage($text);
        $img_name .= '.png';
        
        if(imagepng($this->h_image, $outputPath . $img_name))
            return $img_name;
        return NULL;
    }

    public function createImageJPG($text, $outputPath) : string|NULL
    {
        $img_name = $this->createImage($text);
        $img_name .= '.jpg';
        
        if(imagejpeg($this->h_image, $outputPath . $img_name))
            return $img_name;
        return NULL;
    }

    /* private */

    private function createImage($text, $fontSize=20, $imageWidth=400, $imageHeight=200) : string
    {
        $font = dirname(__FILE__) . '\..\arial.ttf';
        $angle = 0;

        $splitText = explode ( " " , $text );
        if(count($splitText) > 5){
            $text = $splitText[0] . ' ' . $splitText[1] . ' ' . $splitText[2];
        }

        $this->h_image = imagecreatetruecolor($imageWidth, $imageHeight);
        $grey = imagecolorallocate($this->h_image, 128, 128, 128);
        $black = imagecolorallocate($this->h_image, 0, 0, 0);

        imagefilledrectangle($this->h_image, 0, 0, $imageWidth, $imageHeight, $grey);
        $textBox = imagettfbbox($fontSize, $angle ,$font, $text);
        
        $x = $textBox[0] + (imagesx($this->h_image) / 2) - ($textBox[4] / 2) - 25;
        $y = $textBox[1] + (imagesy($this->h_image) / 2) - ($textBox[5] / 2) - 5;
        imagettftext($this->h_image, $fontSize, $angle, (int)$x, (int)$y, $black, $font, $text);

        return str_replace(' ', '_', $text);
    }
}