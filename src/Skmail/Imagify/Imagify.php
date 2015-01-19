<?php
/**
 * @author: Solaiman Kmail - Bluetd <s.kmail@blue.ps> 
 */

namespace Skmail\Imagify;

use Illuminate\Foundation\Application;

class Imagify
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app  = $app;
    }

    /**
     * Return cropped image url
     *
     * @param $source
     * @param $width
     * @param $height
     * @return string
     */
    public function crop($source,$width,$height)
    {
        $parameters = [
            'method' => 'crop',
            'width' =>  $width,
            'height' => $height,
            'source' => $source
        ];
        $path = $this->replaceRouteParameters($parameters);
        return $this->url($path);
    }

    /**
     * Return resized image url
     *
     * @param $source
     * @param $width
     * @param $height
     * @return string
     */
    public function resize($source,$width,$height)
    {
        $parameters = [
            'method' => 'resize',
            'width' =>  $width,
            'height' => $height,
            'source' => $source
        ];
        $path = $this->replaceRouteParameters($parameters);
        return $this->url($path);
    }

    /**
     * Replace route parameters
     *
     * @param $array
     * @return mixed|string
     */
    protected function replaceRouteParameters($array){
        $route = $this->route();
        foreach($array as $name => $value) {
            $route = str_replace('{' . $name . '}', $value, $route);
        }
        return $route;
    }

    /**
     * Return full url
     * @param $path
     * @return mixed
     */
    protected function url($path){
        return \URL::to($path);
    }

    /**
     * Return full route
     * @return string
     */
    protected function route(){
        return $this->app['config']->get('imagify::base_route') . '/' . $this->app['config']->get('imagify::route');
    }
} 