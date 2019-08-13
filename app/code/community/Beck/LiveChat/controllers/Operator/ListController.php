<?php

class Beck_LiveChat_Operator_ListController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('livechat/operator_list')->toHtml()
        );
    }
	
	public function editAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function saveAction()
	{
		$post = $this->getRequest()->getPost();
		//Zend_Debug::dump($post);
		try
		{
			if (empty($post))
			{
				Mage::throwException($this->__('Invalid form data.'));
			}
			
			if (is_array($post))
			{
				//Zend_Debug::dump($post);
				$store = '';
				if (isset($post['stores']) && is_array($post['stores']))
				{
					foreach ($post['stores'] as $storeId)
					{
						if ($store == '')
						{
							$store .= $storeId;
						}
						else
						{
							$store .= '-'.$storeId;
						}
						
					}
				}
				$operator = Mage::getModel('livechat/operator')->load($post['id']);
				
				$operator->setStore_allowed($store)
					->save();
				
				if ($operator->getId() != null)
				{
					Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Operator %s has been successfully modified.', $operator->getName()));
				}
			}
		}
		catch (Exception $e)
		{
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		if (!isset($operator))
		{
			$operator = Mage::getModel('livechat/operator')->load($post['id']);
		}
		$this->_redirect('livechat/operator_list/edit/id/'.$operator->getId());
	}
}