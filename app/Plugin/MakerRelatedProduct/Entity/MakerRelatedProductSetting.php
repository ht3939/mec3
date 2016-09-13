<?php

namespace Plugin\MakerRelatedProduct\Entity;

class MakerRelatedProductSetting extends \Eccube\Entity\AbstractEntity
{
	private $id;
    private $max_num;
    private $max_row_num;
    private $title;
    private $show_price;

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

}

?>
