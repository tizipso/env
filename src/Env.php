<?php

namespace Dcat\Admin\Extension\Env;

use Dcat\Admin\Extension;

class Env extends Extension
{
    const NAME = 'env';

    protected $serviceProvider = EnvServiceProvider::class;

    protected $composer = __DIR__.'/../composer.json';

    protected $assets = __DIR__.'/../resources/assets';

    protected $views = __DIR__.'/../resources/views';

//    protected $lang = __DIR__.'/../resources/lang';

    protected $menu = [
        'title' => 'Env',
        'path'  => 'env',
        'icon'  => 'fa-puzzle-piece',
    ];
}
