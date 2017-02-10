<?php
/**
 *  Core mod
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Toppik_Sales_Model_Order_Address extends Mage_Sales_Model_Order_Address
{

    public function getRegioncode() {
        $region = Mage::getModel('directory/region')->load($this->_getRegionId());
        return $region->getCode() ? $region->getCode() : 'N/A';
    }

    protected function _getRegionId()
    {
        $regionId = $this->getData('region_id');
        $region   = $this->getData('region');
        if (!$regionId) {
            if (is_numeric($region)) {
                $this->setData('region_id', $region);
                $this->unsRegion();
            } else {
                $regionModel = Mage::getModel('directory/region')
                    ->loadByCode($this->getData('region_code'), $this->getCountryId());
                $this->setData('region_id', $regionModel->getId());
            }
        }
        return $this->getData('region_id');
    }

}
