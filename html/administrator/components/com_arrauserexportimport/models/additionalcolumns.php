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
 * file: import.php
 *
 **** class 
     ArrausersexportimportModelAdditionalcolumns 
	 
 **** functions
     __construct();
	 import();
	 csv_txtImport();
	 userExist();
	 getUserType();
	 sql_zipImport();
	 emailExist();
	 collback();
	 saveOldSuperAdministrator();
	 existRowAro();
	 existRowUsers();
	 existRowGroups();
	 backUp();
	 mkfile();
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');
require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'AdditionalTxtImport.php');

/**
 * ArrausersexportimportModelImport Model
 */
class ArrausersexportimportModelAdditionalcolumns extends JModel{
	/**
	 * @access	public
	 * @return	void
	 */
	function __construct(){		
		parent::__construct();
	}
	
	//set method for import end validate uploaded file
	function import(){
		//save in database all settings
		$overwrite_usertype = JRequest::getVar("same_user_option_radio_usertype","","post","string");
		$overwrite_password = JRequest::getVar("same_user_option_radio_password","","post","string");
		$overwrite_email = JRequest::getVar("same_user_option_radio_email","","post","string");
		$overwrite_block = JRequest::getVar("same_user_option_radio_block","","post","string");
		$password_encripted = JRequest::getVar("encripted_password_radio","","post","string");
		$generate_password = JRequest::getVar("generate_password_radio","","post","string");
		
		$subject_template = JRequest::getVar("subject_template","","post","string");
		$from_email = JRequest::getVar("from_email","","post","string");
		$from_name = JRequest::getVar("from_name","","post","string");
		$sitename = JRequest::getVar("sitename","","post","string");
		$email_template = JRequest::getVar("email_template","","post","string");
		$separator = JRequest::getVar("separator", ",", "post", "string");
		
		$db =& JFactory::getDBO();
		$sql = "select c.params from #__components c where c.option='com_arrauserexportimport' and c.link='option=com_arrauserexportimport'";
	    $db->setQuery($sql);
	    $content = $db->loadResult();
		
		$all_array = array();		
		if($content != NULL && strlen(trim($content))>0){
			$content = str_replace("'", "\'", $content);
			$all_array = unserialize($content);
		}
		
		$all_array["AdditionalColumns"] = "same_user_option_radio_usertype=".$overwrite_usertype.";".
								   	 "same_user_option_radio_password=".$overwrite_password.";".
								     "same_user_option_radio_email=".$overwrite_email.";".
								     "same_user_option_radio_block=".$overwrite_block.";".
								     "encripted_password_radio=".$password_encripted.";".
								     "generate_password_radio=".$generate_password.";".
								     "subject_template=".$subject_template.";".
								     "from_email=".$from_email.";".
								     "from_name=".$from_name.";".
								     "sitename=".$sitename.";".
									 "separator=".addslashes($separator).";".
								     "email_template=".$email_template.";";		
									 
	    $sql = "update #__components c set params='".serialize($all_array)."' where c.option='com_arrauserexportimport' and c.link='option=com_arrauserexportimport'";
	    $db->setQuery($sql);
	    $db->query();			   	
		
		$file = JRequest::getVar('sqlzip_file_upload', NULL, 'files', 'array');
		if($file['name'] != ""){
			$file = JRequest::getVar('sqlzip_file_upload', NULL, 'files', 'array');
		}
		else{
			$file = JRequest::getVar('csvtxt_file_upload', NULL, 'files', 'array');
		}
		
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
				// apel functions for import
				$message = $this->$function($data, $fileNameOnServer);
			}
		}
		return $message;		
	}
						
	function existRowGroups($id, $column_to_check, $value, $table){
		$db =& JFactory::getDBO();		
		$sql = "select count(*) from #__".$table." where id='".$id."' and ".$column_to_check."='".$value."'";
		$db->setQuery($sql);
		$result = $db->loadResult();
		if($table=="core_acl_aro_groups"){
			if($result == NULL && $result == 0){
				return false;
			}
			else{
				return true;
			}
		}	
	}	
	
	//save data from txt file
	function csv_txtImport($data, $fileNameOnServer){
		$this->insertAdditionalColumns();
		$txtImport = new TxtImport();
		//evidence for header line and body lines
		$i = "header";
		$columns_imported = array();
		$all_user_type = array(); 
		$separator = JRequest::getVar("separator", ",", "post", "string");		
		$group_map_group_id = "18";// default registered
		$group_map_aro_id = "";	
		$users_existent = array();
		$empty_columns = array();
		$columns_type = array();
		
	    $fp = fopen($fileNameOnServer, 'r');
	    while(!feof($fp)){
            $all_user_type = $txtImport->getUserType();   	    
			$temp_row = fgets($fp);//line by line			
			//if we have a blank line
			if(strlen($temp_row)==0){
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
		    //if is first line(header line)
		    if($i == "header"){	
			    //convert from UTF-8 to ANSI
			    $temp_row = $temp_row;								
			    if(strpos(trim($temp_row), $separator) == false){
				    return "ERROR+".JText::_('ARRA_ERROR_UNKNOWN_SEPARATOR');
				}				
				
				$columns_imported = explode($separator, trim($temp_row));								
				$db =& JFactory::getDBO();
				$sql= "DESCRIBE #__users ";
				$db->setQuery($sql);
				$result = $db->loadAssocList();
				
				$array_all_columns = array();
				foreach($result as $key=>$value){
					 if($value['Field'] != "gid"){
						$array_all_columns[] = $value['Field'];
						$columns_type[$value['Field']] = $value["Type"];
					 }	
				}
								
				$unknown_fields = array();
				if(count($array_all_columns)!=0){
					foreach($columns_imported as $key=>$value){
					     //convert from UTF-8 to ANSI
						 $value = $value;
						 if(!in_array($value, $array_all_columns)){						    
							$unknown_fields[] = "'".$value."'";
						 }
					}
				}					
				if(isset($unknown_fields) && count($unknown_fields)!=0){
					$error_field_message = implode(",", $unknown_fields);
					return "ERROR+".JText::_('ARRA_ERROR_UNKNOWN_FIELDS').$error_field_message;
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
				$user_id_from_database = "-1"; 
				$gid = "";
				$value = "";
				$usertype = ""; 
				$row = explode($separator, $temp_row);		
				//if user is not in database(imported new user)
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
						$value = $txtImport->saveUsers($columns_imported, $row, $usertype, $gid, $columns_type);
									
						if(isset($value) && $value != ""){ 
							$group_map_aro_id = $txtImport->saveAclAro($value, $columns_imported, $row);
						}
											
						//to login in sistem must insert registered in _core_acl_groups_aro_map table
						if(isset($group_map_aro_id) && isset($group_map_group_id) && $group_map_aro_id != "" && $group_map_group_id != ""){					      
							$txtImport->saveInGroupsAroMap($group_map_group_id, $group_map_aro_id);
						}
						}//if email not exist in database
						else{
							$users_existent[] = $temp_row;
						}
				}
				//else, if imported existing user
				else{
					//make un update to existing user
					$update_message = $txtImport->updateUser($columns_imported, $row, $user_id_from_database, $columns_type);
					if($update_message == "empty_column"){
						$empty_columns[] = $temp_row;
					}
				}	
				$temp = "";//reset line
			}//else
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
	
	//return all user types from database
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
		
	//create a nuw file if not exist
	function mkfile($filename,$mode) { 
        if(!file_exists($filename)) { 
			$handle = fopen($filename,'w+'); 
			fclose($handle);
			chmod($filename,$mode); 
        } 
    } 	
	
	function getAdditionalColumns(){
		$db =& JFactory::getDBO();
		$sql = "DESCRIBE #__users ";
		$db->setQuery($sql);
		$result = $db->loadAssocList();		
		$array_all_columns = array();
		 	 	 	 	 	 	 	 	 	 	
		foreach($result as $key=>$value){
			 if($value['Field'] != "id" && $value['Field'] != "name" && $value['Field'] != "username" && $value['Field'] != "email" && $value['Field'] != "password" && $value['Field'] != "usertype" && $value['Field'] != "block" && $value['Field'] != "sendEmail" && $value['Field'] != "gid" && $value['Field'] != "registerDate" && $value['Field'] != "lastvisitDate" && $value['Field'] != "activation" && $value['Field'] != "params"){
				$array_all_columns[] = $value['Field'];
			 }	
		}
		return $array_all_columns;
	}
	
	function insertAdditionalColumns(){
		$existing_additional_columns = $this->getAdditionalColumns();
		$new_additional_columns = array();
		$db =& JFactory::getDBO();
		
		$number_of_columns = JRequest::getVar("number_columns", "0");
		for($i=1; $i<=$number_of_columns; $i++){
			$new_additional_columns[] = JRequest::getVar("column".$i);
		}
		
		foreach($new_additional_columns as $key=>$value){
			if(!in_array($value, $existing_additional_columns)){
				$sql = "ALTER TABLE #__users ADD COLUMN `".$value."` TEXT";
				$db->setQuery($sql);
				$db->query();
			}
		}		
	}
	
	function getFields(){
		$db =& JFactory::getDBO();
		$return = array();
		$sql = "SHOW COLUMNS FROM #__users";
		$db->setQuery($sql);
		$result = $db->loadResultArray();
		if(isset($result) && count($result) > 0){
			foreach($result as $key=>$column){
				if($column != 'id' && $column != 'name' && $column != 'username' && $column != 'email' && $column != 'password' && $column != 'usertype' && $column != 'block' && $column != 'sendEmail' && $column != 'gid' && $column != 'registerDate' && $column != 'lastvisitDate' && $column != 'activation' && $column != 'params'){
					$return[] = $column;
				}
			}
		}
		return $return;
	}
}

?>