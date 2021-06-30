<?php

namespace TatTran\PaymentVNPAY\Controller\Order;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Info extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $jsonFac;

    /** @var  Order */
    protected $order;

    /** @var ScopeConfigInterface $scopeConfig */
    protected $scopeConfig;

    /** @var  StoreManagerInterface */
    protected $storeManager;

    /** @var  Session        Magento = Magento230sp */
    protected $checkoutSession;

    public function __construct(
        Context $context, PageFactory $resultPageFactory, Json $json, Order $order, ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager, Session $checkoutSession
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonFac = $json;
        $this->order = $order;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('order_id', 0);
        $order = $this->order->load(intval($id));
        $url = $this->scopeConfig->getValue('payment/tattranvnpay/paymentUrl');
        if ($order->getId()) {
            $incrementID = $order->getIncrementId();


            $returnUrl = $this->storeManager->getStore()->getBaseUrl();
            $returnUrl = rtrim($returnUrl, "/");
            $returnUrl .= "/paymentvnpay/order/pay";
            $inputData = array(
                "vnp_Version" => "2.0.0",
                "vnp_TmnCode" => $this->scopeConfig->getValue('payment/tattranvnpay/tmnCode'),
                "vnp_Amount" => round($order->getTotalDue() * 100, 0),
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'],
                "vnp_Locale" => 'vn',
                "vnp_OrderInfo" => $incrementID,
                "vnp_OrderType" => 'other',
                "vnp_ReturnUrl" => $returnUrl,
                "vnp_TxnRef" => $incrementID,
            );
            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . $key . "=" . $value;
                } else {
                    $hashdata .= $key . "=" . $value;
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $url . "?" . $query;
            $SECURE_SECRET = $this->scopeConfig->getValue('payment/tattranvnpay/secretCode');
            if (isset($SECURE_SECRET)) {
                $vnpSecureHash = hash('sha256', $SECURE_SECRET . $hashdata);
                $vnp_Url .= 'vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;
            }
        }
        $this->jsonFac->setData($vnp_Url);
        return $this->jsonFac;
    }

}
