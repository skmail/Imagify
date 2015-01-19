<?php
/**
 * Created by PhpStorm.
 * User: solaimankmail
 * Date: 1/19/15
 * Time: 1:17 AM
 */

namespace Skmail\Imagify;

use Imagine\Image\ImageInterface;
use Illuminate\Filesystem\Filesystem;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\Point\Center;
use Illuminate\Http\Response;

class Image {

    protected $params = [];

    protected $source;

    protected $files;

    protected $imagine;

    protected $image;

    protected $box;

    public function __construct(Imagine $imagine,Filesystem $files){
        $this->files = $files;
        $this->imagine = $imagine;
    }
    /**
     * Set parameters
     *
     * @param $params
     * @return Image
     */
    public function setParams($params){
        $this->params = array_merge($this->params,$params);
        return $this;
    }

    public function getParam($param,$default = null){
        if(array_key_exists($param,$this->params)){
            return  $this->params[$param];
        }
        return $default;
    }

    /**
     * @param $source
     * @return Image
     */
    public function setSource($source){
        $this->source = $source;
        return $this;
    }

    public function getSource()
    {
        return $this->source;
    }
    /**
     * Render the image and send a response
     */
    public function response()
    {
        $this->process();

        return $this->image->show('jpeg',['quality' => 8]);
    }

    public function save()
    {

        return $this;
    }

    protected function  process()
    {
        $this->image = $this->imagine->open($this->getSource());
        $this->box = new Box($this->getParam('width'),$this->getParam('height'));
        switch($this->getParam('method','resize')){
            case 'resize':
                $this->resize();
                break;
            case 'crop':
                $this->crop();
                break;
        }
    }

    public function crop()
    {
        //original size
        $srcBox = $this->image->getSize();
        //we scale on the smaller dimension
        if ($srcBox->getWidth() > $srcBox->getHeight()) {
            $width  = $srcBox->getWidth()*($this->box->getHeight()/$srcBox->getHeight());
            $height =  $this->box->getHeight();
            //we center the crop in relation to the width
            $cropPoint = new Point((max($width - $this->box->getWidth(), 0))/2, 0);
        } else {
            $width  = $this->box->getWidth();
            $height =  $srcBox->getHeight()*($this->box->getWidth()/$srcBox->getWidth());
            //we center the crop in relation to the height
            $cropPoint = new Point(0, (max($height - $this->box->getHeight(),0))/2);
        }

        $box = new Box($width, $height);
        //we scale the image to make the smaller dimension fit our resize box
        $image = $this->image->thumbnail($box, \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND);

        //and crop exactly to the box
        $image->crop($cropPoint, $this->box);
        $this->image = $image;


    }

    public function resize()
    {
        $this->image->resize(new Box($this->getParam('width'), $this->getParam('height')));
    }


} 