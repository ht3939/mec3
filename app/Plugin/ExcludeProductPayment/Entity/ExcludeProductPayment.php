<?php
/*
 * Copyright(c) 2015 SystemFriend Inc. All rights reserved.
 * http://ec-cube.systemfriend.co.jp/
 */

namespace Plugin\ExcludeProductPayment\Entity;

/**
 * Information about payment of an order
 *
 */
class ExcludeProductPayment extends \Eccube\Entity\AbstractEntity
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $excludemonthly;

    /**
     * @var integer
     */
    private $payment_id;

    private $payment_ids;

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
    public function setPaymentId($payment_id)
    {
        $this->payment_id = $payment_id;

        return $this;
    }

    /**
     * Get redirect_product_id
     *
     * @return integer
     */
    public function getPaymentId()
    {
        return $this->payment_id;
    }
    /**
     * Set redirect_product_id
     *
     * @param  integer $redirect_product_id
     * @return Order
     */
    public function setPaymentIds($payment_ids)
    {
        $this->payment_ids = $payment_ids;

        return $this;
    }

    /**
     * Get redirect_product_id
     *
     * @return integer
     */
    public function getPaymentIds()
    {
        // if(is_array($this->payment_ids)){

        // }else{
        //     $this->payment_ids = unserialize($this->payment_ids);
        // }
        return $this->payment_ids;
    }
    /**
     * Set excludemonthly
     *
     * @param  interger $excludemonthly
     * @return 
     */
    public function setExcludeMonthly($excludemonthly)
    {
        $this->excludemonthly = $excludemonthly;

        return $this;
    }

    /**
     * Get excludemonthly
     *
     * @return integer
     */
    public function getExcludeMonthly()
    {
        return $this->excludemonthly;
    }


}
