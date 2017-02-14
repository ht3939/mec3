<?php


namespace Plugin\CustomNonMember\Event\WorkPlace;

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

class FrontContactConfirm extends AbstractWorkPlace
{    
    private $tag = '<!-- ## customnonmember ## -->'; 
    public function createTwig(TemplateEvent $event)
    {   

        $app = $this->app;
        $tag = $this->tag;
        $source = $event->getSource();

        if (strpos($source, $tag)) {
            $snipet = file_get_contents($app['config']['plugin_realdir']. '/CustomNonMember/Resource/template/default/Event/Contact/contact_confirm.twig');
            $newHtml = $tag.$snipet;
            $source = str_replace($tag, $newHtml, $source);
        }



        $event->setSource($source);

    }

}
