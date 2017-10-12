<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ShapeShift\Model;

/**
 * Class Transactions
 *
 * @package Firebear\ShapeShift\Model
 */
class Transactions extends \Magento\Framework\Model\AbstractModel
    implements \Firebear\ShapeShift\Api\Data\TransactionInterface
{
    protected function _construct()
    {
        $this->_init('Firebear\ShapeShift\Model\ResourceModel\Transactions');
    }

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function getDepositAddress()
    {
        return $this->getData(self::DEPOSIT_ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setDepositAddress($depositAddress)
    {
        return $this->setData(self::DEPOSIT_ADDRESS, $depositAddress);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmountDeposit()
    {
        return $this->getData(self::AMOUNT_DEPOSIT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAmountDeposit($amountDeposit)
    {
        return $this->setData(self::AMOUNT_DEPOSIT, $amountDeposit);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}