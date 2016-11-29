<?php

/*
 * This file is part of the CRM3
 *
 * Copyright (C) 2016 Nobuhiko Kimoto
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CRM3;

use Eccube\Event\EventArgs;

class CRM3Event
{

    /** @var  \Eccube\Application $app */
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function onMailContact(EventArgs $event)
    {
        $data = $event['formData'];

        $contact = new \Plugin\CRM3\Entity\Contact();
        $contact->setName($data['name01'].$data['name02']);
        $contact->setKana($data['kana01'].$data['kana02']);
        $contact->setZip($data['zip01'].$data['zip02']);
        $contact->setAddr($data['pref']->getName().$data['addr01'].$data['addr02']);
        $contact->setTel($data['tel01'].$data['tel02'].$data['tel03']);
        $contact->setContents($data['contents']);
        $contact->setEmail($data['email']);

        if (is_numeric($this->app['user']->getId())) {
            $customer_id = $this->app['user']->getId();
        } else {
            $customer_id = 0;
        }
        $contact->setCustomerId($customer_id);
        $contact->setStatus(1); //default

        $this->app['orm.em']->persist($contact);
        $this->app['orm.em']->flush();
    }

}
