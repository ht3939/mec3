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

namespace Plugin\ProductColor\Entity;

use Eccube\Util\EntityUtil;

class ProductProductColor extends \Eccube\Entity\AbstractEntity
{
    // /**
    //  * @return string
    //  */
    // public function __toString()
    // {
    //     return $this->get();
    // }

    private $id;
    private $create_date;
    private $update_date;
    private $ProductColor;
    private $Product;
    //private $customer_id;
    //private $productcolor_id;

    public function __construct()
    {
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }
    // public function setCustomerId($cid)
    // {
    //     $this->customer_id = $cid;
    //     return $this;
    // }

    // public function getCustomerId()
    // {
    //     return $this->customer_id;
    // }
    // public function setProductColorId($productcolor_id)
    // {
    //     $this->productcolor_id = $productcolor_id;
    //     return $this;
    // }

    // public function getProductColorId()
    // {
    //     return $this->productcolor_id;
    // }


    // public function setProductColorUrl($productcolor_url)
    // {
    //     $this->productcolor_url = $productcolor_url;
    //     return $this;
    // }

    // public function getProductColorUrl()
    // {
    //     return $this->productcolor_url;
    // }

    // public function setDelFlg($delFlg)
    // {
    //     $this->del_flg = $delFlg;

    //     return $this;
    // }

    // public function getDelFlg()
    // {
    //     return $this->del_flg;
    // }

    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    public function getCreateDate()
    {
        return $this->create_date;
    }

    // public function setUpdateDate($updateDate)
    // {
    //     $this->update_date = $updateDate;

    //     return $this;
    // }

    // public function getUpdateDate()
    // {
    //     return $this->update_date;
    // }
    
    public function setProductColor(ProductColor $productcolor)
    {
        $this->ProductColor = $productcolor;

        return $this;
    }

    public function getProductColor()
    {
        if (EntityUtil::isEmpty($this->ProductColor)) {
            return null;
        }

        return $this->ProductColor;
    }
    public function setProduct(\Eccube\Entity\Product $product)
    {
        $this->Product = $product;

        return $this;
    }

    public function getProduct()
    {
        if (EntityUtil::isEmpty($this->Product)) {
            return null;
        }

        return $this->Product;
    }

}
