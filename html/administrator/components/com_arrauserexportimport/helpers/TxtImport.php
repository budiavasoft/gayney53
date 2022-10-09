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
 * file: TxtImport.php
 *
 **** class 
     TxtImport 
	 
 **** functions
     __construct();
	 getUserType();
	 saveInGroup();
	 saveUsers();
	 saveAclAro();
	 sendEmail();
	 getIDUserType();
	 saveInGroupsAroMap();
	 updateUser();
	 generatePassword();
	 processText();
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * TxtImport class
 */
class TxtImport{
     /**
	 * @access	public
	 * @return	void
	 */
	function __construct(){		
	}
	
	//return all users type
	function getUserType(){
	    $db =& JFactory::getDBO();		
		$sql = "SELECT DISTINCT name
				FROM #__core_acl_aro_groups";
		$db->setQuery($sql);
		$result = $db->loadResultArray();
		return $result;
	}
	
	//to login in sistem must insert registered in _core_acl_groups_aro_map table
	//save datas in datrabase
	function saveInGroupsAroMap($group_map_group_id, $group_map_aro_id){
	     $db =& JFactory::getDBO();
	     $sql = "";
		 $sql .= "insert into #__core_acl_groups_aro_map (`group_id`, `section_value`, `aro_id` ) values (";
		 $sql .= $group_map_group_id . ", '', " . $group_map_aro_id . ")";
    	 $db->setQuery($sql);
		 if($db->query()){		    
		 }
		 else{
		 } 
	}
	
	//get usertype's id
	function getIDUserType($usertype){
	    $id = "";
	    $db =& JFactory::getDBO();		
		$sql = "SELECT id
				FROM #__core_acl_aro_groups
				WHERE name = '" . $usertype . "' limit 1";
		$db->setQuery($sql);
		$id .= $db->loadResult();
		return $id;
	}
	
	//save user in core_acl_aro table
	function saveAclAro($value, $columns_imported, $row){    
	    $db =& JFactory::getDBO();	
		$id = "";
	    $temp = array_keys($columns_imported, "name");
	    $position = $temp[0];
	    $name = trim($row[$position]);		
		$sql = "";
		$sql .= "insert into #__core_acl_aro(`section_value`, `value`, `order_value`, `name`, `hidden`) values (";
		$sql .= "'users', '" . $value . "', 0, '" . addslashes($name) . "', 0)";		
		$db->setQuery($sql);
				
		if($db->query()){
			$sql = "select id from #__core_acl_aro where value='" . $value . "' and name='" . addslashes($name) ."' limit 1";
			$db->setQuery($sql);
			$id = $db->loadResult();	   
		}
		return $id;
	}
	
