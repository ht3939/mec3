<?php
/*
 * Plugin Name : ProductOption
 *
 * Copyright (C) 2015 BraTech Co., Ltd. All Rights Reserved.
 * http://www.bratech.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ProductOption\Repository;

use Doctrine\ORM\EntityRepository;

class ExtensionRepository extends EntityRepository
{
    private $app;

    public function setApp($app)
    {
        $this->app = $app;
    }

    public function delete(\Plugin\ProductOption\Entity\Extension $Extension)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();
        try {
            $em->remove($Extension);
            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();

            return false;
        }
        return true;
    }

}
