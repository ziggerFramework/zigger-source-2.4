<?php
namespace Make\Library;

use Corelib\Func;

class Imgresize {

    public $orgimg;
    public $newimg;
    public $width;
    public $quality = SET_IMAGE_QUALITY;
    public $type;
    private $tmporg;
    private $tmpnew;

    public function set($arr)
    {
        foreach ($arr as $key => $value) {
            $this->{$key} = $value;
        }
    }

    private function make_tmporg()
    {
        $createFn = array(
            'png' => 'imagecreatefrompng',
            'gif' => 'imagecreatefromgif',
        );
        
        $this->tmporg = isset($createFn[$this->type]) ? $createFn[$this->type]($this->orgimg) : imagecreatefromjpeg($this->orgimg);   
    }

    private function make_resampled()
    {
        $sizeinfo = getimagesize($this->orgimg);
        $org_width = $sizeinfo[0];
        $org_height = $sizeinfo[1];

        if ($org_width > $this->width) {
            $height = intval($this->width * ($org_height / $org_width));

        } else {
            $this->width = intval($org_width);
            $height = intval($org_height);
        }

        $this->tmpnew = imagecreatetruecolor($this->width,$height);
        if ($this->type == 'png') {
            imagealphablending($this->tmpnew, false);
            imagesavealpha($this->tmpnew, true);
        }
        imagecopyresampled($this->tmpnew, $this->tmporg, 0, 0, 0, 0, $this->width, $height, $org_width, $org_height);
    }

    private function destroy()
    {
        imagedestroy($this->tmporg);
        imagedestroy($this->tmpnew);
    }

    public function make()
    {
        $this->type = Func::get_filetype($this->orgimg);
        $this->make_tmporg();
        $this->make_resampled();

        $outputFn = array(
            'png' => 'imagepng',
            'gif' => 'imagegif'
        );
        
        $output = isset($outputFn[$this->type]) ? $outputFn[$this->type] : 'imagejpeg';
        
        if (in_array($output, array('imagejpeg'))) {
            $output($this->tmpnew, $this->newimg, $this->quality);
        } else {
            $output($this->tmpnew, $this->newimg);
        }

        $this->destroy();
    }
}
