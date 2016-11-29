<?php

namespace Plugin\HSDRelatedProduct\Entity;

class HSDRelatedProductSetting extends \Eccube\Entity\AbstractEntity
{
	private $id;
    private $max_num;
    private $max_row_num;
    private $title;
    private $show_price;
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

    public function getMaxNum()
    {
        return $this->max_num;
    }
    public function setMaxNum($num)
    {
        $this->max_num = $num;

        return $this;
    }

    public function getMaxRowNum()
    {
        return $this->max_row_num;
    }
    public function setMaxRowNum($num)
    {
        $this->max_row_num = $num;

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

    public function getShowPrice()
    {
        return $this->show_price;
    }
    public function setShowPrice($str)
    {
        $this->show_price = $str;

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