	//save row in _users table
	function saveUsers($columns_imported, $row, $usertype, $gid, $from_jomsocial=""){	   	
	    $sql = "";
		$position = "";
		$id = "";
		$db =& JFactory::getDBO();
		//name and username is for last select, for return id.
		$username = "";
		$name = "";
		$password = "";
		$email = "";		
		$sql .= "insert into #__users(`name`, `username`, `email`, `password`, `usertype`, `block`, `sendEmail`, `gid`, `registerDate`,	`lastvisitDate`, `activation`, `params`) values (";		
		if(in_array("name", $columns_imported)){
			$temp = array_keys($columns_imported, "name");
			$position = $temp[0];
			$sql .= "'" . addslashes(trim($row[$position])) . "', ";
			$name .= trim($row[$position]);
		}
		
		if(in_array("username", $columns_imported)){
			$temp = array_keys($columns_imported, "username");
			$position = $temp[0];			
			$sql .= "'" . addslashes(trim($row[$position])) . "', ";
			$username .= trim($row[$position]);
		}
		
		if(in_array("email", $columns_imported)){
			$temp = array_keys($columns_imported, "email");
			$position = $temp[0];
			$sql .= "'" . addslashes(trim($row[$position])) . "', ";
			$email .= trim($row[$position]);
		}
		
		if(in_array("password", $columns_imported)){
			$temp = array_keys($columns_imported, "password");
			$position = $temp[0];
			$encripted_password = JRequest::getVar("encripted_password_radio","1","post","string");
			
			if($encripted_password != 0){
			    if(trim($row[$position]) == ""){
				    $default_password = JRequest::getVar("default_password","","post","string");				   
					$sql .= "'".$this->encriptPassword(trim($default_password))."', ";
				}
				else{	
					$sql .= "'".$this->encriptPassword(trim($row[$position]))."', ";			
				}	
			}
			else{
				if(trim($row[$position]) == ""){
				    $default_password = JRequest::getVar("default_password","","post","string");
					if($default_password != ""){			   
						$sql .= "'" . trim($default_password) . "', ";
					}
					else{
						$password_gen = $this->generatePassword();
						$sql .= "'".$this->encriptPassword(trim($password_gen)) . "', ";
					}	
				}
				else{	
					$sql .= "'" . trim($row[$position]) . "', ";			
				}
			}
			if(trim($row[$position]) == ""){
				$default_password = JRequest::getVar("default_password","","post","string");
				if($default_password != ""){			   
					$password .= trim($default_password);
				}
				else{
					$password_gen = $this->generatePassword();
					$password .= trim($password_gen);
				}	
			}
			else{	
				$password .= trim($row[$position]);			
			}
		}
		else{
			$default_password = JRequest::getVar("default_password","","post","string");
			$generate_password = JRequest::getVar("generate_password_radio","1","post","string");
			$encripted_password = JRequest::getVar("encripted_password_radio","1","post","string");
			 //if generate new password
			if($default_password != ""){
				if($encripted_password != 0){
					$sql .= "'".$this->encriptPassword(trim($default_password))."', ";
				}
				else{
					$sql .= "'" . trim($default_password) . "', ";
				}	
				$password .= trim($default_password);				 
			}
			else{
				if($generate_password != 1){
					$password .= $this->generatePassword();
					if($encripted_password != 0){
						$sql .= "'".$this->encriptPassword(trim($password))."', ";
					}
					else{
					 	$sql .= "'".trim($password)."', ";
					}
				}
			}			 
		}
		
		if(in_array("usertype", $columns_imported)){
			$temp = array_keys($columns_imported, "usertype");
			$position = $temp[0];
			if(trim($row[$position])== ""){
			    $sql .= "'" . addslashes($usertype) . "', ";
			}
			else{
				$sql .= "'" . addslashes(trim($row[$position])) . "', ";
			}	
		}
		else{
			$sql .= "'" . addslashes($usertype) . "', "; 
		}
		
		if(in_array("block", $columns_imported)){
			$temp = array_keys($columns_imported, "block");
			$position = $temp[0];
			if(trim($row[$position]) != ""){
				$sql .= trim($row[$position]) . ", ";
			}
			else{
				$sql .= "0" . ", ";
			}
		}
		else{
			$sql .= "0" . ", ";
		}
		
		if(in_array("sendEmail", $columns_imported)){
			$temp = array_keys($columns_imported, "sendEmail");
			$position = $temp[0];
			if(trim($row[$position]) != ""){
				$sql .= trim($row[$position]) . ", ";
			}
			else{
				$sql .= "0" . ", ";
			}
		}
		else{
			$sql .= "0" . ", ";
		}
		
		if(in_array("gid", $columns_imported)){
			$temp = array_keys($columns_imported, "gid");
			$position = $temp[0];
			if(trim($row[$position]) != ""){
				$sql .= trim($row[$position]) . ", ";
			}
			else{
				$sql .= $gid . ", ";
			}
		}
		else{
			$sql .= $gid . ", ";
		}
		
		if(in_array("registerDate", $columns_imported)){
			$temp = array_keys($columns_imported, "registerDate");
			$position = $temp[0];
			if(trim($row[$position]) != ""){
				$sql .= "'".trim($row[$position]). "', ";
			}
			else{
				$date = date("Y-m-d G:i:s");
				$sql .= "'" . $date . "', ";
			}
		}
		else{
			$date = date("Y-m-d G:i:s");
			$sql .= "'" . $date . "', ";
		}
		
		if(in_array("lastvisitDate", $columns_imported)){
			$temp = array_keys($columns_imported, "lastvisitDate");
			$position = $temp[0];
			if(trim($row[$position]) != ""){
				$sql .= "'".trim($row[$position]) . "', ";
			}
			else{
				$sql .= "'0000-00-00 00:00:00', ";
			}
		}
		else{
			$sql .= "'0000-00-00 00:00:00', ";
		}
		
		if(in_array("activation", $columns_imported)){
			$temp = array_keys($columns_imported, "activation");
			$position = $temp[0];
			if(strlen($row[$position]) == 0){
				$sql .= "''" . ", ";
			}
			else{
				$sql .= "'" . trim($row[$position]) . "', ";
			}	 
		}
		else{
			$sql .= "'', ";
		}
		
		if(in_array("params", $columns_imported)){
			$temp = array_keys($columns_imported, "params");
			$position = $temp[0];	
			if(strlen($row[$position]) == 0){
				$sql .= "''" . ", ";
			}
			else{
				$sql .= "'" . str_replace("***", "\r\n", trim($row[$position])) . "', ";
			}		 
			 
		}
		else{
			$sql .= "'', ";
		}
		
		$sql = substr($sql, 0, strlen($sql)-2);
		$sql .= ")";		
		$db->setQuery($sql);
		
		if($db->query()){
			$sql = "SELECT id " .
					"FROM #__users " .
					"WHERE name = '" . addslashes($name) . "' and username = '" . addslashes($username) . "' " .
					"LIMIT 1";
			$db->setQuery($sql);
			$id .= $db->loadResult();
			//if is set to send email to new users
			if(JRequest::getVar("send_email_to_import","1","post","string") != "1" && JRequest::getVar("encripted_password_radio","1","post","string") == "1" && $from_jomsocial == ""){	 
				$this->sendEmail($name, $username, $email, $password, $usertype);
			}
			if($from_jomsocial != ""){
				$temp_array = array();
				$temp_array["id"] = $id;
				$temp_array["name"] = $name;
				$temp_array["username"] = $username;
				$temp_array["email"] = $email;
				$temp_array["password"] = $password;
				$temp_array["usertype"] = $usertype;
				return $temp_array;
			}
		}
		return $id;
	}		
	
