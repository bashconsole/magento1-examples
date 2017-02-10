<?php
class Bashconsole_Test_Model_Observer
{

			public function AddGoogleMPN(Varien_Event_Observer $observer)
			{

				if ($product = $observer->getEvent()->getProduct()) {

					$google_mpn = $product->getData('google_mpn');
					if(!empty($google_mpn)){
						$title = $product->getData('name') . '. MPN: ' . $product->getData('google_mpn');
						$product->setMetaTitle($title);
					}
				}
				
				return $this;
			}


}
