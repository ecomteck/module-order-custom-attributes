<?php
namespace Ecomteck\OrderCustomAttributes\Ui\Component\Listing\Column;

use Ecomteck\OrderCustomAttributes\Model\Sales\Order\AttributeFactory;
use Ecomteck\OrderCustomAttributes\Model\Sales\Order\FormFactory;

class OrderCustomAttributes extends \Magento\Ui\Component\Listing\Columns\Column {

    protected $formFactory;
    protected $attributeFactory;
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        FormFactory $formFactory,
        AttributeFactory $attributeFactory,
        array $components = [],
        array $data = []
    ){
        $this->formFactory = $formFactory;
        $this->attributeFactory = $attributeFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {

            foreach ($dataSource['data']['items'] as & $item) {
                $orderCustomAttributes = $item['ecomteck_order_custom_attributes'];
                try {
                    $orderCustomAttributes = json_decode($orderCustomAttributes,true);
                    if(!empty($orderCustomAttributes)){
                        $orderCustomAttributesFormat = [];
                        foreach($orderCustomAttributes as $formCode => $attributes){
                            foreach($attributes as $attrCode => $attrValue) {
                                $attribute = $this->attributeFactory->create()->loadByCode('order',$attrCode);
                                
                                $value = is_array($attrValue) ? implode(',',$attrValue) : $attrValue;
                                if($attribute->usesSource()){
                                    $value = $attribute->getSource()->getOptionText($value);
                                }
                                if($attribute->getId()){
                                    if(!$attribute->getIsUsedInGrid()){
                                        continue;
                                    }
                                    $orderCustomAttributesFormat[$attribute->getAttributeCode()] = [
                                        'label' => $attribute->getStoreLabel(),
                                        'value' => $value
                                    ];
                                }
                            }
                        }
                        $html = [];
                        foreach($orderCustomAttributesFormat as $attribute) {
                            $html[] = '<strong>'.$attribute['label'].':</strong>';
                            $html[] = '<span class="price">'.nl2br($attribute['value']).'</span><br/>';
                        }
                        $item['ecomteck_order_custom_attributes'] = implode("\n",$html);
                    }
                    
                } catch(\Exception $e){
                }
                 //Here you can do anything with actual data

            }
        }

        return $dataSource;
    }
}