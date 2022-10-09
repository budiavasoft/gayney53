<?php
define( '_JEXEC', 1 );
define('JPATH_BASE', substr(substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), "administra")),0,-1));
if (!isset($_SERVER["HTTP_REFERER"])) exit("Direct access not allowed.");
$mosConfig_absolute_path =substr(JPATH_BASE, 0, strpos(JPATH_BASE, "/administra")); 
define( 'DS', DIRECTORY_SEPARATOR );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'methods.php');
require_once ( JPATH_BASE .DS.'configuration.php' );
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'base'.DS.'object.php');
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'database'.DS.'database.php');
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'database'.DS.'database'.DS.'mysql.php');
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'filesystem'.DS.'folder.php');
$jFolder=new JFolder();
$config = new JConfig();
$options = array ("host" => $config->host,"user" => $config->user,"password" => $config->password,"database" => $config->db,"prefix" => $config->dbprefix);
$database = new JDatabaseMySQL($options);

$task = JRequest::getVar("task", "", "get", "string");
switch($task){
     case "all_active_users" : getActiveUsers(); 
	                           break;
	 case "at_least_one_visit" : getAtLeastOneVisit(); 
	                           break;
	 case "truncate_tables" : truncateAllTables(); 
	                           break;
	 case "save_settings_email_export" : saveEmailExportSettings();
	                           break;
	 case "save_email_jomsocial" : saveEmailJomSocial();
	 						   break;
	 case "jomsocial_save_email_export" : JomSocialSaveEmailExport();
	 						   break;								   						   
}

function JomSocialSaveEmailExport(){
	global $database;

	$subject_template = JRequest::getVar("subject_template","","get","string");
	$from_email = JRequest::getVar("from_email","","get","string");
	$from_name = JRequest::getVar("from_name","","get","string");
	$sitename = JRequest::getVar("sitename","","get","string");
	$email_template = JRequest::getVar("email_template","","get","string");
	
	$sql = "select c.params from #__components c where c.option='com_arrauserexportimport' and c.link='option=com_arrauserexportimport'";
	$database->setQuery($sql);
	$content = $database->loadResult();
	$total_array = array();
	
	if($content != NULL && trim($content) != ""){
		$total_array = unserialize($content);												
	}
	
	$total_array["JomSocialExport"] =	"subject_template=".addslashes($subject_template).";\n".
									   	"from_email=".addslashes($from_email).";\n".				 
										"from_name=".addslashes($from_name).";\n".
										"sitename=".addslashes($sitename).";\n".
										"email_template=".$email_template.";";
										
	$sql = "update #__components c set params='".serialize($total_array)."' where c.option='com_arrauserexportimport' and c.link='option=com_arrauserexportimport'";
	$database->setQuery($sql);
	if($database->query()){
		echo "1"."Email template successfully saved.";
	}
	else{
		echo "2"."Settings can't saved";
	}	
}

function saveEmailJomSocial(){
	 global $database;

     $subject_template = JRequest::getVar("subject_template","","get","string");
	 $from_email = JRequest::getVar("from_email","","get","string");
	 $from_name = JRequest::getVar("from_name","","get","string");
	 $sitename = JRequest::getVar("sitename","","get","string");
	 $email_template = JRequest::getVar("email_template","","get","string");
	 $send_email_to_import = JRequest::getVar("send_email_to_import","","get","string");
	 
	 $sql = "select c.params from #__components c where c.option='com_arrauserexportimport' and c.link='option=com_arrauserexportimport'";
	 $database->setQuery($sql);
	 $content = $database->loadResult();
	 $all_array = array();
	 
	 if($content != NULL && strlen(trim($content))>0){
	 	  $all_array = unserialize($content);
	 }	
	 
	 $all_array["JomSocialImport"] = "send_email_to_import=".addslashes($send_email_to_import).";\n".
	       	 					   	 "subject_template=".addslashes($subject_template).";\n".
									 "from_email=".addslashes($from_email).";\n".				 
									 "from_name=".addslashes($from_name).";\n".
									 "sitename=".addslashes($sitename).";\n".
									 "email_template=".$email_template.";";
	 
	$sql = "update #__components c set params='".serialize($all_array)."' where c.option='com_arrauserexportimport' and c.link='option=com_arrauserexportimport'";			
	$database->setQuery($sql);
	if($database->query()){
	   echo "1"."Email template successfully saved.";
	}
	else{
	   echo "2"."Settings can't saved";
	}	
}

