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
 
class Toit_Gallery_Block_Adminhtml_Gallery_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
									  'id' => 'edit_form',
									  'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
									  'method' => 'post',
									  'enctype' => 'multipart/form-data'
								   )
		);
		$form->setUseContainer(true);
		$this->setForm($form);
		
		if(Mage::helper('gallery')->getCurrentGalleryType() == "gallery")
			$fieldset = $form->addFieldset('gallery_form', array('legend'=>Mage::helper('gallery')->__('Photo information')));
		else 
			$fieldset = $form->addFieldset('gallery_form', array('legend'=>Mage::helper('gallery')->__('Album information')));
		
		$fieldset->addField('type', 'hidden', array(
		  'name'      => 'type',
		));
		$fieldset->addField('title', 'text', array(
		  'label'     => Mage::helper('gallery')->__('Title'),
		  'class'     => 'required-entry',
		  'required'  => true,
		  'name'      => 'title',
		));

		$ac = Mage::helper('gallery')->getAlbumsArray();
		$fieldset->addField('parent_id', 'select', array(
				'label' => Mage::helper('gallery')->__('Album'),
				'required' => true,
				'name'=>'parent_id',
				'values'=>$ac
			));
		
		$fieldset->addField('filename', 'file', array(
		  'label'     => Mage::helper('gallery')->__('File'),
		  'required'  => false,
		  'name'      => 'filename',
		));
		$gallery_id = $this->getRequest()->getParam('id');
		if($gallery_id)
		{
			$model = Mage::getModel('gallery/toitgallery')->load($gallery_id);
			$filename = $model->getFilename();
			if($filename){
				$thumbnail_url = $model->getThumbnailUrl(100,100);
				$fieldset->addField('img', 'note', array(
					'label'	=> 'Image',
					'required' => false,
					'text' => '<img src="'.str_replace("\\","/",$thumbnail_url).'"/>'
				));
			}
		}
		
		$fieldset->addField('alt', 'text', array(
		  'label'     => Mage::helper('gallery')->__('ALT Text'),
		  'class'     => 'alt',
		  'required'  => true,
		  'name'      => 'alt',
		));

		$fieldset->addField('description', 'editor', array(
		  'name'      => 'description',
		  'label'     => Mage::helper('gallery')->__('Description'),
		  'title'     => Mage::helper('gallery')->__('Description'),
		  'style'     => 'width:300px; height:150px;',
		  'wysiwyg'   => false,
		  'required'  => true,
		));
		$fieldset->addField('status', 'select', array(
		  'label'     => Mage::helper('gallery')->__('Status'),
		  'name'      => 'status',
		  'values'    => array(
			  array(
				  'value'     => 1,
				  'label'     => Mage::helper('gallery')->__('Enabled'),
			  ),

			  array(
				  'value'     => 2,
				  'label'     => Mage::helper('gallery')->__('Disabled'),
			  ),
		  ),
		));

		
		$fieldset->addField('meta_description', 'editor', array(
		  'name'      => 'meta_description',
		  'label'     => Mage::helper('gallery')->__('Meta Description'),
		  'title'     => Mage::helper('gallery')->__('Meta Description'),
		  'style'     => 'width:300px; height:150px;',
		  'wysiwyg'   => false,
		  'required'  => false,
		));
		$fieldset->addField('meta_keywords', 'editor', array(
		  'name'      => 'meta_keywords',
		  'label'     => Mage::helper('gallery')->__('Meta Keywords'),
		  'title'     => Mage::helper('gallery')->__('Meta Keywords'),
		  'style'     => 'width:300px; height:150px;',
		  'wysiwyg'   => false,
		  'required'  => false,
		));
		
		$fieldset->addField('priority', 'text', array(
		  'label'     => Mage::helper('gallery')->__('Sort Order'),
		  'class'     => 'priority',
		  'required'  => true,
		  'name'      => 'priority',
		));
		
		$type = Mage::helper('gallery')->getCurrentGalleryType();
		if ( Mage::getSingleton('adminhtml/session')->getGalleryData() )
		{
			$data = Mage::getSingleton('adminhtml/session')->getGalleryData();
			$data['type'] = $type;
			$form->setValues($data);
			Mage::getSingleton('adminhtml/session')->setGalleryData(null);
		} elseif ( Mage::registry('gallery_data') ) {
			$data = Mage::registry('gallery_data')->getData();
			$data['type'] = $type;
			$form->setValues($data);
		}else{
			$form->setValues(array('type' => $type));
		}
		return parent::_prepareForm();
	}
}