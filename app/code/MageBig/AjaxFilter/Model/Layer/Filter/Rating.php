<?php
/**
 * Copyright © www.magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageBig\AjaxFilter\Model\Layer\Filter;

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Resolver;
use \Magento\Framework\App\ObjectManager;
/**
 * Layer attribute filter
 */
class Rating extends AbstractFilter
{
    const AVG_RATING_PERCENT = 'avg_percent';
    const RATING_CODE = 'rating';

    protected $objectManager;

    protected $helper;

    protected $sqlFieldName;

    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $data
        );
        $this->objectManager = ObjectManager::getInstance();
        $this->helper = $this->objectManager->get('MageBig\AjaxFilter\Helper\Data');
        $this->_requestVar = self::RATING_CODE;
        $this->sqlFieldName = self::AVG_RATING_PERCENT;
    }

    public function getName()
    {
        return __('Rating');
    }

    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $attributeValue = $request->getParam($this->_requestVar);

        if (empty($attributeValue) && !is_numeric($attributeValue)) {
            return $this;
        }

        $productCollection = $this->getLayer()->getProductCollection();

        $productCollection->setFlag('before_apply_faceted_data_'.$this->_requestVar,
            $this->_getRatingsData($productCollection)
        );
        $this->ratingFilter($productCollection, $attributeValue);

        $label = $this->_getRatingLabel($attributeValue);
        $this->getLayer()
            ->getState()
            ->addFilter($this->_createItem($label, $attributeValue));

        return $this;
    }

    public function ratingFilter($productCollection, $attributeValue)
    {
        $sqlFieldName = self::AVG_RATING_PERCENT;
        if ($this->helper->getRatingTypes() == 'interval') {
            $maxPercent = max(0, 100 * $attributeValue / 5);
            $minPercent = max(0, 100 * ($attributeValue - 1) / 5);
            $productCollection->getSelect()->where("({$minPercent} < {$sqlFieldName}) AND ({$sqlFieldName} <= {$maxPercent})");
        } else {
            $minPercent = 100 * $attributeValue / 5;
            $productCollection->getSelect()->where("{$sqlFieldName} >= {$minPercent}");
        }
        return $productCollection;
    }

    protected function _getRatingLabel($score)
    {
        if ($this->helper->getRatingTypes() == 'interval') {
            $maxScore = $score;
            $minScore = $score - 1;
            if ($minScore == 0) {
                return __('1 star');
            }
            return __('%1 < star ≤ %2', $minScore, $maxScore);
        } else {
            return ($score < 5) ? __('%1 star and up', $score) : __('%1 star', $score);
        }
    }

    protected function _getRatingsData($collection)
    {
        $connection = $collection->getConnection();
        $options = [];
        $ratingType = $this->helper->getRatingTypes();
        if ($ratingType == 'interval') {
            for ($i = 5; $i > 0; $i--) {
                $maxPercent = 100 * $i / 5;
                $minPercent = 100 * ($i-1) / 5;
                $cloneCollection = clone $collection;
                $cloneCollection->getSelect()->where("({$minPercent} < {$this->sqlFieldName}) AND ({$this->sqlFieldName} <= {$maxPercent})");
                $this->helper->ratingCollection($cloneCollection);
                $options[] = [
                    'label' => $this->_getRatingLabel($i),
                    'value' => $i,
                    'count' => $connection->fetchOne($cloneCollection->getSelectCountSql())
                ];
            }
        } else {
            for ($i = 5; $i > 0; $i--) {
                $percent = 100 * $i / 5;
                $cloneCollection = clone $collection;
                $cloneCollection->getSelect()->where("{$this->sqlFieldName} >= {$percent}");

                $this->helper->ratingCollection($cloneCollection);

                $options[] = [
                    'label' => $this->_getRatingLabel($i),
                    'value' => $i,
                    'count' => $connection->fetchOne($cloneCollection->getSelectCountSql())
                ];
            }
        }
        return $options;
    }

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
     protected function _getItemsData()
     {
        $productCollection = $this->getLayer()->getProductCollection();
        if ($data = $productCollection->getFlag('before_apply_faceted_data_'.$this->_requestVar)) {
        } else {
            $data = $this->_getRatingsData($productCollection);
        }
        $this->setData('items_data', $data);
        return $data;
    }
}
