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
 
class Toit_Gallery_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getCurrentGalleryType()
	{
		if(Mage::registry('gallery_type')){
			if(!Mage::registry('gallery_type') || Mage::registry('gallery_type') == "gallery")
				$type = "gallery";
			else $type = "album";
		}else if ( Mage::registry('gallery_data') ) {
			$data = Mage::registry('gallery_data')->getData();
			$type = $data['type'];
		}	
		return $type;
	}
	public function getAlbumsArray(){
	
		$ac = array();
		$albums = Mage::getModel('gallery/toitgallery')->getAllAlbums();
		$ac[0] = array('value'=>1, 'label'=>Mage::helper('gallery')->__('Root'));
		foreach($albums as $key=>$album){
			$level = $album->getData('level');
			$tabs="";
			for($i=0;$i<$level;$i++)
				$tabs .= "-";
			$ac[] = array('value'=>$album->getGalleryId(), 'label'=>$tabs.$album->getTitle());
		}
		return $ac;
	}
	public function getAlbumsArrayGrid(){
	
		$ac = array();
		$albums = Mage::getModel('gallery/toitgallery')->getAllAlbums();
		$ac[1] = Mage::helper('gallery')->__('Root');
		foreach($albums as $key=>$album){
			$level = $album->getData('level');
			$tabs="";
			for($i=0;$i<$level;$i++)
				$tabs .= "-";
			$ac[$album->getGalleryId()] = $tabs.$album->getTitle();
		}
		return $ac;
	}
}