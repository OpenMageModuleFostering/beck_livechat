<?php

class Beck_LiveChat_TestController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		/*$visitors = Mage::getModel('log/visitor')->getCollection()
					->useOnlineFilter();
		foreach ($visitors as $visitor)
		{
			echo 'visiteur :' . $visitor->getSession_id().'<br>';
		}*/
		//Zend_Debug::dump($res);
	}
}