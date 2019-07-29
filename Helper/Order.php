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
namespace Ecomteck\OrderCustomAttributes\Helper;
use Magento\Store\Model\ScopeInterface;

/**
 *  Customer EAV Attributes Data Helper
 *
 */
class Order extends Data
{
    /**
     * Input validator
     *
     * @var \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator $_inputValidator
     */
    protected $_inputValidator;

    protected $moduleManager;

    /**
     * @var \Ecomteck\OrderCustomAttributes\Model\ResourceModel\Sales\Order\Form\CollectionFactory
     */
    protected $_formTypesFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator $inputValidator
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Ecomteck\OrderCustomAttributes\Model\ResourceModel\Sales\Order\Form\CollectionFactory $formTypesFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator $inputValidator,
        \Magento\Framework\Module\Manager $moduleManager,
        \Ecomteck\OrderCustomAttributes\Model\ResourceModel\Sales\Order\Form\CollectionFactory $formTypesFactory
    ) {
        parent::__construct($context, $eavConfig, $localeDate, $filterManager,$moduleManager);
        $this->_inputValidator = $inputValidator;
        $this->moduleManager = $moduleManager;
        $this->_formTypesFactory = $formTypesFactory;
    }

    /**
     * Default attribute entity type code
     *
     * @return string
     */
    protected function _getEntityTypeCode()
    {
        return 'order';
    }

    /**
     * Return available customer attribute form as select options
     *
     * @return array
     */
    public function getDisplayAreasInCheckoutOptions()
    {
        $displayAreas = $this->getDisplayAreasInCheckout();
        $options = [];
        foreach($displayAreas as $area){
            $options[] = ['label' => $area['label'], 'value' => $area['name']];
        }
        return $options;
    }

    /**
     * Return available display area in checkout layout as select options
     *
     * @return array
     */
    public function getDisplayAreasInCheckout()
    {
        $result = [
            'before-shipping-address' => [
                'label' => 'Before Shipping Address',
                'name' => 'before-shipping-address',
                'is_step' => false,
                'path' => 'components/checkout/children/steps/children/shipping-step/children/shippingAddress/children/before-shipping-address',
            ],
            'after-shipping-address' => [
                'label' => 'After Shipping Address',
                'name' => 'after-shipping-address',
                'is_step' => false,
                'path' => 'components/checkout/children/steps/children/shipping-step/children/shippingAddress/children/after-shipping-address',
            ],
            'before-shipping-address-fields' => [
                'label' => 'Before Shipping Address Fields',
                'name' => 'before-shipping-address-fields',
                'is_step' => false,
                'path' => 'components/checkout/children/steps/children/shipping-step/children/shippingAddress/children/before-form',
            ],
            'after-shipping-address-fields' => [
                'label' => 'After Shipping Address Fields',
                'name' => 'after-shipping-address-fields',
                'is_step' => false,
                'path' => 'components/checkout/children/steps/children/shipping-step/children/shippingAddress/children/after-form',
            ],
            'before-shipping-method-rates' => [
                'label' => 'Before Shipping Method Rates',
                'name' => 'before-shipping-method-rates',
                'is_step' => false,
                'path' => 'components/checkout/children/steps/children/shipping-step/children/shippingAddress/children/before-shipping-method-form',
            ],
            'after-shipping-method-rates' => [
                'label' => 'After Shipping Method Rates',
                'name' => 'after-shipping-method-rates',
                'is_step' => false,
                'path' => 'components/checkout/children/steps/children/shipping-step/children/shippingAddress/children/shippingAdditional',
            ],
            'before-payment-methods' => [
                'label' => 'Before Payment Methods',
                'name' => 'before-payment-methods',
                'is_step' => false,
                'path' => 'components/checkout/children/steps/children/billing-step/children/payment/children/beforeMethods',
            ],
            'after-payment-methods' => [
                'label' => 'After Payment Methods',
                'name' => 'after-payment-methods',
                'is_step' => false,
                'path' => 'components/checkout/children/steps/children/billing-step/children/payment/children/afterMethods',
            ]
        ];

        /** @var $forms \Ecomteck\OrderCustomAttributes\Model\ResourceModel\Sales\Order\Form\Collection */
        $forms = $this->_formTypesFactory->create();
        foreach($forms as $form){
            $result[$form->getCode()] = [
                'label' => $form->getLabel(),
                'name' => $form->getCode(),
                'is_step' => (bool)$form->getIsStep(),
                'sort_order' => (int)$form->getSortOrder(),
                'visible' => (bool)$form->getIsVisible(),
                'icon'  => $form->getIcon(),
                'path' => $form->getPath(),
                'form' => $form
            ];
        }
        return $result;
    }

    /**
     * Filter post data
     *
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return array
     */
    public function filterPostData($data)
    {
        $data = parent::filterPostData($data);

        //validate frontend_input
        if (isset($data['frontend_input'])) {
            $this->_inputValidator->setHaystack(array_keys($this->getAttributeInputTypes()));
            if (!$this->_inputValidator->isValid($data['frontend_input'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($this->filterManager->stripTags(implode(' ', $this->_inputValidator->getMessages())))
                );
            }
        }
        return $data;
    }
}
