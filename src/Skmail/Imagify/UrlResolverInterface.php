<?php
/**
 * @author: Solaiman Kmail - Bluetd <s.kmail@blue.ps> 
 */

namespace Skmail\Imagify;

use Illuminate\Foundation\Application;

interface UrlResolverInterface
{
    public function __construct(Application $app);

    /**
     * Replace route parameters
     *
     * @param $array
     * @return mixed|string
     */
    public function replaceRouteParameters($array);

    /**
     * Return full url
     * @param $path
     * @return mixed
     */
    public function url($path);
    /**
     * Return full route
     * @return string
     */
    public function route();
} 