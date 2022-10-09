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
 * file: SqlJomSocialExport.php
 *
 **** class 
     SqlJomSocialExport 
	 
 **** functions
     __construct();	
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class SqlJomSocialExport{
	
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
	
	function getCommGroupsCategory($config){
		$db =& JFactory::getDBO();
		$data  = "";
		$data .= "\n-- --------------------------------------------------------" . "\n";
		$data .= "--" . "\n" . 
				"-- Table structure for table `" . "#__" . "community_groups_category`" . "\n" . 
				"--\n\n"; 
				  
		$data .= "CREATE TABLE IF NOT EXISTS `" . "#__" . "community_groups_category` (" . "\n" .
				"`id` int(11) NOT NULL AUTO_INCREMENT," . "\n" .
				"`name` varchar(255) NOT NULL," . "\n" .
				"`description` text NOT NULL," . "\n" .				
				"PRIMARY KEY (`id`)," . "\n" .
				") ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;" . "\n\n" .
				
				"--" . "\n" .
				"-- Dumping data for table `" . "#__" . "community_groups_category`" . "\n" .
				"--" . "\n\n";		  
		
		//select all dates from _core_acl_aro_groups table from database 
		$sql =  "SELECT * " .
				" FROM #__community_groups_category";
		$db->setQuery($sql);
		$result = $db->loadAssocList();	 
		
		$data .= "INSERT INTO `" . "#__" . "community_groups_category` (`id`, `name`, `description`) VALUES\n";               		
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
						
					$data .= "(" . $value['id'] . ", '" . $value['name'] . "', '" . $value['description'] . "')" . $separator . "\n";
					
					if(isset($result[$i+1])){	
						$data .= "INSERT INTO `" . "#__" . "community_groups_category` (`id`, `name`, `description`) VALUES\n";
					}	
					$separator = ",";
						
			   }
			   // if is not 280 or more				   	
			   else{     		
				   $data .= "(" . $value['id'] . ", '" . $value['name'] . "', '" . $value['description'] . "')" . $separator . "\n";
			   }
				$i++;		
			 }
		}
		//eliminate last , and replace with ;
		$data = substr($data, 0, strlen($data)-2);
		$data .= ";";
		 
		return $data;
	}
	
	
	function getCommUsers($config){
		$db =& JFactory::getDBO();
		$data  = "";
		$data .= "\n-- --------------------------------------------------------" . "\n";
		$data .= "--" . "\n" . 
				"-- Table structure for table `" . "#__" . "community_users`" . "\n" . 
				"--\n\n"; 
				  
		$data .= "CREATE TABLE IF NOT EXISTS `" . "#__" . "community_users` (" . "\n" .
				"`userid` int(11) NOT NULL," . "\n" .
				"`status` text NOT NULL," . "\n" .
				"`points` int(11) NOT NULL," . "\n" .					
				"`posted_on` datetime NOT NULL," . "\n" .				
				"`avatar` text NOT NULL," . "\n" .				
				"`thumb` text NOT NULL," . "\n" .				
				"`invite` int(11) NOT NULL DEFAULT '0'," . "\n" .				
				"`params` text NOT NULL," . "\n" .				
				"`view` int(11) NOT NULL DEFAULT '0'," . "\n" .				
				"`friendcount` int(11) NOT NULL DEFAULT '0'," . "\n" .				
				"`alias` varchar(255) NOT NULL," . "\n" .				
				"`latitude` float NOT NULL DEFAULT '255'," . "\n" .				
				"`longitude` float NOT NULL DEFAULT '255'," . "\n" .							
				"PRIMARY KEY (`userid`)," . "\n" .
				") ENGINE=MyISAM  DEFAULT CHARSET=utf8;" . "\n\n" .
				
				"--" . "\n" .
				"-- Dumping data for table `" . "#__" . "community_users`" . "\n" .
				"--" . "\n\n";		  
		
		//select all dates from _core_acl_aro_groups table from database 
		$sql =  "SELECT * " .
				" FROM #__community_users";
		$db->setQuery($sql);
		$result = $db->loadAssocList();	 
		
		$data .= "INSERT INTO `" . "#__" . "community_users` (`userid`,	`status`, `points`, `posted_on`, `avatar`, `thumb`, `invite`, `params`, `view`, `friendcount`, `alias`, `latitude`, `longitude`) VALUES\n";
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
						
					$data .= "(" . $value['userid'] . ", '" . $value['status'] . "', " . $value['points'] . ", '" . $value['posted_on'] . "', '" . $value['avatar'] . "', '" . $value['thumb'] ."', " . $value['invite']. ", '" . $value['params'] . "', " . $value['view'] . ", " .  $value['friendcount'] . ", '" . $value['alias'] . "', " . $value['latitude'] . ", " . $value['longitude'] . ")" . $separator . "\n";
					
					if(isset($result[$i+1])){	
						$data .= "INSERT INTO `" . "#__" . "community_users` (`userid`,	`status`, `points`, `posted_on`, `avatar`, `thumb`, `invite`, `params`, `view`, `friendcount`, `alias`, `latitude`, `longitude`) VALUES\n";
					}	
					$separator = ",";
						
			   }
			   // if is not 280 or more				   	
			   else{     		
				   $data .= "(" . $value['userid'] . ", '" . $value['status'] . "', " . $value['points'] . ", '" . $value['posted_on'] . "', '" . $value['avatar'] . "', '" . $value['thumb'] ."', " . $value['invite']. ", '" . $value['params'] . "', " . $value['view'] . ", " .  $value['friendcount'] . ", '" . $value['alias'] . "', " . $value['latitude'] . ", " . $value['longitude'] . ")" . $separator . "\n";
			   }
				$i++;		
			 }
		}
		//eliminate last , and replace with ;
		$data = substr($data, 0, strlen($data)-2);
		$data .= ";";
		 
		return $data;
	}
	
	
	function getCommFields($config){
		$db =& JFactory::getDBO();
		$data  = "";
		$data .= "\n-- --------------------------------------------------------" . "\n";
		$data .= "--" . "\n" . 
				"-- Table structure for table `" . "#__" . "community_fields`" . "\n" . 
				"--\n\n";
								  
		$data .= "CREATE TABLE IF NOT EXISTS `" . "#__" . "community_fields` (" . "\n" .
				"`id` int(1) NOT NULL AUTO_INCREMENT," . "\n" .
				"`type` varchar(255) NOT NULL," . "\n" .
				"`ordering` int(11) DEFAULT '0'," . "\n" .	
				"`published` tinyint(1) NOT NULL DEFAULT '0'," . "\n" .				
				"`min` int(5) NOT NULL," . "\n" .
			    "`max` int(5) NOT NULL," . "\n" .
			    "`name` varchar(255) NOT NULL," . "\n" .
			    "`tips` text NOT NULL," . "\n" .
			    "`visible` tinyint(1) DEFAULT '0'," . "\n" .
			    "`required` tinyint(1) DEFAULT '0'," . "\n" .
			    "`searchable` tinyint(1) DEFAULT '1'," . "\n" .
			    "`registration` tinyint(1) DEFAULT '1'," . "\n" .
			    "`options` text," . "\n" .
			    "`fieldcode` varchar(255) NOT NULL," . "\n" .
			    "PRIMARY KEY (`id`)," . "\n" .
			    "KEY `fieldcode` (`fieldcode`)" . "\n" .				
				") ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;" . "\n\n" .
				
				"--" . "\n" .
				"-- Dumping data for table `" . "#__" . "community_fields`" . "\n" .
				"--" . "\n\n";		  
		
		//select all dates from _core_acl_aro_groups table from database 
		$sql =  "SELECT * " .
				" FROM #__community_fields";
		$db->setQuery($sql);
		$result = $db->loadAssocList();	 
		
		$data .= "INSERT INTO `" . "#__" . "community_fields` (`id`, `type`, `ordering`, `published`, `min`, `max`, `name`, `tips`, `visible`, `required`, `searchable`, `registration`, `options`, `fieldcode`) VALUES\n";               		
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
						
					$data .= "(" . $value['id'] . ", '" . $value['type'] . "', " . $value['ordering'] . ", ".$value['published']. ", " . $value['min'] . ", " . $value['max'] . ", '" . $value['name'] . "', '" . $value['tips'] . "', " . $value['visible'] . ", " . $value['required'] . ", " . $value['searchable'] . ", " . $value['registration'] . ", '" . $value['options'] . "', '" . $value['fieldcode'] . "')" . $separator . "\n";
					
					if(isset($result[$i+1])){	
						$data .= "INSERT INTO `" . "#__" . "community_fields` (`id`, `type`, `ordering`, `published`, `min`, `max`, `name`, `tips`, `visible`, `required`, `searchable`, `registration`, `options`, `fieldcode`) VALUES\n";
					}	
					$separator = ",";
						
			   }
			   // if is not 280 or more				   	
			   else{     		
				   $data .= "(" . $value['id'] . ", '" . $value['type'] . "', " . $value['ordering'] . ", ".$value['published']. ", " . $value['min'] . ", " . $value['max'] . ", '" . $value['name'] . "', '" . $value['tips'] . "', " . $value['visible'] . ", " . $value['required'] . ", " . $value['searchable'] . ", " . $value['registration'] . ", '" . $value['options'] . "', '" . $value['fieldcode'] . "')" . $separator . "\n";
			   }
				$i++;		
			 }
		}
		//eliminate last , and replace with ;
		$data = substr($data, 0, strlen($data)-2);
		$data .= ";";
		 
		return $data;
	}
	
	
	function getCommFieldsValue($config){
		$db =& JFactory::getDBO();
		$data  = "";
		$data .= "\n-- --------------------------------------------------------" . "\n";
		$data .= "--" . "\n" . 
				"-- Table structure for table `" . "#__" . "community_fields_values`" . "\n" . 
				"--\n\n"; 
								  
		$data .= "CREATE TABLE IF NOT EXISTS `" . "#__" . "community_fields_values` (" . "\n" .
				"`id` int(11) NOT NULL AUTO_INCREMENT," . "\n" .
				"`user_id` int(11) NOT NULL," . "\n" .
				"`field_id` int(10) NOT NULL," . "\n" .	
				"`value` text NOT NULL," . "\n" .				
				"PRIMARY KEY (`id`)," . "\n" .
				"KEY `field_id` (`field_id`)," . "\n" .
  				"KEY `user_id` (`user_id`)," . "\n" .
  				"KEY `idx_user_fieldid` (`user_id`,`field_id`) \n" .
				") ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;" . "\n\n" .
				
				"--" . "\n" .
				"-- Dumping data for table `" . "#__" . "community_fields_values`" . "\n" .
				"--" . "\n\n";		  
		
		//select all dates from _core_acl_aro_groups table from database 
		$sql =  "SELECT * " .
				" FROM #__community_fields_values";
		$db->setQuery($sql);
		$result = $db->loadAssocList();	 
		
		$data .= "INSERT INTO `" . "#__" . "community_fields_values` (`id`,	`user_id`, `field_id`, `value`) VALUES\n";               		
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
						
					$data .= "(" . $value['id'] . ", " . $value['user_id'] . ", " . $value['field_id'] . ", '".$value['value']."')" . $separator . "\n";
					
					if(isset($result[$i+1])){	
						$data .= "INSERT INTO `" . "#__" . "community_fields_values` (`id`,	`user_id`, `field_id`, `value`) VALUES\n";
					}	
					$separator = ",";
						
			   }
			   // if is not 280 or more				   	
			   else{     		
				   $data .= "(" . $value['id'] . ", " . $value['user_id'] . ", " . $value['field_id'] . ", '".$value['value']."')" . $separator . "\n";
			   }
				$i++;		
			 }
		}
		//eliminate last , and replace with ;
		$data = substr($data, 0, strlen($data)-2);
		$data .= ";";
		 
		return $data;
	}
	
	
	function getCommGroups($config){
		$db =& JFactory::getDBO();
		$data  = "";
		$data .= "\n-- --------------------------------------------------------" . "\n";
		$data .= "--" . "\n" . 
				"-- Table structure for table `" . "#__" . "community_groups`" . "\n" . 
				"--\n\n"; 
		
		$data .= "CREATE TABLE IF NOT EXISTS `" . "#__" . "community_groups` (" . "\n" .
				"`id` int(11) NOT NULL AUTO_INCREMENT," . "\n" .
				"`published` tinyint(1) NOT NULL," . "\n" .
				"`ownerid` int(11) NOT NULL," . "\n" .					
				"`categoryid` int(11) NOT NULL," . "\n" .				
				"`name` varchar(255) NOT NULL," . "\n" .				
				"`description` text NOT NULL," . "\n" .				
				"`email` varchar(255) NOT NULL," . "\n" .				
				"`website` varchar(255) NOT NULL," . "\n" .				
				"`approvals` tinyint(1) NOT NULL," . "\n" .				
				"`created`datetime NOT NULL," . "\n" .				
				"`avatar` text NOT NULL," . "\n" .				
				"`thumb` text NOT NULL," . "\n" .				
				"`discusscount` int(11) NOT NULL DEFAULT '0'," . "\n" .	
				"`wallcount` int(11) NOT NULL DEFAULT '0'," . "\n" .
  				"`membercount` int(11) NOT NULL DEFAULT '0'," . "\n" .
  				"`params` text NOT NULL," . "\n" .						
				"PRIMARY KEY (`id`)," . "\n" .
				") ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;" . "\n\n" .
				
				"--" . "\n" .
				"-- Dumping data for table `" . "#__" . "community_groups`" . "\n" .
				"--" . "\n\n";		  
		
		//select all dates from _core_acl_aro_groups table from database 
		$sql =  "SELECT * " .
				" FROM #__community_groups";
		$db->setQuery($sql);
		$result = $db->loadAssocList();	 
		
		$data .= "INSERT INTO `" . "#__" . "community_groups` (`id`, `published`, `ownerid`, `categoryid`, `name`, `description`, `email`, `website`, `approvals`, `created`, `avatar`, `thumb`, `discusscount`, `wallcount`, `membercount`, `params`) VALUES\n";
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
						
					$data .= "(" . $value['id'] . ", " . $value['published'] . ", " . $value['ownerid'] . ", " . $value['categoryid'] . ", '" . $value['name'] . "', '" . $value['description'] ."', '" . $value['email']. "', '" . $value['website'] . "', " . $value['approvals'] . ", '" .  $value['created'] . "', '" . $value['avatar'] . "', '" . $value['thumb'] . "', " . $value['discusscount'] . ", " . $value['wallcount'] . ", " . $value['membercount'] . ", '" . $value['params'] . "')" . $separator . "\n";
					
					if(isset($result[$i+1])){	
						$data .= "INSERT INTO `" . "#__" . "community_groups` (`id`, `published`, `ownerid`, `categoryid`, `name`, `description`, `email`, `website`, `approvals`, `created`, `avatar`, `thumb`, `discusscount`, `wallcount`, `membercount`, `params`) VALUES\n";
					}	
					$separator = ",";
						
			   }
			   // if is not 280 or more				   	
			   else{     		
				   $data .= "(" . $value['id'] . ", " . $value['published'] . ", " . $value['ownerid'] . ", " . $value['categoryid'] . ", '" . $value['name'] . "', '" . $value['description'] ."', '" . $value['email']. "', '" . $value['website'] . "', " . $value['approvals'] . ", '" .  $value['created'] . "', '" . $value['avatar'] . "', '" . $value['thumb'] . "', " . $value['discusscount'] . ", " . $value['wallcount'] . ", " . $value['membercount'] . ", '" . $value['params'] . "')" . $separator . "\n";
			   }
				$i++;		
			 }
		}
		//eliminate last , and replace with ;
		$data = substr($data, 0, strlen($data)-2);
		$data .= ";";
		 
		return $data;
	}
	
	
	function getCommGroupMembers($config){
		$db =& JFactory::getDBO();
		$data  = "";
		$data .= "\n-- --------------------------------------------------------" . "\n";
		$data .= "--" . "\n" . 
				"-- Table structure for table `" . "#__" . "community_groups_members`" . "\n" . 
				"--\n\n"; 
								  
		$data .= "CREATE TABLE IF NOT EXISTS `" . "#__" . "community_groups_members` (" . "\n" .
				"`groupid` int(11) NOT NULL," . "\n" .
				"`memberid` int(11) NOT NULL," . "\n" .
				"`approved` int(11) NOT NULL," . "\n" .	
				"`permissions` int(1) NOT NULL," . "\n" .								
				"KEY `groupid` (`groupid`)," . "\n" .
  				"KEY `idx_memberid` (`memberid`)," . "\n" .
				")  ENGINE=MyISAM DEFAULT CHARSET=utf8 ;" . "\n\n" .
				
				"--" . "\n" .
				"-- Dumping data for table `" . "#__" . "community_groups_members`" . "\n" .
				"--" . "\n\n";		  
		
		//select all dates from _core_acl_aro_groups table from database 
		$sql =  "SELECT * " .
				" FROM #__community_groups_members";
		$db->setQuery($sql);
		$result = $db->loadAssocList();	 
		
		$data .= "INSERT INTO `" . "#__" . "community_groups_members` (`groupid`, `memberid`, `approved`, `permissions`) VALUES\n";               		
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
						
					$data .= "(" . $value['groupid'] . ", " . $value['memberid'] . ", " . $value['approved'] . ", ".$value['permissions'].")" . $separator . "\n";
					
					if(isset($result[$i+1])){	
						$data .= "INSERT INTO `". "#__" . "community_groups_members` (`groupid`, `memberid`, `approved`, `permissions`) VALUES\n";
					}	
					$separator = ",";
						
			   }
			   // if is not 280 or more				   	
			   else{     		
				   $data .= "(" . $value['groupid'] . ", " . $value['memberid'] . ", " . $value['approved'] . ", ".$value['permissions'].")" . $separator . "\n";
			   }
				$i++;
			 }
		}
		//eliminate last , and replace with ;
		$data = substr($data, 0, strlen($data)-2);
		$data .= ";";
		 
		return $data;
	}
	
};

?>