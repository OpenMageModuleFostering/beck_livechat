<?php

class Beck_LiveChat_Model_Api extends Mage_Api_Model_Resource_Abstract
{
	public function getsessions($operatorName)
	{
		$result = array();
		$operator = Mage::getModel('livechat/operator');
		$customerOnline = Mage::getResourceSingleton('log/visitor_collection')
            				->useOnlineFilter();
		$list = array();
		foreach ($customerOnline as $customer)
		{
			$list[] = $customer->getSession_id();
		}
		if ($operator->Exist($operatorName))
		{
			$sessions = $operator->getSessionsAvailable();
			
			foreach ($sessions as $session)
			{
				if (!$session->Expired($list))
				{
					if ($session->getDispatched() == 0 || $session->getDispatched() == $operator->getId())
					{
						$session->setDispatched($operator->getId())
							->save();
						$result[] = $session->toArray();
					}
				}
				else
				{
					$session->Close();
				}
			}
		}
		return ($result);
	}
	
	public function getmessages($session_id)
	{
		$result = array();
		$session = Mage::getModel('livechat/session');
		if ($session->Exist($session_id))
		{
			$messages = $session->getMessages();
			foreach ($messages as $message)
			{
				$result[] = $message->toArray();
			}
		}
		return ($result);
	}
	
	public function sendmessage($session_id, $autor, $message)
	{
		$session = Mage::getModel('livechat/session');
		if ($session->Exist($session_id))
		{
			$session->saveMessage($autor, $message);
			//return true;
		}
		//return false;
	}
	
	public function operatorconnect($name)
	{
		$operator = Mage::getModel('livechat/operator');
		if (!$operator->Exist($name))
		{
			$operator->setName($name)
			->save();
		}
		$operator->Connected();
	}
	
	public function operatordisconnect($name)
	{
		$operator = Mage::getModel('livechat/operator');
		if ($operator->Exist($name))
		{
			$operator->Disconnected();
		}
	}
	
	public function refreshmessages($current_messages)
	{
		$data = $this->FormatCurrentData($current_messages);
		$session = Mage::getModel('livechat/session');
		$newMessages = array();
		foreach ($data['sessions'] as $sessionData)
		{
			$session->load($sessionData['id']);
			$newMessages = array_merge($session->getNewMessages($sessionData['messages']), $newMessages);
		}
		return ($newMessages);
	}
	
	public function refreshsessions($operatorName, $current_sessions)
	{
		$current_sessions = explode('-', $current_sessions);
		$sessions = $this->getsessions($operatorName);
		
		$result = array();
		foreach ($sessions as $session)
		{
			if (!in_array($session['id'], $current_sessions, false))
			{
				$result[] = $session;
			}
		}
		return ($result);
	}
	
	private function FormatCurrentData($current_Data)
	{
		$data = array();
		$sessions = explode('#', $current_Data);
		foreach ($sessions as $session)
		{
			eregi("^([0-9]+)-", $session, $regs);
			$session_id = $regs[0];
			$session = str_ireplace($session_id, '', $session);
			$session_id = str_ireplace('-', '', $session_id);
			$messages = explode(',', $session);
			$data['sessions'][$session_id]['id'] = $session_id;
			foreach ($messages as $message)
			{
				$data['sessions'][$session_id]['messages'][$message] = $message;
			}
		}
		return ($data);
	}
}