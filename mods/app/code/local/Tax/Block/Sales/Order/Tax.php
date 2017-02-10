<?php
/**
 *  Core mod
 *
 * Tax totals modification block. Can be used just as subblock of Mage_Sales_Block_Order_Totals
 */
class Toppik_Tax_Block_Sales_Order_Tax extends Mage_Tax_Block_Sales_Order_Tax
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
            $grandtotalExcl     = $grandtotal - $this->_source->getTaxAmount();
            $baseGrandtotalExcl = $baseGrandtotal - $this->_source->getBaseTaxAmount();
            $grandtotalExcl     = max($grandtotalExcl, 0);
            $baseGrandtotalExcl = max($baseGrandtotalExcl, 0);
            $tax = $this->_source->getTaxAmount();
            $baseTax = $this->_source->getBaseTaxAmount();
            $totalExcl = new Varien_Object(array(
                'code'      => 'tax',
                'strong'    => true,
                'value'     => $tax,
                'base_value'=> $baseTax,
                'label'     => $this->__('Tax')
            ));
            $totalIncl = new Varien_Object(array(
                'code'      => 'grand_total',
                'strong'    => true,
                'value'     => $grandtotal,
                'base_value'=> $baseGrandtotal,
                'label'     => $this->__('Grand Total')
            ));
            $parent->addTotal($totalExcl, 'grand_total');
            $this->_addTax('grand_total');
            $parent->addTotal($totalIncl, 'tax');
        }
        return $this;
    }

}
