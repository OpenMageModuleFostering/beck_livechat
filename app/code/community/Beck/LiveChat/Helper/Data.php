<?php

class Beck_LiveChat_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isSessionExpired(Beck_LiveChat_Model_Session $session, array $customerOnlines)
	{
		//Zend_Debug::dump($customerOnlines, 'customerOnlines');
		//Zend_Debug::dump($session->getCustomer_session_id(),'session_id');
		$result = true;
		if ($session->getId() > 0)
		{
			if (is_array($customerOnlines))
			{
	            foreach ($customerOnlines as $customerSessionID)
	            {
	            	if ($customerSessionID == $session->getCustomer_session_id())
	            	{
	            		$result = false;
	            	}
	            }
			}
			else
			{
				$result = false;
			}
		}
		//Zend_Debug::dump($result, 'isSessionExpired');
		return ($result);
	}
}