<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$cmsPageData = array(
    'title' => 'Guest Password Reset',
    'root_template' => 'one_column',
    'meta_keywords' => 'meta,keywords',
    'meta_description' => 'Guest Password Reset',
    'identifier' => Toppik_Customer_Helper_Data::GUEST_CUSTOMER_PASSWORD_CHANGE_URL,
    'content_heading' => 'Guest Password Reset',
    'stores' => array(0),
    'content' => "Your new password has been sent to email"
);

$page = Mage::getModel('cms/page')->load(Toppik_Customer_Helper_Data::GUEST_CUSTOMER_PASSWORD_CHANGE_URL);
if(! $page->getId()) {
    Mage::getModel('cms/page')->setData($cmsPageData)->save();
}

$installer->endSetup();