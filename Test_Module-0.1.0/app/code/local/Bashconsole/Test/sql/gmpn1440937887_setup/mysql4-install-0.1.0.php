<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute('catalog_product', 'google_mpn', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Google MPN',
    'group'         => "General",
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
));

$setup->endSetup();
			 