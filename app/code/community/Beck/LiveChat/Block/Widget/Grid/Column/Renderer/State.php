<?php

class Beck_LiveChat_Block_Widget_Grid_Column_Renderer_State extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{	
	public function render(Varien_Object $row)
	{
		$html = '';
		$state = (int)$row->close;
		
		$list = array();
		
		$session = Mage::getSingleton('adminhtml/session');
		$list = $session->getData('customerOnline');
		
		$session = Mage::getModel('livechat/session')->load($row->id);
		if (is_array($list))
		{
			if (Mage::Helper('livechat')->isSessionExpired($session, $list))
			{
				$session->Expired();
				$state = 1;
			}
		}
		
		if ($state == 1)
		{
			$html .= Mage::Helper('livechat')->__('Closed');
		}
		else
		{
			$html .= Mage::Helper('livechat')->__('Open');
		}
		
		return ($html);
	}
}