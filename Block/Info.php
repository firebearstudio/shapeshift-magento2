<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ShapeShift\Block;

use Magento\Framework\Phrase;
use Magento\Payment\Block\ConfigurableInfo;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Gateway\ConfigInterface;
use Firebear\ShapeShift\Model\TransactionsRepository;
use Firebear\ShapeShift\Model\ResourceModel\Transactions\Collection;

class Info extends ConfigurableInfo
{

    private $transactionRepository;
    private $transactionCollectionFactory;

    public function __construct(
        Context $context,
        ConfigInterface $config,
        TransactionsRepository $transactionsRepository,
        Collection $transactionCollectionFactory,
        array $data = []
    ) {
        $template                           = 'Firebear_ShapeShift::info/shapeshift.phtml';
        $this->transactionRepository        = $transactionsRepository;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->setTemplate($template);
        parent::__construct($context, $config, $data);
    }

    public function getTemplateData()
    {
        $transactionModel = $this->transactionRepository->getByOrderId($this->getInfo()->getOrder()->getId());
        $arrayData = [
            'depositAddress' => $transactionModel->getDepositAddress(),
            'depositAmount' => $transactionModel->getAmountDeposit()
            ];

        return $arrayData;
    }

    /**
     * Returns label
     *
     * @param string $field
     *
     * @return Phrase
     */
    protected function getLabel($field)
    {
        return __($field);
    }

    /**
     * Returns value view
     *
     * @param string $field
     * @param string $value
     *
     * @return string | Phrase
     */
    protected function getValueView($field, $value)
    {
        switch ($field) {
            case FraudHandler::FRAUD_MSG_LIST:
                return implode('; ', $value);
        }

        return parent::getValueView($field, $value);
    }
}
