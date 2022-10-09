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
 * file: jomsocial.php
 *
 **** class 
     ArrausersexportimportModelJomsocial 
	 
 **** functions
     __construct();	 
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');
require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'SqlJomSocialExport.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'SqlJomSocialImport.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'TxtImport.php');

/**
 * ArrausersexportimportModelJomsocial Model
 *
 */
class ArrausersexportimportModelJomsocial extends JModel{
	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct(){		
		parent::__construct();
	}
	
	function getUserType(){
	      $db =& JFactory::getDBO();
		  $sql = "SELECT name FROM #__core_acl_aro_groups ".
				 "WHERE name IN ('Registered') ".
				 "UNION ALL ". 
				 "SELECT name FROM #__core_acl_aro_groups ". 
				 "WHERE (name <> 'ROOT' and name <> 'USERS' and name <> 'Public Frontend' and name <> 'Public Backend' and name <> 'Registered')";  
		  $db->setQuery($sql);
		  $result = $db->loadAssocList();
		  return $result; 
	}
	
	function getGroups(){
		$db =& JFactory::getDBO();
		$sql = "select id, name from #__community_groups";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();			
		return $result;	
	}
	
	function getColumns(){		
		$db =& JFactory::getDBO();
		$sql = "select name, fieldcode from #__community_fields where fieldcode <> ''";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();			
		return $result;
	}
	
