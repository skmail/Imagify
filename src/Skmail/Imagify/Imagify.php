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
    public function crop($source,$width,$height)
    {
        $parameters = [
            'method' => 'crop',
            'width' =>  $width,
            'height' => $height,
            'source' => $source
        ];
        $path = $this->urlResolver->replaceRouteParameters($parameters);
        return $this->urlResolver->url($path);
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
        $path = $this->urlResolver->replaceRouteParameters($parameters);
        return $this->urlResolver->url($path);
    }
} 