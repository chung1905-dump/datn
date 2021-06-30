<?php

namespace TatTran\PaymentVNPAY\Model;

use Magento\Payment\Model\Method\AbstractMethod;
use TatTran\PaymentVNPAY\Model\ResourceModel\tattranvnpay as ResourceModel;

class tattranvnpay extends AbstractMethod
{
    const PAYMENT_METHOD_VNPAY_CODE = 'tattranvnpay';

    /**
     * Payment code
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_VNPAY_CODE;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;


}
