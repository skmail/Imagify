<?php

namespace Skmail\Imagify\Controllers;

use Skmail\Imagify\Image;
use \Illuminate\Routing\Controller;
class ImagifyController extends  Controller
{

    protected $image;

    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    public function response($method,$width,$height,$source)
    {

        if(\Config::get('imagify::watermark',null)){
            if(strpos($source,'w/') === 0){
                $watermark = true;
                $source = substr($source,2);
            }else{
                $watermark = false;
            }
        }else{
            $watermark = false;
        }
        $this->image->setSource($source);
        $this->image->setParams([
            'width' => $width,
            'height' => $height,
            'method' => $method,
            'watermark' => $watermark
        ]);
        return $this->image->save()->response();
    }
} 