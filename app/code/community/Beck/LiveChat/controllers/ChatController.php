<?php

class Beck_LiveChat_ChatController extends Mage_Core_Controller_Front_Action
{
	public function sendmessageAction()
	{
		$store_id = $this->getRequest()->getParam('store_id', 0);
		$session_id = Mage::getSingleton('checkout/session')->getSessionId();
		//$session_id = Mage::getSingleton('checkout/session')->getEncryptedSessionId();
		$message = trim($this->getRequest()->getParam('message', ''));
		//$message = trim($message);
		if ($message != '')
		{
			if (Mage::Helper('customer/data')->isLoggedIn())
			{
				$customer_name = Mage::getSingleton('customer/session')->getCustomer()->getName();
			}
			else
			{
				$customer_name = Mage::getStoreConfig('livechatconfiguration/display/defaultcustomername', $store_id);
			}

			$session = Mage::getModel('livechat/session');
			$new_session = false;
			if (!$session->Exist($session_id))
			{
				$session->setCustomer_name($customer_name)
				->setCustomer_session_id($session_id)
				->setDate_started(now())
				->setStore_id($store_id)
				->save();
				
				$new_session = true;
			}
			$CustomerUrl = Mage::getSingleton('checkout/session')->getData('livechatCustomerUrl');
			if ($session->getClose() == '1')
			{
				$allowreopen = Mage::getStoreConfig('livechatevents/oncustomersendmessage/allowcustomerreopen', 0) == '1' ? true : false;
				if ($allowreopen == true)
				{
					$session->Open();
					$mess = $session->saveMessage($customer_name, $message, '1');
				}
			}
			else
			{
				$mess = $session->saveMessage($customer_name, $message, '1');
			}
			echo $mess->RenderLine();
			if ($new_session)
			{
				$mess = $session->saveMessage(Mage::getStoreConfig('livechatconfiguration/display/systemname', $store_id), Mage::getStoreConfig('livechatconfiguration/display/systemwaitmessage', $store_id));
				echo $mess->RenderLine();
			}
		}
	}
	
	public function getMessageSessionAction()
	{
		$session_id = Mage::getSingleton('checkout/session')->getEncryptedSessionId();
		$session = Mage::getModel('livechat/session');
		if ($session->Exist($session_id))
		{
			$messages = $session->getMessages();
			foreach ($messages as $message)
			{
				echo $message->RenderLine();
			}
		}
	}
}