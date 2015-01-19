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
        $this->image->setSource($source);
        $this->image->setParams([
            'width' => $width,
            'height' => $height,
            'method' => $method
        ]);
        return $this->image->save()->response();
    }
} 