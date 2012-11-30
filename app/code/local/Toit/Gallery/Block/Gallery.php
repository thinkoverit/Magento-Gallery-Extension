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
 
class Toit_Gallery_Block_Gallery extends Mage_Core_Block_Template
{
    public function getAlbums($album_id=0)
    {
		$model = $this->getCurrentGallery();

		if($album_id)
			return $model->getAlbums($album_id);
		else
			return $model->getAlbums($model->getGalleryId());
    }
    public function getBreadbcrumbTree()
    {
		$model = $this->getCurrentGallery();
		return $model->getBreadbcrumbTree($model);
    }

    public function getGalleryTitle()
    {
    	$model = $this->getCurrentGallery();
    	if($model->getTitle())
    		return $model->getTitle();    	
    	else
    		return "Gallery";
    }
    public function getCurrentGallery()
    {
    	if(!Mage::registry('current_gallery'))    	
    		Mage::register('current_gallery', Mage::getModel('gallery/toitgallery'));
    	
    	return Mage::registry('current_gallery');
    }
    public function getGalleryUrl($model)
    {
    	if($model->getType() == 'album')
    		return $this->getUrl('gallery/index/view/').'id/'.$model->getGalleryId();    	
    	else
    		return $this->getUrl('gallery/index/photo/').'id/'.$model->getGalleryId();    	
    		//return str_replace("\\","/",$model->getThumbnailUrl(300,300));    	
    }
    public function getFancyClass($model)
    {
    	if($model->getType() == 'album')
    		return ' album ';
    	else
    		return 'gallery_fancybox';    	
    }
}