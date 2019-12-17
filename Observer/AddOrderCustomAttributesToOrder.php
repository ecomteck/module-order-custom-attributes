<?php
namespace Ecomteck\OrderCustomAttributes\Observer;

use Ecomteck\OrderCustomAttributes\Model\Data\OrderCustomAttributes;

class AddOrderCustomAttributesToOrder implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * transfer the order comment from the quote object to the order object during the
     * sales_model_service_quote_submit_before event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();
        
        /** @var $quote \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        $order->setData(OrderCustomAttributes::ORDER_CUSTOM_ATTRIBUTES_FIELD_NAME, $quote->getData(OrderCustomAttributes::ORDER_CUSTOM_ATTRIBUTES_FIELD_NAME));
    }
}
