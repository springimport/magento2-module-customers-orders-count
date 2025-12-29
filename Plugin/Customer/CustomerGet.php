<?php

namespace SpringImport\CustomersOrdersCount\Plugin\Customer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;

class CustomerGet
{
    /**
     * @param CustomerRepositoryInterface $subject
     * @param CustomerInterface $entity
     * @return CustomerInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        CustomerRepositoryInterface $subject,
        CustomerInterface $entity
    ) {
        return $this->processItem($entity);
    }

    /**
     * @param CustomerRepositoryInterface $subject
     * @param CustomerCollection $result
     * @return CustomerCollection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(
        CustomerRepositoryInterface $subject,
        //CustomerCollection $result
        $result
    ) {
        foreach ($result->getItems() as $item) {
            $this->processItem($item);
        }
        return $result;
    }

    /**
     * @param CustomerInterface $entity
     * @return CustomerInterface
     */
    protected function processItem(CustomerInterface $entity)
    {
        $extensionAttributes = $entity->getExtensionAttributes();

        if ($extensionAttributes) {
            //$extensionAttributes->setOrdersCount($entity->getData('orders_count'));
            //$entity->setExtensionAttributes($extensionAttributes);
        }

        return $entity;
    }
}
