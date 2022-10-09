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
     ArrausersexportimportModelImport 
	 
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
require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'TxtImport.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'SqlExport.php');

/**
 * ArrausersexportimportModelImport Model
 */
class ArrausersexportimportModelImport extends JModel{
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
		$overwrite_params = JRequest::getVar("same_user_option_radio_params","","post","string");
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
			$all_array = unserialize($content);
		}
		
		$all_array["JoomlaImport"] =   "same_user_option_radio_usertype=".$overwrite_usertype.";".
									   "same_user_option_radio_password=".$overwrite_password.";".
									   "same_user_option_radio_email=".$overwrite_email.";".
									   "same_user_option_radio_block=".$overwrite_block.";".
									   "same_user_option_radio_params=".$overwrite_params.";".
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
	
	//save data from sql file
	function sql_zipImport($data_file, $fileNameOnServer){
		$db =& JFactory::getDBO();
		$data = "";
		$tables = array("core_acl_aro_groups", "users", "core_acl_aro", "core_acl_groups_aro_map");
		
		//save existing dates and then delete to import another dates.
		foreach($tables as $num=>$table){
			$sql= "DESCRIBE #__".$table;
			$db->setQuery($sql);
			$result = $db->loadAssocList();
			$array_all_columns = array();
			foreach($result as $key=>$value){
				$array_all_columns[] = $value['Field'];
			}
			
			$data .= $table."\n";
			$data .= implode(",", $array_all_columns)."\n";		
			
			$sql = "select * from #__".$table;
			$db->setQuery($sql);
			$result = $db->loadAssocList();
			foreach($result as $key_row=>$value_row){
				$current_row = ""; 
				foreach($array_all_columns as $key_column=>$value_column){
					$col_val = $value_row[$value_column];
					$col_val = str_replace("\n", "", $col_val);
					$current_row .= $col_val.",";
				}
				$temp = substr($current_row, 0, strlen($current_row)-1);
				$data .= $temp."\n";
			}
			$data .= "#####";
		}
						
		if($data != ""){
			foreach($tables as $num=>$table){
				$sql = "delete from #__".$table;
				$db->setQuery($sql);
				$db->query();
			}
		}	
		
		//return all create operations
		preg_match_all('/CREATE(.*);/msU',$data_file, $create);
		//return all insert operations
		preg_match_all('/INSERT(.*)\);/msU',$data_file, $insert);        		
		
		if(isset($create) && count($create)!=0){
			foreach($create[0] as $key=>$value){			    			   
				$db->setQuery($value);
				if(!$db->query()){
					return "ERROR+".JText::_('ARRA_ERROR_TO_CREATE');
				}
			}
		}
		if(isset($insert) && count($insert)!=0){		  
			foreach($insert[0] as $key=>$value){				
				$db->setQuery($value);
				if(!$db->query()){
					foreach($tables as $num=>$table){
						$sql = "delete from #__".$table;
						$db->setQuery($sql);
						$db->query();
					}
					$this->collback($data);
					return "ERROR+".JText::_('ARRA_ERROR_TO_INSERT');
				}
			}
			$this->saveOldSuperAdministrator($data);
			return "OK+".JText::_('ARRA_UPLOADED_FILE');
		}		
	}
	
	function collback($data){
		$db =& JFactory::getDBO();
		//insert old datas
		$all_tables = explode("#####", $data);
		$aray_new_user_id = array();
		$array_new_value_aro = array();
		
		foreach($all_tables as $k=>$group_datas){
			if($group_datas != ""){
				$rows = explode("\n", $group_datas);
				$table = trim($rows[0]);
				$table_columns = $rows[1];
				for($i=2; $i<count($rows)-1; $i++){								
					if($table=="core_acl_aro_groups"){
						$cells = explode(",", $rows[$i]);						
						$sql = "insert into #__core_acl_aro_groups values(".$cells[0].",".$cells[1].", '".$cells[2]."', ".$cells[3].", ".$cells[4].", '".$cells[5]."')";
						$db->setQuery($sql);
						$db->query();
					}
					if($table=="users"){
						$cells = explode(",", $rows[$i]);						
						$new_id = $this->existRowUsers($cells[0], $table);						
						$sql = "insert into #__users values(".$cells[0].", '".$cells[1]."', '".$cells[2]."', '".$cells[3]."', '".$cells[4]."', '".$cells[5]."', ".$cells[6].", ".$cells[7].", ".$cells[8].", '".$cells[9]."', '".$cells[10]."', '".$cells[11]."', '".$cells[12]."')";
						$db->setQuery($sql);
						$db->query();
						
					}
					if($table=="core_acl_aro"){
						$cells = explode(",", $rows[$i]);
						$cells = explode(",", $rows[$i]);
						$sql = "insert into #__core_acl_aro values(".$cells[0].", '".$cells[1]."', '".$cells[2]."', ".$cells[3].", '".$cells[4]."', ".$cells[5].")";
						$db->setQuery($sql);
						$db->query();							
					}//if
					if($table=="core_acl_groups_aro_map"){																				
						$cells = explode(",", $rows[$i]);								
						$sql = "insert into #__core_acl_groups_aro_map values(".$cells[0].", '".$cells[1]."', ".$cells[2].")";
						$db->setQuery($sql);
						$db->query();							
					}//if						
				}//for
			}//if				
		}//foreach
	}
	
	function saveOldSuperAdministrator($data){
		$db =& JFactory::getDBO();
		//insert old datas
		$all_tables = explode("#####", $data);
		$aray_new_user_id = array();
		$array_new_value_aro = array();
		$id_user_to_delete = array();
		$value_to_delete = array();
		
		foreach($all_tables as $k=>$group_datas){
			if($group_datas != ""){
				$rows = explode("\n", $group_datas);
				$table = trim($rows[0]);
				$table_columns = $rows[1];
				for($i=2; $i<count($rows)-1; $i++){								
					if($table=="core_acl_aro_groups"){
						$cells = explode(",", $rows[$i]);
						if($this->existRowGroups($cells[0], "name", $cells[2], $table) == false){
							$sql = "insert into #__core_acl_aro_groups values(".$cells[0].", ".$cells[1].", '".$cells[2]."', ".$cells[3].", ".$cells[4].", '".$cells[5]."')";
							$db->setQuery($sql);
							$db->query();
						}
					}
					if($table=="users"){
						$cells = explode(",", $rows[$i]);
						if($this->existSuperAdmin($cells[2],$cells[3]) == false){					
							$new_id = $this->existRowUsers($cells[0], $table);
							if($new_id == -1){
								$sql = "insert into #__users values(".$cells[0].", '".$cells[1]."', '".$cells[2]."', '".$cells[3]."', '".$cells[4]."', '".$cells[5]."', ".$cells[6].", ".$cells[7].", ".$cells[8].", '".$cells[9]."', '".$cells[10]."', '".$cells[11]."', '".$cells[12]."')";
								$db->setQuery($sql);
								$db->query();
							}
							else{
								$sql = "insert into #__users values(".$new_id.", '".$cells[1]."', '".$cells[2]."', '".$cells[3]."', '".$cells[4]."', '".$cells[5]."', ".$cells[6].", ".$cells[7].", ".$cells[8].", '".$cells[9]."', '".$cells[10]."', '".$cells[11]."', '".$cells[12]."')";
								$db->setQuery($sql);
								$db->query();
								$temp = $cells[0].",".$new_id;
								$aray_new_user_id[] = $temp;
							}
						}
						else{
							$id_user_to_delete[] = $cells[0];
						}	
					}
					if($table=="core_acl_aro"){
						$cells = explode(",", $rows[$i]);
						if(count($id_user_to_delete)== 0 || !in_array($cells[2], $id_user_to_delete)){
							if(count($aray_new_user_id)>0){
								$old_id = $cells[2];
								$new_id = "";
								foreach($aray_new_user_id as $key2=>$exist_new){
									$temp = explode(",", $exist_new);
									$old_id2 = $temp[0];
									$new_id2 = $temp[1];
									if($old_id2 == $old_id){
										$new_id = $new_id2;
										break; 
									}
								}
								$rows[$i] = str_replace(",".$old_id.",", ",".$new_id.",", $rows[$i]);
								$id_new = $this->existRowAro($cells[0], $table);
								if($id_new == -1){
										$cells = explode(",", $rows[$i]);
										$sql = "insert into #__core_acl_aro values(".$cells[0].", '".$cells[1]."', '".$cells[2]."', ".$cells[3].", '".$cells[4]."', ".$cells[5].")";
										$db->setQuery($sql);
										$db->query();
									}
									else{
										$cells = explode(",", $rows[$i]);
										$val = $cells[0].",".$id_new;
										$array_new_value_aro[] = $val;
										$sql = "insert into #__core_acl_aro values(".$id_new.", '".$cells[1]."', '".$cells[2]."', ".$cells[3].", '".$cells[4]."', ".$cells[5].")";
										$db->setQuery($sql);
										$db->query();
									}
							}
							else{
								$sql = "insert into #__core_acl_aro values(".$cells[0].", '".$cells[1]."', '".$cells[2]."', ".$cells[3].", '".$cells[4]."', ".$cells[5].")";
								$db->setQuery($sql);
								$db->query();
							}
						}
						else{
							$value_to_delete[] = $cells[2];
						}	
					}//if
					if($table=="core_acl_groups_aro_map"){					
						$cells = explode(",", $rows[$i]);
						if(count($value_to_delete) == 0 || !in_array($cells[2], $value_to_delete)){
							if(count($array_new_value_aro)>0){
								foreach($array_new_value_aro as $key2=>$exist_new){
									$temp = explode(",", $exist_new);
									$old_id = $temp[0];
									$new_id = $temp[1];							
									$rows[$i] = str_replace(",".$old_id, ",".$new_id, $rows[$i]);														
									$cells = explode(",", $rows[$i]);								
									$sql = "insert into #__core_acl_groups_aro_map values(".$cells[0].", '".$cells[1]."', ".$cells[2].")";
									$db->setQuery($sql);
									$db->query();
								}
							}
							else{						
								$sql = "insert into #__core_acl_groups_aro_map values(".$cells[0].", '".$cells[1]."', ".$cells[2].")";
								$db->setQuery($sql);
								$db->query();
							}
						}	
					}//if						
				}//for
			}//if				
		}//foreach
	}
	
	function existSuperAdmin($username, $email){
		$db =& JFactory::getDBO();		
		$sql = "select id from #__users where username='".$username."' and email='".$email."'";
		$db->setQuery($sql);
		$result = $db->loadResult();
		if($result != NULL){
			return true;
		}
		else{
			return false;
		}
	}
	
	function existRowAro($id, $table){
		$db =& JFactory::getDBO();		
		$sql = "select count(*) from #__".$table." where id='".$id."'";
		$db->setQuery($sql);
		$result = $db->loadResult();
		if($result == NULL && $result == 0){
			return -1;
		}
		else{
			$sql = "select max(id) from #__".$table;
			$db->setQuery($sql);
			$result = $db->loadResult();
			return $result+1;
		}
	}
	
	function existRowUsers($id, $table){
		$db =& JFactory::getDBO();		
		$sql = "select count(*) from #__".$table." where id='".$id;
		$db->setQuery($sql);
		$result = $db->loadResult();
		if($result == NULL && $result == 0){
			return -1;
		}
		else{
			$sql = "select max(id) from #__".$table;
			$db->setQuery($sql);
			$result = $db->loadResult();
			return $result+1;
		}
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
		$txtImport = new TxtImport();
		//evidence for header line and body lines
		$i = "header";
		$columns_imported = array();
		$all_user_type = $txtImport->getUserType();
		$separator = JRequest::getVar("separator", ",", "post", "string");		
		$group_map_group_id = "18";// default registered
		$group_map_aro_id = "";	
		$users_existent = array();
		$empty_columns = array();
		
	    $fp = fopen($fileNameOnServer, 'r');
	    while(!feof($fp)){   	    
			$temp_row = fgets($fp);//line by line			
			//if we have a blank line
			if(strlen(trim($temp_row))==0 || ((strpos($temp_row, ",,") == 0 && strpos($temp_row, ",,") !== FALSE) && $i != "header")){
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
						$value = $txtImport->saveUsers($columns_imported, $row, $usertype, $gid);								
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
					$update_message = $txtImport->updateUser($columns_imported, $row, $user_id_from_database);
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
		
	function backUp(){
		$config = new JConfig();		
		$sql_export = new SqlExport();		
		
		$data  = "";
		//return header of exported file 
		$data .= $sql_export->getFileHeader($config);
		//return create and insert dates in core_acl_aro_groups table
		$data .= $sql_export->getCoreAclAroGroups($config);
		//return create and insert dates in users table
		$data .= $sql_export->getUsers($config);
		//return create and insert dates in core_acl_aro table
		$data .= $sql_export->getCoreAclAro($config);
		//return create and insert dates in core_acl_groups_aro_map table
		$data .= $sql_export->getCoreAclGroupsAroMap($config); 
		
		$sql_file_name = $config->db."_usersBK.zip";		
		$path = JPATH_SITE .DS."administrator".DS."components".DS."com_arrauserexportimport".DS."files".DS. $sql_file_name;	
		//if file not exist then create a new file
		if(!is_file($path)){	  
			$this->mkfile(JPATH_SITE .DS."administrator".DS."components".DS."com_arrauserexportimport".DS."files".DS. $sql_file_name, 0777);
		}		
		$zip = new ZipArchive;
		$res = $zip->open($path, ZipArchive::OVERWRITE);
		if ($res === TRUE) {
			$zip->addFromString($config->db."_usersBK.sql", $data);
			$zip->close();
		}		
	}	
}