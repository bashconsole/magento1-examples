<?php
/* @var $installer Mage_Customer_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$this->addAttribute('customer', 'is_guest', array(
    'group'     =>  'Account Information',
    'input'     =>  'select',
    'type'      =>  'int',
    'label'     =>  'Is Guest',
    'source'   =>  'eav/entity_attribute_source_boolean',
    'visible'   =>  0,
    'required'  =>  0,
    'visible_on_front'=>0,
    'global'    =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'user_defined'=>0,
    'is_user_defined'=>false,
    'default'=>0,
));

$this->addAttribute('customer', 'is_old', array(
    'group'     =>  'Account Information',
    'input'     =>  'select',
    'type'      =>  'int',
    'label'     =>  'Is Old',
    'source'   =>  'eav/entity_attribute_source_boolean',
    'visible'   =>  0,
    'required'  =>  0,
    'visible_on_front'=>0,
    'global'    =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'user_defined'=>0,
    'is_user_defined'=>false,
    'default'=>0,
));

$installer->endSetup();