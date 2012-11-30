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
 
class Toit_Gallery_Block_Adminhtml_Gallery_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('galleryGrid');
		$this->setDefaultSort('main_table.gallery_id');
		$this->setDefaultDir('asc');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$tableName = Mage::getSingleton('core/resource')->getTableName('toitgallery');
			

		$collection = Mage::getModel('gallery/toitgallery')->getCollection();
		
		$collection->join(array('gallery_alias'=> $tableName), 
						'main_table.parent_id=gallery_alias.gallery_id', 
						array('album_name' =>'gallery_alias.title'));

		$collection->addFieldToFilter('main_table.type', Mage::helper('gallery')->getCurrentGalleryType());
		$collection->setOrder('main_table.priority','asc');
		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('gallery_id', array(
		  'header'    => Mage::helper('gallery')->__('ID'),
		  'align'     =>'right',
		  'width'     => '50px',
		  'index'     => 'gallery_id',
		));

		$this->addColumn('title', array(
		  'header'    => Mage::helper('gallery')->__('Title'),
		  'align'     =>'left',
		  'index'     => 'title',
		  'filter_index' => 'main_table.title', 
		));
		$this->addColumn('priority', array(
		  'header'    => Mage::helper('gallery')->__('Sort Order'),
		  'align'     =>'left',
		  'width'     => '150',
		  'index'     => 'priority',
		  'filter_index' => 'main_table.priority', 

		));
		$ac = Mage::helper('gallery')->getAlbumsArrayGrid();
		$this->addColumn('album_name', array(
		  'header'    => Mage::helper('gallery')->__('Album'),
		  'align'     =>'left',
		  'index'     => 'album_name',
		  'type'      => 'options',
		  'options'   => $ac,
		  'filter_index' => 'gallery_alias.gallery_id', 
		));

		$this->addColumn('description', array(
		  'header'    => Mage::helper('gallery')->__('Description'),
		  'align'     =>'left',
		  'width'     => '150',
		  'index'     => 'description',
		  'filter_index' => 'main_table.description', 
		));
		$this->addColumn('alt', array(
		  'header'    => Mage::helper('gallery')->__('Alt Text'),
		  'align'     =>'left',
		  'width'     => '150',
		  'index'     => 'alt',
		  'filter_index' => 'main_table.alt',
		));
		$this->addColumn('status', array(
		  'header'    => Mage::helper('gallery')->__('Status'),
		  'align'     => 'left',
		  'width'     => '80px',
		  'index'     => 'status',
		  'filter_index' => 'main_table.status',
		  'type'      => 'options',
		  'options'   => array(
			  1 => 'Enabled',
			  2 => 'Disabled',
		  ),
		));

		$this->addColumn('action',
			array(
				'header'    =>  Mage::helper('gallery')->__('Action'),
				'width'     => '100',
				'type'      => 'action',
				'getter'    => 'getId',
				'actions'   => array(
					array(
						'caption'   => Mage::helper('gallery')->__('Edit'),
						'url'       => array('base'=> '*/*/edit'),
						'field'     => 'id'
					),
					array(
						'caption'   => Mage::helper('gallery')->__('Delete'),
						'url'       => array('base'=> '*/*/delete'),
						'field'     => 'id',
						'confirm'  => Mage::helper('gallery')->__('Are you sure?')
					)
				),
				'filter'    => false,
				'sortable'  => false,
				'index'     => 'stores',
				'is_system' => true,
		));

		return parent::_prepareColumns();
	}

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('main_table.gallery_id');
        $this->getMassactionBlock()->setFormFieldName('gallery');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('gallery')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('gallery')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('gallery/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('gallery')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('gallery')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}