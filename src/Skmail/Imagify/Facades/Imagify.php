<?php

/**
 * @author: Solaiman Kmail - Bluetd <s.kmail@blue.ps>
 */

namespace Skmail\Imagify\Facades;

use \Illuminate\Support\Facades\Facade;

class Imagify extends Facade
{
    /**
     * @see Illuminate\Support\Facades\Facade#getFacadeAccessor()
     */
    protected static function getFacadeAccessor()
    {
        return 'imagify';
    }
}
