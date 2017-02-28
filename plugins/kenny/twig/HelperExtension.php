<?php namespace Kenny\Twig;

use Twig_Extension;
use Twig_Environment;
use Twig_SimpleFunction;
use Cms\Classes\Controller;
use Cms\Classes\ComponentBase;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use October\Rain\Database\Model;

class HelperExtension extends Twig_Extension
{
    public function getName(){
        return 'SK Helper';
    }

    public function getFunctions() {
        return [
            new Twig_SimpleFunction('dump', [$this, 'runDump'], [
                'is_safe' => ['html'],
                'needs_context' => true,
                'needs_environment' => true
            ]),
        ];
    }

    public function bar($baz) {
        return $this->foo . $baz;
    }

}
