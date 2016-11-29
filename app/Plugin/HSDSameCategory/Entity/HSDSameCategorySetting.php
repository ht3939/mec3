<?php

namespace Plugin\HSDSameCategory\Entity;

class HSDSameCategorySetting extends \Eccube\Entity\AbstractEntity
{
	private $id;
    private $max_count;
    private $title;
    private $mode;
    private $show_type;
    private $pagination;
    private $navbuttons;
    private $showloop;

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

    public function getShowType()
    {
        return $this->show_type;
    }
    public function setShowType($str)
    {
        $this->show_type = $str;

        return $this;
    }

    public function getPagination()
    {
        return $this->pagination;
    }
    public function setPagination($str)
    {
        $this->pagination = $str;

        return $this;
    }

    public function getNavbuttons()
    {
        return $this->navbuttons;
    }
    public function setNavbuttons($str)
    {
        $this->navbuttons = $str;

        return $this;
    }

    public function getShowloop()
    {
        return $this->showloop;
    }
    public function setShowloop($str)
    {
        $this->showloop = $str;

        return $this;
    }


}

?>
