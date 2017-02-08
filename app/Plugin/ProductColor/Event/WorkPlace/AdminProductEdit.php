<?php

namespace Plugin\ProductColor\Event\WorkPlace;

use Eccube\Common\Constant;
use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Validator\Constraints as Assert;

class AdminProductEdit extends AbstractWorkPlace
{    
    public function createTwig(TemplateEvent $event)
    {
        $app = $this->app;

        $source = $event->getSource();
//dump($source);

        if(preg_match('/(.*)id="detail_box__footer" class="row hidden-xs hidden-sm">(.*)\n/',$source, $result)){
        //if(preg_match('/<div id="detail_box__footer".*>\n/',$source, $result)){
            $search = $result[0];
// dump($result);
            $snipet = file_get_contents($app['config']['plugin_realdir']. '/ProductColor/View/admin/product_productcolor.twig');
            $replace = $snipet.$search;
            $source = str_replace($search, $replace, $source);
        }
        

        $event->setSource($source);

    }

}
