<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */
 
namespace Firebear\ShapeShift\Api;

use Firebear\ShapeShift\Api\Data\TransactionInterface;

interface TransactionsRepositoryInterface
{
    /**
     * @param \Firebear\ShapeShift\Api\Data\TransactionInterface $transactionModel
     *
     * @return \Firebear\ShapeShift\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(TransactionInterface $transactionModel);

    /**
     * @param int $id
     *
     * @return \Firebear\ShapeShift\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param \Firebear\ShapeShift\Api\Data\TransactionInterface $transactionModel
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(TransactionInterface $transactionModel);

    /**
     * @param int $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);
}
