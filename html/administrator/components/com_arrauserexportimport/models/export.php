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
 * file: export.php
 *
 **** class 
     ArrausersexportimportModelExport 
	 
 **** functions
     __construct();
	 export();
	 getUserType();
	 setExportType();
	 csvExport();
	 txtExport();
	 htmlExport();
	 sqlExport();
	 zipExport();
	 sendMail();
	 sendSqlMail();
	 mkfile();
	 checked();
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');
require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'SqlExport.php');

/**
 * ArrausersexportimportModelExport Model
 *
 */
class ArrausersexportimportModelExport extends JModel{
	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct(){		
		parent::__construct();
	}
		
	// return all types by users
	function getUserType(){
	    $db =& JFactory::getDBO();		
		$sql = "SELECT DISTINCT usertype
		        FROM #__users
                WHERE usertype <> ''";
		$db->setQuery($sql);
		$result = $db->loadAssocList();

		return $result;
	}	
			 	
	//set settings for export(checked user type for export and file type)
	function export(){	
        $userType = array();
        $column = array();		
		$top_column_checkbox = JRequest::getVar("top_column_checkbox","","post","array");
	    if(isset($top_column_checkbox)){
			$user_type_checkbox = JRequest::getVar("user_type_checkbox","EMPTY","post","array");			
		    if(!in_array("EMPTY", $user_type_checkbox)){
				$userType = JRequest::getVar("user_type_checkbox","","post","array");
				if(isset($userType["No user type"])){
					$userType["No user type"] = "";		
				}		
			}
			else{
				$userType = "";
			}
			$column = JRequest::getVar("top_column_checkbox","","post","array");			
			$this->setExportType($userType, $column);
		}  				  
	}
	
	//set type file for export
	function setExportType($userType, $column){
	    $file_type = ""; 
		$radio_type_export = JRequest::getVar("radio_type_export", "", "post", "string");
		$order = JRequest::getVar("ordering", "0", "post", "string");
		$mode_order = JRequest::getVar("mode_order", "0", "asc", "string");
						
	    if(isset($radio_type_export)){
			$file_type = JRequest::getVar("radio_type_export", "", "post", "string");
		}
		else{
			// csv file is default
			$file_type = "csv";
		}
		//set method for export
		$method = $file_type . "Export";
        $this->$method($userType , $column, $order, $mode_order);	
	}
	
