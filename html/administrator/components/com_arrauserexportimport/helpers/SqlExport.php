<?php
/**
 * ARRA User Export Import component for Joomla! 1.5
 * @version 1.0.0
 * @author ARRA (joomlarra@gmail.com)
 * @link http://www.joomlarra.com
 * @Copyright (C) 2010 joomlarra.com. All Rights Reserved.
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 *
    *ARRA User Export is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
    *the Free Software Foundation, either version 3 of the License, or(at your option) any later version.
    *This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    *GNU General Public License for more details.
    *You should have received a copy of the GNU General Public License along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * file: SqlExport.php
 *
 **** class 
     SqlExport 
	 
 **** functions
     __construct();	 
	 getFileHeader();
	 getCoreAclAroGroups();
	 getUsers();
	 getCoreAclAro();
	 getCoreAclGroupsAroMap();
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * SqlExport class
 */
class SqlExport{
     /**
	 * @access	public
	 * @return	void
	 */
	function __construct(){		    		
	}
	
	//return header export file
	function getFileHeader($config){
		//set curent date to write in export file
		$search_date = date("D M j G:i:s Y");
		$data = "-- phpMyAdmin SQL Dump" . "\n" . 
				"-- version 3.2.0.1" . "\n" . 
				"-- http://www.phpmyadmin.net" . "\n" . 
				"--" . "\n" . 
				"-- Host: " . $config->host . "\n" . 
				"-- Generation Time: " . $search_date . "\n" . 
				"-- Server version: 5.1.36" . "\n" . 
				"-- PHP Version: 5.3.0" . "\n\n" . 
				
				"SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";" . "\n\n" . 
					
				"--" . "\n" . 
				"-- Database: `" . $config->db . "`" . "\n" . 
				"--";
		return $data;
	}
	
	//return configs for core_acl_aro_groups table
	function getCoreAclAroGroups($config){
		$db =& JFactory::getDBO();
		$data  = "";
		$data .= "\n-- --------------------------------------------------------" . "\n";
		$data .= "--" . "\n" . 
				"-- Table structure for table `" . "#__" . "core_acl_aro_groups`" . "\n" . 
				"--\n\n"; 
				  
		$data .= "CREATE TABLE IF NOT EXISTS `" . "#__" . "core_acl_aro_groups` (" . "\n" .
				"`id` int(11) NOT NULL AUTO_INCREMENT," . "\n" .
				"`parent_id` int(11) NOT NULL DEFAULT '0'," . "\n" .
				"`name` varchar(255) NOT NULL DEFAULT ''," . "\n" .
				"`lft` int(11) NOT NULL DEFAULT '0'," . "\n" .
				"`rgt` int(11) NOT NULL DEFAULT '0'," . "\n" .
				"`value` varchar(255) NOT NULL DEFAULT ''," . "\n" .
				"PRIMARY KEY (`id`)," . "\n" .
				"KEY `"."#__" ."gacl_parent_id_aro_groups` (`parent_id`)," . "\n" .
				"KEY `"."#__" ."gacl_lft_rgt_aro_groups` (`lft`,`rgt`)" . "\n" .
				") ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;" . "\n\n" .
				
				"--" . "\n" .
				"-- Dumping data for table `" . "#__" . "core_acl_aro_groups`" . "\n" .
				"--" . "\n\n";		  
		
		//select all dates from _core_acl_aro_groups table from database 
		$sql =  "SELECT * " .
				" FROM #__core_acl_aro_groups";
		$db->setQuery($sql);
		$result = $db->loadAssocList();	 
		
		$data .= "INSERT INTO `" . "#__" . "core_acl_aro_groups` (`id`, `parent_id`, `name`, `lft`, `rgt`, `value`) VALUES\n";               		
		$i=0;
		$value_max_for_insert = 279;
		$separator = ",";
		if(is_array($result)){	
			// for each user from database make an insert in new databse table	 
			foreach($result as $key=>$value){
				//after 280 rows, reenter insert into...
				if($i == $value_max_for_insert){
					$separator = ";";
					$value_max_for_insert += 279; 
						
					$data .= "('" . $value['id'] . "', '" . $value['parent_id'] . "', '" . $value['name'] . "', '" . $value['lft'] . "', '" . $value['rgt']. "', '" . $value['value'] . "')" . $separator . "\n";
					
					if(isset($result[$i+1])){	
						$data .= "INSERT INTO `" . "#__" . "core_acl_aro_groups` (`id`, `parent_id`, `name`, `lft`, `rgt`, `value`) VALUES\n";
					}	
					$separator = ",";
						
			   }
			   // if is not 280 or more				   	
			   else{     		
				   $data .= "('" . $value['id'] . "', '" . $value['parent_id'] . "', '" . $value['name'] . "', '" . $value['lft'] . "', '" . $value['rgt']. "', '" . $value['value'] . "')" . $separator . "\n"; 
			   }
				$i++;		
			 }
		}
		//eliminate last , and replace with ;
		$data = substr($data, 0, strlen($data)-2);
		$data .= ";";
		 
		return $data;
	}
	
