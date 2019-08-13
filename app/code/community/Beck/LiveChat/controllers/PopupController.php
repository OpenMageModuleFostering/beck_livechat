<?php

class Beck_LiveChat_PopupController extends Mage_Core_Controller_Front_Action
{

	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
}