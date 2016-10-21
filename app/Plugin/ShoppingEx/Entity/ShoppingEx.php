<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\ShoppingEx\Entity;


class ShoppingEx extends \Eccube\Entity\AbstractEntity
{
    private $id;

    private $cardno1;
    private $cardno2;
    private $cardno3;
    private $cardno4;

    private $holder;
    private $cardtype;
    private $limitmon;
    private $limityear;
    private $cardsec;
    private $content;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    public function getCardno1()
    {
        return $this->cardno1;
    }

    public function setCardno1($v)
    {
        $this->cardno1 = $v;

        return $this;
    }
    public function getCardno2()
    {
        return $this->cardno2;
    }

    public function setCardno2($v)
    {
        $this->cardno2 = $v;

        return $this;
    }
    public function getCardno3()
    {
        return $this->cardno3;
    }

    public function setCardno3($v)
    {
        $this->cardno3 = $v;

        return $this;
    }
    public function getCardno4()
    {
        return $this->cardno4;
    }

    public function setCardno4($v)
    {
        $this->cardno4 = $v;

        return $this;
    }

    public function getHolder()
    {
        return $this->holder;
    }
    public function setHolder($v)
    {
        $this->holder = $v;

        return $this;
    }

    public function getCardtype()
    {
        return $this->cardtype;
    }
    public function setCardtype($v)
    {
        $this->cardtype = $v;

        return $this;
    }

    public function getLimitmon()
    {
        return $this->limitmon;
    }
    public function setLimitmon($v)
    {
        $this->limitmon = $v;

        return $this;
    }

    public function getLimityear()
    {
        return $this->limityear;
    }
    public function setLimityear($v)
    {
        $this->limityear = $v;

        return $this;
    }

    public function getCardsec()
    {
        return $this->cardsec;
    }

    public function setCardsec($v)
    {
        $this->cardsec = $v;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}
