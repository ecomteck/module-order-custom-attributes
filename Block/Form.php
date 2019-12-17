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
namespace Ecomteck\OrderCustomAttributes\Block;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Eav\Model\Entity\Type as EntityType;

/**
 * Customer Dynamic attributes Form Block
 *
 * @author      Ecomteck <ecomteck@gmail.com>
 */
class Form extends \Ecomteck\CustomAttributeManagement\Block\Form
{
    /**
     * @var \Magento\Customer\Model\Metadata\Form
     */
    protected $_metadataForm;

    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $_metadataFormFactory;

    /** @var \Magento\Customer\Model\Session */
    protected $_customerSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Data\Collection\ModelFactory $modelFactory
     * @param \Magento\Eav\Model\Form\Factory $formFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Data\Collection\ModelFactory $modelFactory,
        \Magento\Eav\Model\Form\Factory $formFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $modelFactory, $formFactory, $eavConfig, $data);
        $this->_metadataFormFactory = $metadataFormFactory;
        $this->_customerSession = $customerSession;
        $this->_isScopePrivate = true;
    }

    /**
     * Name of the block in layout update xml file
     *
     * @var string
     */
    protected $_xmlBlockName = 'customer_form_template';

    /**
     * Class path of Form Model
     *
     * @var string
     */
    protected $_formModelPath = 'Magento\Customer\Model\Form';

    /**
     * @return \Magento\Customer\Model\Metadata\Form
     */
    public function getMetadataForm()
    {
        if ($this->_metadataForm === null) {
            $this->_metadataForm = $this->_metadataFormFactory->create(
                $this->_entityType->getEntityTypeCode(),
                $this->_formCode
            );
        }
        return $this->_metadataForm;
    }

    /**
     * Return whether the form should be opened in an expanded mode showing the change password fields
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getChangePassword()
    {
        return $this->_customerSession->getChangePassword();
    }

    /**
     * Return Entity object
     *
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function getEntity()
    {
        if ($this->_entity === null && $this->_entityModelClass) {
            $this->_entity = $this->_modelFactory->create($this->_entityModelClass);
            $entityId = $this->getCurrentEntityId($this->_entity->getEntityType());
            if ($entityId) {
                $this->_entity->load($entityId);
            }
        }
        return $this->_entity;
    }

    /**
     * Retrieve current entity type
     *
     * @param EntityType $entityType
     * @return int|null
     */
    protected function getCurrentEntityId(EntityType $entityType)
    {
        switch ($entityType->getEntityTypeCode()) {
            case CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER:
                return $this->_customerSession->getCustomerId();
                break;
            case AddressMetadataInterface::ENTITY_TYPE_ADDRESS:
                return (int)$this->getRequest()->getParam('id');
                break;
            default:
                return null;
        }
    }
}
