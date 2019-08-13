<?php

class Beck_LiveChat_Block_Template extends Mage_Core_Block_Template
{
	public $isActive = false;
	public $refreshfrequency = 15;
	public $refreshdecay = 1;
	public $chatlabel = 'Need help ? Ask live !';
	public $imageStyle = '3';
	public $unavailablelabel = 'Sorry';
	public $titlelabel = 'LIVECHAT';
	
	protected function _construct()
	{
		parent::_construct();
		$this->isActive				= $this->getConfigData('livechatconfiguration/general/active') == '0' ? false : true;
		if ($this->isActive)
		{
			$this->refreshfrequency		= $this->getConfigData('livechatconfiguration/general/refreshfrequency');
			$this->refreshdecay			= $this->getConfigData('livechatconfiguration/general/refreshdecay');
			$this->chatlabel			= $this->getConfigData('livechatconfiguration/display/chatlabel');
			$this->imageStyle			= $this->getConfigData('livechatconfiguration/display/imagestyle');
			$this->unavailablelabel		= $this->getConfigData('livechatconfiguration/display/unavailablelabel');
			$this->titlelabel			= $this->getConfigData('livechatconfiguration/display/titlelabel');
			$session_id = Mage::getSingleton('checkout/session')->getSessionId();
			//$session_id = Mage::getSingleton('checkout/session')->getEncryptedSessionId();
			$session = Mage::getModel('livechat/session');
			if ($session->Exist($session_id))
			{
				$url = 'http://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
				if ($session->getCustomer_url() != $url && $session->getClose() == '0')
				{
					$session->setCustomer_url($url)->save();
				}
			}
			else
			{
				$sessionCreation = $this->getConfigData('livechatconfiguration/general/sessioncreation');
				if ($sessionCreation != '0')
				{
					if (Mage::Helper('customer/data')->isLoggedIn())
					{
						$customer_name = Mage::getSingleton('customer/session')->getCustomer()->getName();
					}
					else
					{
						$customer_name = Mage::getStoreConfig('livechatconfiguration/display/defaultcustomername', $this->getStoreId());
					}
					$message = trim(Mage::getStoreConfig('livechatconfiguration/display/systemautosessionmessage', $this->getStoreId()));
					if ($sessionCreation == '1')
					{
						$session->setCustomer_name($customer_name)
							->setCustomer_session_id($session_id)
							->setDate_started(now())
							->setStore_id($this->getStoreId())
							->save();
						if ($message != '')
						{
							$session->saveMessage(Mage::getStoreConfig('livechatconfiguration/display/systemname', $this->getStoreId()), $message);
						}
					}
					elseif ($sessionCreation == '2' && Mage::Helper('customer/data')->isLoggedIn())
					{
						$session->setCustomer_name($customer_name)
							->setCustomer_session_id($session_id)
							->setDate_started(now())
							->setStore_id($this->getStoreId())
							->save();
						if ($message != '')
						{
							$session->saveMessage(Mage::getStoreConfig('livechatconfiguration/display/systemname', $this->getStoreId()), $message);
						}
					}
				}
			}
		}
	}
	
	protected function getConfigData($path)
	{
		return (Mage::getStoreConfig($path, $this->getStoreId()));
	}
	
	public function ChatSessionStarted()
	{
		$session_id = Mage::getSingleton('checkout/session')->getEncryptedSessionId();
		return (Mage::getModel('livechat/session')->Exist($session_id));
	}
	
	public function returnChatImage()
	{
		$url =$this->getSkinUrl('images/livechat/livechat_icon_'.$this->imageStyle.'_offline.gif');
		if ($this->AreOperatorOnline())
		{
			$url = $this->getSkinUrl('images/livechat/livechat_icon_'.$this->imageStyle.'_online.gif');
		}
		$img = '<img src="'.$url.'" alt="'.$this->chatlabel.'" style="float:left">';
		return $img;
	}
	
	public function AreOperatorOnline()
	{
		$operatorCollection = Mage::getModel('livechat/operator')->getCollection();
		$nb  = 0;
		foreach ($operatorCollection as $operator)
		{
			if ($operator->getIs_online() == '1')
			{
				if ($operator->IsOperatorAffectedToStore($this->getStoreId()))
				{
					$nb++;
				}
			}
		}
		if ($nb > 0)
		{
			return true;
		}
		return false;
	}
	
	public function getStoreId()
	{
		return (Mage::app()->getStore(true)->getId());
	}
	
	public function getMessages()
	{
		$session_id = Mage::getSingleton('checkout/session')->getEncryptedSessionId();
		$session = Mage::getModel('livechat/session');
		if ($session->Exist($session_id))
		{
			$messages = $session->getMessages();
			return ($messages);
			//Zend_Debug::dump($messages);
		}
		return (null);
	}
}