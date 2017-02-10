<?php
class Hunter_PaymentAvailable_Model_System_Config_Source_Scopes {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => Hunter_PaymentAvailable_Helper_Data::SCOPE_BOTH, 'label'=>Mage::helper('core')->__('Both')),
            array('value' => Hunter_PaymentAvailable_Helper_Data::SCOPE_FRONTEND, 'label'=>Mage::helper('core')->__('Front End')),
            array('value' => Hunter_PaymentAvailable_Helper_Data::SCOPE_ADMIN, 'label'=>Mage::helper('core')->__('Admin'))
        );
    }
	
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray() {
        return array(
            Hunter_PaymentAvailable_Helper_Data::SCOPE_BOTH => Mage::helper('core')->__('Both'),
            Hunter_PaymentAvailable_Helper_Data::SCOPE_FRONTEND => Mage::helper('core')->__('Front End'),
            Hunter_PaymentAvailable_Helper_Data::SCOPE_ADMIN => Mage::helper('core')->__('Admin')
        );
    }
	
}
