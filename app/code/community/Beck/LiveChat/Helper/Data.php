<?php

class Beck_LiveChat_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isSessionExpired(Beck_LiveChat_Model_Session $session)
	{
		$result = true;		
		$visitors = Mage::getModel('log/visitor')
					->getCollection()
					->useOnlineFilter();
		if ($session->getId() > 0)
		{
			foreach ($visitors as $visitor)
			{
				if ($visitor->getSession_id() == $session->getCustomer_session_id())
				{
					$result = false;
				}
			}
		}
		return ($result);
	}
}