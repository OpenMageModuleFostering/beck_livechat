<?php

class Beck_LiveChat_Block_Notification_Toolbar extends Mage_Adminhtml_Block_Template
{
	protected function _construct()
    {
	}
	
	protected function _getHelper()
    {
        return Mage::helper('livechat');
    }
	
	public function isShow()
    {

        return true;
    }
}