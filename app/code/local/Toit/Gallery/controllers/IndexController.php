<?php
/**
 * Toit Gallery
 *
 * Provides an image gallery extension for Magento
 *
 * @category	ThinkOverIT
 * @package		Gallery
 * @author		ThinkOverIT
 * @copyright	Copyright (c) 20012, ThinkOverIT.com	
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/us/
 * @link		http://www.ThinkOverIT.com
 * @since		Version 0.1.0
 *
 * Please feel free to modify or distribute this as you like,
 * so long as it's for noncommercial purposes and any
 * copies or modifications keep this comment block intact
 *
 * If you would like to use this for commercial purposes,
 * please contact me at info@thinkoverit.com
 *
 * For any feedback, comments, or questions, please post
 * it on our blog at http://www.thinkoverit.com/plugins/magento/gallery/
 *
 */
 class Toit_Gallery_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->_redirect('*/*/view');
    }
    public function photoAction()
    {
		$gallery_id = $this->getRequest()->getParam('id');
		if(!$gallery_id) $gallery_id = 0;

		$model = Mage::getModel('gallery/toitgallery')->load($gallery_id);
		Mage::register('current_gallery', $model);

		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->loadLayout(); 
			$block = $this->getLayout()->createBlock('gallery/gallery','Gallery');
			$block->setTemplate('gallery/photo.phtml');
			print $block->toHtml();	;
		}else{
			$this->loadLayout(); 
			$this->renderLayout();
		}
	}

	public function viewAction() {
		$gallery_id = $this->getRequest()->getParam('id');
		if(!$gallery_id) $gallery_id = 0;

		$model = Mage::getModel('gallery/toitgallery')->load($gallery_id);
		Mage::register('current_gallery', $model);

		$this->loadLayout(); 
		if($head = $this->getLayout()->getBlock('head')){
			if($model->getGalleryId()){
				$head->setTitle($model->getTitle());
				$head->setKeywords($model->getMetaKeywords());
				$head->setDescription($model->getMetaDescription());
			}else{
				$head->setTitle(Mage::helper('gallery')->__('Gallery'));
				$head->setKeywords(Mage::helper('gallery')->__('Gallery'));
				$head->setDescription(Mage::helper('gallery')->__('Gallery'));
			}
		}
		$this->renderLayout();
	}
}