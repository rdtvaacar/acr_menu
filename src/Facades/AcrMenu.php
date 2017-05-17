<?php

namespace Acr\Menu\Facades;

use Illuminate\Support\Facades\Facade;

class AcrMenu extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'AcrMenu';
    }

}