	//made a csv file
	function csvExport($userType, $column, $order="", $mode_order=""){		  	   
		$data = "";
        $where = "";
		$header = "";
		$separator = JRequest::getVar("separator", ",", "post", "string");
		$split_name = JRequest::getVar("split_name", "no", "post", "string");
		$export_name = false; //if is true separate each name from result in first name and last name  	
		//set file header
        foreach($column as $key=>$value){
			//if is in columns for export name and if split the name
			if($value=="name" && $split_name != "no"){
				$header .= JText::_("ARRA_FIRST_NAME") . $separator . JText::_("ARRA_LAST_NAME") . $separator;
				$export_name = true;
			}
			else{
				$header .= $value . $separator;
			}	 
        }
		//remove last separator
		$header = substr($header, 0, strlen($header)-1);
		//complet criteria for select from database
		if(is_array($userType)){
			$where .= " where usertype in (";
			foreach($userType as $key=>$value){
				$where .= "'" . $value . "',"; 
			}			
			$var = substr($where, 0, strlen($where)-1);
			$where = $var . ")";			
		}
		else{
			$where .= "";
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
		$sql = "SELECT " . $columns .
		        " FROM #__users " . $where . $ordering;
		$db->setQuery($sql);	
		$result = $db->loadAssocList();				
		
        $remove_header = JRequest::getVar("remove_header", "");		
		if($remove_header == ""){
			$data .= $header . "\n";
		}
		
		$exist = false;
		$row = "";
		if(is_array($result)){
			//for each row
			foreach($result as $key=>$value){				
				$next = true;
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
							$row .= trim($first_name) . $separator . trim($last_name) . $separator;
						}
						elseif(count($temp_array)==2){
							$first_name = $temp_array[0];
							$last_name = $temp_array[1];
							$row .= trim($first_name) . $separator . trim($last_name) . $separator;
						}
						else{
							$first_name .= $temp_array[0];
							$row .= trim($first_name) . $separator . $separator;
						}
						$next = false;
					}
					elseif($key2 == "params"){
						$params = trim($value2);
						$params = str_replace("\r\n", "***", $params);
						$row .= $params.$separator;
					}
					else{
						$row .= trim($value2).$separator;
					}										
				}
				//set true for next row
				$next = true;
				//remove last coma
				$temp = substr($row, 0, strlen($row)-1);
				//start new row
				$data .= $temp . "\n";					
				$row = "";			
			} 					
		}
		$config = new JConfig();
		$csv_filename = $config->db."_users.csv"; 
		$size_in_bytes=strlen($data);
		header("Content-Type: application/x-msdownload");
		//header("Content-Length:" . $size_in_bytes);
		header("Content-Disposition: attachment; filename=".$csv_filename);
		header("Pragma: no-cache");
		header("Expires: 0");
		echo utf8_decode($data);	
		
		//send emails
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
	
	//make a html file
	function htmlExport($userType, $column, $order="", $mode_order=""){
	    $split_name = JRequest::getVar("split_name", "no", "post", "string"); 
		$header = "";
		$where = "";   
		$data  = "";
		$export_name = "";
		$data .= 	"<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n".
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
		foreach($column as $key=>$value){
			//if is in columns for export name and if split the name
			if($value=="name" && $split_name != "no"){
				$header .= "<th>".JText::_("ARRA_FIRST_NAME")."</th>"."<th>".JText::_("ARRA_LAST_NAME")."</th>";
				$export_name = true;
			}
			else{
				$header .= "<th>".$value."</th>";
			}	 
        }
		$data .= $header."</tr>";
		
		if(is_array($userType)){
			$where .= " where usertype in (";
			foreach($userType as $key=>$value){
				$where .= "'" . $value . "',"; 
			}			
			$var = substr($where, 0, strlen($where)-1);
			$where = $var . ")";			
		}
		else{
			$where .= "";
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
		$sql = "SELECT " . $columns .
			   " FROM #__users " . $where . $ordering;
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		//set table body
		$exist = false;
		$row = "";
		$par_row = true;
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
					    $row .= "<td>".trim($value2)."</td>";
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
	
	//made a txt file
	function txtExport($userType, $column, $order="", $mode_order=""){
	    $data = "";
        $where = "";
		$header = "";
		$separator = JRequest::getVar("separator", ",", "post", "string");
		$split_name = JRequest::getVar("split_name", "no", "post", "string");
		$export_name = false; //if is true separate each name from result in first name and last name  	
		//set file header
        foreach($column as $key=>$value){
			//if is in columns for export name and if split the name
			if($value=="name" && $split_name != "no"){
				$header .= JText::_("ARRA_FIRST_NAME") . $separator . JText::_("ARRA_LAST_NAME") . $separator;
				$export_name = true;
			}
			else{
				$header .= $value . $separator;
			}	 
        }
		//remove last separator
		$header = substr($header, 0, strlen($header)-1);
		//complet criteria for select from database
		if(is_array($userType)){
			$where .= " where usertype in (";
			foreach($userType as $key=>$value){
				$where .= "'" . $value . "',"; 
			}			
			$var = substr($where, 0, strlen($where)-1);
			$where = $var . ")";			
		}
		else{
			$where .= "";
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
		$sql = "SELECT " . $columns .
			   " FROM #__users " . $where . $ordering;
		$db->setQuery($sql);
		$result = $db->loadAssocList();				
		
        $remove_header = JRequest::getVar("remove_header", "");		
		if($remove_header == ""){
			$data .= $header . "\n";
		}
		
		$exist = false;
		$row = "";
		if(is_array($result)){
			//for each row
			foreach($result as $key=>$value){
				$next = true;
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
							$row .= trim($first_name) . $separator . trim($last_name) . $separator;
						}
						elseif(count($temp_array)==2){
							$first_name = $temp_array[0];
							$last_name = $temp_array[1];
							$row .= trim($first_name) . $separator . trim($last_name) . $separator;
						}
						else{
							$first_name .= $temp_array[0];
							$row .= trim($first_name) . $separator . $separator;
						}
						$next = false;
					}
					elseif($key2 == "params"){
						$params = trim($value2);
						$params = str_replace("\r\n", "***", $params);
						$row .= $params.$separator;
					}
					else{
						$row .= trim($value2) . $separator;
					}										
				}
				//set true for next row
				$next = true;
				//remove last coma
				$temp = substr($row, 0, strlen($row)-1);
				//start new row
				$data .= $temp . "\n";					
				$row = "";			
			} 					
		}
		$config = new JConfig();
		$txt_filename = $config->db."_users.txt";		
		$size_in_bytes=strlen($data);
		header("Content-Type: application/x-msdownload");
		//header("Content-Length:" . $size_in_bytes);
		header("Content-Disposition: attachment; filename=".$txt_filename);
		header("Pragma: no-cache");
		header("Expires: 0");
		echo utf8_decode($data);	
		
		//send emails
		$list_emails = JRequest::getVar("text_emails", "", "post", "string");
		$email_to_super_admin = JRequest::getVar("email_to_super_admin", "no", "post", "string");		
		//if is set an enails list
		if($list_emails != ""){
			$this->sendMail($data, $txt_filename, $list_emails);
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
		    $this->sendMail($data, $txt_filename, $emails);
		}   
			
		exit();
	}
	
	// make a sql file
	function sqlExport($userType, $column, $order=""){
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
	
	//create a nuw file if not exist
	function mkfile($filename,$mode) { 
        if(!file_exists($filename)) { 
			$handle = fopen($filename,'w+'); 
			fclose($handle);
			chmod($filename,$mode); 
        } 
    } 

	
	//make a zip file for export
	function zipExport(){
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
	
	//send mail with zip file
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
				if(isset($all_array["JoomlaExport"]) && strlen(trim($all_array["JoomlaExport"]))>0){
					$result = $all_array["JoomlaExport"];			
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
				if(isset($all_array["JoomlaExport"]) && strlen(trim($all_array["JoomlaExport"]))>0){
					$result = $all_array["JoomlaExport"];			
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

}