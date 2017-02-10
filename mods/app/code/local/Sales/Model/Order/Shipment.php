<?php
/**
 * Core mod
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Toppik_Sales_Model_Order_Shipment extends Mage_Sales_Model_Order_Shipment
{

    /**
     * Register shipment
     *
     * Apply to order, order items etc.
     *
     * @return unknown
     */
    public function register()
    {
        if ($this->getId()) {
            Mage::throwException(
                Mage::helper('sales')->__('Cannot register existing shipment')
            );
        }

        $totalQty = 1;
        foreach ($this->getAllItems() as $item) {
            if ($item->getQty()>0) {
                $item->register();
                if (!$item->getOrderItem()->isDummy(true)) {
                    $totalQty+= $item->getQty();
                }
            }
            else {
                #$item->isDeleted(true);
            }
        }
        $this->setTotalQty($totalQty);

        return $this;
    }

    /**
     * Before object save
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    protected function _beforeSave()
    {
        #if ((!$this->getId() || null !== $this->_items) && !count($this->getAllItems())) {
        #    Mage::throwException(
        #        Mage::helper('sales')->__('Cannot create an empty shipment.')
        #    );
        #}

        if (!$this->getOrderId() && $this->getOrder()) {
            $this->setOrderId($this->getOrder()->getId());
            $this->setShippingAddressId($this->getOrder()->getShippingAddress()->getId());
        }
        if ($this->getPackages()) {
            $this->setPackages(serialize($this->getPackages()));
        }

        return parent::_beforeSave();
    }

}
