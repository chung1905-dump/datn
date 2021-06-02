<?php
/**
 * Copyright © www.magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\AjaxFilter\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const AVG_RATING_PERCENT = \MageBig\AjaxFilter\Model\Layer\Filter\Rating::AVG_RATING_PERCENT;
    const RATING_CODE = \MageBig\AjaxFilter\Model\Layer\Filter\Rating::RATING_CODE;
    const ENABLE_AJAX = 'magebig_ajaxfilter/general/enable';
    const ENABLE_PRICE_SLIDER = 'magebig_ajaxfilter/general/enable_price_slider';
    const MAX_HEIGHT_BOX_STAGE = 'magebig_ajaxfilter/general/max_height_box_state';
    const ENABLE_FILTER_BY_RATING = 'magebig_ajaxfilter/general/enable_filter_rating';
    const ENABLE_SORT_BY_RATING = 'magebig_ajaxfilter/general/enable_sort_rating';
    const RATING_FILTER_TYPE_PATH = 'magebig_ajaxfilter/general/rating_filter_type';

    protected $filterManager;

    protected $enable;

    protected $layout;

    protected $objectManager;

    protected $_filters;

    protected $enableMultiSelect;

    protected $ratingFilterType;

    protected $_beforeApply = null;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Filter\FilterManager $filterManager
    ) {
        parent::__construct($context);
        $this->layout = $layout;
        $this->filterManager = $filterManager;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_beforeApply = null;
    }

    public function getLayout()
    {
        if (null === $this->layout) {
            $this->layout = $this->objectManager->get('\Magento\Framework\View\LayoutInterface');
        }
        return $this->layout;
    }

    public function getFilterManager()
    {
        return $this->filterManager;
    }

    public function getConfig($path)
    {
        return $this->scopeConfig->getValue($path, 'store');
    }

    public function boxMaxHeight()
    {
        return $this->getConfig(self::MAX_HEIGHT_BOX_STAGE, 'store');
    }

    public function enableAjaxFilter()
    {
        return (int) $this->getConfig(self::ENABLE_AJAX, 'store');
    }

    public function enablePriceSlider()
    {
        return (bool) $this->getConfig(self::ENABLE_PRICE_SLIDER, 'store');
    }

    public function getFilters()
    {
        if (null === $this->_filters) {
            if ($this->_request->getFullActionName() === 'catalogsearch_result_index') {
                $this->_filters = $this->objectManager->get('Magento\LayeredNavigation\Block\Navigation\Search')->getFilters();
            } else {
                $this->_filters = $this->objectManager->get('Magento\LayeredNavigation\Block\Navigation\Category')->getFilters();
            }
        }
        return $this->_filters;
    }

    public function getBeforeApplyFacetedData($collection, $attribute, $currentFilter = null)
    {
        $cloneCollection = clone $collection;
        $cloneFilterBuilder = clone $this->objectManager->get(\Magento\Framework\Api\FilterBuilder::class);
        $cloneCollection->setFilterBuilder($cloneFilterBuilder);

        $cloneSearchCriteriaBuilder = clone $this->objectManager->get(\Magento\Framework\Api\Search\SearchCriteriaBuilder::class);
        $cloneCollection->setSearchCriteriaBuilder($cloneSearchCriteriaBuilder);

        $attributeCode = $attribute->getAttributeCode();
        foreach ($this->getFilters() as $filter) {
            if ($filter->getRequestVar() != $attributeCode) {
                if (method_exists($filter, 'applyToCollection')) {
                    $filter->applyToCollection($cloneCollection, $this->_request, $filter->getRequestVar());
                }
            }
        }
        if ($currentFilter) {
            $currentFilter->setBeforeApplyCollection($cloneCollection);
            $this->_beforeApply = $currentFilter->getBeforeApplyCollection();
        }
        if ($this->_request->getParam(self::RATING_CODE, false)) {
            $clone2 = clone $cloneCollection;
            $facetedData = $cloneCollection->getFacetedData($attribute->getAttributeCode());
            $connection = $clone2->getConnection();
            foreach ($facetedData as $value => $option) {
                if ($facetedData[$value]['count'] > 0) {
                    $clone = clone $clone2;
                    $facetedData[$value]['count'] = $connection->fetchOne($clone->addFieldToFilter($attributeCode, $value)->getSelectCountSql());
                }
            }
            return $facetedData;
        }
        return $cloneCollection->getFacetedData($attribute->getAttributeCode());
    }

    public function getProduction() {
        return $this->_beforeApply;
    }

    public function enableRatingFilter()
    {
        return (bool) $this->getConfig(self::ENABLE_FILTER_BY_RATING);
    }

    public function enableRatingSort()
    {
        return (bool) $this->getConfig(self::ENABLE_SORT_BY_RATING);
    }

    public function getObjectManager()
    {
        return $this->objectManager;
    }

    public function getRatingTypes()
    {
        return $this->getConfig(self::RATING_FILTER_TYPE_PATH) ? : 'up';
    }

    public function ratingCollection($productCollection)
    {
        if ($productCollection->hasFlag('rating_collection')) {
            return $productCollection;
        }
        $productCollection->setFlag('rating_collection', 1);
        $connection = $productCollection->getConnection();
        $storeId = $productCollection->getStoreId();
        $select = $connection->select()
            ->from(['product' => $productCollection->getTable('catalog_product_entity')], ['entity_pk_value' => 'entity_id'])
            ->joinLeft(
                ['rating' => $connection->select()
                    ->from(['vote' => $productCollection->getTable('rating_option_vote_aggregated')],
                        ['entity_pk_value' => 'entity_pk_value', self::AVG_RATING_PERCENT => 'avg(percent_approved)'])
                    ->where('vote.store_id = ' . $storeId)
                    ->group('vote.entity_pk_value')],
                'product.entity_id = rating.entity_pk_value',
                [self::AVG_RATING_PERCENT]
            )->group('product.entity_id');
        $productCollection->getSelect()
            ->join(
                ['rating' => $select],
                'e.entity_id = rating.entity_pk_value',
                [self::AVG_RATING_PERCENT => self::AVG_RATING_PERCENT]
            );
        return $productCollection;
    }
}
