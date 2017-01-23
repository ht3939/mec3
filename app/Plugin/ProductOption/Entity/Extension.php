<?php
/*
 * Plugin Name : ProductOption
 *
 * Copyright (C) 2015 BraTech Co., Ltd. All Rights Reserved.
 * http://www.bratech.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ProductOption\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class Extension extends \Eccube\Entity\AbstractEntity
{

    private $option_id;
    private $descdisp_flg;
    private $Option;

    public function __construct()
    {
        // $this->Option = new ArrayCollection();
        // if(is_null($this->Option)){
        //     $this->Option = new \Plugin\ProductOption\Entity\Option();
        // }
    }

    public function setId($id)
    {
        $this->option_id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->option_id;
    }

    public function setDescdispFlg($descdisp_flg)
    {
        $this->descdisp_flg = $descdisp_flg;

        return $this;
    }

    public function getDescdispFlg()
    {
        return $this->descdisp_flg;
    }

    public function setOption(\Plugin\ProductOption\Entity\Option $option)
    {
        $this->Option = $option;

        return $this;
    }

    public function getOption()
    {
        return $this->Option;
    }

}
