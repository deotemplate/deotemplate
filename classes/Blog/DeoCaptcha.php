<?php
/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
if (!defined('_PS_VERSION_')) { exit; }

if (!class_exists('DeoCaptcha')) {

    class DeoCaptcha
    {
        protected $code;
        protected $width = 33;
        protected $height = 100;

        public function __construct()
        {
            $this->code = Tools::substr(sha1(mt_rand()), 17, 6);
        }

        public function getCode()
        {
            return $this->code;
        }

        public function showImage()
        {
            $image = imagecreatetruecolor($this->height, $this->width);

            $width = imagesx($image);
            $height = imagesy($image);

            $black = imagecolorallocate($image, 0, 0, 0);
            $white = imagecolorallocate($image, 255, 255, 255);
            $red = imagecolorallocatealpha($image, 255, 033, 0, 75);
            $green = imagecolorallocatealpha($image, 33, 255, 0, 75);
            $blue = imagecolorallocatealpha($image, 0, 22, 255, 75);

            imagefilledrectangle($image, 0, 0, $width, $height, $white);

            imagefilledellipse($image, ceil(rand(5, 145)), ceil(rand(0, 35)), 30, 30, $red);
            imagefilledellipse($image, ceil(rand(5, 145)), ceil(rand(0, 35)), 30, 30, $green);
            imagefilledellipse($image, ceil(rand(5, 145)), ceil(rand(0, 35)), 30, 30, $blue);

            imagefilledrectangle($image, 0, 0, $width, 0, $black);
            imagefilledrectangle($image, $width - 1, 0, $width - 1, $height - 1, $black);
            imagefilledrectangle($image, 0, 0, 0, $height - 1, $black);
            imagefilledrectangle($image, 0, $height - 1, $width, $height - 1, $black);

            imagestring($image, 10, (($width - (Tools::strlen($this->code) * 9)) / 2), (($height - 15) / 2), $this->code, $black); # validate module

            header('Content-type: image/jpeg');
            // Tools::redirect(false, false, null, 'Content-type: image/jpeg');

            imagejpeg($image);

            imagedestroy($image);
        }
    }
}
