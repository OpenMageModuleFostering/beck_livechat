<?php

class Beck_LiveChat_Block_Widget_Grid_Column_Renderer_State extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{	
	public function render(Varien_Object $row)
	{
		$html = '';
		$state = (int)$row->close;
		$session = Mage::getSingleton('adminhtml/session');
		$list = array();
		if ($session->getData('customerOnline') == null)
		{
			try
			{
				$customerOnline = Mage::getResourceSingleton('log/visitor_collection')
	            				->useOnlineFilter();
				foreach ($customerOnline as $customer)
				{
					$list[] = $customer->getSession_id();
				}
				$session->setData('customerOnline', $list);
			}
			catch (Exception $e)
			{
				$list = $session->getData('customerOnline');
			}
		}
		else
		{
			$list = $session->getData('customerOnline');
		}
		$session = Mage::getModel('livechat/session')->load($row->id);
		if ($session->Expired($list))
		{
			$session->Close();
			$state = 1;
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