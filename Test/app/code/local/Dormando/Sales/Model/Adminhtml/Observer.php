<?php 

class Dormando_Sales_Model_Adminhtml_Observer 
{
    public function addRegisterCustomerButton($observer) {
        $block = Mage::app()->getLayout()->getBlock('sales_order_edit');
        if (!$block){
            return $this;
        }
        $order = Mage::registry('current_order');

        //if customer is not guest then do not add register customer button
        if(!$order->getCustomerIsGuest())
            return $this;

        $url = Mage::helper("adminhtml")->getUrl(
            "adminhtml/dormandosales/register",
            array('order_id'=>$order->getId())
        );
        $message = Mage::helper('sales')->__('Are you sure you want to Register Guest Customer?');
        $block->addButton('register_customer', array(
                'label'     => Mage::helper('sales')->__('Register Customer'),
                'onclick'   => "confirmSetLocation('{$message}', '{$url}')",
                'class'     => 'go'
        ));
        return $this;
    }
}