	//save user in _core_acl_aro_group table
	function saveInGroup($row, $position="", $usertype=""){
	      $parent_id = "";
		  $gid = "";
		  //make new user registered, and select parent_id for new insert command
		  $db =& JFactory::getDBO();		
		  $sql = "SELECT parent_id
		          FROM #__core_acl_aro_groups
                  WHERE name = 'Registered' limit 1";
		  $db->setQuery($sql);
		  $parent_id .= $db->loadResult();
		  //insert in database new user group
		  if($position != ""){
				$sql = "insert into #__core_acl_aro_groups(`parent_id`, `name`, `lft`, `rgt`, `value`)
						values(" . $parent_id . ", '" . trim($row[$position]) . "', 4, 11, '" . trim($row[$position]) . "')";
		  }
		  else{
				$sql = "insert into #__core_acl_aro_groups(`parent_id`, `name`, `lft`, `rgt`, `value`)
						values(" . $parent_id . ", '" . trim($usertype) . "', 4, 11, '" . trim($usertype) . "')";
		  }			  
		  $db->setQuery($sql);
		  if($db->query()){
				//id selected will be gid in _users table
				if($position != ""){
					$sql = "SELECT id
							FROM #__core_acl_aro_groups
							WHERE name = '" . trim($row[$position]) . "' limit 1";
				}	
				else{
					$sql = "SELECT id
							FROM #__core_acl_aro_groups
							WHERE name = '" . trim($usertype) . "' limit 1";
				}		  
				$db->setQuery($sql);
				$gid .= $db->loadResult();		  
		  }
		  else{
		     echo "ERROR: saveInGroup";
		  }
		  return $gid;
	}
	