	//return _users config table
	function getUsers($config){
		$db =& JFactory::getDBO();
		//select all dates from _users table from database 
		$sql = "SELECT * " .
		        " FROM #__users";
		$db->setQuery($sql);
		$result = $db->loadAssocList();			  
		$data  = "";
		$data .= "\n\n-- --------------------------------------------------------";
		  		 
		$data .= "\n\n" . "--" . "\n" . 
				  "-- Table structure for table `" . "#__" . "users`" . "\n" . 
				  "--\n\n";				  		  		   
				  	
		$data .= "CREATE TABLE IF NOT EXISTS `" . "#__" . "users`" . " (" . "\n" . 
					  "  `id` int(11) NOT NULL AUTO_INCREMENT," . "\n" . 
					  "  `name` varchar(255) NOT NULL DEFAULT ''," . "\n" .
					  "  `username` varchar(150) NOT NULL DEFAULT ''," . "\n" .
					  "  `email` varchar(100) NOT NULL DEFAULT ''," . "\n" . 
					  "  `password` varchar(100) NOT NULL DEFAULT ''," . "\n" .
					  "  `usertype` varchar(25) NOT NULL DEFAULT ''," . "\n" . 
					  "  `block` tinyint(4) NOT NULL DEFAULT '0'," . "\n" . 
					  "  `sendEmail` tinyint(4) DEFAULT '0'," . "\n" . 
					  "  `gid` tinyint(3) unsigned NOT NULL DEFAULT '1'," . "\n" .
					  "  `registerDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'," . "\n" . 
					  "  `lastvisitDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'," . "\n" . 
					  "  `activation` varchar(100) NOT NULL DEFAULT ''," . "\n" . 
					  "  `params` text NOT NULL," . "\n" . 
					  "  PRIMARY KEY (`id`)," . "\n" . 
					  "  KEY `usertype` (`usertype`)," . "\n" . 
					  "  KEY `idx_name` (`name`)," . "\n" . 
					  "  KEY `gid_block` (`gid`,`block`)," . "\n" . 
					  "  KEY `username` (`username`)," . "\n" . 
					  "  KEY `email` (`email`)" . "\n" . 
				   ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=63 ;" . "\n\n" . 
			
					"--" . "\n" . 
					"-- Dumping data for table `" . "#__" . "users`" . "\n" . 
					"--\n\n";
		$data .= "INSERT INTO `" . "#__" . "users` (`id`, `name`, `username`, `email`, `password`, `usertype`, `block`, `sendEmail`, `gid`, `registerDate`, `lastvisitDate`, `activation`, `params`) VALUES\n";               		
		$i=0;
		$value_max_for_insert = 279;
		$separator = ",";
		if(is_array($result)){	
			// for each user from database make an insert in new databse table	 
			foreach($result as $key=>$value){
				//after 280 rows, reenter insert into...
				if($i == $value_max_for_insert){
					$separator = ";";
					$value_max_for_insert += 279; 
						
					$data .= "('" . $value['id'] . "', '" . $value['name'] . "', '" . $value['username'] . "', '" . $value['email'] . "', '" . $value['password']. "', '" . $value['usertype'] . "', '" . $value['block'] . "', '" . $value['sendEmail'] . "', '" . $value['gid'] . "', '" . $value['registerDate'] . "', '" . $value['lastvisitDate'] . "', '" . $value['activation'] . "', '" . $value['params'] . "')" . $separator . "\n";
					
					if(isset($result[$i+1])){	
						$data .= "INSERT INTO `" . "#__" . "users` (`id`, `name`, `username`, `email`, `password`, `usertype`, `block`, `sendEmail`, `gid`, `registerDate`, `lastvisitDate`, `activation`, `params`) VALUES\n";
					}	
					$separator = ",";
						
				}
				// if is not 280 or more				   	
				else{     		
					$data .= "('" . $value['id'] . "', '" . $value['name'] . "', '" . $value['username'] . "', '" . $value['email'] . "', '" . $value['password']. "', '" . $value['usertype'] . "', '" . $value['block'] . "', '" . $value['sendEmail'] . "', '" . $value['gid'] . "', '" . $value['registerDate'] . "', '" . $value['lastvisitDate'] . "', '" . $value['activation'] . "', '" . $value['params'] . "')" . $separator . "\n"; 
				}
				$i++;		
			}
		}
		//eliminate last , and replace with ;
		$data = substr($data, 0, strlen($data)-2);
		$data .= ";";
		 
		return $data;
	}
	
