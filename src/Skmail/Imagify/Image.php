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
use Illuminate\Support\Facades\Response;
use Exception;
use Config;


class Image {

    protected $params = [];

    protected $source;

    protected $files;

    protected $imagine;

    protected $image;

    protected $box;

    protected $options = [];

    protected $processed;

    protected $savePath;

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
    public function setParams( array $params)
    {
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
     * @throws Exception
     * @return Image
     */
    public function setSource($source)
    {
        if(!$this->files->exists($source)){
            throw new Exception("Image not found");
        }
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
        $format = $this->format();
        $contents = $this->image->get($format,$this->getOptions());
        //Create the response
        $mime = $this->getMimeFromFormat($format);
        $response = Response::make($contents, 200);
        $response->header('Content-Type', $mime);
        return $response;
    }

    public function save()
    {
        $this->process();
        $this->createDirectory();
        $this->image->save($this->getSavePath(),$this->getOptions());
        return $this;
    }


    protected function  process()
    {
        if($this->isProcessed()){
            return ;
        }
        $this->setProcessed(true);
        $this->maxExceed();
        $this->minExceed();
        $this->image = $this->imagine->open($this->getSource());
        $this->box = new Box($this->getParam('width'),$this->getParam('height'));
        $method = $this->getParam('method','resize');
        if(!in_array($method,$this->getMethods())){
            throw new Exception('Undefined method '.  $method);
        }
        if(!$this->existsOption('quality')){
            $this->setOption('quality', $this->config('imagify::quality'));
        }
        if($this->format() === 'png') {
            $this->setOption($this->getOption('quality'),round((100 - $this->getOption('quality')) * 9 / 100));
        }
        $this->{$method}();
        return $this;
    }

    public function crop()
    {
        $srcBox = $this->image->getSize();
        $sourceWidth = $srcBox->getWidth();
        $sourceHeight = $srcBox->getHeight();
        $targetWidth = $this->box->getWidth();
        $targetHeight = $this->box->getHeight();
        $sourceRatio = $sourceWidth / $sourceHeight;
        $targetRatio = $targetWidth / $targetHeight;
        if ( $sourceRatio < $targetRatio ) {
            $scale = $sourceWidth / $targetWidth;
        } else {
            $scale = $sourceHeight / $targetHeight;
        }
        $resizeWidth = (int)($sourceWidth / $scale);
        $resizeHeight = (int)($sourceHeight / $scale);
        $cropLeft = (int)(($resizeWidth - $targetWidth) / 2);
        $cropTop = (int)(($resizeHeight - $targetHeight) / 2);
        $cropPoint = new Point($cropLeft,$cropTop);
        $box = new Box($resizeWidth, $resizeHeight);
        $image = $this->image->thumbnail($box, \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND);
        $image->crop($cropPoint, $this->box);
        $this->image = $image;

    }

    public function resize()
    {
        $this->image->resize(new Box($this->getParam('width'), $this->getParam('height')));
    }



    public function getType(){
        $ext = pathinfo($this->source, PATHINFO_EXTENSION);
        switch($ext){
            case 'jpg':
            case 'jpeg':
                return 'jpeg';
            break;
            default :
                return $ext;
        }
    }

    /**
     * Get the format of an image
     *
     * @return ImageInterface
     */
    public function format()
    {
        $format = @exif_imagetype($this->getSource());
        switch($format) {
            case IMAGETYPE_GIF:
                return 'gif';
                break;
            case IMAGETYPE_JPEG:
                return 'jpeg';
                break;
            case IMAGETYPE_PNG:
                return 'png';
                break;
        }
        return null;
    }

    /**
     * Get mime type from image format
     *
     * @return string
     */
    protected function getMimeFromFormat($format)
    {
        switch($format) {
            case 'gif':
                return 'image/gif';
                break;
            case 'jpg':
            case 'jpeg':
                return 'image/jpeg';
                break;
            case 'png':
                return 'image/png';
                break;
        }
        return null;
    }

    public function setOptions($options)
    {
        $this->options  = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOption($name,$value){
        $this->options[$name] = $value;
        return $this;
    }

    public function getOption($name){
        if($this->existsOption($name)){
            return $this->options[$name];
        }
    }

    public function existsOption($name){
        return array_key_exists($name,$this->options);
    }

    protected function getMethods(){
        return [
            'crop',
            'resize',
        ];
    }

    public function maxExceed(){
        if($this->config('imagify::max.width') && $this->getParam('width') > $this->config('imagify::max.width') ){
            throw new Exception("Maximum width is " . $this->config('imagify::max.width') );
        }
        if($this->config('imagify::max.height') && $this->getParam('height') > $this->config('imagify::max.height') ){
            throw new Exception("Maximum width is " . $this->config('imagify::max.height') );
        }
    }
    
    public function minExceed(){
        if($this->config('imagify::min.width') && $this->getParam('width') < $this->config('imagify::min.width') ){
            throw new Exception("The minimum width is " . $this->config('imagify::min.width') );
        }
        if($this->config('imagify::max.height') && $this->getParam('height') < $this->config('imagify::min.height') ){
            throw new Exception("The minimum height is  = " . $this->config('imagify::min.height') );
        }
    }

    public function config($name,$default = null){
        return Config::get($name,$default);
    }

    public function isProcessed(){
        return (bool)$this->processed;
    }

    public function setProcessed($bool = true){
        $this->processed = $bool;
        return $this;
    }

    public function createDirectory()
    {
        $dir = $this->getSavePath();
        $dir = dirname($dir);
        if(!$this->files->exists($dir)){
            $this->files->makeDirectory($dir, 0777, true,true);
        }
    }

    public function getSavePath()
    {
        $arr = [
            $this->config('imagify::base_route') ,
            $this->config('imagify::route') ,
            $this->getParam('method') ,
            $this->getParam('width') ,
            $this->getParam('height') ,
            $this->getSource()
        ];
        return implode(DIRECTORY_SEPARATOR,$arr);
    }


} 