	function sendEmail($name, $username, $email, $password, $usertype){
	      $recipient = array(); 
		  $recipient[] = $email;
	      $from = JRequest::getVar("from_email", "", "post", "string");
		  $fromname = JRequest::getVar("from_name", "", "post", "string");
		  $sitename = JRequest::getVar("sitename", "", "post", "string");		  		  
		  $subject_mambot = JRequest::getVar("subject_template", "", "post", "string");
		  $body_mambot = JRequest::getVar("email_template", "", "post", "string");
		  
		  $subject_procesed = $this->processText($subject_mambot, $name, $username, $password, $usertype, $from, $fromname, $sitename); 
		  $body_procesed = $this->processText($body_mambot, $name, $username, $password, $usertype, $from, $fromname, $sitename);
		  $mode = false;		  
		  JUtility::sendMail($from, $fromname, $recipient, $subject_procesed, $body_procesed, $mode);	    
	}
	
	function processText($text, $name, $username, $password, $usertype, $from, $fromname, $sitename){		 
		if(preg_match("/{name}/", $text) ){
			$text = str_replace("{name}", $name, $text);
		}
		if(preg_match("/{username}/", $text) ){
			$text = str_replace("{username}", $username, $text);
		}
		if(preg_match("/{password}/", $text) ){
			$text = str_replace("{password}", $password, $text);
		}
		if(preg_match("/{usertype}/", $text) ){
			$text = str_replace("{usertype}", $usertype, $text);
		}
		if(preg_match("/{from_name}/", $text) ){
			$text = str_replace("{from_name}", $fromname, $text);
		}
		if(preg_match("/{sitename}/", $text) ){
			$text = str_replace("{sitename}", $sitename, $text);
		}
		if(preg_match("/{from_email}/", $text) ){
			$text = str_replace("{from_email}", $from, $text);
		}
		return $text;
	}
	
