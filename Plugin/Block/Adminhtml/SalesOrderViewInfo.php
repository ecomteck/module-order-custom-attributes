<?php
namespace Ecomteck\OrderCustomAttributes\Plugin\Block\Adminhtml;

use Ecomteck\OrderCustomAttributes\Model\Data\OrderCustomAttributes;
use Ecomteck\OrderCustomAttributes\Model\Sales\Order\AttributeFactory;
use Ecomteck\OrderCustomAttributes\Model\Sales\Order\FormFactory;

class SalesOrderViewInfo
{
    protected $formFactory;
    protected $attributeFactory;
    /**
     * @param FormFactory $quoteIdMaskFactory
     * @param AttributeFactory $attributeFactory
     */
    public function __construct(
        FormFactory $formFactory,
        AttributeFactory $attributeFactory
    ) {
        $this->formFactory = $formFactory;
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View\Info $subject
     * @param string $result
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterToHtml(
        \Magento\Sales\Block\Adminhtml\Order\View\Info $subject,
        $result
    ) {
        $orderCustomAttributesBlock = $subject->getLayout()->getBlock('order_custom_attributes');
        if ($orderCustomAttributesBlock !== false && $subject->getNameInLayout() == 'order_info') {
            $orderCustomAttributes = $subject->getOrder()->getData(OrderCustomAttributes::ORDER_CUSTOM_ATTRIBUTES_FIELD_NAME);
            if(!empty($orderCustomAttributes)){
                try {
                    $orderCustomAttributes = json_decode($orderCustomAttributes,true);
                    if(!empty($orderCustomAttributes)){
                        $orderCustomAttributesFormat = [];
                        foreach($orderCustomAttributes as $formCode => $attributes){
                            $form = $this->formFactory->create()->loadByCode($formCode);
                            if($form->getId()){
                                
                                $orderCustomAttributesFormat[$form->getId()] = [
                                    'label' => $form->getLabel(),
                                    'attributes' => [

                                    ]
                                ];
                                foreach($attributes as $attrCode => $attrValue) {
                                    $attribute = $this->attributeFactory->create()->loadByCode('order',$attrCode);
                                    $value = is_array($attrValue) ? implode(',',$attrValue) : $attrValue;
                                    if($attribute->usesSource()){
                                        $value = $attribute->getSource()->getOptionText($value);
                                    }
                                    if($attribute->getId()){
                                        $orderCustomAttributesFormat[$form->getId()]['attributes'][$attribute->getAttributeCode()] = [
                                            'label' => $attribute->getStoreLabel(),
                                            'value' => $value
                                        ];
                                    }
                                }
                            }
                        }
                        $orderCustomAttributesBlock->setOrderCustomAttributes($orderCustomAttributesFormat);
                        $result = $result . $orderCustomAttributesBlock->toHtml();
                    }
                    
                } catch(\Exception $e){
                }
            }
        }
        
        return $result;
    }
}
