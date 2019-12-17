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
namespace Ecomteck\OrderCustomAttributes\Model\Sales\Order;

/**
 * Customer Order model
 *
 *
 * @author      Ecomteck <ecomteck@gmail.com>
 */
class Form extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    private $ruleFactory;

    protected $rule;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     * @since 100.2.0
     */
    protected $serializer;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->ruleFactory = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\SalesRule\Model\RuleFactory::class);
        $this->serializer = \Magento\Framework\App\ObjectManager::getInstance()->get(
            \Magento\Framework\Serialize\Serializer\Json::class
        );
        $this->_init('Ecomteck\OrderCustomAttributes\Model\ResourceModel\Sales\Order\Form');
    }

    public function loadByCode($code)
    {
        $id = $this->_getResource()->loadByCode($code);
        if($id){
            $this->load($id);
        }
        return $this;
    }

    /**
     * Prepare data before saving
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function beforeSave()
    {
        // Serialize conditions
        if ($this->getRule()->getConditions()) {
            $this->setConditionsSerialized($this->serializer->serialize($this->getRule()->getConditions()->asArray()));
        }

        parent::beforeSave();
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function afterLoad()
    {
        $this->getRule()->setConditionsSerialized($this->getConditionsSerialized());
        return parent::afterLoad();
    }

    public function getRule()
    {
        if(!$this->rule){
            $this->rule = $this->ruleFactory->create();
            if($this->hasConditionsSerialized()){
                $this->rule->setConditionsSerialized($this->getConditionsSerialized());
            }
        }
        return $this->rule;
    }
}
