<?php

namespace Plugin\MatchProduct\Entity;


class MatchProduct extends \Eccube\Entity\AbstractEntity
{
	private $id;
    private $from_id;
    private $to_id;
    private $updated_at;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getFromId()
    {
        return $this->from_id;
    }
    public function setFromId($p_id)
    {
        $this->from_id = $p_id;
        return $this;
    }

    public function getToId()
    {
        return $this->to_id;
    }

    public function setToId($p_id)
    {
        $this->to_id = $p_id;
        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setUpdatedAt($datetime)
    {
        $this->updated_at = new \DateTime($datetime);
        return $this;
    }

}

?>
