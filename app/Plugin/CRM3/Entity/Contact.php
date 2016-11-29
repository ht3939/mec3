<?php

namespace Plugin\CRM3\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contact
 */
class Contact extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $contactId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $kana;

    /**
     * @var string
     */
    private $zip;

    /**
     * @var string
     */
    private $addr;

    /**
     * @var string
     */
    private $tel;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $contents;

    /**
     * @var integer
     */
    private $customerId;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var \DateTime
     */
    private $createDate;

    /**
     * @var \DateTime
     */
    private $updateDate;


    /**
     * Get contactId
     *
     * @return integer 
     */
    public function getContactId()
    {
        return $this->contactId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Contact
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set kana
     *
     * @param string $kana
     * @return Contact
     */
    public function setKana($kana)
    {
        $this->kana = $kana;

        return $this;
    }

    /**
     * Get kana
     *
     * @return string 
     */
    public function getKana()
    {
        return $this->kana;
    }

    /**
     * Set zip
     *
     * @param string $zip
     * @return Contact
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string 
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set addr
     *
     * @param string $addr
     * @return Contact
     */
    public function setAddr($addr)
    {
        $this->addr = $addr;

        return $this;
    }

    /**
     * Get addr
     *
     * @return string 
     */
    public function getAddr()
    {
        return $this->addr;
    }

    /**
     * Set tel
     *
     * @param string $tel
     * @return Contact
     */
    public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string 
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Contact
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set contents
     *
     * @param string $contents
     * @return Contact
     */
    public function setContents($contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * Get contents
     *
     * @return string 
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Set customerId
     *
     * @param integer $customerId
     * @return Contact
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Get customerId
     *
     * @return integer 
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Contact
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Contact
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Contact
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     * @return Contact
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }
}
