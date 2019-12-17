<?php
/**
 * Ecomteck
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Ecomteck.com license that is
 * available through the world-wide-web at this URL:
 * https://ecomteck.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Ecomteck
 * @package     Ecomteck_OrderCustomAttributes
 * @copyright   Copyright (c) 2018 Ecomteck (https://ecomteck.com/)
 * @license     https://ecomteck.com/LICENSE.txt
 */
namespace Ecomteck\OrderCustomAttributes\Block\Checkout;
use Magento\Framework\Stdlib\ArrayManager;
use \Magento\Checkout\Model\Session as CheckoutSession;

class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * @var \Ecomteck\OrderCustomAttributes\Model\Sales\Order\AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    /**
     * @var \Magento\Ui\Component\Form\AttributeMapper
     */
    protected $attributeMapper;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var \Magento\Checkout\Block\Checkout\AttributeMerger
     */
    protected $merger;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    protected $orderSpecialFieldsConfig;

    /**
     * @var \Ecomteck\OrderCustomAttributes\Helper\Order
     */
    protected $orderHelper;

    protected $layout;

    /** @var CheckoutSession */
    protected $checkoutSession;

    /** @var \Magento\Quote\Model\Quote  */
    protected $quote;

    protected $request;


    /**
     * @param \Ecomteck\OrderCustomAttributes\Model\Sales\Order\AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param \Magento\Ui\Component\Form\AttributeMapper $attributeMapper
     * @param \Magento\Checkout\Block\Checkout\AttributeMerger $merger
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param ArrayManager $arrayManager
     * @param \Ecomteck\OrderCustomAttributes\Helper\Order $orderHelper
     * @param \Magento\Framework\View\Layout $layout
     * @param CheckoutSession $checkoutSession
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Ecomteck\OrderCustomAttributes\Model\Sales\Order\AttributeMetadataDataProvider $attributeMetadataDataProvider,
        \Magento\Ui\Component\Form\AttributeMapper $attributeMapper,
        \Magento\Checkout\Block\Checkout\AttributeMerger $merger,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        ArrayManager $arrayManager,
        \Ecomteck\OrderCustomAttributes\Helper\Order $orderHelper,
        \Magento\Framework\View\Layout $layout,
        CheckoutSession $checkoutSession,
        \Magento\Framework\App\Request\Http $request
    ) {
        
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->attributeMapper = $attributeMapper;
        $this->merger = $merger;
        $this->filterProvider = $filterProvider;
        $this->arrayManager = $arrayManager;
        $this->orderHelper = $orderHelper;
        $this->layout = $layout;
        $this->checkoutSession = $checkoutSession;
        $this->request = $request;
        /** @var \Magento\Quote\Model\Quote  */
        $this->quote = $this->checkoutSession->getQuote();
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        if($this->request->getFullActionName() == 'ordercustomattributes_index_index') {
            return $jsLayout;
        }
        $this->getOrderSpecialFieldsConfig($jsLayout);
        $this->replaceShippingStepTemplate($jsLayout);
        $this->insertCustomAttributeIntoCustomSteps($jsLayout);
        $this->insertCustomAttributeIntoAreas($jsLayout);
        return $jsLayout;
    }

    public function insertCustomAttributeIntoCustomSteps(&$jsLayout)
    {
        $areas = $this->orderHelper->getDisplayAreasInCheckout();
        foreach($areas as $area) {
            if($area['is_step']){
                if(isset($area['form']) && $area['form'] instanceof \Ecomteck\OrderCustomAttributes\Model\Sales\Order\Form){
                    if($this->canShowForm($area['form'])){
                        $this->addCustomAttributeToCustomStep($area,$jsLayout);
                    }
                } else {
                    $this->addCustomAttributeToCustomStep($area,$jsLayout);
                }
                
            }
        }
        return $jsLayout;
    }

    public function addCustomAttributeToCustomStep($step,&$jsLayout)
    {
        $orderSpecialFields = $this->getOrderSpecialFields($jsLayout, $step['name']);
        $orderElements = $this->getOrderFields($jsLayout, $step['name']);
        if(empty($orderElements) && empty($orderSpecialFields)){
            return;
        }

        $containerPath = 'components/checkout/children/steps/children';
        if ($this->arrayManager->exists($containerPath,$jsLayout)) {
            $newStepName = 'order-custom-attribute-'.$step['name'];

            $fields = $this->merger->merge(
                $orderElements,
                'checkoutProvider',
                'order.'.$step['name'],
                []
            );

            if($orderSpecialFields && count($orderSpecialFields) > 0) {
                foreach($orderSpecialFields as $code => $specialField) {
                    $fields[$code] = $specialField;
                }
            }

            $jsLayout = $this->arrayManager->merge(
                $containerPath,
                $jsLayout,
                [
                    $newStepName => [
                        'component' => $this->getStepComponentPath($step),
                        'sortOrder' => $step['sort_order'],
                        'provider'  => 'checkoutProvider',
                        'title' => $step['label'],
                        'code'  => $step['name'],
                        'stepOrder' => $step['sort_order'],
                        'icon'  => $step['icon'],
                        'visible' => $step['visible'],
                        'children' => [
                            'order-custom-attributes' => [
                                'component' => 'uiComponent',
                                'displayArea' => 'order-custom-attributes',
                                'children' => $fields
                            ] 
                        ],
                    ]
                ]
            );
        }
        return $jsLayout;
    }

    public function getStepComponentPath($step)
    {
        $block = $this->layout->getBlockSingleton('Magento\Framework\View\Element\Template');
        return $block->getViewFileUrl('Ecomteck_OrderCustomAttributes/js/view/order-custom-attributes-step.js').'?v='.$step['name'];
    }

    public function insertCustomAttributeIntoLayout(&$jsLayout,$targetPath,$name)
    {
        $orderSpecialFields = $this->getOrderSpecialFields($jsLayout, $name);
        $orderElements = $this->getOrderFields($jsLayout, $name);
        if(empty($orderElements) && empty($orderSpecialFields)){
            return;
        }
        $containerPath = $this->arrayManager->slicePath($targetPath, 0, -1);
        if(!$this->arrayManager->exists($targetPath,$jsLayout)){
            $jsLayout = $this->arrayManager->merge(
                $containerPath,
                $jsLayout,
                [
                    $name => [
                        'component' => 'uiComponent',
                        'displayArea' => $name,
                        'children' => []
                    ]
                ]
            );
        }

        $elementsPath = $targetPath.ArrayManager::DEFAULT_PATH_DELIMITER.'children';
        if($orderSpecialFields && count($orderSpecialFields) > 0) {
            $jsLayout = $this->arrayManager->merge(
                $elementsPath,
                $jsLayout,
                $orderSpecialFields
            );
        }
        $fields = $this->merger->merge(
            $orderElements,
            'checkoutProvider',
            'order.'.$name,
            []
        );

        $jsLayout = $this->arrayManager->merge(
            $elementsPath,
            $jsLayout,
            $fields
        );
        return $jsLayout;
    }

    public function insertCustomAttributeIntoAreas(&$jsLayout)
    {
        $areas = $this->orderHelper->getDisplayAreasInCheckout();

        foreach($areas as $area) {
            if(!$area['is_step']){
                $this->insertCustomAttributeIntoLayout(
                    $jsLayout,
                    $area['path'],
                    $area['name']
                );
            }
        }
        return $jsLayout;
    }

    public function getOrderFields(&$jsLayout,$position=null)
    {   
        $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'order',
            $position
        );
        $orderElements = [];
        
        foreach ($attributes as $attribute) {
            if (!$attribute->getIsUserDefined() || !$this->canShowAttribute($attribute)) {
                continue;
            }
            
            $attributeConfig =  $this->attributeMapper->map($attribute);
            if($attributeConfig['dataType'] == 'boolean'){
                $attributeConfig['formElement'] = 'select';
            }
            if($attributeConfig['formElement'] == 'label'){
                $attributeConfig['value'] = '<div class="step-title" data-role="title">'.$attributeConfig['label'].'</div>';
                $attributeConfig['label'] = '';
                $attributeConfig['formElement'] = 'html';
            } elseif($attributeConfig['formElement'] == 'html'){
                $attributeConfig['value'] = $this->filterProvider->getBlockFilter()->filter($attributeConfig['default']);
                $attributeConfig['label'] = '';
                $attributeConfig['formElement'] = 'html';
            } elseif(in_array($attributeConfig['formElement'],array_keys($this->getOrderSpecialFieldsConfig()))){
                continue;
            }
            $orderElements[$attribute->getAttributeCode()] = $attributeConfig;
        }
        return $orderElements;
    }

    public function getOrderSpecialFields(&$jsLayout,$position=null)
    {
        $orderSpecialFieldsConfig = $this->getOrderSpecialFieldsConfig();
        $orderElements = [];
        $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'order',
            $position
        );   
        
        foreach ($attributes as $attribute) {
            $type = $attribute->getFrontendInput();
            if (!$attribute->getIsUserDefined() || !in_array($type,array_keys($orderSpecialFieldsConfig))) {
                continue;
            }
            $elementConfig = null;
            
            if(!empty($orderSpecialFieldsConfig[$type]['component'])){
                $elementConfig = $orderSpecialFieldsConfig[$type]['component'];
                $elementConfig['sortOrder'] = $attribute->getSortOrder();
                if(!$attribute->getRemoveExistComponent() && !empty($orderSpecialFieldsConfig[$type]['component_path'])){
                    $jsLayout = $this->arrayManager->remove($orderSpecialFieldsConfig[$type]['component_path'],$jsLayout);
                }
                if($elementConfig){
                    $orderElements[$attribute->getAttributeCode()] = $elementConfig;
                }
            }
        }
        
        return $orderElements;      
    }



    public function replaceShippingStepTemplate(&$jsLayout)
    {
        if($this->orderHelper->isOneStepCheckoutEnabled()){
            return $jsLayout;
        }
        
        $jsLayout = $this->arrayManager->replace(
            'components/checkout/children/steps/children/shipping-step/children/shippingAddress/config',
            $jsLayout,
            [
                'template' => 'Ecomteck_OrderCustomAttributes/shipping'
            ]
        );
        return $jsLayout;
    }

    protected function getOrderSpecialFieldsConfig($jsLayout=null)
    {
        if(is_null($this->orderSpecialFieldsConfig) && $jsLayout){
            $types = $this->orderHelper->getAttributeInputTypes();
            $this->orderSpecialFieldsConfig = [];
            foreach($types as $type => $config) {
                if(empty($config['is_custom_component'])){
                    continue;
                }

                if(!empty($config['component_path'])){
                    $config['component'] = $this->arrayManager->get($config['component_path'],$jsLayout);
                }
                if(!empty($config['component'])){
                    $this->orderSpecialFieldsConfig[$type] = $config;
                }
            }
        }
        return $this->orderSpecialFieldsConfig;
    }

    public function canShowAttribute($attribute)
    {
        $rule = $attribute->getRule();
        if($rule){
            $address = $this->quote->getShippingAddress();
            if (!$rule->validate($address)) {
                $rule->setIsValidForAddress($address, false);
                return false;
            }
        }
        return true;
    }

    public function canShowForm($form)
    {
        if(!$form->getIsVisible()){
            return false;
        }
        $rule = $form->getRule();
        if($rule){
            $address = $this->quote->getShippingAddress();
            if (!$rule->validate($address)) {
                $rule->setIsValidForAddress($address, false);
                return false;
            }
        }
        return true;
    }
}
