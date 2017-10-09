<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */
 
namespace Firebear\ShapeShift\Model\ResourceModel\Transactions;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Firebear\ShapeShift\Model\Transactions', 'Firebear\ShapeShift\Model\ResourceModel\Transactions');
    }
}
