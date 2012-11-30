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
 
class Toit_Gallery_Block_Adminhtml_Gallery_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                
        $this->_objectId = 'id';
        $this->_blockGroup = 'gallery';
        $this->_controller = 'adminhtml_gallery';
        
		if(Mage::helper('gallery')->getCurrentGalleryType() == "gallery")
		{
			$this->_updateButton('save', 'label', Mage::helper('gallery')->__('Save Photo'));
			$this->_updateButton('delete', 'label', Mage::helper('gallery')->__('Delete Photo'));
		}else{
			$this->_updateButton('save', 'label', Mage::helper('gallery')->__('Save Album'));
			$this->_updateButton('delete', 'label', Mage::helper('gallery')->__('Delete Album'));
		}
	}
    public function getHeaderText()
    {
		if(Mage::helper('gallery')->getCurrentGalleryType() == "gallery")
		{
			if( Mage::registry('gallery_data') && Mage::registry('gallery_data')->getId() ) {
				return Mage::helper('gallery')->__("Edit Photo '%s'", $this->htmlEscape(Mage::registry('gallery_data')->getTitle()));
			} else {
				return Mage::helper('gallery')->__('Add Photo');
			}
		}else{
			if( Mage::registry('gallery_data') && Mage::registry('gallery_data')->getId() ) {
				return Mage::helper('gallery')->__("Edit Album '%s'", $this->htmlEscape(Mage::registry('gallery_data')->getTitle()));
			} else {
				return Mage::helper('gallery')->__('Add Album');
			}
		}
    }
}