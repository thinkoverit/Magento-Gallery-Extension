<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('toitgallery')};
CREATE TABLE {$this->getTable('toitgallery')} (
  `gallery_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `alt` varchar(255) NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  `parent_id` int(11) unsigned NOT NULL  default '1',
  `description` text NOT NULL default '',
  `meta_description` text NOT NULL default '',
  `meta_keywords` text NOT NULL default '',
  `priority` smallint(6) unsigned NOT NULL  default '0',
  `status` smallint(6) unsigned NOT NULL default '1',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`gallery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    INSERT INTO `{$installer->getTable('toitgallery')}` VALUES (1,'Root','','Root','album',0, 'Root','','',0,1, NOW(), NOW());

");
$installer->endSetup(); 