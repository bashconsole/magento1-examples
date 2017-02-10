<?php
/**
 *  Core mod
 *
 * Tax Calculation Model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Toppik_Tax_Model_Calculation extends Mage_Tax_Model_Calculation
{

    public function getStrikeIronRate($postcode)
    {
        $USER_ID = '05EFD2BEED12A2ADCFB2';
        $PASSWORD = 'strikespencer12';
        $WSDL = 'http://ws.strikeiron.com/taxdatabasic5?WSDL';
        $client = new SoapClient($WSDL, array('trace' => 1, 'exceptions' => 1));
        $registered_user = array("RegisteredUser" => array("UserID" => $USER_ID, "Password" => $PASSWORD));
        $header = new SoapHeader("http://ws.strikeiron.com", "LicenseInfo", $registered_user);
        $client->__setSoapHeaders($header);
        $result = $client->__soapCall("GetTaxRateUS", array(array("ZIPCode" => $postcode)), null, null, $output_header);
        return $result->GetTaxRateUSResult->ServiceResult->TotalSalesTax;
    }

    /**
     * Get calculation tax rate by specific request
     *
     * @param   Varien_Object $request
     * @return  float
     */
    public function getRate($request)
    {

        require_once('CompanyName/NewModule/Model/console.php');

        if (!$request->getCountryId() || !$request->getCustomerClassId() || !$request->getProductClassId()) {
            return 0;
        }

        $cacheKey = $this->_getRequestCacheKey($request);
        if (!isset($this->_rateCache[$cacheKey])) {
            $this->unsRateValue();
            $this->unsCalculationProcess();
            $this->unsEventModuleId();
            Mage::dispatchEvent('tax_rate_data_fetch', array('request'=>$request));
            if (!$this->hasRateValue()) {
                $rateInfo = $this->_getResource()->getRateInfo($request);
                $this->setCalculationProcess($rateInfo['process']);
                $this->setRateValue($rateInfo['value']);
            } else {
                $this->setCalculationProcess($this->_formCalculationProcess());
            }
            $this->_rateCache[$cacheKey] = $this->getRateValue();
            $this->_rateCalculationProcess[$cacheKey] = $this->getCalculationProcess();
        }

        $enableStrikIron = false;

        if($enableStrikIron)
        {
            console::log('magento rate: '.$request->getPostcode().' :', $this->_rateCache[$cacheKey]);
            $pcCheck = substr($request->getPostcode(), 0, 3);
            if( ($pcCheck>=900 && $pcCheck<=966) || ($pcCheck>=460 && $pcCheck<=479) )
            {
                $strikeIronRate = $this->getStrikeIronRate($request->getPostcode()) * 100;
                console::log('strike iron rate: '.$request->getPostcode().' :', $strikeIronRate);
                return $strikeIronRate;
            } else {
                return $this->_rateCache[$cacheKey];
            }

        }
        else
        {
            return $this->_rateCache[$cacheKey];
        }
    }

}
