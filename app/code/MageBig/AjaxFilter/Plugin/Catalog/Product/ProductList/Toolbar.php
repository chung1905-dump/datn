<?php
/**
 * Copyright © www.magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageBig\AjaxFilter\Plugin\Catalog\Product\ProductList;

class Toolbar
{
    /**
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param \Closure $proceed
     * @param $collection
     * @return mixed
     */
    public function aroundSetCollection(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        \Closure $proceed,
        $collection
    ) {
        $order = $subject->getCurrentOrder();
        $direction = $subject->getCurrentDirection();
        $result = $proceed($collection);
        $ratingCode = \MageBig\AjaxFilter\Model\Layer\Filter\Rating::RATING_CODE;

        if ($ratingCode && ($order == $ratingCode)) {
            $this->sortByRating($collection, $direction);
        }
        return $result;
    }

    public function sortByRating($collection, $direction) {
        $helper = \Magento\Framework\App\ObjectManager::getInstance()->get(\MageBig\AjaxFilter\Helper\Data::class);
        $helper->ratingCollection($collection);
        $order = \MageBig\AjaxFilter\Model\Layer\Filter\Rating::AVG_RATING_PERCENT;
        $collection->getSelect()->order("$order $direction");
    }
}
