<?php
/*
 * Copyright(c) 2015 SystemFriend Inc. All rights reserved.
 * http://ec-cube.systemfriend.co.jp/
 */

namespace Plugin\CloseProductRedirect\Entity;

/**
 * Information about payment of an order
 *
 */
class CprProductRedirect extends \Eccube\Entity\AbstractEntity
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $redirect_product_id;

    /**
     * @var string
     */
    private $redirect_url;

    /**
     * @var integer
     */
    private $redirect_select;

    /**
     * Set id
     *
     * @return Order
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set redirect_product_id
     *
     * @param  integer $redirect_product_id
     * @return Order
     */
    public function setRedirectProductId($redirect_product_id)
    {
        $this->redirect_product_id = $redirect_product_id;

        return $this;
    }

    /**
     * Get redirect_product_id
     *
     * @return integer
     */
    public function getRedirectProductId()
    {
        return $this->redirect_product_id;
    }

    /**
     * Set redirect_url
     *
     * @param  string $redirect_url
     * @return 
     */
    public function setRedirectUrl($redirect_url)
    {
        $this->redirect_url = $redirect_url;

        return $this;
    }

    /**
     * Get redirect_url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirect_url;
    }

    /**
     * Set redirect_select
     *
     * @param  string $redirect_select
     * @return 
     */
    public function setRedirectSelect($redirect_select)
    {
        $this->redirect_select = $redirect_select;

        return $this;
    }

    /**
     * Get redirect_select
     *
     * @return string
     */
    public function getRedirectSelect()
    {
        return $this->redirect_select;
    }
}
