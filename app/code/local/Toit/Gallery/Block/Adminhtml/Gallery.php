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
 
class Toit_Gallery_Block_Adminhtml_Gallery extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_gallery';
		$this->_blockGroup = 'gallery';

		if(Mage::helper('gallery')->getCurrentGalleryType() == "gallery")
		{
			$this->_headerText = Mage::helper('gallery')->__('Gallery Manager');
			$this->_addButtonLabel = Mage::helper('gallery')->__('Add Gallery');
		}else{
			$this->_headerText = Mage::helper('gallery')->__('Album Manager');
			$this->_addButtonLabel = Mage::helper('gallery')->__('Add Album');
		}
		parent::__construct();
	}
    public function getCreateUrl()
    {
		if(Mage::helper('gallery')->getCurrentGalleryType() == "gallery")
			return $this->getUrl('*/*/new/');
        else
			return $this->getUrl('*/*/newalbum/');
    }
}