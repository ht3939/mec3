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
namespace Plugin\CustomUrlUserPage\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Eccube\Util\EntityUtil;

/**
 * CustomUrlUserPage
 */
class CustomUrlUserPage extends \Eccube\Entity\AbstractEntity
{

    /**
     *
     * @var integer
     */
    private $id;

    private $customurl;
    private $userpage;
    private $bindname;
    private $pagethumbnail;
    private $pageinfo;
    private $pagecategorykey;
    private $index_flg;

    /**
     *
     * @var integer
     */
    private $rank;

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
     * @var \Eccube\Entity\PageLayout
     */
    private $PageLayout;

    private $CustomUrlUserPageImages;

    private $images;

    private $add_images;
    private $delete_images;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->CustomUrlUserPageImages = new ArrayCollection();

    }

    /**
     * Get recommend product id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setCustomUrl($v)
    {
        $this->customurl = $v;

        return $this;
    }
    public function getCustomUrl()
    {
        return $this->customurl;
    }
    public function setUserPage($v)
    {
        $this->userpage = $v;

        return $this;
    }
    public function getUserPage()
    {
        return $this->userpage;
    }
    public function setBindName($v)
    {
        $this->bindname = $v;

        return $this;
    }
    public function getBindName()
    {
        return $this->bindname;
    }
    public function setPageThumbnail($v)
    {
        $this->pagethumbnail = $v;

        return $this;
    }
    public function getPageThumbnail()
    {
        return $this->pagethumbnail;
    }
    public function setPageInfo($v)
    {
        $this->pageinfo = $v;

        return $this;
    }
    public function getPageInfo()
    {
        return $this->pageinfo;
    }
    public function setPageCategoryKey($v)
    {
        $this->pagecategorykey = $v;

        return $this;
    }
    public function getPageCategoryKey()
    {
        return $this->pagecategorykey;
    }
    public function setIndexFlg($v)
    {
        $this->index_flg = $v;

        return $this;
    }
    public function getIndexFlg()
    {
        return $this->index_flg;
    }


    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set rank
     *
     * @param
     *            integer
     * @return Module
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

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

    /**
     * Set PageLayout
     *
     * @param \Eccube\Entity\PageLayout $product
     * @return PageLayout
     */
    public function setPageLayout( $pagelayout)
    {//\Plugin\CustomUrlUserPage\Entity\PageLayout
        
        $this->PageLayout = $pagelayout;

        return $this;
    }

    /**
     * Get PageLayout
     *
     * @return \Eccube\Entity\PageLayout 
     */
    public function getPageLayout()
    {
        if (EntityUtil::isEmpty($this->PageLayout)) {
            return null;
        }
        return $this->PageLayout;
    }



    /**
     * Add ProductImage
     *
     * @param \Eccube\Entity\ProductImage $productImage
     * @return Product
     */
    public function addCustomUrlUserPageImage(\Plugin\CustomUrlUserPage\Entity\CustomUrlUserPageImage $image)
    {
        $this->CustomUrlUserPageImages[] = $image;

        return $this;
    }

    /**
     * Remove ProductImage
     *
     * @param \Eccube\Entity\ProductImage $productImage
     */
    public function removeCustomUrlUserPageImage(\Plugin\CustomUrlUserPage\Entity\CustomUrlUserPageImage $image)
    {
        $this->CustomUrlUserPageImages->removeElement($image);
    }


    public function getMainFileName()
    {
        if (count($this->CustomUrlUserPageImages) > 0) {
            return $this->CustomUrlUserPageImages[0];
        } else {
            return null;
        }
    }


    /**
     * Get Product
     *
     * @return \Plugin\ProductClassEx\Entity\ProductClassExImage
     */
    public function getCustomUrlUserPageImages()
    {
        return $this->CustomUrlUserPageImages;
    }

    /**
     * Set images
     *
     * @param  string       $images
     * @return ProductClassEx
     */
    public function setImages($images)
    {
        $this->images = $images;

        return $this;
    }

    /**
     * Get stock
     *
     * @return string
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set images
     *
     * @param  string       $images
     * @return ProductClassEx
     */
    public function setAddImages($images)
    {
        $this->add_images = $images;

        return $this;
    }

    /**
     * Get stock
     *
     * @return string
     */
    public function getAddImages()
    {
        return $this->add_images;
    }

    /**
     * Set images
     *
     * @param  string       $images
     * @return ProductClassEx
     */
    public function setDeleteImages($images)
    {
        $this->delete_images = $images;

        return $this;
    }

    /**
     * Get stock
     *
     * @return string
     */
    public function getDeleteImages()
    {
        return $this->delete_images;
    }



}
