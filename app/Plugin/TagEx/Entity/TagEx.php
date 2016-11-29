<?php

namespace Plugin\TagEx\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TagEx
 */
class TagEx extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $color1;

    /**
     * @var string
     */
    private $color2;

    /**
     * @var string
     */
    private $color3;

    /**
     * @var \Eccube\Entity\Master\Tag
     */
    private $Tag;


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
     * Set color1
     *
     * @param string $color1
     * @return TagEx
     */
    public function setColor1($color1)
    {
        $this->color1 = $color1;

        return $this;
    }

    /**
     * Get color1
     *
     * @return string 
     */
    public function getColor1()
    {
        return $this->color1;
    }

    /**
     * Set color2
     *
     * @param string $color2
     * @return TagEx
     */
    public function setColor2($color2)
    {
        $this->color2 = $color2;

        return $this;
    }

    /**
     * Get color2
     *
     * @return string 
     */
    public function getColor2()
    {
        return $this->color2;
    }

    /**
     * Set color3
     *
     * @param string $color3
     * @return TagEx
     */
    public function setColor3($color3)
    {
        $this->color3 = $color3;

        return $this;
    }

    /**
     * Get color3
     *
     * @return string 
     */
    public function getColor3()
    {
        return $this->color3;
    }

    /**
     * Set Tag
     *
     * @param \Eccube\Entity\Master\Tag $tag
     * @return TagEx
     */
    public function setTag(\Eccube\Entity\Master\Tag $tag)
    {
        $this->Tag = $tag;

        return $this;
    }

    /**
     * Get Tag
     *
     * @return \Eccube\Entity\Master\Tag 
     */
    public function getTag()
    {
        return $this->Tag;
    }
}
