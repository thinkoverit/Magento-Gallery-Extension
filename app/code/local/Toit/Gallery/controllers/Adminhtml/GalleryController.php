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
 
class Toit_Gallery_Adminhtml_GalleryController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('galley/grid')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Gallery Items Manager'), Mage::helper('adminhtml')->__('Gallery Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		Mage::register('gallery_type', 'gallery');
		$this->_initAction()
			->renderLayout();
	}
	public function albumAction() {
		Mage::register('gallery_type', 'album');
		$this->_initAction()
			->renderLayout();
	}
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('gallery/toitgallery')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('gallery_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('gallery/grid');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Gallery Item'), Mage::helper('adminhtml')->__('Gallery Item'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('gallery/adminhtml_gallery_edit'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('gallery')->__('Gallery Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
	public function newAction() {
		Mage::register('gallery_type', 'gallery');
		$this->_forward('edit');
	}
	public function newalbumAction() {
		Mage::register('gallery_type', 'album');
		$this->_forward('edit');
	}
	public function saveAction() {

		if ($data = $this->getRequest()->getPost()) {

			$model = Mage::getModel('gallery/toitgallery');

			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
							
					// We set media/gallery as the upload dir
					$path = Mage::getBaseDir('media') . DS . 'gallery' .DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					
				} catch (Exception $e) {
		      
		        }
		        //this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			}
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	

				$model->save();
				if($data['type'] == "gallery")
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('gallery')->__('Gallery Photo was successfully saved'));
				else
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('gallery')->__('Gallery Album was successfully saved'));
				
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				if($data['type'] == "gallery")
					$this->_redirect('*/*/');
				else 
					$this->_redirect('*/*/album');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('gallery')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			$type = "gallery";
			try {
				$model = Mage::getModel('gallery/toitgallery');
				 
				$model->setId($this->getRequest()->getParam('id'))->delete();
					
				$type = $model->type;
				$delete_id = $this->getRequest()->getParam('id');
				$tablename = $model->getResource()->getTable('gallery/toitgallery');
				$write = Mage::getSingleton('core/resource')->getConnection('core_write');
				$write->raw_query("delete from $tablename where parent_id = $delete_id");			

				if($type == "gallery"){
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Gallery Item was successfully deleted'));
					$this->_redirect('*/*/');
				}else{
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Gallery album was successfully deleted'));
					$this->_redirect('*/*/album');
				}
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				if($type == "gallery"){
					$this->_redirect('*/*/');
				}else{
					$this->_redirect('*/*/album');
				}
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $galleryIds = $this->getRequest()->getParam('gallery');
		$galleryType = "gallery";		
		if(!is_array($galleryIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s).'));
		} else {
            try {
                foreach ($galleryIds as $galleryId) {
                    $gallery = Mage::getModel('gallery/toitgallery')->load($galleryId);
                    $gallery->delete();
					$galleryType= $gallery->getType();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($galleryIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
		if($galleryType == "gallery")
			$this->_redirect('*/*/');
		else
			$this->_redirect('*/*/album');
    }
	
    public function massStatusAction()
    {
        $galleryIds = $this->getRequest()->getParam('gallery');

		$galleryType = "gallery";
		if(!is_array($galleryIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($galleryIds as $galleryId) {
                    $gallery = Mage::getSingleton('gallery/toitgallery')
                        ->load($galleryId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
						
					$galleryType= $gallery->getType();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($galleryIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }		
		if($galleryType == "gallery")
			$this->_redirect('*/*/');
		else
			$this->_redirect('*/*/album');
	}
}