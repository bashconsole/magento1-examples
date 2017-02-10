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
 * @package     Mage_Log
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Log Resource Model
 *
 * @category    Mage
 * @package     Mage_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Hunter_Log_Model_Resource_Log extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Init Resource model and connection
     *
     */
    protected function _construct()
    {
        $this->_init('log/visitor', 'visitor_id');
    }

    /**
     * Clean logs
     *
     * @param Mage_Log_Model_Log $object
     * @return Mage_Log_Model_Resource_Log
     */
    public function clean(Mage_Log_Model_Log $object)
    {
        $cleanTime = $object->getLogCleanTime();

        Mage::dispatchEvent('log_log_clean_before', array(
            'log'   => $object
        ));

//         $this->_cleanVisitors($cleanTime);
//         $this->_cleanCustomers($cleanTime);
//         $this->_cleanUrls();
        $this->_cleanQuotes();

        Mage::dispatchEvent('log_log_clean_after', array(
            'log'   => $object
        ));

        return $this;
    }

    /**
     * Clean visitors table
     *
     * @param int $time
     * @return Mage_Log_Model_Resource_Log
     */
    protected function _cleanVisitors($time)
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();

        $timeLimit = $this->formatDate(Mage::getModel('core/date')->gmtTimestamp() - $time);

        while (true) {
            $select = $readAdapter->select()
                ->from(
                    array('visitor_table' => $this->getTable('log/visitor')),
                    array('visitor_id' => 'visitor_table.visitor_id'))
                ->joinLeft(
                    array('customer_table' => $this->getTable('log/customer')),
                    'visitor_table.visitor_id = customer_table.visitor_id AND customer_table.log_id IS NULL',
                    array())
                ->where('visitor_table.last_visit_at < ?', $timeLimit)
                ->limit(100);

            $visitorIds = $readAdapter->fetchCol($select);

            if (!$visitorIds) {
                break;
            }

            $condition = array('visitor_id IN (?)' => $visitorIds);
            
            // remove visitors from log/quote
            $writeAdapter->delete($this->getTable('log/quote_table'), $condition);

            // remove visitors from log/url
            $writeAdapter->delete($this->getTable('log/url_table'), $condition);

            // remove visitors from log/visitor_info
            $writeAdapter->delete($this->getTable('log/visitor_info'), $condition);

            // remove visitors from log/visitor
            $writeAdapter->delete($this->getTable('log/visitor'), $condition);
        }

        return $this;
    }


    /**
     * Clean quote tables
     *
     * @param int $time
     * @return Mage_Log_Model_Resource_Log
     */
    protected function _cleanQuotes()
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();
        $time = 60  * (60 * 60 * 24); //60 days old cleaning - hardcoded because it's important, 60 days and not less

        $timeLimit = $this->formatDate(Mage::getModel('core/date')->gmtTimestamp() - $time);

        while (true) {
            $select = $readAdapter->select()
                ->from(
                    array('sales_quote_table' => $this->getTable('log/sales_quote_table')),
                    array('quote_id' => 'sales_quote_table.entity_id'))
                ->where('sales_quote_table.updated_at < ?', $timeLimit)
               ->order('quote_id')
                ->limit(10);


            $quote_ids = $readAdapter->fetchCol($select);

            if (!$quote_ids) {
                break;
            }

            $condition = array('quote_id IN (?)' => $quote_ids);
            

            // remove  from log/sales_quote_address_item
            $select = $readAdapter->select()
                ->from(
                    array('sales_quote_address_item_table' => $this->getTable('log/sales_quote_address_item_table')),
                    array('address_item_id' => 'sales_quote_address_item_table.address_item_id'))
                ->from(
                    array('sales_quote_address_table' => $this->getTable('log/sales_quote_address_table')),
                    array())
               ->where('sales_quote_address_table.address_id = sales_quote_address_item_table.quote_address_id')
               ->where('sales_quote_address_table.quote_id IN (?)', array($quote_ids));

		$address_item_ids = $readAdapter->fetchCol($select);

		if($address_item_ids){
			$writeAdapter->delete($this->getTable('log/sales_quote_address_item_table'), array('address_item_id IN (?)' => $address_item_ids));
		}

            // remove from log/sales_quote_shipping_rate
            $select = $readAdapter->select()
                ->from(
                    array('sales_quote_shipping_rate' => $this->getTable('log/sales_quote_shipping_rate_table')),
                    array('rate_id' => 'sales_quote_shipping_rate.rate_id'))
                ->from(
                    array('sales_quote_address' => $this->getTable('log/sales_quote_address_table')),
                    array())
               ->where('sales_quote_address.address_id = sales_quote_shipping_rate.address_id')
               ->where('sales_quote_address.quote_id IN (?)', array($quote_ids));

		$rate_ids = $readAdapter->fetchCol($select);

		if($rate_ids){
			$writeAdapter->delete($this->getTable('log/sales_quote_shipping_rate_table'), array('rate_id IN (?)' => $rate_ids));
		}


               // remove from log/sales_quote_address
              $writeAdapter->delete($this->getTable('log/sales_quote_address_table'), $condition);

               // remove  from log/sales_quote_item_option
              $select = $readAdapter->select()
                ->from(
                    array('sales_quote_item_option' => $this->getTable('log/sales_quote_item_option_table')),
                    array('option_id' => 'sales_quote_item_option.option_id'))
               ->from(
                    array('sales_quote_item' => $this->getTable('log/sales_quote_item_table')),
                    array())
                ->where('sales_quote_item_option.item_id = sales_quote_item.item_id')
                ->where('sales_quote_item.quote_id IN (?)', $quote_ids);


		$option_ids = $readAdapter->fetchCol($select);

		if($option_ids){
			$writeAdapter->delete($this->getTable('log/sales_quote_item_option_table'), array('option_id IN (?)' => $option_ids));
		}

               // remove from log/sales_quote_item
               $writeAdapter->delete($this->getTable('log/sales_quote_item_table'), $condition);

               // remove  from log/sales_quote_payment
               $writeAdapter->delete($this->getTable('log/sales_quote_payment_table'), $condition);

               // remove  from log/sales_quote
               $writeAdapter->delete($this->getTable('log/sales_quote_table'), array('entity_id IN (?)' => $quote_ids));

        }

        //clean broken sales_quote_address_table table
         while(true){

            $select = $readAdapter->select()
             ->from(
                    array('sales_quote_address' => $this->getTable('log/sales_quote_address_table')),
                    array('address_id'))
            ->joinLeft(
                array('sales_quote' => $this->getTable('log/sales_quote_table')),
                'sales_quote_address.quote_id = sales_quote.entity_id ',
                array())
            ->where('sales_quote.entity_id IS NULL')
            ->order('address_id')
            ->limit(100);

		$address_ids = $readAdapter->fetchCol($select);

		if($address_ids){
			$writeAdapter->delete($this->getTable('log/sales_quote_shipping_rate_table'), array('address_id IN (?)' => $address_ids));
			$writeAdapter->delete($this->getTable('log/sales_quote_address_table'), array('address_id IN (?)' => $address_ids));
		}else break;

        }

       //clean broken  sales_quote_item_table
       while(true){

            $select = $readAdapter->select()
             ->from(
                    array('sales_quote_item' => $this->getTable('log/sales_quote_item_table')),
                    array('item_id'))
            ->joinLeft(
                array('sales_quote' => $this->getTable('log/sales_quote_table')),
                'sales_quote_item.quote_id = sales_quote.entity_id ',
                array())
            ->where('sales_quote.entity_id IS NULL')
            ->order('item_id')
            ->limit(100);

		$item_ids = $readAdapter->fetchCol($select);

		if($item_ids){
			$writeAdapter->delete($this->getTable('log/sales_quote_item_table'), array('item_id IN (?)' => $item_ids));
		}else break;

      }


       //clean broken  sales_quote_item_table
       while(true){

            $select = $readAdapter->select()
             ->from(
                    array('sales_quote_item_option' => $this->getTable('log/sales_quote_item_option_table')),
                    array('item_id'))
            ->joinLeft(
                array('sales_quote_item' => $this->getTable('log/sales_quote_item_table')),
                'sales_quote_item.item_id = sales_quote_item_option.item_id ',
                array())
            ->where('sales_quote_item.item_id IS NULL')
            ->order('item_id')
            ->limit(100);

		$item_ids = $readAdapter->fetchCol($select);

		if($item_ids){
			$writeAdapter->delete($this->getTable('log/sales_quote_item_option_table'), array('item_id IN (?)' => $item_ids));
		}else break;

      }


       //clean broken  sales_quote_payment_table
       while(true){

            $select = $readAdapter->select()
             ->from(
                    array('sales_quote_payment' => $this->getTable('log/sales_quote_payment_table')),
                    array('payment_id'))
            ->joinLeft(
                array('sales_quote' => $this->getTable('log/sales_quote_table')),
                'sales_quote_payment.quote_id = sales_quote.entity_id ',
                array())
            ->where('sales_quote.entity_id IS NULL')
            ->order('payment_id')
            ->limit(100);

		$payment_ids = $readAdapter->fetchCol($select);

		if($payment_ids){
			$writeAdapter->delete($this->getTable('log/sales_quote_payment_table'), array('payment_id IN (?)' => $payment_ids));
		}else break;

      }


        return $this;
    }

    /**
     * Clean customer table
     *
     * @param int $time
     * @return Mage_Log_Model_Resource_Log
     */
    protected function _cleanCustomers($time)
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();

        $timeLimit = $this->formatDate(Mage::getModel('core/date')->gmtTimestamp() - $time);

        // retrieve last active customer log id
        $lastLogId = $readAdapter->fetchOne(
            $readAdapter->select()
                ->from($this->getTable('log/customer'), 'log_id')
                ->where('login_at < ?', $timeLimit)
                ->order('log_id DESC')
                ->limit(1)
        );

        if (!$lastLogId) {
            return $this;
        }

        // Order by desc log_id before grouping (within-group aggregates query pattern)
        $select = $readAdapter->select()
            ->from(
                array('log_customer_main' => $this->getTable('log/customer')),
                array('log_id'))
            ->joinLeft(
                array('log_customer' => $this->getTable('log/customer')),
                'log_customer_main.customer_id = log_customer.customer_id '
                    . 'AND log_customer_main.log_id < log_customer.log_id',
                array())
            ->where('log_customer.customer_id IS NULL')
            ->where('log_customer_main.log_id < ?', $lastLogId + 1);

        $needLogIds = array();
        $query = $readAdapter->query($select);
        while ($row = $query->fetch()) {
            $needLogIds[$row['log_id']] = 1;
        }

        $customerLogId = 0;
        while (true) {
            $visitorIds = array();
            $select = $readAdapter->select()
                ->from(
                    $this->getTable('log/customer'),
                    array('log_id', 'visitor_id'))
                ->where('log_id > ?', $customerLogId)
                ->where('log_id < ?', $lastLogId + 1)
                ->order('log_id')
                ->limit(100);

            $query = $readAdapter->query($select);
            $count = 0;
            while ($row = $query->fetch()) {
                $count++;
                $customerLogId = $row['log_id'];
                if (!isset($needLogIds[$row['log_id']])) {
                    $visitorIds[] = $row['visitor_id'];
                }
            }

            if (!$count) {
                break;
            }

            if ($visitorIds) {
                $condition = array('visitor_id IN (?)' => $visitorIds);

                // remove visitors from log/quote
                $writeAdapter->delete($this->getTable('log/quote_table'), $condition);

                // remove visitors from log/url
                $writeAdapter->delete($this->getTable('log/url_table'), $condition);

                // remove visitors from log/visitor_info
                $writeAdapter->delete($this->getTable('log/visitor_info'), $condition);

                // remove visitors from log/visitor
                $writeAdapter->delete($this->getTable('log/visitor'), $condition);

                // remove customers from log/customer
                $writeAdapter->delete($this->getTable('log/customer'), $condition);
            }

            if ($customerLogId == $lastLogId) {
                break;
            }
        }

        return $this;
    }

    /**
     * Clean url table
     *
     * @return Mage_Log_Model_Resource_Log
     */
    protected function _cleanUrls()
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();

        while (true) {
            $select = $readAdapter->select()
                ->from(
                    array('url_info_table' => $this->getTable('log/url_info_table')),
                    array('url_id'))
                ->joinLeft(
                    array('url_table' => $this->getTable('log/url_table')),
                    'url_info_table.url_id = url_table.url_id',
                    array())
                ->where('url_table.url_id IS NULL')
                ->limit(100);

            $urlIds = $readAdapter->fetchCol($select);

            if (!$urlIds) {
                break;
            }

            $writeAdapter->delete(
                $this->getTable('log/url_info_table'),
                array('url_id IN (?)' => $urlIds)
            );
        }

        return $this;
    }
}
