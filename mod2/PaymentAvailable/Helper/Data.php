<?php
class Hunter_PaymentAvailable_Helper_Data extends Mage_Core_Helper_Abstract {
	
	const SCOPE_BOTH 		= 1;
	const SCOPE_FRONTEND 	= 2;
	const SCOPE_ADMIN 		= 3;
	
    public function paymentIsAvailable($payment) {
		$result = true;
		$scope 	= (int) $payment->getConfigData('scope_visibility');
		
		if($scope) {
			if(!in_array($scope, $this->getScopes())) {
				$result = false;
			}
		}
		
		return $result;
	}
	
	public function getScopes() {
		$data = array(self::SCOPE_BOTH);
		
		$data[] = (Mage::app()->getStore()->isAdmin()) ? self::SCOPE_ADMIN : self::SCOPE_FRONTEND;
		
		return $data;
	}
	
}
