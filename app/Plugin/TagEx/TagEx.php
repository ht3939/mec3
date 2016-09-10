<?php
/*
 * Copyright(c) 2016 SYSTEM_KD
 */

namespace Plugin\TagEx;

use Eccube\Event\TemplateEvent;
use Eccube\Event\EventArgs;
use Doctrine\ORM\EntityRepository;

class TagEx
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 商品詳細テンプレート
     *
     * @param TemplateEvent $event
     */
    public function onProductDetailTwig(TemplateEvent $event)
    {
        $app = $this->app;

        $parameters = $event->getParameters();

        /* @var $Product \Eccube\Entity\Product */
        $Product = $parameters['Product'];

        /* @var $TwigRenderService \Plugin\TagEx\Service\TwigRenderService */
        $TwigRenderService = $this->app['tagex.service.twigrenderservice'];
        $TwigRenderService->initTwigRenderControl($event);

        // タグの出力順がRank順となるよう表示項目変更
        $search = '{% for ProductTag in Product.ProductTag %}';
        $replace = "{% for ProductTag in ProductTags %}";
        $TwigRenderService->twigReplace($search, $replace);

        // タグをリンクへ変更
        $search = '<span id="product_tag_box__product_tag--{{ ProductTag.Tag.id }}" class="product_tag_list">{{ ProductTag.Tag }}</span>';
        $replace = '<a href="{{ url(\'product_list\') }}?tag_id={{ ProductTag.Tag.id }}">' . $search . "</a>";
        $TwigRenderService->twigReplace($search, $replace);


        $event->setSource($TwigRenderService->getContent());


        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $app['orm.em']->getRepository('Eccube\Entity\ProductTag')->createQueryBuilder('pt');
        $qb->innerJoin('pt.Product', 'p')
            ->innerJoin('pt.Tag', 'tag')
            ->andWhere('p.id = :product_id')
            ->setParameter(':product_id', $Product->getId())
            ->orderBy('tag.rank', 'desc');

        $ProductTags = $qb->getQuery()->getResult();
        $parameters['ProductTags'] = $ProductTags;
        $event->setParameters($parameters);
    }

    /**
     * 商品一覧初期処理
     *
     * @param EventArgs $event
     */
    public function onFrontProductIndexInitialize(EventArgs $event)
    {
        // タグを検索条件へ追加
        // フォームの追加
        $builder = $event->getArgument('builder');
        $builder->add('tag_id', 'entity', array(
            'class' => 'Eccube\Entity\Master\Tag',
            'property' => 'Name',
            'query_builder' => function (EntityRepository $er) {
                                    return $er
                                    ->createQueryBuilder('t')
                                    ->orderBy('t.rank', 'DESC');
                                    },
            'empty_value' => '',
            'empty_data' => null,
            'required' => false,
            'label' => 'タグ',
            ));
    }

    /**
     * 商品一覧テンプレート
     *
     * @param TemplateEvent $event
     */
    public function onProductListTiwg(TemplateEvent $event)
    {
        $paramenters = $event->getParameters();

        /* @var $sf \Symfony\Component\Form\FormView */
        $searchFormView = $paramenters['search_form'];
        if(empty($searchFormView->vars['value']['tag_id'])) {
            // 無効な値の場合はエラーとなるためフォームを変更しない
            return;
        }

        /* @var $TwigRenderService \Plugin\TagEx\Service\TwigRenderService */
        $TwigRenderService = $this->app['tagex.service.twigrenderservice'];
        $TwigRenderService->initTwigRenderControl($event);

        // 選択したタグ情報を追加する
        $search = '<ol id="list_header_menu">';
        $insert = '{% if search_form.vars.value.tag_id %}';
        $insert.= '<li> {{ search_form.vars.value.tag_id }} </li>';
        $insert.= '{% endif %}';

        $TwigRenderService->twigInsert($search, $insert, 10);

        $event->setSource($TwigRenderService->getContent());
    }

    /**
     * フロント商品一覧検索処理
     *
     * @param EventArgs $event
     */
    public function onFrontProductIndexSearch(EventArgs $event)
    {

        $searchData = $event->getArgument('searchData');

        if(empty($searchData['tag_id'])) {
            return;
        }

        $Tag = $searchData['tag_id'];

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $event->getArgument('qb');

        if(!empty($Tag) && $Tag) {

            $qb->innerJoin('p.ProductTag', 'prodtag')
                ->innerJoin('prodtag.Tag', 'tag')
                ->andWhere($qb->expr()->in('prodtag.Tag', ':Tag'))
                ->setParameter(':Tag', $Tag);

        }
    }

    /**
     * 商品一覧初期処理
     *
     * @param EventArgs $event
     */
    public function onAdminProductIndexInitialize(EventArgs $event)
    {

        // フォームの追加
        $builder = $event->getArgument('builder');
        $builder->add('tag_id', 'tag', array(
                'label' => 'タグ',
                'empty_value' => '選択してください',
                'required' => false,
            ));
    }

    /**
     * 商品一覧テンプレート
     *
     * @param TemplateEvent $event
     */
    public function onAdminProductIndexTwig(TemplateEvent $event)
    {

        /* @var $TwigRenderService \Plugin\SortEx\Service\TwigRenderService */
        $TwigRenderService = $this->app['tagex.service.twigrenderservice'];
        $TwigRenderService->initTwigRenderControl($event);

        $search = '<div class="extra-form col-md-12">';
        $snipet = '<div class="col-sm-6">';
        $snipet.= '<label>タグ</label>';
        $snipet.= '<div class="form-group rang">';
        $snipet.= '{{ form_widget(searchForm.tag_id) }}';
        $snipet.= '</div>';
        $snipet.= '</div>';
        $snipet.= '</div>';
        $snipet.= $search;

        $TwigRenderService->twigReplace($search, $snipet);
        $event->setSource($TwigRenderService->getContent());
    }

    /**
     * 商品一覧検索処理
     *
     * @param EventArgs $event
     */
    public function onAdminProductIndexSearch(EventArgs $event)
    {

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $event->getArgument('qb');
        $searchData = $event->getArgument('searchData');

        $tag_id = $searchData['tag_id'];

        if(empty($tag_id)) {
            return;
        }

        $qb->innerJoin('p.ProductTag', 'pt');
        $qb->innerJoin('pt.Tag', 'Tag');
        $qb->andWhere('Tag.id = :tag_id');
        $qb->setParameter(':tag_id', $tag_id);

        // セッションから検索条件を復元対応
        if (!empty($searchData['tag_id'])) {
            $searchData['tag_id'] = $this->app['orm.em']
                ->getRepository('Eccube\Entity\Master\Tag')->find($tag_id);

            $event->setArgument('searchData', $searchData);
        }
    }
}