function saveEmailExportSettings(){
	global $database;
	
	$subject_template = JRequest::getVar("subject_template","","get","string");
	$from_email = JRequest::getVar("from_email","","get","string");
	$from_name = JRequest::getVar("from_name","","get","string");
	$sitename = JRequest::getVar("sitename","","get","string");
	$email_template = JRequest::getVar("email_template","","get","string");
	
	$sql = "select c.params from #__components c where c.option='com_arrauserexportimport' and c.link='option=com_arrauserexportimport'";
	$database->setQuery($sql);
	$content = $database->loadResult();
	$total_array = array();
	
	if($content != NULL && trim($content) != ""){
		$total_array = unserialize($content);												
	}
	
	$total_array["JoomlaExport"] =  "subject_template=".addslashes($subject_template).";\n".
										"from_email=".addslashes($from_email).";\n".				 
										"from_name=".addslashes($from_name).";\n".
										"sitename=".addslashes($sitename).";\n".
										"email_template=".addslashes($email_template).";";
										
	$sql = "update #__components c set params='".serialize($total_array)."' where c.option='com_arrauserexportimport' and c.link='option=com_arrauserexportimport'";		  
	$database->setQuery($sql);
	if($database->query()){
	echo "1"."Email template successfully saved.";
	}
	else{
	echo "2"."Settings can't saved";
	}		 	
}

function getActiveUsers(){
    global $database;		
	$sql = "select count(*) from #__users where (UNIX_TIMESTAMP(registerDate)<=UNIX_TIMESTAMP(NOW()) and lastvisitDate <> '0000-00-00 00:00:00')";
	$database->setQuery($sql);
	$content = $database->loadResult();	
	
	echo $content;	
}

function getAtLeastOneVisit(){
    global $database;		
	$sql = "select count(*) from #__users where (UNIX_TIMESTAMP(registerDate)<=UNIX_TIMESTAMP(NOW()) and lastvisitDate <> '0000-00-00 00:00:00')";
	$database->setQuery($sql);
	$content = $database->loadResult();	
	
	echo $content;
}

function truncateAllTables(){
    global $database;
	global $config; 
	
	$message_ok = "";
	$message_not_ok = "";
	$array_ok = array();
	$array_not_ok = array();	
	
	$id_super_administrator = "";
	$id_public_backend = "";
	$id_user = array();
	
	$sql = "select id from #__core_acl_aro_groups where name = 'Super Administrator'";
	$database->setQuery($sql);		
	$id_super_administrator .= $database->loadResult();
	
	$sql = "select id from #__core_acl_aro_groups where name = 'Public Backend'";
	$database->setQuery($sql);		
	$id_public_backend .= $database->loadResult();
	
	if($id_super_administrator != "" && $id_public_backend != ""){
		$sql = "select id from #__users where gid = '".$id_super_administrator."'";
		$database->setQuery($sql);		
		$id_user_array = $database->loadAssocList();
		if(isset($id_user_array) && count($id_user_array)>0){
			foreach($id_user_array as $key=>$value){
				$id_user[] = "'".$value['id']."'";
			}
		}
	}
	else{
	    echo "ERROR-" . $message_not_ok . " could not be deleted.";
		return;
	}
	
	if($id_super_administrator != "" && $id_public_backend != ""){	
		$sql1 = "delete from #__core_acl_groups_aro_map where group_id <> '".$id_super_administrator."'";
		$sql2 = "delete from #__core_acl_aro where value not in (". implode(",", $id_user). ")";
		$sql3 = "delete from #__users where id not in (". implode(",", $id_user). ")";
		$sql4 = "delete from #__core_acl_aro_groups where id not in ('".$id_super_administrator."', '".$id_public_backend."')";
	}
	else{
		echo "ERROR-" . $message_not_ok . " could not be deleted.";
		return;
	}	
	
	$database->setQuery($sql1);
	if($database->query()){
	   $array_ok[] = $config->dbprefix . "core_acl_groups_aro_map";
	}
	else{
	   $array_not_ok[] = $config->dbprefix . "core_acl_groups_aro_map";
	}
	
	$database->setQuery($sql2);
	if($database->query()){
	   $array_ok[] = $config->dbprefix . "core_acl_aro";
	}
	else{
	   $array_not_ok[] = $config->dbprefix . "core_acl_aro";
	}
	
	$database->setQuery($sql3);
	if($database->query()){
	   $array_ok[] = $config->dbprefix . "users";
	}
	else{
	   $array_not_ok[] = $config->dbprefix . "users";
	}
	
	$database->setQuery($sql4);
	if($database->query()){
	   $array_ok[] = $config->dbprefix . "core_acl_aro_groups";
	}
	else{
	   $array_not_ok[] = $config->dbprefix . "core_acl_aro_groups";
	}
	
	if(is_array($array_ok) && count($array_ok)!=0){
	   $message_ok = implode(", ", $array_ok);
	}
	if(is_array($array_not_ok) && count($array_not_ok)!=0){   
	   $array_not_ok = implode(", ", $array_not_ok);
	}
	
	if($message_ok != ""){
	   $message_ok = "Following tables: " . $message_ok . " are empty!";
	}
	
	if($message_not_ok != ""){
	   $message_not_ok = "ERROR-" . $message_not_ok . " could not be deleted.";
	}
	
	echo $message_ok . "<br/>" . $message_not_ok;
}
	
?>	