	function getjoomSocial(){
		$db =& JFactory::getDBO();
		$sql = "select count(*) from #__components where link='option=com_community'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		if($result == 0){
			return FALSE;	
		}
		return TRUE;
	}
	
	function getcomBuilder(){
		$db =& JFactory::getDBO();
		$sql = "select count(*) from #__components where link='option=com_comprofiler'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		if($result == 0){
			return FALSE;	
		}
		return TRUE;
	}
	
	function import(){
		$this->saveOptions();
		$type_file = JRequest::getVar("type_file");
		
		$file = JRequest::getVar('file_upload', NULL, 'files', 'array');
		$fileNameOnClientPc = $file['name'];
		$fileNameOnServer = $file['tmp_name'];
		
		$message = "";
		$data = "";		
			    
	    if (strlen($fileNameOnClientPc) == 0){
			$message = "ERROR+".JText::_('ARRA_ERROR_NO_FILE_TO_UPLOAD');
		}
		elseif(strlen($fileNameOnServer) == 0){
			$message = "ERROR+".JText::_('ARRA_ERROR_UPLOADING_FILE');
		}
		else{
			$data = file_get_contents($fileNameOnServer);			
			if($data === false){
				$message = "ERROR+".JText::_('ARRA_ERROR_READING_UPLOADED_FILE'); 
			}
			elseif(strlen($data) == 0){
				$message = "ERROR+".JText::_('ARRA_ERROR_FILE_EMPTY');
			}
			else{
				$extension_array = array();
				if($fileNameOnClientPc != ""){
					$extension_array = explode(".", $fileNameOnClientPc);
				}
				
				$length = count($extension_array);
				$extension = $extension_array[$length-1];			
				$function = "";			    
				
				if($extension == "txt" || $extension == "csv"){
					$function .= "csv_txtImport";
					$data = file_get_contents($fileNameOnServer);
				}
				elseif($extension == "sql" || $extension == "zip"){
					$function .= "sql_zipImport";										
					if($extension == "zip"){
						if (($temp = zip_open($fileNameOnServer))) {
							while ($entry = zip_read($temp)){
								if (preg_match('/.sql$/',zip_entry_name($entry))){ 
									$data = zip_entry_read($entry, zip_entry_filesize($entry));
								}   
							}
							zip_close($temp);
						}
					}
					elseif($extension == "sql"){
						$data = file_get_contents($fileNameOnServer);
					}
				}
				// apel functions for import
				$message = $this->$function($data, $fileNameOnServer);
			}
		}
		return $message;
	}
	
	function getComponentParams(){
		$db =& JFactory::getDBO();
		$sql = "select c.params from #__components c where c.option='com_arrauserexportimport' and c.link='option=com_arrauserexportimport'";
	    $db->setQuery($sql);
		$db->query();
	    $content = $db->loadResult();			
		return $content;
	}
	
	function csv_txtImport($data, $fileNameOnServer){			
		$db =& JFactory::getDBO();
		$txtImportJomSocial = new JomSocialImport();
		$txtImport = new TxtImport();
		//evidence for header line and body lines
		$i = "header";
		$columns_imported = array();		
		$all_user_type = $txtImport->getUserType();
		$separator = JRequest::getVar("separator", ",", "post", "string");
		$group_map_aro_id = "";	
		$users_existent = array();
		$empty_columns = array();		
	    $fp = fopen($fileNameOnServer, 'r');
		$array_from_joomla = array();
		$component_params = $this->getComponentParams();
		$category = array();
		$videos_category = array();
		$photos_albums = array();		
		
		//complete array with field details
		$fields_details = array();		
		$ok = false;
		$cursor = "0";
		while(!feof($fp)){				
			$temp_row = fgets($fp);										
			if(strpos($temp_row, "*****") >= 0 && strpos($temp_row, "*****") !== FALSE){
				$ok = true;
			}
			if($ok === true){				
				$temp = explode("=>", trim($temp_row));				
				$temp["0"] = str_replace('"', "", $temp["0"]);
				if(isset($temp["1"])){				 
					$fields_details[$temp["0"]] = $temp["1"];
				}
			}			
			$temp_row = "";
			$cursor ++;
		}
		fclose($fp);
		
		unset($fields_details["*****"]);						
		
		//------------------------------------------------------------------
		//variables details - after ****** in imported file
		if(isset($fields_details["category"])){
			$category_object = json_decode($fields_details["category"]);
			foreach($category_object as $key=>$value){
				$category[$value->id] = $value;
			}
		}		
		
		$sql = "select * from #__community_videos_category";
		$db->setQuery($sql);
		$db->query();
		$videos_category = $db->loadAssocList("name");

		$sql = "select * from #__community_photos_albums";
		$db->setQuery($sql);
		$db->query();
		$photos_albums = $db->loadAssocList("name");
		//------------------------------------------------------------------
				
		$fp = fopen($fileNameOnServer, 'r');
		$counter = 0;
		$min_value = JRequest::getVar("min_value");
		$max_value = JRequest::getVar("max_value");
				
		while(!feof($fp)){						   	    
			$temp_row = fgets($fp);//line by line
																
			if(strpos($temp_row, "*****")===FALSE){	
				//if we have a blank line
				if(strlen(trim($temp_row))==0){
				}
				else{						
					//if is first line(header line)
					if($i == "header"){
						//convert from UTF-8 to ANSI
						$temp_row = utf8_encode(trim($temp_row));									
						if(strpos(trim($temp_row), $separator) == false){
							return "ERROR+".JText::_('ARRA_ERROR_UNKNOWN_SEPARATOR');
						}				
					
						$columns_imported = explode($separator, trim($temp_row));						
						$sql= "DESCRIBE #__users ";
						$db->setQuery($sql);
						$result = $db->loadAssocList();
						$array_all_columns = array();
						foreach($result as $key=>$value){
							 if($value['Field'] != "gid" && $value['Field'] != "params"){
								$array_all_columns[] = $value['Field'];
							 }	
						}
														
						if(!in_array("name", $columns_imported) || !in_array("username", $columns_imported) || !in_array("email", $columns_imported)){
							return "ERROR+".JText::_('ARRA_ERROR_NO_NAME_USERNAME_EMAIL');
						}
						$default_password = JRequest::getVar("default_password","","post","string");
						$generate_password = JRequest::getVar("generate_password_radio","1","post","string");			
						if(!in_array("password", $columns_imported) && strlen(trim($default_password)) == 0 && $generate_password==1){
							return "ERROR+".JText::_('ARRA_NO_PASSWORD_DEFAULT_PASSWORD');
						}
						$i = "body";
					}
					//else if is a row(from body content)
					else{												
						$counter ++;										
						if(($counter >= $min_value) && ($counter <= $max_value)){																					
							$user_id_from_database = "-1"; 
							$gid = "";
							$value = "";
							$usertype = "";					
							$row = explode($separator, utf8_encode(trim($temp_row)));									 																														
							$user_id_from_database = $this->userExist($columns_imported, $row);	
							if($user_id_from_database == -1){					
								$email = "";				
								//if email isn't already in databse
								if(in_array("email", $columns_imported)){					    
									$temp = array_keys($columns_imported, "email");
									$position = $temp[0];
									$email = trim($row[$position]);
								}					
								if($this->emailExist($email) == false){											 
									//verify if is imported usertype
									if(in_array("usertype", $columns_imported)){
										$temp = array_keys($columns_imported, "usertype");							
										$position = $temp[0];
										$usertype = trim($row[$position]);															
										//if insert a new usertype								
										if(!in_array(trim($row[$position]), $all_user_type)){										
											//if none set usertype then save default usertype in database
											if(trim($row[$position])== ""){
												$usertype = JRequest::getVar("position","Registered","post","string");
												if(trim($usertype) == ""){
													$usertype = "Registered";
												}
												//if default usertype exist in database
												if(!in_array($usertype, $all_user_type)){											
													$gid = $txtImport->saveInGroup($row, "", $usertype);
													$group_map_group_id = $gid;
												}
												else{
													$gid = $txtImport->getIDUserType($usertype);
													$group_map_group_id = $gid;
												}									
											}
											else{
												$gid = $txtImport->saveInGroup($row, $position, "");
												$group_map_group_id = $gid;									
											}
											$all_user_type[] = $usertype;
										}
										//if exist this user type
										else{									
											$gid = $txtImport->getIDUserType($usertype);
											$group_map_group_id = $gid;
										}						  		    
									}
									else{
										//set a new type of usertype
										$usertype = JRequest::getVar("position","Registered","post","string");
										if(trim($usertype) == ""){
											$usertype = "Registered";
										}
										//if default usertype is not in database
										if(!in_array(trim($usertype), $all_user_type)){	
											$gid = $txtImport->saveInGroup($row, "", $usertype);
											$group_map_group_id = $gid;
										}
										//else if default type of usertype exist in database
										else{
											$gid = $txtImport->getIDUserType($usertype);
											$group_map_group_id = $gid; 
										}	  
									}
									//insert row in _users table								
									$array_from_joomla = $txtImport->saveUsers($columns_imported, $row, $usertype, $gid, "jomsocial");
									$value = $array_from_joomla["id"];										
									if(isset($value) && $value != ""){ 
										$group_map_aro_id = $txtImport->saveAclAro($value, $columns_imported, $row);
									}
														
									//to login in sistem must insert registered in _core_acl_groups_aro_map table
									if(isset($group_map_aro_id) && isset($group_map_group_id) && $group_map_aro_id != "" && $group_map_group_id != ""){		      							$txtImport->saveInGroupsAroMap($group_map_group_id, $group_map_aro_id);																		
										$temp_array = array();
										$k = 0;
										foreach($columns_imported as $key=>$val){
											$temp_array[$k++] = $row[$key];
										}
										$row = $temp_array;
										$txtImportJomSocial->saveUserInJomSocial($columns_imported, $row, $value, $fields_details, $array_from_joomla, $component_params, $category, $videos_category, $photos_albums);
									}
								}//if email not exist in database
								else{
									$users_existent[] = $temp_row;
								}							
							}
							//else, if imported existing user
							else{							
								//make un update to existing user
								$update_message = $txtImport->updateUser($columns_imported, $row, $user_id_from_database);							
								if($update_message == "empty_column"){
									$empty_columns[] = $temp_row;
								}
								if(!$this->existUserJomSocial($user_id_from_database)){								
									$txtImportJomSocial->saveUserInJomSocial($columns_imported, $row, $user_id_from_database, $fields_details, $array_from_joomla, $component_params, $category, $videos_category, $photos_albums);
								}
								else{
									$update_jomsocial = JRequest::getVar("same_user_option_radio_basic_informations", "1");									
									if($update_jomsocial != "1"){																				
										$txtImportJomSocial->updateUserJomSocial($columns_imported, $row, $user_id_from_database, $fields_details, $videos_category, $photos_albums);
									}
								}
							}									
							$temp = "";//reset line
						}//from min to max value
						elseif(($counter >= $min_value) && ($counter > $max_value)){
							break;
						}	
					}//else
				}
			}//first if, if not *****
			else{
				break;
			}
        }//while			
		fclose($fp);
		$message_error1 = "";
		$message_error2 = "";
		if(count($users_existent)>0){
			$_SESSION['link_eror'] = "error";
			$file_path = JPATH_SITE .DS."administrator".DS."components".DS."com_arrauserexportimport".DS."files".DS."error_same_email.csv";
			$g = fopen ($file_path, "w");
			$data = implode($separator, $columns_imported)."\n";
			foreach($users_existent as $key=>$value){
				$data .= trim($value)."\n"; 
			}					
			if(fwrite ($g, $data)){
				fclose($g);
			}
			$message_error1 .= "ERROR+".JText::_('ARRA_ERROR_MESSAGE_SAME_EMAIL');							
	    }
		if(count($empty_columns)>0){
			$_SESSION['error_empty_column'] = "error_empty_column";
			$file_path = JPATH_SITE .DS."administrator".DS."components".DS."com_arrauserexportimport".DS."files".DS."error_empty_column.csv";
			$g = fopen ($file_path, "w");
			$data = implode($separator, $columns_imported)."\n";
			foreach($empty_columns as $key=>$value){
				$data .= trim($value)."\n"; 
			}					
			if(fwrite ($g, $data)){
				fclose($g);
			}
			$message_error2 .= "ERROR+".JText::_('ARRA_ERROR_MESSAGE_EMPTY_COLUMN');							
	    }
		
		if($message_error1 != "" && $message_error2 == ""){
			return $message_error1;
		}
		elseif($message_error1 == "" && $message_error2 != ""){
			return $message_error2;
		}
		elseif($message_error1 != "" && $message_error2 != ""){
			return $message_error1 . "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . JText::_('ARRA_ERROR_MESSAGE_EMPTY_COLUMN');
		}
		else{
			return "OK+".JText::_('ARRA_UPLOADED_FILE'); 
		}	
	}
	
	function existUserJomSocial($user_id){
		$db =& JFactory::getDBO();
		$sql = "select count(*) from #__community_users where `userid`=".$user_id;		
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		if($result != "0"){
			return true;
		}
		return false;
	}
	
	function saveOptions(){
		$basic_informations = JRequest::getVar("same_user_option_radio_basic_informations","","post","string");
		$overwrite_usertype = JRequest::getVar("same_user_option_radio_usertype","","post","string");
		$overwrite_password = JRequest::getVar("same_user_option_radio_password","","post","string");
		$overwrite_email = JRequest::getVar("same_user_option_radio_email","","post","string");
		$overwrite_block = JRequest::getVar("same_user_option_radio_block","","post","string");
		$password_encripted = JRequest::getVar("encripted_password_radio","","post","string");
		$generate_password = JRequest::getVar("generate_password_radio","","post","string");
		
		$db =& JFactory::getDBO();
		$sql = "select c.params from #__components c where c.option='com_arrauserexportimport' and c.link='option=com_arrauserexportimport'";
	    $db->setQuery($sql);
	    $content = $db->loadResult();
		
		$all_array = array();
		
		if($content != NULL && strlen(trim($content))>0){
			$content = str_replace("'", "\'", $content);
			$all_array = unserialize($content);
		}
		
		$all_array["JomSocialOptions"] = "same_user_option_radio_basic_informations=".$basic_informations.";".
		   	   							 "same_user_option_radio_usertype=".$overwrite_usertype.";".
									     "same_user_option_radio_password=".$overwrite_password.";".
									     "same_user_option_radio_email=".$overwrite_email.";".
									     "same_user_option_radio_block=".$overwrite_block.";".
									     "encripted_password_radio=".$password_encripted.";".
									     "generate_password_radio=".$generate_password.";";
		
	    $sql = "update #__components c set params='".serialize($all_array)."' where c.option='com_arrauserexportimport' and c.link='option=com_arrauserexportimport'";		
	    $db->setQuery($sql);
	    $db->query();		
	}
	
	function export(){		
		$file_type = ""; 
		$radio_type_export = JRequest::getVar("radio_type_export", "", "post", "string");
		$order = JRequest::getVar("ordering", "0", "post", "string");
		$mode_order = JRequest::getVar("mode_order", "0", "asc", "string");
		$userType = JRequest::getVar("group_type_checkbox", "");
		$column = JRequest::getVar("top_column_checkbox","","post","array");		
		
		//add column id for export
		$column["id"] = "id";
						
		if(isset($radio_type_export)){
			$file_type = JRequest::getVar("radio_type_export", "", "post", "string");
		}
		else{
			$file_type = "csv";
		}
				
		if($file_type == "csv" || $file_type == "txt"){				
			$this->csvtxtJoomSocExport($userType, $column, $order, $mode_order, $file_type);
		}
		elseif($file_type == "html"){				
			$this->htmlJoomSocExport($userType, $column, $order, $mode_order, $file_type);
		}
		elseif($file_type == "sql"){				
			$this->sqlJoomSocExport($userType, $column, $order, $mode_order, $file_type);
		}
		elseif($file_type == "zip"){				
			$this->zipJoomSocExport($userType, $column, $order, $mode_order, $file_type);
		}
	}
	
	function csvtxtJoomSocExport($userType, $column, $order, $mode_order, $file_type){		
		$select_videos = false;
		$select_photos = false;
		
		$data = "";
        $user_type = "";
		$header = "";
		$header_array = array();
		$result_array = array();
		
		$users = array();
		$joom_users = array();
		$joom_group = "";
		$user_columns = "";
		$fields_columns = "";
		$fields_name_id = array();
		$fields_values = "";
		$group_concat = "";
		$select_columns = array();
		$from_tables1 = array("#__users ju", "#__community_users");
		$conditions1 = "";
		
		$separator = JRequest::getVar("separator_export", ",", "post", "string");		
		$split_name = JRequest::getVar("split_name", "no", "post", "string");
		$export_name = false; //if is true separate each name from result in first name and last name  	
		$select_id = "ju.id, ";
		
		//complet criteria for select from database
		if(is_array($userType) && count($userType)>0){		
			$user_type .= " and gm.groupid in (";
			foreach($userType as $key=>$value){
				$user_type .= "'" . $value . "',"; 
			}			
			$var = substr($user_type, 0, strlen($user_type)-1);
			$user_type = $var . ")";
			$from_tables1["tables1"] = "#__community_groups g, #__community_groups_members gm";
			$from_tables2["tables2"] = "#__community_groups_members gm";
			$conditions1 = " and g.id=gm.groupid and gm.memberid=#__community_users.userid ";
			$conditions2 = " and gm.memberid=ju.id ";	
		}
		$db =& JFactory::getDBO();
		$columns = implode(",", $column);
		
		$ordering = "";		
		if($order != "" && $order != "0"){
			if($mode_order != ""){
				$ordering = " order by ".$order." ".$mode_order;
			}
			else{
				$ordering = " order by ".$order." asc";
			}	 
		}
		
		//set file header
         foreach($column as $key=>$value){
			if($value=="id" || $value=="name" || $value=="username" || $value=="email" || $value=="password" || $value=="block" || $value=="registerDate" || $value=="lastvisitDate" || $value=="usertype"){
				$select_columns[] = "ju.".$value;
				unset($column[$key]);
			}
			elseif($value=="status"){
				$select_columns[] = $value;
				unset($column[$key]);
			}
			elseif($value=="group_name"){
				$joom_group = "true";
				$select_columns[] = "GROUP_CONCAT(distinct g.name) as group_name";
				if(!isset($from_tables1["tables1"])){
					$from_tables1["tables1"] = "#__community_groups g, #__community_groups_members gm";
				}
				unset($column[$key]);
			}
			elseif($value=="videos"){
				$select_videos = true;
			}
			elseif($value=="photos"){
				$select_photos = true;
			}
			else{
				$value = str_replace("\n", " ", $value);
				$value = str_replace("`", "'", $value);
				$value = str_replace("'", "\'", $value);				
				$column[$key] = $value;
			}	 
        }
		if(count($column)>0){		
			$sql = "select id, name from #__community_fields where fieldcode in('".implode("','", $column)."')";			
			$db->setQuery($sql);
			$db->query();
			$result = $db->loadAssocList();
			if(count($result)>0){
				foreach($result as $key=>$value){
					$fields_name_id[$value["name"]] = $value["id"];
				}			
			}
			if(!isset($from_tables1["tables1"]) && in_array("group_name", $column)){
				$from_tables1["tables1"] = "#__community_groups g, #__community_groups_members gm";
			}
		}
		
		$sql = "SELECT ".$select_id.implode(",", $select_columns).
			   " FROM ".implode(",", $from_tables1).
			   " where ju.id=#__community_users.userid ".$user_type.
					  $conditions1.
					  " GROUP BY #__community_users.userid ".$ordering;
		$db->setQuery($sql);
		$db->query();		
		$result = $db->loadAssocList();				
		
		if(count($result)>0){
			foreach($result as $key=>$value){
				if(count($column)>0){
					$fields_values = "";
					$names = ""; 
					$sql = "select cfv.value, cfv.field_id from #__community_fields_values cfv where cfv.field_id in (select id from #__community_fields where fieldcode in('".implode("','", $column)."')) and cfv.user_id=".$value["id"];
					$db->setQuery($sql);
					$db->query();		
					$fields_values_all_arrays = $db->loadAssocList();
					$fields_values_array = array();											
					if(count($fields_values_all_arrays)>0){
						foreach($fields_values_all_arrays as $key1=>$value1){
							$fields_values_array[$value1["field_id"]] = $value1["value"];
						}
					}
					if(count($fields_name_id)>0){
						foreach($fields_name_id as $name=>$id){
							if(isset($fields_values_array[$id])){
								$fields_values_array[$id] = str_replace(",", "", $fields_values_array[$id]);
								$fields_values .= $fields_values_array[$id].",";
							}
							else{
								$fields_values .= ",";
							}
						}
						$fields_values = substr($fields_values, 0, -1);
					}					
					if($fields_values != ""){
						$result[$key]["value"] = $fields_values;
					}					
				}
				if($joom_group != ""){					
					$sql = "select g.name from #__community_groups g, #__community_groups_members gm where g.id=gm.groupid and gm.memberid=".$value["id"];										
					$db->setQuery($sql);
					$db->query();
					$all_names_groups = $db->loadAssocList();					
					$names = "";
					if(count($all_names_groups)>0){
						foreach($all_names_groups as $group_key=>$group_value){
							$names .= $group_value["name"];
							if($group_key != count($all_names_groups)-1){
								$names .= ",";
							}
						}						
					}	
					if($names != ""){
						$result[$key]["group_name"] = $names;
					}
					else{
						$result[$key]["group_name"] = "";
					}				
					$names = "";
				}
				//unset($result[$key]["id"]);				
			}
		}	
		
		if(isset($column["photos"])){
			unset($column["photos"]);
		}
		if(isset($column["videos"])){
			unset($column["videos"]);
		}
		
		if(isset($result[0])){			
			foreach($result[0] as $key=>$value){				
				if($key == "value"){					
					$header_array[] = implode($separator, $column);
				}
				elseif($key=="group_name"){
					$header_array[] = "group";
				}				
				else{
					if($key=="name" && $split_name != "no"){
						$header_array[] = JText::_("ARRA_FIRST_NAME");
						$header_array[] = JText::_("ARRA_LAST_NAME");
						$export_name = true;
					}
					else{						
						$header_array[] = $key;
					}
				}
			}
			if($select_videos === TRUE){				
				$header_array[] = "videos";
			}
			if($select_photos === TRUE){
				$header_array[] = "photos";
			}
		}
		
		$remove_header = JRequest::getVar("remove_header", "");		
		if($remove_header == ""){
        	$data .= implode($separator, $header_array) . "\n";
			$data = str_replace("\'", "'", $data);
		}
				
		$exist = false;		
		$row = "";
		if(is_array($result)){			
			foreach($result as $key=>$value){
				$next = true;
				$row_values_array = array();
			    // for each column of row		    
				foreach($value as $key2=>$value2){
					$value2 = str_replace("\n", " ", $value2);
					$value2 = str_replace("\r", " ", $value2);
					if($key2 == "value"){
						$value2 = str_replace(",", $separator, $value2);	
					}
					//if is in columns for export name and if split the name
					if($export_name == true && $split_name != "no" && $next == true){
						$first_name = "";
						$last_name = "";
						$temp_array = explode(" ", $value2);
						//if we can split name in first name and last name
						if(count($temp_array)>2){
							$last_name .= $temp_array[count($temp_array)-1];
							for($i=0; $i<count($temp_array)-1; $i++){
								$first_name .= $temp_array[$i] . " ";
							}
							//$row .= trim($first_name) . $separator . trim($last_name) . $separator;
							$row_values_array[] = trim($first_name);
							$row_values_array[] = trim($last_name);
						}
						elseif(count($temp_array)==2){
							$first_name = $temp_array[0];
							$last_name = $temp_array[1];
							//$row .= trim($first_name) . $separator . trim($last_name) . $separator;
							$row_values_array[] = trim($first_name);
							$row_values_array[] = trim($last_name);
						}
						else{
							$first_name .= $temp_array[0];
							//$row .= trim($first_name) . $separator . $separator;
							$row_values_array[] = trim($first_name);
							$row_values_array[] = "";
						}
						$next = false;
					}
					elseif($key2 == "group_name"){
						$temp_val = trim($value2);
						$temp_val = str_replace(",", "***", $temp_val);						
						//$row .= $temp_val . $separator;	
						$row_values_array[] = $temp_val;
					}
					elseif($key2 == "field_details"){
						$temp_val = trim($value2);
						$temp_val = str_replace(",", "***", $temp_val);						
						//$row .= $temp_val . $separator;
						$row_values_array[] = $temp_val;
					}
					else{
						//$row .= trim($value2) . $separator;
						$row_values_array[] = trim($value2);
					}					
				}//foreach	
				
				//set values for videos and photos
				/*if($select_videos === TRUE || $select_photos === TRUE){
					$row = substr($row, 0, strlen($row)-1);
				}*/
				if($select_videos === TRUE){					
					$sql = "select * from #__community_videos where creator = ".intval($value["id"]);
					$db->setQuery($sql);
					$db->query();
					$video_result = $db->loadAssocList();
					if(isset($video_result) && is_array($video_result) && count($video_result) > 0){
						$video_params = json_encode($video_result);
						$video_params = str_replace(",", "***", $video_params);
						//$row .= trim($video_params).$separator;
						$row_values_array[] = trim($video_params);
					}					
				}
				if($select_photos === TRUE){					
					$sql = "select * from #__community_photos where creator = ".intval($value["id"]);
					$db->setQuery($sql);
					$db->query();
					$photo_result = $db->loadAssocList();
					if(isset($photo_result) && is_array($photo_result) && count($photo_result) > 0){
						$photo_params = json_encode($photo_result);
						$photo_params = str_replace(",", "***", $photo_params);
						//$row .= trim($photo_params).$separator;
						$row_values_array[] = trim($photo_params);
					}
				}
				unset($value["id"]);
				
				//set true for next row
				$next = true;
				
				$temp = implode($separator, $row_values_array);
				//start new row
				$data .= $temp."\n";					
				$row = "";			
			} 					
		}
		
		$config = new JConfig();
		$filename = "";
		
		if($file_type=="csv"){
			$filename = $config->db."_users.csv"; 
		}
		elseif($file_type=="txt"){
			$filename = $config->db."_users.txt";
		}
		
		$group_array = array();
		$sql = "select * from #__community_groups";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		foreach($result as $key=>$value){
			$group_array[$value["name"]] = $value;	
		}
		
		$group_category = array();
		$sql = "select * from #__community_groups_category";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		foreach($result as $key=>$value){
			$group_category[$value["name"]] = $value;	
		}
		
		$video_array = array();
		$sql = "select * from #__community_videos_category";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		foreach($result as $key=>$value){
			$video_array[$value["name"]] = $value;	
		}
				
		$photo_array = array();
		$sql = "select * from #__community_photos_albums";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		foreach($result as $key=>$value){
			$photo_array[$value["name"]] = $value;	
		}				
		
		$size_in_bytes=strlen($data);
		header("Content-Type: application/x-msdownload");
		//header("Content-Length:" . $size_in_bytes);
		header("Content-Disposition: attachment; filename=".$filename);
		header("Pragma: no-cache");
		header("Expires: 0");
		
		$data .= "\n*****";		
		//create files details		
		foreach($column as $key=>$value){			
			$sql = "select * from #__community_fields where fieldcode='".$value."'";			
			$db->setQuery($sql);
			$db->query();
			$result = json_encode($db->loadAssocList());
			$value = str_replace("\'", "'", $value);
			$data .= "\n".$value."=>".$result;
		}
		if($joom_group != ""){
			$data .= "\n"."group=>".json_encode($group_array);
			$data .= "\n"."category=>".json_encode($group_category);
		}
		if($select_videos === TRUE){
			$data .= "\n"."videos=>".json_encode($video_array);
		}
		if($select_photos === TRUE){
			$data .= "\n"."photos=>".json_encode($photo_array);
		}		
				
		echo utf8_decode($data);
		
		//send emails
		$list_emails = JRequest::getVar("text_emails", "", "post", "string");
		$email_to_super_admin = JRequest::getVar("email_to_super_admin", "no", "post", "string");		
		//if is set an enails list
		if($list_emails != ""){		
			$this->sendMail($data, $filename, $list_emails);
		}
		//send email to Super Administrator users type
		if($email_to_super_admin != "no"){
			$db =& JFactory::getDBO();
			$sql = "select email from #__users where usertype='Super Administrator'";
			$db->setQuery($sql);
			$result = $db->loadAssocList();
			$emails = "";
			foreach($result as $key=>$value){
				$emails .= $value['email'] . ",";
			}
			$emails = substr($emails, 0, strlen($emails)-1);
		    $this->sendMail($data, $filename, $emails);
		}   
			
		exit();	 
	}
	
	function htmlJoomSocExport($userType, $column, $order, $mode_order, $file_type){
		$data = "";
        $user_type = "";
		$header = "";
		$header_array = array();
		$result_array = array();
		
		$users = array();
		$joom_users = array();
		$joom_group = "";
		$user_columns = "";
		$fields_columns = "";
		$fields_name_id = array();
		$fields_values = "";
		$group_concat = "";
		$select_columns = array();
		$from_tables1 = array("#__users ju", "#__community_users");
		$conditions1 = "";
		
		$separator = JRequest::getVar("separator", ",", "post", "string");
		$split_name = JRequest::getVar("split_name", "no", "post", "string");
		$export_name = false; //if is true separate each name from result in first name and last name  	
		$select_id = "ju.id, ";
		
		//complet criteria for select from database
		if(is_array($userType) && count($userType)>0){		
			$user_type .= " and gm.groupid in (";
			foreach($userType as $key=>$value){
				$user_type .= "'" . $value . "',"; 
			}			
			$var = substr($user_type, 0, strlen($user_type)-1);
			$user_type = $var . ")";
			$from_tables1["tables1"] = "#__community_groups g, #__community_groups_members gm";
			$from_tables2["tables2"] = "#__community_groups_members gm";
			$conditions1 = " and g.id=gm.groupid and gm.memberid=#__community_users.userid ";
			$conditions2 = " and gm.memberid=ju.id ";	
		}
		$db =& JFactory::getDBO();
		$columns = implode(",", $column);
		
		$ordering = "";		
		if($order != "" && $order != "0"){
			if($mode_order != ""){
				$ordering = " order by ".$order." ".$mode_order;
			}
			else{
				$ordering = " order by ".$order." asc";
			}	 
		}
		
		//set file header
		
         foreach($column as $key=>$value){
			if($value=="name" || $value=="username" || $value=="email" || $value=="password" || $value=="block" || $value=="registerDate" || $value=="lastvisitDate" || $value=="usertype"){
				$select_columns[] = "ju.".$value;
				unset($column[$key]);
			}
			elseif($value=="status"){
				$select_columns[] = $value;
				unset($column[$key]);
			}
			elseif($value=="group_name"){
				$joom_group = "true";
				$select_columns[] = "GROUP_CONCAT(distinct g.name) as group_name";
				if(!isset($from_tables1["tables1"])){
					$from_tables1["tables1"] = "#__community_groups g, #__community_groups_members gm";
				}
				unset($column[$key]);
			}
			else{
				$value = str_replace("`", "'", $value);
				$value = str_replace("'", "\'", $value);
				$column[$key] = $value;
			}	 
        }		
		if(count($column)>0){		
			$sql = "select id, name from #__community_fields where fieldcode in('".implode("','", $column)."')";
			$db->setQuery($sql);
			$db->query();
			$result = $db->loadAssocList();
			if(count($result)>0){
				foreach($result as $key=>$value){
					$fields_name_id[$value["name"]] = $value["id"];
				}			
			}
			if(!isset($from_tables1["tables1"]) && in_array("group_name", $column)){
				$from_tables1["tables1"] = "#__community_groups g, #__community_groups_members gm";
			}
		}					 
		$sql = "SELECT ".$select_id.implode(",", $select_columns).
			   " FROM ".implode(",", $from_tables1).
			   " where ju.id=#__community_users.userid ".$user_type.
					  $conditions1.
					  " GROUP BY #__community_users.userid ".$ordering;		
		$db->setQuery($sql);
		$db->query();		
		$result = $db->loadAssocList();				
		
		if(count($result)>0){			
			foreach($result as $key=>$value){
				if(count($column)>0){
					$fields_values = "";
					$names = ""; 
					$sql = "select cfv.value, cfv.field_id from #__community_fields_values cfv where cfv.field_id in (select id from #__community_fields where fieldcode in('".implode("','", $column)."')) and cfv.user_id=".$value["id"];
					$db->setQuery($sql);
					$db->query();		
					$fields_values_all_arrays = $db->loadAssocList();
					$fields_values_array = array();											
					if(count($fields_values_all_arrays)>0){
						foreach($fields_values_all_arrays as $key1=>$value1){
							$fields_values_array[$value1["field_id"]] = $value1["value"];
						}
					}
					if(count($fields_name_id)>0){
						foreach($fields_name_id as $name=>$id){
							if(isset($fields_values_array[$id])){
								$fields_values .= $fields_values_array[$id].",";
							}
							else{
								$fields_values .= ",";
							}
						}
					}					
					if($fields_values != ""){
						$result[$key]["value"] = $fields_values;
					}					
				}
				if($joom_group != ""){					
					$sql = "select g.name from #__community_groups g, #__community_groups_members gm where g.id=gm.groupid and gm.memberid=".$value["id"];										
					$db->setQuery($sql);
					$db->query();
					$all_names_groups = $db->loadAssocList();					
					$names = "";
					if(count($all_names_groups)>0){
						foreach($all_names_groups as $group_key=>$group_value){
							$names .= $group_value["name"];
							if($group_key != count($all_names_groups)-1){
								$names .= ",";
							}
						}						
					}	
					if($names != ""){
						$result[$key]["group_name"] = $names;
					}
					else{
						$result[$key]["group_name"] = "";
					}				
					$names = "";
				}
				unset($result[$key]["id"]);				
			}
		}
			
		if(isset($result[0])){			
			foreach($result[0] as $key=>$value){				
				if($key == "value"){					
					$header_array[] = implode($separator, $column);
				}
				elseif($key=="group_name"){
					$header_array[] = "group";
				}
				else{
					if($key=="name" && $split_name != "no"){
						$header_array[] = JText::_("ARRA_FIRST_NAME");
						$header_array[] = JText::_("ARRA_LAST_NAME");
						$export_name = true;
					}
					else{						
						$header_array[] = $key;
					}
				}
			}
		}
		
	    $data .="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n".
					"<html xmlns=\"http://www.w3.org/1999/xhtml\">\n".
					"<head>".
					"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n".
					"<title>User Export</title>\n".
					"<style>\n".
					"table {\n".
						"border-collapse: collapse;\n".
						"border: 1px solid #333333;\n".
						"margin: auto;".
						"font-family: Arial, Helvetica, sans-serif;".
					"}".
					"tr {\n".
						"text-align: left;\n".
					"}\n".
					"tr.one {\n".
						"background-color:#E2E2E2;\n".
					"}\n".
					"tr.two {\n".
						"background-color:#BAC5DA;\n".
					"}\n".
					"td {\n".
						"text-align: left;\n".
					"}\n".
					"th {\n".
						"text-align: left;\n".
						"background-color:#ADADCC;\n".
					"}\n".
					"</style>\n".
					"<script type='text/javascript'>
					window.onload=initAll;
					function initAll(){
						var trs = document.getElementsByTagName('TR');
						for(var i=0;i<trs.length;i++){
							var current = trs[i];
							current.onmouseover = changeBGK;
							current.onmouseout = setOriginalBG;
						}
					}
					function changeBGK(){
						this.style.backgroundColor = '#FFFFFF';
					}
					function setOriginalBG(){
						if(this.className == 'one') {
							this.style.backgroundColor = '#E2E2E2';
						} else if (this.className == 'two') {
							this.style.backgroundColor = '#BAC5DA';
						}
					}
					</script>\n".
					"</head>\n".				
					"<body>\n".
					"<table border=\"1\" cellpadding=\"5\" cellpadding=\"5\">\n";
		$data .= "<tr class='headers'>\n";
		
		foreach($header_array as $key=>$value){
			$value = str_replace("\'", "'", $value);
			$temp = explode(",", $value);
			if(count($temp)>1){
				foreach($temp as $key2=>$value2){
					$header .= "<th>".$value2."</th>";
				}			
			}
			else{
				$header .= "<th>".$value."</th>";
			}
		}		
		$data .= $header."</tr>";	
		
		if(is_array($result)){
			//for each row
			foreach($result as $key=>$value){
				$next = true;
				if($par_row == true){
				    $row .= "<tr class=\"one\">";
					$par_row = false;
				}
				else{
					$row .= "<tr class=\"two\">";
					$par_row = true;
				}	
			    // for each column of row		    
				foreach($value as $key2=>$value2){				   
					//if is in columns for export name and if split the name
					if($export_name == true && $split_name != "no" && $next == true){
						$first_name = "";
						$last_name = "";
						$temp_array = explode(" ", $value2);
						//if we can split name in first name and last name
						if(count($temp_array)>2){
							$last_name .= $temp_array[count($temp_array)-1];
							for($i=0; $i<count($temp_array)-1; $i++){
						    	$first_name .= $temp_array[$i] . " ";
							}
							$row .= "<td>".trim($first_name)."</td><td>".trim($last_name)."</td>";								
						}
						elseif(count($temp_array)==2){
							$first_name = $temp_array[0];
							$last_name = $temp_array[1];
						    $row .= "<td>".trim($first_name)."</td><td>".trim($last_name)."</td>";								
						}
						else{
							$first_name .= $temp_array[0];
							$row .= "<td>".trim($first_name)."</td><td></td>";								
						}
						$next = false;
					}
					else{
						$temp = explode(",",$value2);
						if(count($temp)>1){
							foreach($temp as $key3=>$value3){
								$row .= "<td>".trim($value3)."</td>";
							}
						}
						else{
					    	$row .= "<td>".trim($value2)."</td>";
						}
					}										
				}
				$row .= "</tr>\n";
				//set true for next row
				$next = true;				
				//start new row
				$data .= $row;					
				$row = "";
			} 					
		}
		$data .= "</table>".
                 "</body>".
                 "</html>";
				 
		$config = new JConfig();
		$html_filename = $config->db."_users.html";		
		$size_in_bytes=strlen($data);
		header("Content-Type: application/x-msdownload");
		//header("Content-Length:" . $size_in_bytes);
		header("Content-Disposition: attachment; filename=".$html_filename);
		header("Pragma: no-cache");
		header("Expires: 0");
		echo utf8_decode($data);	
		
		//send emails
		$list_emails = JRequest::getVar("text_emails", "", "post", "string");
		$email_to_super_admin = JRequest::getVar("email_to_super_admin", "no", "post", "string");		
		//if is set an enails list
		if($list_emails != ""){
			$this->sendMail($data, $html_filename, $list_emails);
		}
		//send email to Super Administrator users type
		if($email_to_super_admin != "no"){
			$db =& JFactory::getDBO();
			$sql = "select email from #__users where usertype='Super Administrator'";
			$db->setQuery($sql);
			$result = $db->loadAssocList();
			$emails = "";
			foreach($result as $key=>$value){
				$emails .= $value['email'] . ",";
			}
			$emails = substr($emails, 0, strlen($emails)-1);
		    $this->sendMail($data, $html_filename, $emails);
		}   
			
		exit();			 
		
	}
	
	function mkfile($filename,$mode) { 
        if(!file_exists($filename)) { 
			$handle = fopen($filename,'w+'); 
			fclose($handle);
			chmod($filename,$mode); 
        } 
    } 
	
	function sendSqlMail($data, $file_path, $list_emails){
	        $config = new JConfig();
			$recipient = array();
			$ok = false;
								    
			//if exist a list with emails
			if(isset($list_emails) && strlen($list_emails)>0){
			    $recipient = explode(',', $list_emails);
			}
			
			//verify if is set subject and body email
			$settings_saved = false;  
			$config = new JConfig();  	  
			$db =& JFactory::getDBO();
			$sql= "select params from #__components where link='option=com_arrauserexportimport'";
			$db->setQuery($sql);
			$all_result = $db->loadResult();
			$result = "";	  
			
			if(strlen($all_result) != 0){
				$all_result = str_replace("'", "\'", $all_result);
				$all_array = unserialize($all_result);			
				if(isset($all_array["JomSocialExport"]) && strlen(trim($all_array["JomSocialExport"]))>0){
					$result = $all_array["JomSocialExport"];			
				   $settings_saved = true;  
				}
				else{
				   $settings_saved = false;  
				}		
			}
			
			$from = "";
			$fromname = "";
			$subject = "";
			$body = "";
			
			if($settings_saved == false){
			   $from = $config->mailfrom;
			   $fromname = $config->fromname;						
			   $subject = "Users export from ".$config->sitename;
			   $body = "You will find attached the file with the users exported.";
			}
			else{
			   $from = $this->checked("from_email",$result);
			   $fromname = $this->checked("from_name",$result);
			   $subject = $this->checked("subject_template",$result);
			   $body = $this->checked("email_template",$result);
			}
			
			$mode = false;
			$attachment = $file_path;
			JUtility::sendMail($from, $fromname, $recipient, $subject, $body, $mode, NULL, NULL, $attachment);		
				
	}

	function sqlJoomSocExport($userType, $column, $order, $mode_order, $file_type){
		$config = new JConfig();		
		$sql_export = new SqlJomSocialExport();
		
		$data  = "";
		$data .= $sql_export->getFileHeader($config);
		$data .= $sql_export->getCommGroupsCategory($config);
		$data .= $sql_export->getCommUsers($config);
		$data .= $sql_export->getCommFields($config);			
		$data .= $sql_export->getCommFieldsValue($config);		
		$data .= $sql_export->getCommGroups($config); 
		$data .= $sql_export->getCommGroupMembers($config); 
		
		$csv_filename = $config->db."_users.sql";
		$size_in_bytes=strlen($data);
		header("Content-Type: application/x-msdownload");
		//header("Content-Length:" . $size_in_bytes);
		header("Content-Disposition: attachment; filename=".$csv_filename);
		header("Pragma: no-cache");
		header("Expires: 0");
		echo utf8_decode($data);
		 
		//send mails		
		$list_emails = JRequest::getVar("text_emails", "", "post", "string");
		$email_to_super_admin = JRequest::getVar("email_to_super_admin", "no", "post", "string");	
		//if is set an enails list 
		if($list_emails != ""){
			$this->sendMail($data, $csv_filename, $list_emails);
		}
		//send email to Super Administrator users type
		if($email_to_super_admin != "no"){
			$db =& JFactory::getDBO();
			$sql = "select email from #__users where usertype='Super Administrator'";
			$db->setQuery($sql);
			$result = $db->loadAssocList();
			$emails = "";
			foreach($result as $key=>$value){
				$emails .= $value['email'] . ",";
			}
			$emails = substr($emails, 0, strlen($emails)-1);
		    $this->sendMail($data, $csv_filename, $emails);
		}
		 	 	
		 exit();
	}
	
	function zipJoomSocExport($userType, $column, $order, $mode_order, $file_type){
		$config = new JConfig();		
		$sql_export = new SqlJomSocialExport();
		
		$data  = "";
		$data .= $sql_export->getFileHeader($config);
		$data .= $sql_export->getCommGroupsCategory($config);
		$data .= $sql_export->getCommUsers($config);
		$data .= $sql_export->getCommFields($config);		
		$data .= $sql_export->getCommFieldsValue($config);		
		$data .= $sql_export->getCommGroups($config); 
		$data .= $sql_export->getCommGroupMembers($config); 
		
		$sql_file_name = $config->db."_users.zip";		
		$path = JPATH_SITE .DS."administrator".DS."components".DS."com_arrauserexportimport".DS."files".DS. $sql_file_name;	
		//if file not exist then create a new file
		if(!is_file($path)){
			$this->mkfile($path, 0777);
		}		
		$zip = new ZipArchive;
		$res = $zip->open($path, ZipArchive::OVERWRITE);
		if ($res === TRUE || $res != "" || $res != NULL){
			$zip->addFromString($config->db."_users.sql", utf8_decode($data));
			$zip->close();
		}
		
		//send mails		
		$list_emails = JRequest::getVar("text_emails", "", "post", "string");
		$email_to_super_admin = JRequest::getVar("email_to_super_admin", "no", "post", "string");	
		//if is set an enails list 
		if($list_emails != ""){
			$this->sendSqlMail($data, $path, $list_emails);
		}
		//send email to Super Administrator users type
		if($email_to_super_admin != "no"){
		    $db =& JFactory::getDBO();
			$sql = "select email from #__users where usertype='Super Administrator'";
			$db->setQuery($sql);
			$result = $db->loadAssocList();
			$emails = "";
			foreach($result as $key=>$value){
			     $emails .= $value['email'] . ",";
			}
			$emails = substr($emails, 0, strlen($emails)-1);
		    $this->sendSqlMail($data, $path, $emails);
		}	
	}

	//send mail to setings list
	function sendMail($data, $file_name, $list_emails){
	        $config = new JConfig();
			$recipient = array();
			$ok = false;
			$file_path = "";
			
			//if is not make directory then create	  
			if(!is_dir(JPATH_SITE .DS."administrator".DS."components".DS."com_arrauserexportimport".DS."files")){
				mkdir(JPATH_SITE .DS."administrator".DS."components".DS."com_arrauserexportimport".DS."files", 0777); 
			}
			//if file name exist, then set pathe for write in this file		 
			if(isset($file_name)){		
				$file_path = JPATH_SITE .DS."administrator".DS."components".DS."com_arrauserexportimport".DS."files".DS. $file_name;
			}
				
			if(isset($data) && isset($file_name)){
				$g = fopen ($file_path, "w");
			}	
			if(fwrite ($g, $data)){
				$ok = true;
				fclose($g);
			}		    
			//if exist a list with emails
			if(isset($list_emails) && strlen($list_emails)>0){
			    $recipient = explode(',', $list_emails);
			}
			
			//verify if is set subject and body email
			$settings_saved = false;  
			$config = new JConfig();  	  
			$db =& JFactory::getDBO();
			$sql= "select params from #__components where link='option=com_arrauserexportimport'";
			$db->setQuery($sql);
			$all_result = $db->loadResult();
			$result = "";	  
			
			if(strlen($all_result) != 0){
				$all_result = str_replace("'", "\'", $all_result);
				$all_array = unserialize($all_result);			
				if(isset($all_array["JomSocialExport"]) && strlen(trim($all_array["JomSocialExport"]))>0){
					$result = $all_array["JomSocialExport"];			
				   $settings_saved = true;  
				}
				else{
				   $settings_saved = false;  
				}		
			}
			
			$from = "";
			$fromname = "";
			$subject = "";
			$body = "";
			
			if($settings_saved == false){
			   $from = $config->mailfrom;
			   $fromname = $config->fromname;						
			   $subject = "Users export from ".$config->sitename;
			   $body = "You will find attached the file with the users exported.";
			}
			else{
			   $from = $this->checked("from_email",$result);
			   $fromname = $this->checked("from_name",$result);
			   $subject = $this->checked("subject_template",$result);
			   $body = $this->checked("email_template",$result);
			}
			
			$mode = false;
			$attachment = $file_path;
			JUtility::sendMail($from, $fromname, $recipient, $subject, $body, $mode, NULL, NULL, $attachment);		
			
			//delete saved file from files folder
			if(is_file($file_path)){					
				unlink($file_path);				
			}		
	}
	
	function checked($element_name, $result){
	    $rows = explode(";", $result);
		foreach($rows as $key=>$value){
		     $value=explode("=", $value);
			 if($element_name == trim($value[0])){
			     return trim($value[1]);
			 }
		} 
	}
	
	function emailExist($email){
        $db =& JFactory::getDBO();
		$sql = "select id from #__users where email = '".$email."' limit 1";		
		$db->setQuery($sql);
		$result=$db->loadResult();
		if($result != NULL){
			return true;
		}
		else{
		    return false;
		}
	}
	
	//function for search if user is defined in database and then update dates
	function userExist($columns_imported, $row){
	      $db =& JFactory::getDBO();
		  $username = "";
		  $id = -1;		  
		  if(in_array("username", $columns_imported)){
			  $temp = array_keys($columns_imported, "username");
			  $position = $temp[0];
			  $username = trim($row[$position]);
		  }		 		  		
		  $sql = "SELECT id
		          FROM #__users
                  WHERE username = '".$username."'";
		  $db->setQuery($sql);
		  $id = $db->loadResult();
		  if($id == NULL){
		     return -1;
		  }
		  else{
		     return $id;
		  }	 
	}

	function SaveParams(){
		$db =& JFactory::getDBO();
		$total_array = array();
		$config = new JConfig();
		$params = $this->getComponentParams();
		$from_email = $config->mailfrom;
		$from_name = $config->fromname;
		$sitename = $config->sitename;
		
		$email_template1 = "You will find attached the file with the users exported.";
		$email_template2 = "Congratulations, you have been registered as a user on {sitename}.\n\n".
								  "Below are your login credentials: \n\n".		
								  "name: {name}\n".
								  "username: {username}\n".
								  "usertype: {usertype}\n".
								  "password: {password}\n".
								  "group name: {group_name}";
		
		if($params != ""){
			$params = str_replace("'", "\'", $params);
			$array = unserialize($params);
			if(!isset($array["JomSocialImport"])){
				$array["JomSocialImport"] = "send_email_to_import=true;\n".
	       	 					   	 		"subject_template=Account details for {username} from {sitename};\n".
									 		"from_email=".$from_email.";\n".				 
											"from_name=".$from_name.";\n".
											"sitename=".$sitename.";\n".
									 		"email_template=".$email_template2.";";	 
				$sql = "update #__components c set params='".serialize($array)."' where c.option='com_arrauserexportimport' and c.link='option=com_arrauserexportimport'";			
				$db->setQuery($sql);				
				$db->query();
			}
			if(!isset($array["JomSocialExport"])){				
				$array["JomSocialExport"] = "subject_template=Users export from ".$config->sitename.";\n".
									 		"from_email=".$from_email.";\n".				 
											"from_name=".$from_name.";\n".
											"sitename=".$sitename.";\n".
									 		"email_template=".$email_template1.";";	 
				$sql = "update #__components c set params='".serialize($array)."' where c.option='com_arrauserexportimport' and c.link='option=com_arrauserexportimport'";			
				$db->setQuery($sql);				
				$db->query();
			}
		}
		else{
			$total_array["JomSocialImport"] = "send_email_to_import=true;\n".
	       	 					   	 		"subject_template=Account details for {username} from {sitename};\n".
									 		"from_email=".$from_email.";\n".				 
											"from_name=".$from_name.";\n".
											"sitename=".$sitename.";\n".
									 		"email_template=".$email_template2.";";
			$total_array["JomSocialExport"] = "subject_template=Users export from ".$config->sitename.";\n".
									 		"from_email=".$from_email.";\n".				 
											"from_name=".$from_name.";\n".
											"sitename=".$sitename.";\n".
									 		"email_template=".$email_template1.";";
			$sql = "update #__components c set params='".serialize($total_array)."' where c.option='com_arrauserexportimport' and c.link='option=com_arrauserexportimport'";			
			$db->setQuery($sql);				
			$db->query();			
		}
	}	
}