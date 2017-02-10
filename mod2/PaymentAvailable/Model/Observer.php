<?php
class Hunter_PaymentAvailable_Model_Observer {

    public function paymentIsAvailable($observer) {
		/* If payment method already not available then skip check */
		if(!$observer->getResult()->isAvailable) {
			return;
		}
		
        $method = $observer->getMethodInstance();
		
		$isAvailable = Mage::helper('paymentavailable')->paymentIsAvailable($method);
		
		$observer->getResult()->isAvailable = $isAvailable;
    }
	
}