	//return _core_acl_aro table config
	function getCoreAclAro($config){
		$db =& JFactory::getDBO();
		//select all dates from _core_acl_aro table from database 
		$sql = "SELECT * " .
		        " FROM #__core_acl_aro";
		$db->setQuery($sql);
		$result = $db->loadAssocList();			  
		$data  = ""; 
		$data .= "\n\n-- --------------------------------------------------------";
		 		 
		$data .= "\n\n" . "--" . "\n" . 
				  "-- Table structure for table `" . "#__" . "core_acl_aro`" . "\n" . 
				  "--\n\n";	
		 
		$data .= "CREATE TABLE IF NOT EXISTS `" . "#__" . "core_acl_aro` (". "\n" .
				  "`id` int(11) NOT NULL AUTO_INCREMENT,". "\n" .
				  "`section_value` varchar(240) NOT NULL DEFAULT '0',". "\n" .
				  "`value` varchar(240) NOT NULL DEFAULT '',". "\n" .
				  "`order_value` int(11) NOT NULL DEFAULT '0',". "\n" .
				  "`name` varchar(255) NOT NULL DEFAULT '',". "\n" .
				  "`hidden` int(11) NOT NULL DEFAULT '0',". "\n" .
				  "PRIMARY KEY (`id`),". "\n" .
				  "UNIQUE KEY `"."#__" ."section_value_value_aro` (`section_value`(100),`value`(100)),". "\n" .
				  "KEY `"."#__" ."gacl_hidden_aro` (`hidden`)". "\n" .
				") ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;". "\n\n" .
				
				"--". "\n" .
				"-- Dumping data for table `" . "#__" . "core_acl_aro`". "\n" .
				"--\n\n";
				
		$data .= "INSERT INTO `" . "#__" . "core_acl_aro` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES\n";
		$i=0;
		$value_max_for_insert = 279;
		$separator = ",";
		if(is_array($result)){	
			// for each user from database make an insert in new databse table	 
			foreach($result as $key=>$value){
				//after 280 rows, reenter insert into...
				if($i == $value_max_for_insert){
					$separator = ";";
					$value_max_for_insert += 279; 
						
					$data .= "('" . $value['id'] . "', '" . $value['section_value'] . "', '" . $value['value'] . "', '" . $value['order_value'] . "', '" . $value['name']. "', '" . $value['hidden'] . "')" . $separator . "\n";
					
					if(isset($result[$i+1])){	
						$data .= "INSERT INTO `" . "#__" . "core_acl_aro` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES\n";
					}
					$separator = ",";
						
				   }
				   // if is not 280 or more				   	
				   else{     		
						$data .= "('" . $value['id'] . "', '" . $value['section_value'] . "', '" . $value['value'] . "', '" . $value['order_value'] . "', '" . $value['name']. "', '" . $value['hidden'] . "')" . $separator . "\n";
				   }
				   $i++;		
			 }
		 }
		 //eliminate last , and replace with ;
		 $data = substr($data, 0, strlen($data)-2);
		 $data .= ";";
		 
		 return $data;
	}
	
	// get _core_acl_groups_aro_map config table
	function getCoreAclGroupsAroMap($config){
		$db =& JFactory::getDBO();
		//select all dates from core_acl_groups_aro_map table from database 
		$sql = "SELECT * " .
		        " FROM #__core_acl_groups_aro_map";
		$db->setQuery($sql);
		$result = $db->loadAssocList();			  
		$data  = "";
		$data .= "\n\n-- --------------------------------------------------------";
		 		 
		$data .= "\n\n" . "--" . "\n" . 
				  "-- Table structure for table `" . "#__" . "core_acl_groups_aro_map`" . "\n" . 
				  "--\n\n";
		 
		$data .= "CREATE TABLE IF NOT EXISTS `" . "#__" . "core_acl_groups_aro_map` (" . "\n" .
				  "`group_id` int(11) NOT NULL DEFAULT '0'," . "\n" .
				  "`section_value` varchar(240) NOT NULL DEFAULT ''," . "\n" .
				  "`aro_id` int(11) NOT NULL DEFAULT '0'," . "\n" .
				  "UNIQUE KEY `group_id_aro_id_groups_aro_map` (`group_id`,`section_value`,`aro_id`)" . "\n" .
				") ENGINE=MyISAM DEFAULT CHARSET=utf8;" . "\n\n" .
				
				"--" . "\n" .
				"-- Dumping data for table `"."#__" ."core_acl_groups_aro_map`" . "\n" .
				"--\n\n";
		 
		$data .= "INSERT INTO `" . "#__" . "core_acl_groups_aro_map` (`group_id`, `section_value`, `aro_id`) VALUES\n";
		$i=0;
		$value_max_for_insert = 279;
		$separator = ",";
		if(is_array($result)){	
			// for each user from database make an insert in new databse table	 
			foreach($result as $key=>$value){
				//after 280 rows, reenter insert into...
				if($i == $value_max_for_insert){
					$separator = ";";
					$value_max_for_insert += 279; 						
					$data .= "('" . $value['group_id'] . "', '" . $value['section_value'] . "', '" . $value['aro_id'] . "')" . $separator . "\n";						
					if(isset($result[$i+1])){
						$data .= "INSERT INTO `" . "#__" . "core_acl_groups_aro_map` (`group_id`, `section_value`, `aro_id`) VALUES\n";
					}
					$separator = ",";						
				}
				// if is not 280 or more				   	
				else{     		
					$data .= "('" . $value['group_id'] . "', '" . $value['section_value'] . "', '" . $value['aro_id'] . "')" . $separator . "\n";
				}
				$i++;		
			}
		}
		//eliminate last , and replace with ;
		$data = substr($data, 0, strlen($data)-2);
		$data .= ";";
		 
		return $data;
	}
}//end class

?>