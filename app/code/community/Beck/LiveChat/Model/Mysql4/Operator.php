<?php

class Beck_LiveChat_Model_Mysql4_Operator extends Mage_Core_Model_Mysql4_Abstract 
{
	protected function _construct()
	{
		$this->_init('livechat/livechat_operators', 'id');
	}
	
	public function save(Mage_Core_Model_Abstract $object)
	{
		$message = false;
		if ($object->getId() == null)
		{
			$message = true;
		}
		parent::save($object);
		if ($message)
		{
			$desc = Mage::Helper('livechat')->__('The created operator %s must be affected to a store, whithout that he will never be considered online.', $object->getName());
			$url =Mage::getModel('adminhtml/url')->getUrl('livechat/operator_list/edit', array('id' =>$object->getId()));
			$feedData = array(
                    'severity'      => Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR,
                    'date_added'    => date('Y-m-d H:i:s'),
                    'title'         => Mage::Helper('livechat')->__('New operator must be affected to a store'),
                    'description'   => $desc,
                    'url'           => $url,
                );
			$message = Mage::getModel('adminnotification/inbox')->setData($feedData);
			$message->save();
			Mage::getSingleton('adminhtml/session')->addWarning($desc . '<br /><a href="'.$url.'">'.Mage::Helper('livechat')->__('Configure').'</a>');
		}
		return $this;
	}
}