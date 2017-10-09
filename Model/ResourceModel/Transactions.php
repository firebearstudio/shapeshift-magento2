<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */
 
namespace Firebear\ShapeShift\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Transactions extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('firebear_transaction_entity', 'id');
    }
}