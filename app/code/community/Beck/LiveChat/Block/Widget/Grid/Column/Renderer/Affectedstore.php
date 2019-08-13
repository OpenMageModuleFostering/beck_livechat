<?php

class Beck_LiveChat_Block_Widget_Grid_Column_Renderer_Affectedstore extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{	
	public function render(Varien_Object $row)
	{
		$html = '';
		
		$stores = $this->_getStores(explode('-', $row->getStore_allowed()));
		if (count($stores) <= 0)
		{
			$html = $this->__('No store affected');
		}
		else
		{
			$first = true;
			foreach ($stores as $group)
			{
				if ($first)
				{
					$html .= '<i>'.$group['label'].'</i>';
					$first = false;
				}
				else
				{
					$html .= '<br /><i>'.$group['label'].'</i>';
				}
				foreach ($group['value'] as $store)
				{
					$html .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;'. $store['label'];
				}
			}
		}
		return $html;
	}
	
	protected function _getStores(array $affected_store)
	{
		$values = array();
		$websiteCollection = Mage::getModel('core/website')->getResourceCollection();
		foreach ($websiteCollection as $website)
		{
			$groupCollection = $website->getGroupCollection();
			foreach ($groupCollection as $group)
			{
				$storeCollection = $group->getStoreCollection();
				$values[$group->getId()]['value'] = array();
				$values[$group->getId()]['label'] = $group->getName();
				foreach ($storeCollection as $store)
				{
					$index = count($values[$group->getId()]['value']);
					if (in_array((string)$store->getId(), $affected_store))
					{
						$values[$group->getId()]['value'][$index]['value'] = $store->getId();
						$values[$group->getId()]['value'][$index]['label'] = $store->getName();
						$values[$group->getId()]['value'][$index]['title'] = $store->getName();
					}
				}
				if (count($values[$group->getId()]['value']) <= 0)
				{
					unset($values[$group->getId()]);
				}
			}
		}
		//Zend_Debug::dump($this->_values);
		return $values;
	}
}