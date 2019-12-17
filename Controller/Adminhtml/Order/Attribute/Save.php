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
namespace Ecomteck\OrderCustomAttributes\Controller\Adminhtml\Order\Attribute;

use Magento\Backend\App\Action\Context;
use Ecomteck\OrderCustomAttributes\Model\Sales\Order\AttributeFactory;
use Ecomteck\OrderCustomAttributes\Helper\Order as HelperOrder;
use Ecomteck\OrderCustomAttributes\Helper\Data as HelperData;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Registry;
use Magento\Store\Model\WebsiteFactory;
use Magento\Framework\Serialize\Serializer\FormData;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\LayoutFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Ecomteck\OrderCustomAttributes\Controller\Adminhtml\Order\Attribute
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperOrder
     */
    protected $helperOrder;

    /**
     * @var FilterManager
     */
    protected $filterManager;
    /**
     * @var FormData
     */
    private $formDataSerializer;
    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Config $eavConfig
     * @param AttributeFactory $attrFactory
     * @param SetFactory $attrSetFactory
     * @param WebsiteFactory $websiteFactory
     * @param HelperData $helperData
     * @param HelperOrder $helperOrder
     * @param FilterManager $filterManager
     * @param LayoutFactory $layoutFactory
     * @param FormData|null $formDataSerializer
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Config $eavConfig,
        AttributeFactory $attrFactory,
        SetFactory $attrSetFactory,
        WebsiteFactory $websiteFactory,
        HelperData $helperData,
        HelperOrder $helperOrder,
        FilterManager $filterManager,
        LayoutFactory $layoutFactory,
        FormData $formDataSerializer = null
    ) {
        $this->helperData = $helperData;
        $this->helperOrder = $helperOrder;
        $this->filterManager = $filterManager;
        parent::__construct(
            $context,
            $coreRegistry,
            $eavConfig,
            $attrFactory,
            $attrSetFactory,
            $websiteFactory
        );
        $this->layoutFactory = $layoutFactory;
        $this->formDataSerializer = $formDataSerializer ?? ObjectManager::getInstance()->get(FormData::class);
    }

    /**
     * Save attribute action
     *
     * @return Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        try {
            $optionData = $this->formDataSerializer->unserialize(
                $this->getRequest()->getParam('serialized_options', '[]')
            );
        } catch (\InvalidArgumentException $e) {
            $message = __("The attribute couldn't be saved due to an error. Verify your information and try again. "
                . "If the error persists, please try again later.");
            $this->messageManager->addErrorMessage($message);

            return $this->returnResult('adminhtml/*/edit', ['_current' => true], ['error' => true]);
        }
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $data = $this->getRequest()->getPostValue();
        $data = array_replace_recursive(
            $data,
            $optionData
        );
        if ($this->getRequest()->isPost() && $data) {
            /* @var $attributeObject \Ecomteck\OrderCustomAttributes\Model\Sales\Order\Attribute */
            $attributeObject = $this->_initAttribute();

            //filtering
            try {
                $data = $this->helperOrder->filterPostData($data);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                if (isset($data['attribute_id'])) {
                    $resultRedirect->setPath('adminhtml/*/edit', ['_current' => true]);
                } else {
                    $resultRedirect->setPath('adminhtml/*/new', ['_current' => true]);
                }
                return $resultRedirect;
            }

            $attributeId = $this->getRequest()->getParam('attribute_id');
            if ($attributeId) {
                $attributeObject->load($attributeId);
                if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()) {
                    $this->messageManager->addError(__('You cannot edit this attribute.'));
                    $this->_getSession()->addAttributeData($data);
                    $resultRedirect->setPath('adminhtml/*/');
                    return $resultRedirect;
                }

                $data['attribute_code'] = $attributeObject->getAttributeCode();
                $data['is_user_defined'] = $attributeObject->getIsUserDefined();
                $data['frontend_input'] = $attributeObject->getFrontendInput();
                $data['is_user_defined'] = $attributeObject->getIsUserDefined();
                $data['is_system'] = $attributeObject->getIsSystem();
                if (isset($data['used_in_forms']) && is_array($data['used_in_forms'])) {
                    $data['used_in_forms'][] = 'adminhtml_customer';
                }
            } else {
                $data['backend_model'] = $this->helperData->getAttributeBackendModelByInputType(
                    $data['frontend_input']
                );
                $data['source_model'] = $this->helperData->getAttributeSourceModelByInputType($data['frontend_input']);
                $data['backend_type'] = $this->helperData->getAttributeBackendTypeByInputType($data['frontend_input']);
                $data['is_user_defined'] = 1;
                $data['is_system'] = 0;

                // add set and group info
                $data['attribute_set_id'] = $this->_getEntityType()->getDefaultAttributeSetId();
                /** @var $attrSet \Magento\Eav\Model\Entity\Attribute\Set */
                $attrSet = $this->_attrSetFactory->create();
                $data['attribute_group_id'] = $attrSet->getDefaultGroupId($data['attribute_set_id']);
                $data['used_in_forms'][] = 'adminhtml_customer';
            }

            $defaultValueField = $this->helperData->getAttributeDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $scopeKeyPrefix = $this->getRequest()->getParam('website') ? 'scope_' : '';
                $defaultValue = $this->getRequest()->getParam($scopeKeyPrefix . $defaultValueField);
                if($defaultValueField != 'default_value_html'){
                    $data[$scopeKeyPrefix . 'default_value'] = $defaultValue
                    ? $this->filterManager->stripTags($defaultValue) : null;
                } else {
                    $data[$scopeKeyPrefix . 'default_value'] = $defaultValue
                    ? $defaultValue : null;
                }
                
            }

            $data['entity_type_id'] = $this->_getEntityType()->getId();
            $data['validate_rules'] = $this->helperData->getAttributeValidateRules($data['frontend_input'], $data);

            $validateRulesErrors = $this->helperData->checkValidateRules(
                $data['frontend_input'],
                $data['validate_rules']
            );
            if (count($validateRulesErrors)) {
                foreach ($validateRulesErrors as $message) {
                    $this->messageManager->addError($message);
                }
                $resultRedirect->setPath('adminhtml/*/edit', ['_current' => true]);
                return $resultRedirect;
            }
            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
            }
            if (isset($data['rule']['actions'])) {
                $data['actions'] = $data['rule']['actions'];
            }
            unset($data['rule']);
            $attributeObject->getRule()->loadPost($data);
            $attributeObject->addData($data);

            /**
             * Check "Use Default Value" checkboxes values
             */
            $useDefaults = $this->getRequest()->getPost('use_default');
            if ($useDefaults) {
                foreach ($useDefaults as $key) {
                    $attributeObject->setData($key, null);
                }
            }

            try {
                $this->_eventManager->dispatch(
                    'ecomteck_order_custom_attributes_attribute_before_save',
                    ['attribute' => $attributeObject]
                );
                $attributeObject->save();
                $this->_eventManager->dispatch(
                    'ecomteck_order_custom_attributes_attribute_save',
                    ['attribute' => $attributeObject]
                );

                $this->messageManager->addSuccess(__('You saved the order attribute.'));
                $this->_getSession()->setAttributeData(false);
                if ($this->getRequest()->getParam('back', false)) {
                    $resultRedirect->setPath(
                        'adminhtml/*/edit',
                        ['attribute_id' => $attributeObject->getId(), '_current' => true]
                    );
                } else {
                    $resultRedirect->setPath('adminhtml/*/');
                }
                return $resultRedirect;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setAttributeData($data);
                $resultRedirect->setPath('adminhtml/*/edit', ['_current' => true]);
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t save the order attribute right now.'));
                $this->_getSession()->setAttributeData($data);
                $resultRedirect->setPath('adminhtml/*/edit', ['_current' => true]);
                return $resultRedirect;
            }
        }
        $resultRedirect->setPath('adminhtml/*/');
        return $resultRedirect;
    }
    /**
     * Provides an initialized Result object.
     *
     * @param string $path
     * @param array $params
     * @param array $response
     * @return Json|Redirect
     */
    private function returnResult($path = '', array $params = [], array $response = [])
    {
        if ($this->isAjax()) {
            $layout = $this->layoutFactory->create();
            $layout->initMessages();

            $response['messages'] = [$layout->getMessagesBlock()->getGroupedHtml()];
            $response['params'] = $params;

            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($response);
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath($path, $params);
    }

    /**
     * Define whether request is Ajax
     *
     * @return boolean
     */
    private function isAjax()
    {
        return $this->getRequest()->getParam('isAjax');
    }
}
