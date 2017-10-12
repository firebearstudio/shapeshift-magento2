<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ShapeShift\Model;

use Firebear\ShapeShift\Api\Data;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class TransactionsRepository implements \Firebear\ShapeShift\Api\TransactionsRepositoryInterface
{
    protected $transactionsModelResource;
    protected $transactionsModelFactory;
    private $log;
    
    /**
     * TransactionsRepository constructor.
     *
     * @param ResourceModel\Transactions $transactionsModelResource
     * @param TransactionsFactory        $transactionsModelFactory
     */
    public function __construct(
        \Firebear\ShapeShift\Model\ResourceModel\Transactions $transactionsModelResource,
        \Firebear\ShapeShift\Model\TransactionsFactory $transactionsModelFactory,
        LoggerInterface $log
    ) {
        $this->transactionsModelResource = $transactionsModelResource;
        $this->transactionsModelFactory  = $transactionsModelFactory;
        $this->log = $log;
    }


    /**
     * @param Data\TransactionInterface $transactionModel
     *
     * @return Data\TransactionInterface
     * @throws CouldNotSaveException
     */
    public function save(Data\TransactionInterface $transactionModel)
    {
        if ($transactionModel->getId()) {
            $transactionModel = $this->get($transactionModel->getId())
                ->addData($transactionModel->getData());
        }
        try {
            $this->transactionsModelResource->save($transactionModel);
            unset($this->entities);
        } catch (ValidationException $e) {
            $this->log->info("REPOSITORY SAVE: ".$e->getMessage());
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            $this->log->info("REPOSITORY SAVE: ".__('Unable to save model %1', $transactionModel->getId()));
            throw new CouldNotSaveException(__('Unable to save model %1', $transactionModel->getId()));
        }

        return $transactionModel;
    }

    /**
     * @param int $id
     *
     * @return bool|mixed
     */
    public function get($id)
    {
        if (!isset($this->entities[$id])) {
            $transactionModel = $this->transactionsModelFactory->create();
            $this->transactionsModelResource->load($transactionModel, $id);
            if (!$transactionModel->getId()) {
                return false;
            }
            $this->entities[$id] = $transactionModel;
        }

        return $this->entities[$id];
    }

    /**
     * @param $orderId
     *
     * @return mixed
     */
    public function getByOrderId($orderId)
    {
        $model = $this->transactionsModelFactory->create();
        $this->transactionsModelResource->load($model, $orderId, 'order_id');
        $this->entities[$orderId] = $model;

        return $model;
    }
    
    /**
     * @return mixed
     */
    public function create()
    {
        $model = $this->transactionsModelFactory->create();

        return $model;
    }

    /**
     * @param int $itemId
     *
     * @return bool
     */
    public function deleteById($id)
    {
        $model = $this->get($id);

        if ($this->delete($model)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Data\BoxInterface $boxModel
     *
     * @return bool
     * @throws CouldNotSaveException
     */
    public function delete(Data\TransactionInterface $transactionModel)
    {
        try {
            $this->transactionsModelResource->delete($transactionModel);
        } catch (ValidationException $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to remove entity with ID "%1"', $transactionModel->getId()));
        }

        return true;
    }
}
