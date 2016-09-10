<?php

namespace Plugin\HSDSameCategory\Entity;

class HSDSameCategorySetting extends \Eccube\Entity\AbstractEntity
{
	private $id;
    private $max_count;
    private $title;
    private $mode;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getMaxCount()
    {
        return $this->max_count;
    }
    public function setMaxCount($num)
    {
        $this->max_count = $num;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }
    public function setTitle($str)
    {
        $this->title = $str;

        return $this;
    }

    public function getMode()
    {
        return $this->mode;
    }
    public function setMode($str)
    {
        $this->mode = $str;

        return $this;
    }

}

?>
