<?php
class Dormando_Sales_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function findCustomer($order)
    {       
       $customer = Mage::getModel('customer/customer'); 
       $customer->setWebsiteId(Mage::getModel('core/store')->load($order->getStoreId())->getWebsiteId()); 
        if($customer->loadByEmail($order->getData('customer_email'))->getId()){
                return $customer;
        }
        return null;
    }

    public function initCustomer($order)
    {
             // Prepare new customer
            $customer = Mage::getModel('customer/customer');
            $customer->setEmail($order->getData('customer_email'))
                ->setFirstname($order->getData('customer_firstname'))
                ->setLastname($order->getData('customer_lastname'))
                ->setMiddleName($order->getData('customer_middlename'))
                ->setPrefix($order->getData('customer_prefix'))
                ->setSuffix($order->getData('customer_suffix'))
                ->setGroupId(Mage::helper('customer')->getDefaultCustomerGroupId($order->getStoreId()));

           
            /** @var $customerBillingAddress Mage_Customer_Model_Address */
            $customerBillingAddress = Mage::getModel('customer/address');
            Mage::helper('core')->copyFieldset('sales_convert_quote_address', 'to_customer_address', $order->getBillingAddress(), $customerBillingAddress);
            $customer->addData($customerBillingAddress->getData())
                ->setPassword($customer->generatePassword())
                ->setStore(Mage::app()->getStore($order->getStoreId()));


            $customerBillingAddress->setIsDefaultBilling(true);
            $customer->addAddress($customerBillingAddress);


            /** @var $shippingAddress Mage_Sales_Model_Quote_Address */
            $shippingAddress = $order->getShippingAddress();
            if (!$order->getIsVirtual()
                && !$shippingAddress->getSameAsBilling()
            ) {
                /** @var $customerShippingAddress Mage_Customer_Model_Address */
                $customerShippingAddress = Mage::getModel('customer/address');
                Mage::helper('core')->copyFieldset('sales_convert_quote_address', 'to_customer_address', $shippingAddress, $customerShippingAddress);
                $customerShippingAddress->setIsDefaultShipping(true);
                $customer->addAddress($customerShippingAddress);
            } else {
                $customerBillingAddress->setIsDefaultShipping(true);
            }

            return $customer;
    }

}
