<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ShapeShift\Api\Data;

/**
 * Interface TransactionInterface
 *
 * @package Firebear\ShapeShift\Api\Data
 */
interface TransactionInterface
{
    
    const ENTITY_ID = 'id';
    const ORDER_ID = 'order_id';
    const DEPOSIT_ADDRESS = 'deposit_address';
    const AMOUNT_DEPOSIT = 'amount_deposit';
    const STATUS = 'status';
    
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param $id
     *
     * @return mixed
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getOrderId();

    /**
     * @param $orderId
     *
     * @return mixed
     */
    public function setOrderId($orderId);

    /**
     * @return mixed
     */
    public function getDepositAddress();

    /**
     * @param $depositAddress
     *
     * @return mixed
     */
    public function setDepositAddress($depositAddress);

    /**
     * @return mixed
     */
    public function getAmountDeposit();

    /**
     * @param $amountDeposit
     *
     * @return mixed
     */
    public function setAmountDeposit($amountDeposit);

    /**
     * @return mixed
     */
    public function getStatus();

    /**
     * @param $status
     *
     * @return mixed
     */
    public function setStatus($status);
}
