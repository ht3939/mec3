<?php

namespace Plugin\CRM3\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\Id\SequenceGenerator;
use Eccube\Common\Constant;

class ContactRepository extends EntityRepository
{

    /**
     * find list
     * @return mixed
     */
    public function findList()
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c');

        $qb->addOrderBy('c.contactId', 'DESC');

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

    }
}
