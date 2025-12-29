<?php

namespace SpringImport\CustomersOrdersCount\Plugin\Customer;

use Magento\Framework\DB\Select;
use Magento\Framework\DB\Sql\Expression;

class CollectionPlugin
{
    /**
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $subject
     * @param $result
     * @return mixed
     */
    public function afterGetSelectCountSql(\Magento\Customer\Model\ResourceModel\Customer\Collection $subject, $result)
    {
        $select = $result;
        $conditionExists = $this->checkWhereConditionExists($select, 'orders_count');

        if ($conditionExists) {
            $select = $result;
            $this->applyCustomersCountOrdersToSelect($select, true);
        }

        return $result;
    }

    /**
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $subject
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    public function before_beforeLoad(\Magento\Customer\Model\ResourceModel\Customer\Collection $subject)
    {
        $select = $subject->getSelect();
        $this->applyCustomersCountOrdersToSelect($select, false);

        return $subject;
    }

    /**
     * Add join query for filter by customer orders count
     *
     * @param Select $select
     * @param bool $isSelectQuery
     */
    public function applyCustomersCountOrdersToSelect(Select &$select, $isSelectQuery)
    {
        $orderTable = $select->getConnection()->getTableName('sales_order');

        if ($isSelectQuery) {
            $columns = null;
        } else {
            $columns = ['orders_count_results.orders_count as orders_count'];
        }

        $subQuery = new Expression("(
            SELECT customer_id, COUNT(sales_order.entity_id) AS `orders_count`
            FROM $orderTable
            GROUP BY customer_id
        )");

        $select->joinLeft(
            ['orders_count_results' => $subQuery],
            'orders_count_results.customer_id = e.entity_id',
            $columns
        );
    }

    /**
     * @param Select $select
     * @param $field
     * @return bool
     */
    protected function checkWhereConditionExists(Select $select, $field)
    {
        $whereStatement = $select->getPart(Select::WHERE);
        foreach ($whereStatement as $value) {
            if (!(strpos($value, $field) === false)) {
                return true;
            }
        }

        return false;
    }
}
