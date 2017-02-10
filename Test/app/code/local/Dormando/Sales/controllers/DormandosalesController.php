<?php

class Dormando_Sales_DormandosalesController extends Mage_Adminhtml_Controller_Action 
{

    public function initOrder($id)
    {
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }

    public function registerAction()
    {
        if ($order = $this->initOrder($this->getRequest()->getParam('order_id'))) {
            try {
                if($customer = Mage::helper('dormandosales')->findCustomer($order)) {
                        $this->_getSession()->addSuccess(
                            $this->__('Customer has been found and the order was associated with the customer.')
                        );
                } else {
                        $customer = Mage::helper('dormandosales')->initCustomer($order);
                        $customer->save();
                        $this->_getSession()->addSuccess(
                            $this->__('Customer has been registered.')
                        );
                }
                $order->setCustomerId($customer->getId());
                $order->setCustomerIsGuest(0);
                $order->save();
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('Customer has not been registered'));
                Mage::logException($e);
            }
            $this->_redirect('adminhtml/sales_order/view', array('order_id' => $order->getId()));
        }
    }

    public function indexAction()
    {
    }

    /**
     *  Check for admin permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order');
    }

}

