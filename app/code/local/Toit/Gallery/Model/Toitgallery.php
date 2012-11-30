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
 
class Toit_Gallery_Model_Toitgallery extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('gallery/toitgallery');
    }
	
	public function getAllAlbums($parent_id=1, $level = 0)
	{
		$items = $this->getCollection()
				->addFieldToFilter('type','album')
				->addFieldToFilter('status','1')
				->addFieldToFilter('parent_id',$parent_id)
				->setOrder('priority','asc'); 
		if (empty($items)) {
			return false;
		}
		$childs=array();

		foreach($items as $item){
			$childs[] = $item;

			$item->setLevel($level);
 
			if($subchilds = $this->getAllAlbums($item->getGalleryId(), ($level+1)))
				array_splice($childs, count($childs), 0, $subchilds);
		}
		return $childs;
	}
	
	public function getAlbums($parent_id=1)
	{
		if(empty($parent_id)) $parent_id=1;
		$items = $this->getCollection()
				->addFieldToFilter('status','1')
				->addFieldToFilter('parent_id',$parent_id);
		$items->setOrder('priority','asc'); 	
		return $items;
	}
    public function getFirstChild()
	{
		return  $this->getCollection()
				->addFieldToFilter('status','1')
				->addFieldToFilter('parent_id',$this->GalleryId()) 
				->setOrder('priority','asc')
				->setPageSize(1)
				->getFirstItem();
	}
    public function getNextItem()
	{
		return  $this->getCollection()
				->addFieldToFilter('status','1')
				->addFieldToFilter('parent_id',$this->getParentId()) 
				->addFieldToFilter('priority', array('gteq'=>$this->getPriority()))
				->addFieldToFilter('gallery_id', array('neq'=>$this->getGalleryId()))
				->setOrder('priority','asc')
				->setPageSize(1)
				->getFirstItem();
	}
    public function getPreviousItem()
	{
		return  $this->getCollection()
				->addFieldToFilter('status','1')
				->addFieldToFilter('parent_id',$this->getParentId()) 
				->addFieldToFilter('priority', array('lteq'=>$this->getPriority()))
				->addFieldToFilter('gallery_id', array('neq'=>$this->getGalleryId()))
				->setOrder('priority','desc')
				->setPageSize(1)
				->getFirstItem();
	}
    public function getBreadbcrumbTree($model)
    {
		$links= array();
		if(!$model->getGalleryId())
			return null;

    	if($model->getParentId()){
    		$tree = $this->getBreadbcrumbTree(Mage::getModel('gallery/toitgallery')->load($model->getParentId()));
			$links = array_merge($links, $tree);
		}else{
			return array($model);		
		}
		$links[] = $model;
		return $links;
    }
	public function getPhotoUrl()
	{
		if($this->getFilename()){
			return str_replace('\\', '/', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'gallery' .DS .$this->getFilename());
		}
		return str_replace('\\', '/', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'gallery' .DS .'default.jpg');
	}
	public function getThumbnailUrl($width, $height)
	{
		if($this->getFilename()){
			$originalfilename = $this->getFilename();
		}else{
			$originalfilename = $this->getFirstChild()->getFilename();
		}
		if(!empty($originalfilename)){
			$path = Mage::getBaseDir('media') . DS . 'gallery' . DS ;
			$thumbpath = $path . 'thumbs' . DS;
			$thumbpathurl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'gallery' . DS . 'thumbs' . DS;
			
			$filename = $width . '_' . $height . '_' . $originalfilename;
			if(!file_exists($path.$filename))
				$this->createThumbnail($path.$originalfilename, $thumbpath.$filename, $width, $height);
			return str_replace('\\', '/', $thumbpathurl.$filename);
		}
		return str_replace('\\', '/', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'gallery' .DS .'default.jpg');
	}

	public function createThumbnail($originalfile, $thumbfilename, $thumb_width, $thumb_height){
		
		
		list($imagewidth, $imageheight, $imageType) = getimagesize($originalfile);
		$imageType = image_type_to_mime_type($imageType);
		
		$start_width= $start_height=0;
		$width = $thumb_width; 
		$height = $thumb_height;
		
		$this->getCropImageSize($imagewidth, $imageheight, $width, $height, $start_width, $start_height);
		
		if($thumb_width > $width && $thumb_height > $height){
			$actualwidth = $width;
			$actualheight = $height;
		}	
		else{
			$actualwidth = $thumb_width;
			$actualheight = $thumb_height;		
		}
			
		$newImage = imagecreatetruecolor($actualwidth,$actualheight);	
		
		switch($imageType) {
			case "image/gif":
				$source=imagecreatefromgif($originalfile); 
				break;
		    case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$source=imagecreatefromjpeg($originalfile); 
				break;
		    case "image/png":
			case "image/x-png":
				$source=imagecreatefrompng($originalfile); 
				break;
	  	}
	
		imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$actualwidth,$actualheight,$width,$height);
		switch($imageType) {
			case "image/gif":
		  		imagegif($newImage,$thumbfilename); 
				break;
	      	case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
		  		imagejpeg($newImage,$thumbfilename,90); 
				break;
			case "image/png":
			case "image/x-png":
				imagepng($newImage,$thumbfilename);  
				break;
	    }
		@chmod($thumbfilename, 0777);
		imagedestroy($source); 
		imagedestroy($newImage); 
		return $thumbfilename;
	}
	public function getCropImageSize($oriWidth,$oriHeight,&$width,&$height,&$xPos,&$yPos){
		$standardHeight = $height;
        $standardWidth = $width;
        if (($oriHeight / $standardHeight  ) > ($oriWidth / $standardWidth  ))
        {
            //height rate is bigger than width              
            $height = $standardHeight * ($oriWidth / $standardWidth );
            $width = $oriWidth;
			$xPos = 0;				
			$yPos = ($oriWidth - $width) /2;
        }
        else
        {                
            $height = $oriHeight;
            $width = $standardWidth * ($oriHeight / $standardHeight);
			$xPos = ($oriWidth - $width) /2;
			$yPos = 0;			
        }
	}
}