	function updateUser($columns_imported, $row, $user_id_from_database){							
	        $db =& JFactory::getDBO();
			$changes_array = array();
			$aro_id = ""; // if chenged gid from _users table then must change from _core_acl_group_map table
			$group_id = "";
			$change_all = JRequest::getVar("same_user_option_checkbox","no","post","string");
		    $sql = "";
		    $sql .= "update #__users set ";
			$change_usertype_or_gid = false; // if change usertype then doesn't change gid and viceversa			
			$name = "";
			$email = "";
			$username = "";
			$usertype = "";
			$password = "";
			$error_row = false;
			$change_password_send_email = false;
			
			if(in_array("password", $columns_imported)){
				$position = "";
				$encripted_password = JRequest::getVar("encripted_password_radio","1","post","string");
				//not change all, but if $change_password is checked then change the password
				if($change_all == "no"){
					$change_password = JRequest::getVar("same_user_option_radio_password","1","post","string");
					//if change password
					if(isset($change_password) && $change_password == 0){
						$temp = array_keys($columns_imported, "password");
						$position = $temp[0];						  
						if($encripted_password != 0){
						    if(trim($row[$position]) != ""){
								$changes_array[] = " password='".$this->encriptPassword(trim($row[$position]))."' ";
								$password = trim($row[$position]);
							}
							else{
								$error_row = true;
							}
						}
						else{
							if(trim($row[$position]) != ""){
								$changes_array[] = " password='".trim($row[$position])."' ";
								$password = trim($row[$position]);
							}
							else{
							   $error_row = true;
							}
						}					  
					}					 
				}
				else{					
					$temp = array_keys($columns_imported, "password");
					$position = $temp[0];
					if($encripted_password != 0){
						if(trim($row[$position]) != ""){
							$changes_array[] = " password='".$this->encriptPassword(trim($row[$position]))."' ";
							$password = trim($row[$position]);
						}
						else{
							$error_row = true;
						}
					}
					else{
						if(trim($row[$position]) != ""){
							$changes_array[] = " password='".trim($row[$position])."' ";
							$password = trim($row[$position]);
						}
						else{
						   $error_row = true;
						}
					}
				}				 	 			 
			}
			else{
				$change_password = JRequest::getVar("same_user_option_radio_password","1","post","string");
				if($change_all != "no" || (isset($change_password) && $change_password == 0)){								
					$default_password = JRequest::getVar("default_password","","post","string");
					$generate_password = JRequest::getVar("generate_password_radio","1","post","string");
					//if generate new password
					if($default_password != ""){
						$changes_array[] = " password='".$this->encriptPassword(trim($default_password))."' ";
						$password = trim($default_password);										 
					}
					else{
						if($generate_password != 1){
							$password_gen = $this->generatePassword();
							$changes_array[] = " password='".$this->encriptPassword(trim($password_gen))."' ";
							$password = trim($password_gen);							
						}
					}
					$change_password_send_email = true;
				}
			}			
			
			if(in_array("name", $columns_imported)){
				if($change_all != "no"){
					$position = "";
					$temp = array_keys($columns_imported, "name");
					$position = $temp[0];				
					if(trim($row[$position]) != ""){
						$changes_array[] = " name='".addslashes(trim($row[$position]))."' ";
						//$password = trim($row[$position]);
						$name = trim($row[$position]);
					}
					else{
					   $error_row = true;
					}
				}
				else{
					$position = "";
					$temp = array_keys($columns_imported, "name");
					$position = $temp[0];				
					if(trim($row[$position]) != ""){
						$name = trim($row[$position]);
					}
				}					 	 			 
			}
			
			if(in_array("usertype", $columns_imported)){				
				//if not changed gid because changed gid					 		     
				if($change_usertype_or_gid == false){
					$db =& JFactory::getDBO();				 				  
					//not change all, but if $change_password is checked then change the password
					if($change_all == "no"){
						$change_usertype = JRequest::getVar("same_user_option_radio_usertype","1","post","string");
						//if change password
						if(isset($change_usertype) && $change_usertype == 0){
							$temp = array_keys($columns_imported, "usertype");							
							$position = $temp[0];
							$usertype = trim($row[$position]);
							$sql_2 = "select id from #__core_acl_aro_groups where name='".addslashes(trim($row[$position]))."'";
							$sql_3 = "select id from #__core_acl_aro where value=".$user_id_from_database;								  
							//change gid if changed usertype						  
							$db->setQuery($sql_2);
							$gid = $db->loadResult();	
							//if exist usertype from imported file							  
							if($gid != NULL){		     
								$temp = array_keys($columns_imported, "usertype");
								$position = $temp[0];
								if(trim($row[$position]) != ""){
									$changes_array[] = " usertype='".addslashes(trim($row[$position]))."' ";
									$changes_array[] = " gid=".trim($gid)." ";
									$usertype = trim($row[$position]);
									//for change the value from _core_acl_group_map
									$db->setQuery($sql_3);
									$aro_id = $db->loadResult();
									$group_id = trim($gid);
									//now is not important if doesn't change gid
									$change_usertype_or_gid = true;
								}
								else{
									$error_row = true;
								}	
							} 
							//else generate a new usertype and change in database
							else{
								$gid = $this->saveInGroup($row, "", trim($row[$position]));									
								$db->setQuery($sql_3);
								$aro_id = $db->loadResult();
								$group_id = trim($gid);
								if(trim($row[$position]) != ""){
									$changes_array[] = " usertype='".addslashes(trim($row[$position]))."' ";
									$changes_array[] = " gid=".trim($gid)." ";
									$change_usertype_or_gid = true;
									$usertype = trim($row[$position]);
								}
								else{
									$error_row = true;
								}	
							} 				  
						}														
					}
					else{
						$temp = array_keys($columns_imported, "usertype");
						$position = $temp[0];							 
						$sql_2 = "select id from #__core_acl_aro_groups where name='".addslashes(trim($row[$position]))."'";
						$sql_3 = "select id from #__core_acl_aro where value=".$user_id_from_database;
						$db->setQuery($sql_2);
						$gid = $db->loadResult();
						//if exist usertype from imported file
						if($gid != NULL){		     
							$temp = array_keys($columns_imported, "usertype");
							$position = $temp[0];
							if(trim($row[$position]) != ""){
								$changes_array[] = " usertype='".addslashes(trim($row[$position]))."' ";
								$usertype = trim($row[$position]);
								$changes_array[] = " gid=".trim($gid)." ";
								//for change the value from _core_acl_group_map
								$db->setQuery($sql_3);
								$aro_id = $db->loadResult();
								$group_id = trim($gid);
								//now is not important if doesn't change gid
								$change_usertype_or_gid = true;
							}
							else{
								$error_row = true;
							}	
						} 
						//else generate a new usertype and change in database
						else{
							$gid = $this->saveInGroup($row, "", trim($row[$position]));									
							$db->setQuery($sql_3);
							$aro_id = $db->loadResult();
							$group_id = trim($gid);
							if(trim($row[$position]) != ""){
								$changes_array[] = " usertype='".addslashes(trim($row[$position]))."' ";
								$usertype = trim($row[$position]);
								$changes_array[] = " gid=".trim($gid)." ";
								$change_usertype_or_gid = true;
							}
							else{
								$error_row = true;
							}
						}
					}						 				 						 
				}	 			 
			}			
			
			if(in_array("block", $columns_imported)){
				if($change_all == "no"){
					$change_block = JRequest::getVar("same_user_option_radio_block","1","post","string");
					//if change password
					if(isset($change_block) && $change_block == 0){
						$temp = array_keys($columns_imported, "block");
						$position = $temp[0];
						if(trim($row[$position]) != ""){
							$changes_array[] = " block=".trim($row[$position])." ";
						}
						else{
							$error_row = true;
						}	
					}
				}
				else{
					$temp = array_keys($columns_imported, "block");
					$position = $temp[0];
					if(trim($row[$position]) != ""){
						$changes_array[] = " block=".trim($row[$position])." ";
					}
					else{
						$error_row = true;
					}	
				}				 
			}
			
			if(in_array("email", $columns_imported)){
				$temp = array_keys($columns_imported, "email");
				$position = $temp[0];
				if($change_all == "no"){
					$change_email = JRequest::getVar("same_user_option_radio_email","1","post","string");
					if(isset($change_email) && $change_email == 0){						
						if(trim($row[$position]) != ""){
							$changes_array[] = " email='".addslashes(trim($row[$position]))."' ";							
						}
						else{
							$error_row = true;
						}
					}
				}
				else{					
					if(trim($row[$position]) != ""){
						$changes_array[] = " email='".addslashes(trim($row[$position]))."' ";
						$email = trim($row[$position]);
					}
					else{
						$error_row = true;
					}	
				}
				$email = trim($row[$position]);			 
			}
			
			if(in_array("sendEmail", $columns_imported)){
				if($change_all != "no"){
					$temp = array_keys($columns_imported, "sendEmail");
					$position = $temp[0];
					if(trim($row[$position]) != ""){
						$changes_array[] = " sendEmail=".trim($row[$position])." ";
					}
					else{
						$error_row = true;
					}
				}	  
			}
			
			if(in_array("gid", $columns_imported)){
				//if not changed usertype 
				if($change_usertype_or_gid == false){
					$aro_id = ""; // if chenged gid from _users table then must change from _core_acl_group_map table
					$db =& JFactory::getDBO();
					//if change all				 			
					if($change_all != "no"){
						$temp = array_keys($columns_imported, "gid");
						$position = $temp[0];
						if(trim($row[$position]) != ""){
							$changes_array[] = " gid=".trim($row[$position])." ";
								 
							$sql_2 = "select name from #__core_acl_aro_groups where id=".trim($row[$position])."'";
							$sql_3 = "select id from #__core_acl_aro where value=".$user_id_from_database; 
								 
							//change gid if changed usertype						  
							$db->setQuery($sql_2);
							$usertype = $db->loadResult();
							$changes_array[] = " usertype='".$usertype."' ";
							//for change the value from _core_acl_group_map
							$db->setQuery($sql_3);
							$aro_id = $db->loadResult();
							$group_id = trim($gid);
							$change_usertype_or_gid == true;
						}
						else{
							$error_row = true;
						}	
					}						 
				}		 	  
			}
			
			if(in_array("registerDate", $columns_imported)){
				if($change_all != "no"){
					$temp = array_keys($columns_imported, "registerDate");
					$position = $temp[0];
					if(trim($row[$position]) != ""){
						$changes_array[] = " registerDate='".trim($row[$position])."' ";
					}
					else{
						$error_row = true;
					}	
				}	  
			}
			
			if(in_array("lastvisitDate", $columns_imported)){
				if($change_all != "no"){
					$temp = array_keys($columns_imported, "lastvisitDate");
					$position = $temp[0];
					if(trim($row[$position]) != ""){
						$changes_array[] = " lastvisitDate='".trim($row[$position])."' ";
					}
					else{
						$error_row = true;
					}	
				}	  
			}
			
			if(in_array("activation", $columns_imported)){
				if($change_all != "no"){
					$temp = array_keys($columns_imported, "activation");
					$position = $temp[0];
					$changes_array[] = " activation='".trim($row[$position])."' ";
				}	  
			}
						
			if(in_array("params", $columns_imported)){
				$temp = array_keys($columns_imported, "params");
				$position = $temp[0];
				if($change_all == "no"){
					$change_params = JRequest::getVar("same_user_option_radio_params","1","post","string");
					if(isset($change_params) && $change_params == 0){						
						if(trim($row[$position]) != ""){
							$changes_array[] = " params='".addslashes(str_replace("***", "\r\n", trim($row[$position])))."' ";							
						}
						else{
							$error_row = true;
						}
					}
				}
				else{					
					if(trim($row[$position]) != ""){
						$changes_array[] = " params='".addslashes(str_replace("***", "\r\n", trim($row[$position])))."' ";
						$email = trim($row[$position]);
					}
					else{
						$error_row = true;
					}	
				}				
			}
						
			//make update
			if(is_array($changes_array) && !empty($changes_array)){				
				if($error_row==false){
					$changes = implode(",", $changes_array);
					$sql .= $changes. "where id=".$user_id_from_database;					
					$db->setQuery($sql);
					if($db->query() && $change_usertype_or_gid==true){
						$sql_1 = "update #__core_acl_groups_aro_map set group_id=".$group_id." where aro_id=".$aro_id;
						$db->setQuery($sql_1);
						$db->query();
					}
					if(JRequest::getVar("send_email_to_import","1","post","string") != "1" && JRequest::getVar("encripted_password_radio","1","post","string") == "1" && $change_password_send_email == true){							
						$username = $row["1"];
						$this->sendEmail($name, $username, $email, $password, $usertype);
					}					
				}			
			}			
			if($error_row==true){
				return "empty_column";
			}
			else{
				return "column_ok";
			}
	}//end function
	
	//generate a string that will be the password
	function generatePassword(){
		$chars_array = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		$format_array = array("strtolower", "strtoupper");
		$password = "";
		//generate 6 charaters
		for($i=0; $i<3; $i++){
			$format = $format_array[rand(0,1)];
			$password .= rand(0,9);			 
			$password .= $format($chars_array[rand(0, 25)]);
		}
		return $password;		 
	}
	
	function encriptPassword($password){
		$salt = "";
		for($i=0; $i<=32; $i++) {
			$d = rand(1,30)%2;
		  	$salt .= $d ? chr(rand(65,90)) : chr(rand(48,57));
	   	}		
		$hashed = md5($password.$salt);
		$encrypted = $hashed.':'.$salt;
		return $encrypted;
	}
}//end class

?>