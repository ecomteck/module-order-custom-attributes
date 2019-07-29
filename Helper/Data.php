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

/**
 *  Customer Data Helper
 *
 */
class Data extends \Ecomteck\CustomAttributeManagement\Helper\Data
{

    /**
     * Customer address
     *
     * @var Address
     */
    protected $_customerAddress = null;

    protected $moduleManager;


    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $eavConfig, $localeDate, $filterManager);
    }

    /**
     * Whether a module is enabled in the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isModuleEnabled($moduleName)
    {
        return $this->_moduleManager->isEnabled($moduleName);
    }

    /**
     * Whether a module output is permitted by the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isOutputEnabled($moduleName)
    {
        return $this->_moduleManager->isOutputEnabled($moduleName);
    }

    public function isOneStepCheckoutEnabled()
    {
        if ($this->_moduleManager->isOutputEnabled('Ecomteck_OneStepCheckout')) {
            return $this->scopeConfig->getValue(
                'one_step_checkout/general/enable'
            );
        }
        return false;
    }

    /**
     * Return available customer attribute form as select options
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributeFormOptions()
    {
        throw new \Magento\Framework\Exception\LocalizedException(__('Use helper with defined EAV entity.'));
    }

    /**
     * Default attribute entity type code
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getEntityTypeCode()
    {
        throw new \Magento\Framework\Exception\LocalizedException(__('Use helper with defined EAV entity.'));
    }

    /**
     * Return data array of available attribute Input Types
     *
     * @param string|null $inputType
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getAttributeInputTypes($inputType = null)
    {
        $inputTypes = parent::getAttributeInputTypes();
        unset($inputTypes['file']);
        unset($inputTypes['image']);
        $inputTypes['label'] = [
            'label' => __('Label'),
            'manage_options' => false,
            'validate_types' => ['min_text_length', 'max_text_length'],
            'validate_filters' => ['alphanumeric', 'numeric', 'alpha', 'url', 'email'],
            'filter_types' => ['striptags', 'escapehtml'],
            'backend_type' => 'varchar',
            'default_value' => 'label',
        ];

        $inputTypes['html'] = [
            'label' => __('Html'),
            'manage_options' => false,
            'validate_types' => [],
            'validate_filters' => [],
            'filter_types' => [],
            'backend_type' => 'text',
            'default_value' => 'html',
        ];

        $inputTypes['discount'] = [
            'label' => __('Apply Coupon Code'),
            'manage_options' => false,
            'validate_types' => [],
            'validate_filters' => [],
            'filter_types' => [],
            'backend_type' => 'varchar',
            'default_value' => '',
            'is_custom_component' => true,
            'component_path' => 'components/checkout/children/steps/children/billing-step/children/payment/children/afterMethods/children/discount',
            'component' => null
        ];

        if($this->isOneStepCheckoutEnabled()){
            $inputTypes['discount']['component_path'] = 'components/checkout/children/sidebar/children/after-sidebar/children/discount';
        }

        if($this->isModuleEnabled('Ecomteck_GiftCardAccount') || $this->isModuleEnabled('Magento_GiftCardAccount')){
            $inputTypes['giftCardAccount'] = [
                'label' => __('Apply Gift Card'),
                'manage_options' => false,
                'validate_types' => [],
                'validate_filters' => [],
                'filter_types' => [],
                'backend_type' => 'varchar',
                'default_value' => '',
                'is_custom_component' => true,
                'component_path' => 'components/checkout/children/steps/children/billing-step/children/payment/children/afterMethods/children/giftCardAccount',
                'component' => null
            ];

            if($this->isOneStepCheckoutEnabled()){
                $inputTypes['giftCardAccount']['component_path'] = 'components/checkout/children/sidebar/children/after-sidebar/children/giftCardAccount';
            }
        }
        
        if($this->isModuleEnabled('Ecomteck_CustomerBalance') || $this->isModuleEnabled('Magento_CustomerBalance')){
            $inputTypes['storeCredit'] = [
                'label' => __('Store Credit'),
                'manage_options' => false,
                'validate_types' => [],
                'validate_filters' => [],
                'filter_types' => [],
                'backend_type' => 'varchar',
                'default_value' => '',
                'is_custom_component' => true,
                'component_path' => 'components/checkout/children/steps/children/billing-step/children/payment/children/afterMethods/children/storeCredit',
                'component' => null
            ];
            if($this->isOneStepCheckoutEnabled()){
                $inputTypes['storeCredit']['component_path'] = 'components/checkout/children/sidebar/children/after-sidebar/children/storeCredit';
            }
        }

        if($this->isModuleEnabled('Ecomteck_RewardPoints') || $this->isModuleEnabled('Magento_RewardPoints')){
            $inputTypes['reward'] = [
                'label' => __('Reward Point'),
                'manage_options' => false,
                'validate_types' => [],
                'validate_filters' => [],
                'filter_types' => [],
                'backend_type' => 'varchar',
                'default_value' => '',
                'is_custom_component' => true,
                'component_path' => 'components/checkout/children/steps/children/billing-step/children/payment/children/afterMethods/children/reward',
                'component' => null
            ];
            if($this->isOneStepCheckoutEnabled()){
                $inputTypes['reward']['component_path'] = 'components/checkout/children/sidebar/children/after-sidebar/children/reward';
            }
        }

        if($this->isModuleEnabled('Ecomteck_OrderComment')){
            $inputTypes['order-comment'] = [
                'label' => __('Order Comment'),
                'manage_options' => false,
                'validate_types' => [],
                'validate_filters' => [],
                'filter_types' => [],
                'backend_type' => 'varchar',
                'default_value' => '',
                'is_custom_component' => true,
                'component' => [
                    'component' =>'uiComponent',
                    'children' => [
                        'order-comment-validator' => [
                            'component' => 'Ecomteck_OrderComment/js/view/checkout/order-comment-validator'
                        ],
                        'order-comment' => [
                            'component' => 'Ecomteck_OrderComment/js/view/checkout/order-comment-block'
                        ]
                    ]
                ]
            ];
        }

        if($this->isModuleEnabled('Ecomteck_OrderComment')){
            $inputTypes['order-comment'] = [
                'label' => __('Order Comment'),
                'manage_options' => false,
                'validate_types' => [],
                'validate_filters' => [],
                'filter_types' => [],
                'backend_type' => 'varchar',
                'default_value' => '',
                'is_custom_component' => true,
                'component' => [
                    'component' =>'uiComponent',
                    'children' => [
                        'order-comment-validator' => [
                            'component' => 'Ecomteck_OrderComment/js/view/checkout/order-comment-validator'
                        ],
                        'order-comment' => [
                            'component' => 'Ecomteck_OrderComment/js/view/checkout/order-comment-block'
                        ]
                    ]
                ]
            ];
        }

        /*
        $inputTypes['cart_items'] = [
            'label' => __('Shopping Cart'),
            'manage_options' => false,
            'validate_types' => [],
            'validate_filters' => [],
            'filter_types' => [],
            'backend_type' => 'varchar',
            'default_value' => '',
            'is_custom_component' => true,
            'component_path' => 'components/checkout/children/sidebar/children/summary',
            'component' => null
        ];
        
        $inputTypes['authentication'] = [
            'label' => __('Login Form'),
            'manage_options' => false,
            'validate_types' => [],
            'validate_filters' => [],
            'filter_types' => [],
            'backend_type' => 'varchar',
            'default_value' => '',
            'is_custom_component' => true,
            'component_path' => 'components/checkout/children/authentication',
            'component' => null
        ];

        $inputTypes['next_step_button'] = [
            'label' => __('Next Step Button'),
            'manage_options' => false,
            'validate_types' => [],
            'validate_filters' => [],
            'filter_types' => [],
            'backend_type' => 'varchar',
            'default_value' => '',
            'is_custom_component' => true,
            'component' => [
                'component' => 'Ecomteck_OrderCustomAttributes/js/view/next-step-button'
            ]
        ];

        $inputTypes['back_step_button'] = [
            'label' => __('Back Step Button'),
            'manage_options' => false,
            'validate_types' => [],
            'validate_filters' => [],
            'filter_types' => [],
            'backend_type' => 'varchar',
            'default_value' => '',
            'is_custom_component' => true,
            'component' => [
                'component' => 'Ecomteck_OrderCustomAttributes/js/view/back-step-button'
            ]
        ];*/
        
        if (null === $inputType) {
            return $inputTypes;
        } elseif (isset($inputTypes[$inputType])) {
            
            return $inputTypes[$inputType];
        }
        return [];
    }
}
