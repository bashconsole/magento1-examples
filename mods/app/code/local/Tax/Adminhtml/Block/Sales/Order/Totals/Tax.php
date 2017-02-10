<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Adminhtml order tax totals block
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Toppik_Tax_Adminhtml_Block_Sales_Order_Totals_Tax extends Mage_Adminhtml_Block_Sales_Order_Totals_Tax
{
       protected function _initGrandTotal()
    {
        $store  = $this->getStore();
        $parent = $this->getParentBlock();
        $grandototal = $parent->getTotal('grand_total');
        if (!$grandototal || !(float)$this->_source->getGrandTotal()) {
            return $this;
        }

        if ($this->_config->displaySalesTaxWithGrandTotal($store)) {
            $grandtotal         = $this->_source->getGrandTotal();
            $baseGrandtotal     = $this->_source->getBaseGrandTotal();
            $grandtotalExcl = (float) $this->_source->getSubtotal() + (float) $this->_source->getShippingAmount();
            $baseGrandtotalExcl = (float) $this->_source->getBaseSubtotal() + (float) $this->_source->getBaseShippingAmount();
            $grandtotalExcl     = max($grandtotalExcl, 0);
            $baseGrandtotalExcl = max($baseGrandtotalExcl, 0);

            $totalTaxValue =  (float) $this->_source->getTaxAmount() + (float) $this->_source->getShippingTaxAmount();
            $baseTaxValue = (float) $this->_source->getBaseTaxAmount() + (float) $this->_source->getBaseShippingTaxAmount();

            $totalExcl = new Varien_Object(array(
                'code'      => 'grand_total',
                'strong'    => true,
                'value'     => $grandtotalExcl,
                'base_value'=> $baseGrandtotalExcl,
                'label'     => $this->__('Grand Total (Excl.Tax)')
            ));
            $totalIncl = new Varien_Object(array(
                'code'      => 'grand_total_incl',
                'strong'    => true,
                'value'     => $grandtotal,
                'base_value'=> $baseGrandtotal,
                'label'     => $this->__('Grand Total (Incl.Tax)')
            ));
            $totalTax = new Varien_Object(array(
                'code'      => 'tax',
                'strong'    => false,
                'value'     => $totalTaxValue,
                'base_value'=> $baseTaxValue,
                'label'     => $this->__('Tax')
            ));
            $parent->addTotal($totalExcl, 'grand_total');
            $parent->addTotal($totalTax, 'grand_total');
            $parent->addTotal($totalIncl, 'tax');
        }
        return $this;
    }
}
