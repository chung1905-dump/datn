<?php
/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\AjaxCart\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Checkout\Model\Cart as CustomerCart;

class RemoveMessage implements ObserverInterface
{
    /**
     * @var CustomerCart
     */
    private $cart;

    /**
     * CartMessage constructor.
     * @param CustomerCart $cart
     */
    public function __construct(
        CustomerCart $cart
    ){
        $this->cart = $cart;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $this->cart->getQuote()->setHasError(true);
    }
}