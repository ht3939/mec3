<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */
namespace Plugin\KintoneTransAdmin\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Eccube\Util\EntityUtil;

/**
 * KintoneTransAdminProduct
 */
class KintoneTransAdminProduct extends \Eccube\Entity\AbstractEntity
{

    /**
     *
     * @var integer
     */
    private $id;

    /**
     *
     * @var string
     */
    private $tagtype;
    private $enable_flg;
    private $conditions;
    private $tagurl;

    /**
     *
     * @var integer
     */
    private $del_flg;

    /**
     *
     * @var \DateTime
     */
    private $create_date;

    /**
     *
     * @var \DateTime
     */
    private $update_date;

    /**
     * Constructor
     */
    public function __construct()
    {}

    /**
     * Get kintonetransadmin product id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set kintonetransadmin product id
     *
     * @param integer $kintonetransadmin_product_id
     * @return Module
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get commend
     *
     * @return string
     */
    public function getTagtype()
    {
        return $this->tagtype;
    }

    /**
     * Set comment
     *
     * @param
     *            string
     * @return Module
     */
    public function setTagtype($v)
    {
        $this->tagtype = $v;

        return $this;
    }

    /**
     * Get commend
     *
     * @return string
     */
    public function getEnableFlg()
    {
        return $this->enable_flg;
    }

    /**
     * Set comment
     *
     * @param
     *            string
     * @return Module
     */
    public function setEnableFlg($v)
    {
        $this->enable_flg = $v;

        return $this;
    }


    /**
     * Get commend
     *
     * @return string
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Set comment
     *
     * @param
     *            string
     * @return Module
     */
    public function setConditions($condition)
    {
        $this->conditions = $condition;

        return $this;
    }

    /**
     * Get commend
     *
     * @return string
     */
    public function getTagurl()
    {
        return $this->tagurl;
    }

    /**
     * Set comment
     *
     * @param
     *            string
     * @return Module
     */
    public function setTagurl($v)
    {
        $this->tagurl = $v;

        return $this;
    }



    /**
     * Set del_flg
     *
     * @param integer $delFlg
     * @return Order
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get del_flg
     *
     * @return integer
     */
    public function getDelFlg()
    {
        return $this->del_flg;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return Module
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set update_date
     *
     * @param \DateTime $updateDate
     * @return Module
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }


}
