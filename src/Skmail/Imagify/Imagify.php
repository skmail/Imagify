<?php
/**
 * @author: Solaiman Kmail - Bluetd <s.kmail@blue.ps> 
 */

namespace Skmail\Imagify;

use Illuminate\Foundation\Application;

class Imagify
{
    protected $app;

    protected $urlResolver;

    public function __construct(Application $app,UrlResolverInterface $urlResolver)
    {
        $this->app  = $app;
        $this->urlResolver = $urlResolver;
    }

    /**
     * Return cropped image url
     *
     * @param $source
     * @param $width
     * @param $height
     * @return string
     */
    public function crop($source,$width,$height,$watermark = false)
    {
        $parameters = [
            'method' => 'crop',
            'width' =>  $width,
            'height' => $height,
            'source' => $source
        ];

        if($watermark == true && $this->app['config']->get('imagify::watermark',null)){
            $parameters = ['watermark' => 'w'] + $parameters;
        }
        $path = $this->urlResolver->replaceRouteParameters($parameters);
        return $this->urlResolver->url($path);
    }

    /**
     * Return resized image url
     *
     * @param      $source
     * @param      $width
     * @param      $height
     * @param bool $watermark
     * @return string
     */
    public function resize($source,$width,$height,$watermark = false)
    {
        $parameters = [
            'method' => 'resize',
            'width' =>  $width,
            'height' => $height,
            'source' => $source
        ];

        if($watermark == true && $this->app['config']->get('imagify::watermark',null)){
            $parameters = ['watermark' => 'w'] + $parameters;
        }
        $path = $this->urlResolver->replaceRouteParameters($parameters);
        return $this->urlResolver->url($path);
    }
} 