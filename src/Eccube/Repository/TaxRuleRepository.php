<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Eccube\Common\Constant;

/**
 * TaxRuleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TaxRuleRepository extends EntityRepository
{
    private $rules = array();

    protected $app;

    public function setApplication($app)
    {
        $this->app = $app;
    }

    public function newTaxRule()
    {
        $TaxRule = new \Eccube\Entity\TaxRule();
        $CalcRule = $this->getEntityManager()
            ->getRepository('Eccube\Entity\Master\Taxrule')
            ->find(1);
        $TaxRule->setCalcRule($CalcRule);
        $TaxRule->setTaxAdjust(0);
        $TaxRule->setDelFlg(0);

        return $TaxRule;
    }

    /**
     * 現在有効な税率設定情報を返す
     *
     * @param  int|null|\Eccube\Entity\Product        $Product      商品
     * @param  int|null|\Eccube\Entity\ProductClass   $ProductClass 商品規格
     * @param  int|null|\Eccube\Entity\Master\Pref    $Pref         都道府県
     * @param  int|null|\Eccube\Entity\Master\Country $Country      国
     * @return \Eccube\Entity\TaxRule                 税設定情報
     *
     * @throws NoResultException
     */
    public function getByRule($Product = null, $ProductClass = null, $Pref = null, $Country = null)
    {
        if (!$this->app) {
            throw new \LogicException();
        }

        // Pref Country 設定
        if (!$Pref && !$Country && $this->app['security']->getToken() && $this->app['security']->isGranted('ROLE_USER')) {
            /* @var $Customer \Eccube\Entity\Customer */
            $Customer = $this->app['security']->getToken()->getUser();
            $Pref = $Customer->getPref();
            $Country = $Customer->getCountry();
        }

        // 商品単位税率設定がOFFの場合
        /** @var $BaseInfo \Eccube\Entity\BaseInfo */
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        if ($BaseInfo->getOptionProductTaxRule() !== Constant::ENABLED) {
            $Product = null;
            $ProductClass = null;
        }

        // Cache Key 設定
        if ($Product instanceof \Eccube\Entity\Product) {
            $productId = $Product->getId();
        } elseif ($Product) {
            $productId = $Product;
        } else {
            $productId = '0';
        }
        if ($ProductClass instanceof \Eccube\Entity\ProductClass) {
            $productClassId = $ProductClass->getId();
        } elseif ($ProductClass) {
            $productClassId = $ProductClass;
        } else {
            $productClassId = '0';
        }
        if ($Pref instanceof \Eccube\Entity\Master\Pref) {
            $prefId = $Pref->getId();
        } elseif ($Pref) {
            $prefId = $Pref;
        } else {
            $prefId = '0';
        }
        if ($Country instanceof \Eccube\Entity\Master\Country) {
            $countryId = $Country->getId();
        } elseif ($Country) {
            $countryId = $Country;
        } else {
            $countryId = '0';
        }
        $cacheKey = $productId.':'.$productClassId.':'.$prefId.':'.$countryId;

        // すでに取得している場合はキャッシュから
        if (isset($this->rules[$cacheKey])) {
            return $this->rules[$cacheKey];
        }

        $parameters = array();
        $qb = $this->createQueryBuilder('t')
            ->where('t.apply_date < CURRENT_TIMESTAMP()');

        // Pref
        if ($Pref) {
            $qb->andWhere('t.Pref IS NULL OR t.Pref = :Pref');
            $parameters['Pref'] = $Pref;
        } else {
            $qb->andWhere('t.Pref IS NULL');
        }

        // Country
        if ($Country) {
            $qb->andWhere('t.Country IS NULL OR t.Country = :Country');
            $parameters['Country'] = $Country;
        } else {
            $qb->andWhere('t.Country IS NULL');
        }

        /*
         * Product, ProductClass が persist される前に TaxRuleEventSubscriber によってアクセスされる
         * 場合があるため、ID の存在もチェックする.
         * https://github.com/EC-CUBE/ec-cube/issues/677
         */

        // Product
        if ($Product && $productId > 0) {
            $qb->andWhere('t.Product IS NULL OR t.Product = :Product');
            $parameters['Product'] = $Product;
        } else {
            $qb->andWhere('t.Product IS NULL');
        }

        // ProductClass
        if ($ProductClass && $productClassId > 0) {
            $qb->andWhere('t.ProductClass IS NULL OR t.ProductClass = :ProductClass');
            $parameters['ProductClass'] = $ProductClass;
        } else {
            $qb->andWhere('t.ProductClass IS NULL');
        }

        $TaxRules = $qb
            ->setParameters($parameters)
            ->orderBy('t.apply_date', 'DESC') // 実際は usort() でソートする
            ->getQuery()
            ->getResult();

        // 地域設定を優先するが、システムパラメーターなどに設定を持っていくか
        // 後に書いてあるほど優先される
        $priorityKeys = explode(',', $this->app['config']['tax_rule_priority']);
        $priorityKeys = array();
        foreach (explode(',', $this->app['config']['tax_rule_priority']) as $key) {
            $priorityKeys[] = str_replace('_', '', preg_replace('/_id\z/', '', $key));
        }

        foreach ($TaxRules as $TaxRule) {
            $rank = 0;
            foreach ($priorityKeys as $index => $key) {
                $arrayProperties = array_change_key_case($TaxRule->toArray());
                if ($arrayProperties[$key]) {

                    // 配列の数値添字を重みとして利用する
                    $rank += 1 << ($index + 1);
                }
            }
            $TaxRule->setRank($rank);
        }

        // 適用日降順, rank 降順にソートする
        usort($TaxRules, function($a, $b) {
            return $a->compareTo($b);
        });

        if (!empty($TaxRules)) {
            $this->rules[$cacheKey] = $TaxRules[0];

            return $TaxRules[0];
        } else {
            throw new NoResultException();
        }
    }

    /**
     * getList
     *
     * @return array|null
     */
    public function getList()
    {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.apply_date', 'DESC')
            ->where('t.Product IS NULL AND t.ProductClass IS NULL');
        $TaxRules = $qb
            ->getQuery()
            ->getResult();

        return $TaxRules;
    }

    /**
     * getById
     *
     * @param  int   $id
     * @return array
     */
    public function getById($id)
    {
        $criteria = array(
            'id' => $id,
        );

        return $this->findOneBy($criteria);
    }

    /**
     * getByTime
     *
     * @param  string $applyDate
     * @return mixed
     */
    public function getByTime($applyDate)
    {
        $criteria = array(
            'apply_date' => $applyDate,
        );

        return $this->findOneBy($criteria);
    }

    /**
     * 税規約の削除.
     *
     * @param  int|\Eccube\Entity\TaxRule $TaxRule 税規約
     * @return void
     * @throws NoResultException
     */
    public function delete($TaxRule)
    {
        if (!$TaxRule instanceof \Eccube\Entity\TaxRule) {
            $TaxRule = $this->find($TaxRule);
        }
        if (!$TaxRule) {
            throw new NoResultException;
        }
        $TaxRule->setDelFlg(1);
        $em = $this->getEntityManager();
        $em->persist($TaxRule);
        $em->flush();
    }

    /**
     * TaxRule のキャッシュをクリアする.
     *
     * getByRule() をコールすると、結果をキャッシュし、2回目以降はデータベースへアクセスしない.
     * このメソッドをコールすると、キャッシュをクリアし、再度データベースを参照して結果を取得する.
     */
    public function clearCache()
    {
        $this->rules = array();
    }
}
