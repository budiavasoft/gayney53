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
 * file: view.html.php
 *
 **** class 
     ArrausersexportimportViewExport 
	 
 **** functions
     display();
     userType();
     fileType();
	 tableFileType();
	 firstColumnExport();
	 generateCheckbox();
	 setSeparators();
	 setEmailTo();
	 secondColumnExport1();
	 secondColumnExport2();
	 setOrdering();
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );
JHTML::_( 'behavior.modal' );
/**
 * ArrausersexportimportViewExport View
 *
 */
class ArrausersexportimportViewUtf extends JView{
	/**
	 * display method 
	 * @return void
	 **/
	function display($tpl = null){						
		
		// make ToolBarHelper with name of component.
		JToolBarHelper::custom('export_button', 'export.png', 'export.png', 'Export', false, false);
		JToolBarHelper::custom('import_button', 'import.png', 'import.png', 'Import', false, false);
		JToolBarHelper::title(   JText::_( 'ARRA_USER_EXPORT' ), 'generic.png' );
		
		JToolBarHelper::cancel ('cancel', 'Cancel');
		
		$this->export_result = $this->get("Export");
		
		//user type
		$user_type = $this->userType();		
		$this->assignRef('user_type', $user_type);
		
		//name, username, email
		$columns_export = $this->firstColumnExport();		
		$this->assignRef('first_columns_export', $columns_export);
		
		//user type, password, blocked
		$second_columns_export1 = $this->secondColumnExport1();		
		$this->assignRef('second_columns_export1', $second_columns_export1);
		
		//data registered, lsat visit, activation
		$second_columns_export2 = $this->secondColumnExport2();		
		$this->assignRef('second_columns_export2', $second_columns_export2);
		
		//additional columns
		$second_columns_export3 = $this->secondColumnExport3();		
		$this->assignRef('second_columns_export3', $second_columns_export3);
				
		//combo separator
		$separators = $this->setSeparators();		
		$this->assignRef('separators', $separators);
		
		$ordering = $this->setOrdering();		
		$this->assignRef('ordering', $ordering);
		
		//setting email
		$email_to = $this->setEmailTo();		
		$this->assignRef('email_to', $email_to);
	   	
		$allSettings = $this->allSettings();		
		$this->assignRef('allSettings', $allSettings);
		
		$emailSettings = $this->emailSettings();		
		$this->assignRef('emailSettings', $emailSettings);
				
		$error_message = $this->errorMessage();
		$this->assignRef('error_message', $error_message);
		
		parent::display($tpl);
	}				
		
	// make check boxes with users type
	function userType(){	
	    $array_user_type = $this->get('UserType');
		$users_array = array();
		foreach($array_user_type as $key=>$value){
		    $users_array[] = $value["usertype"];
		}
		// add no user type to the end
		$users_array[] = JText::_("ARRA_NO_USER_TYPE");
		
		$columns = $this->generateCheckbox("checkbox", "bottom_columns_for_export", "header_colum", "td_class", "user_type_checkbox", $users_array, $users_array,"","all_user_type");		
		return $columns;	  
	}
	
	// make check boxes with columns by exporting default columns
	function firstColumnExport(){
	    $columns_value = array("name", "username", "email");
		$columns_name = array("Name", "Username", "Email");
		$columns = $this->generateCheckbox("checkbox", "top_columns_for_export", "header_colum", "td_class", "top_column_checkbox", $columns_value, $columns_name, "checked=\"yes\"");
		return $columns;
	}
	
	// make check boxes with columns by choose columns
	function secondColumnExport1(){
	    $columns_value = array("password", "usertype", "block", "params");
		$columns_name = array("Password", "User Type", "Blocked", "Params");
		$columns = $this->generateCheckbox("checkbox", "bottom_columns_for_export", "header_colum", "td_class", "top_column_checkbox", $columns_value, $columns_name);
		return $columns;
	}
	
	// make check boxes with columns by choose columns
	function secondColumnExport2(){
	    $columns_value = array("registerDate", "lastvisitDate", "activation");
		$columns_name = array("Register Date", "Last Visit Date", "Activation");
		$columns = $this->generateCheckbox("checkbox", "bottom_columns_for_export", "header_colum", "td_class", "top_column_checkbox", $columns_value, $columns_name);
		return $columns;
	}
	
	function secondColumnExport3(){
		$all_additional_columns = $this->get("AdditionalColumns");
	    $columns_value = array();
		$columns_name = array();
		if(isset($all_additional_columns) && is_array($all_additional_columns) && count($all_additional_columns)>0){
			foreach($all_additional_columns as $key=>$value){
				$columns_value[] = $value;
				$value = str_replace("_", " ", $value);
				$columns_name[] = ucwords($value);
			}
		}
		if(count($columns_value)>0 && count($columns_name)>0){
			$columns = $this->generateCheckbox("checkbox", "bottom_columns_for_export", "header_colum", "td_class", "top_column_checkbox", $columns_value, $columns_name);
			return $columns;	
		}		
		else{
			return "";
		}
	}	
	
	//generate automat checkbox and radio buttons
	function generateCheckbox($type, $class, $header_column_class, $td_class, $element_name, $columns_value, $columns_name, $checked="", $all_user_type=""){
	     $columns = "";
		 $header = false;
		 $br = "";
		 
		 $columns .= "<table class=\"" . $class . "\">"; 			
		 		 
		 for($j=0; $j<count($columns_name); $j++){			   
				$name = "";
				$javascript = "";
				if($type == "checkbox"){
					$name = $element_name . "[" . $columns_value[$j] . "] ";
					$br = "<br/>";
			   }
			   elseif($type == "radio"){
					if($columns_value[$j] == "csv" || $columns_value[$j]=="txt" || $columns_value[$j]=="html"){
						//set csv default
						if($columns_value[$j] == "csv"){
							$checked = " CHECKED ";
						}
						else{
							$checked = "";
						}
						$javascript = " onclick=\"javascript:showSeparator();\" ";
					}
					elseif($columns_value[$j] == "sql" || $columns_value[$j]=="zip"){
						$javascript = " onclick=\"javascript:hideSeparator();\" ";
					}					
					$name = $element_name;
					 
			   }
			   $columns .= "<tr>";
					//if is not csv, txt, sql, zip and is not for user type do background and tool tip
					if($columns_value[$j] != "html" && $columns_value[$j] != "csv" && $columns_value[$j]!="txt" && $columns_value[$j] != "sql" && $columns_value[$j]!="zip" && $all_user_type==""){
						$columns .= "<td class=\"" . $td_class . "\">";
						$tool_tip = 	"ARRA_TOOLTIP_" . str_replace(" ", "_", strtoupper($columns_name[$j])) . "_EXPORT";					
						$columns .= 	"<span class=\"editlinktip hasTip\" title=\"" . $columns_name[$j] . "::" . JText::_($tool_tip). "\" >";
						$columns .= 		$columns_name[$j];
						$columns .= 	"</span>";						
					    $columns .= "</td>";
					}
					else{
					    if (class_exists('ZipArchive')){
    						$columns .= "<td class=\"" . $td_class . "_2\">";
							$columns .= 	$columns_name[$j];
							$columns .= "</td>";
						}
						else{
						    if($columns_name[$j] != "ZIP"){
								$columns .= "<td class=\"" . $td_class . "_2\">";
								$columns .= 	$columns_name[$j];
								$columns .= "</td>";
							}
						}
					}
					if (class_exists('ZipArchive')){				
						$columns .= "<td>";  
						$columns .= 	"<input type=\"" . $type . "\" name=\"" . $name . "\" id=\"".$columns_value[$j]."\"  value=\"" .  $columns_value[$j] . "\"" . $checked . $javascript . ">" . $br;						
						$columns .= "</td>";
						if($columns_value[$j] == "csv" || $columns_value[$j]=="txt" || $columns_value[$j] == "sql" || $columns_value[$j]=="zip" || $columns_value[$j]=="html"){
							$columns .= "<td class=\"td_export_definitions_2\">";
							$columns .= 	JText::_("ARRA_TOOLTIP_TEXT_" . str_replace(" ","_" , strtoupper($columns_name[$j])) );
							$columns .= "</td>";
					   }   
						$columns .= "</tr>";
					}
					else{
						if($columns_name[$j] != "ZIP"){
							$columns .= "<td>";  
							$columns .= 	"<input type=\"" . $type . "\" name=\"" . $name . "\" id=\"".$columns_value[$j]."\"  value=\"" .  $columns_value[$j] . "\"" . $checked . $javascript . ">" . $br;						
							$columns .= "</td>";
							if($columns_value[$j] == "csv" || $columns_value[$j]=="txt" || $columns_value[$j] == "sql" || $columns_value[$j]=="html"){
								$columns .= "<td class=\"td_export_definitions_2\">";
								$columns .= 	JText::_("ARRA_TOOLTIP_TEXT_" . str_replace(" ","_" , strtoupper($columns_name[$j])) );
								$columns .= "</td>";
					   		}   
							$columns .= "</tr>";
						}
					}						 
		  }	 
		  		  	 	
		$columns .= "</table>";		 
		return $columns;
	}
	
	//set a check box with separators
	function setSeparators(){
		$combo = "";
		$combo .= "<select name=\"separator\">";
		$combo .= 		"<option value=\",\"> , " .JText::_("ARRA_COMMA") . "</option>";
		$combo .= 		"<option value=\";\"> ; " . JText::_("ARRA_SEMICOLON") . "</option>";
		$combo .= 		"<option value=\"|\"> | " . JText::_("ARRA_VERTICAL_BAR") . "</option>";
		$combo .= 		"<option value=\".\"> . " . JText::_("ARRA_DOT") . "</option>";			 
		$combo .= "</select>";
		return $combo;
	}
	
	function setOrdering(){
		$combo = "";
		$combo .= "<select name=\"ordering\">";
		$combo .= 		"<option value=\"0\">" .JText::_("ARRA_SELECT_ORDER") . "</option>";
		$combo .= 		"<option value=\"name\">" . JText::_("ARRA_ORDER_BY_NAME") . "</option>";
		$combo .= 		"<option value=\"username\">" . JText::_("ARRA_ORDER_BY_USERNAME") . "</option>";
		$combo .= 		"<option value=\"usertype\">" . JText::_("ARRA_ORDER_BY_USERTYPE") . "</option>";
		$combo .= 		"<option value=\"email\">" . JText::_("ARRA_ORDER_BY_EMAIL") . "</option>";			 
		$combo .= "</select>";
		$combo .= "&nbsp;&nbsp;"."<input type=\"radio\" name=\"mode_order\" value=\"asc\" checked> ".JText::_("ARRA_ORDER_ASC");
		$combo .= "&nbsp;&nbsp;"."<input type=\"radio\" name=\"mode_order\" value=\"desc\"> ".JText::_("ARRA_ORDER_DESC");
		return $combo;
	}
	
	//set combo box and text for emails address
	function setEmailTo(){
		$email_to = "";
		$email_to .= "<tr>";
		$email_to .= 	"<td class=\"td_export_definitions\">"; 
		$email_to .= 		"<span class=\"editlinktip hasTip\" title=\"" . JText::_("ARRA_SUPER_ADMIN_EMAIL_CHECK")."::".JText::_("ARRA_TOOLTIP_SUPER_ADMIN_EMAIL_CHECK"). "\" >";
		$email_to .= 			JText::_("ARRA_SUPER_ADMIN_EMAIL_CHECK") . "<input type=\"checkbox\" name=\"email_to_super_admin\">";
		$email_to .= 		"</span>";
		$email_to .= 	"</td>";			
		$email_to .= "</tr>";
		$email_to .= "<tr>";
		$email_to .= 	"<td class=\"td_export_definitions\">";
		$email_to .= 		"<span class=\"editlinktip hasTip\" title=\"" . JText::_("ARRA_EMAIL_TEXT")."::".JText::_("ARRA_TOOLTIP_EMAIL_TEXT"). "\" >";
		$email_to .= 			JText::_("ARRA_EMAIL_TEXT");
		$email_to .= 	"</td>";	
		$email_to .= 	"<td>";
		$email_to .= 		"<input type=\"text\" name=\"text_emails\" size=\"50\">";				    
		$email_to .= 	"</td>";			
		$email_to .= "</tr>";
		$email_to .= "<tr>";
		$email_to .= 	"<td>";
		$email_to .=    	"<a rel=\"{handler: 'iframe', size: {x: 850, y: 350}}\"  class=\"modal\"  href=\"index.php?option=com_arrauserexportimport&controller=customemail&task=email_export&tmpl=component\">".JText::_('VIEW_CUSTOM_EMAIL_BUTTON')."</a>";
		$email_to .= 	"</td>"; 
		$email_to .= "</tr>";	
		return $email_to;
	}
	
	function errorMessage(){		
		$same_user = 0;
		$empty_column = 0;
		
		if(isset($_SESSION['link_eror']) && $_SESSION['link_eror']=="error"){
			$same_user = 1;
		}
		if(isset($_SESSION['error_empty_column']) && $_SESSION['error_empty_column']=="error_empty_column"){
			$empty_column = 1;
		}		
		$error  = "";
		$error .= "<table>";
		if($same_user == 1 && $empty_column == 0){
			$error .=      "<tr>";
			$error .=          "<td>";
			$error .=              JText::_("ARRA_ERROR_MESSAGE_SAME_EMAIL");  
			$error .=          "</td>";
			$error .=      "</tr>";
			$error .=      "<tr>";
			$error .=          "<td>";
			$error .=             "<a href=\"".Juri::base()."components/com_arrauserexportimport/files/error_same_email.csv"."\" target=\"_blank\">error_same_email.csv</a>";      
			$error .=          "</td>";
			$error .=      "</tr>";
		}
		elseif($same_user == 0 && $empty_column == 1){
			$error .=      "<tr>";
			$error .=          "<td>";
			$error .=              JText::_("ARRA_NOTE_MESSAGE_EMPTY_COLUMN");  
			$error .=          "</td>";
			$error .=      "</tr>";
			$error .=      "<tr>";
			$error .=          "<td>";
			$error .=             "<a href=\"".Juri::base()."components/com_arrauserexportimport/files/error_empty_column.csv"."\" target=\"_blank\">error_empty_column.csv</a>";
			$error .=          "</td>";
			$error .=      "</tr>";
		}
		elseif($same_user == 1 && $empty_column == 1){
			$error .=      "<tr>";
			$error .=          "<td>";
			$error .=              JText::_("ARRA_ERROR_MESSAGE_SAME_EMAIL");  
			$error .=          "</td>";
			$error .=      "</tr>";
			$error .=      "<tr>";
			$error .=          "<td>";
			$error .=             "<a href=\"".Juri::base()."components/com_arrauserexportimport/files/error_same_email.csv"."\" target=\"_blank\">error_same_email.csv</a>";      
			$error .=          "</td>";
			$error .=      "</tr>";
			$error .=      "<tr>";
			$error .=          "<td>";
			$error .=              JText::_("ARRA_NOTE_MESSAGE_EMPTY_COLUMN");  
			$error .=          "</td>";
			$error .=      "</tr>";			
			$error .=          "<td>";
			$error .=             "<a href=\"".Juri::base()."components/com_arrauserexportimport/files/error_empty_column.csv"."\" target=\"_blank\">error_empty_column.csv</a>";
			$error .=          "</td>";
			$error .=      "</tr>";
		}
		$error .= "</table>";		
		return $error;
	}
	
	function emailSettings(){
	
		$db =& JFactory::getDBO();
	    $sql= "select params from #__components where link='option=com_arrauserexportimport'";
	    $db->setQuery($sql);
	    $all_result = $db->loadResult();
		$result = "";
		$settings_saved = false;	  
		
	    if(strlen($all_result) != 0){
		    $all_array = unserialize($all_result);			
			if(isset($all_array["JoomlaImport"]) && strlen(trim($all_array["JoomlaImport"]))>0){
			   $result = $all_array["JoomlaImport"];			
		       $settings_saved = true;  
			}
			else{
			   $settings_saved = false;  
			}		
		}
	
		$defaul_email_template  = "";
		$defaul_email_template .= "Congratulations, you have been registered as a user on {sitename}.\n\n";
		$defaul_email_template .= "Below are your login credentials: \n\n";		
		$defaul_email_template .= "name: {name}\n";
		$defaul_email_template .= "username: {username}\n";
		$defaul_email_template .= "usertype: {usertype}\n";
		$defaul_email_template .= "password: {password}\n";
		
		$default_subject_template  = "";
		$default_subject_template .= "Account details for {username} from {sitename}";
		
	    $config = new JConfig();
	    $emailSettings = "";
		
		$emailSettings .= "<table width=\"100%\" cellspacing=\"5\">";
		$emailSettings .=     "<tr>";
		$emailSettings .=        "<td width=\"55%\" valign=\"top\">";
				
		$emailSettings .= "<table>";
		$emailSettings .=     "<tr>";
		$emailSettings .=         "<td colspan=\"2\"  class=\"td_settings_2\">";
		$emailSettings .=             "<span><a style=\"color:red;\" href=\"#\" onclick=\"javascript:hide_show('email_div'); return false;\">".JText::_("ARRA_EMAILS_TO_NEW_USERS_2_HEADER")."</a></span>".
							   			"<div id=\"email_div\" style=\"display:none; color:red;\">".JText::_("ARRA_EMAILS_TO_NEW_USERS_2")."</div><br/>";
		$emailSettings .=             "<input type=\"checkbox\" name=\"send_email_to_import\" id=\"send_email_to_import\">" . "&nbsp;&nbsp;" . JText::_("ARRA_EMAILS_TO_NEW_USERS");
		$emailSettings .=         "</td>";
		$emailSettings .=     "</tr>"; 	
		
		$emailSettings .=     "<tr>";
		$emailSettings .=         "<td  class=\"td_settings_options\">";
		$emailSettings .=              "<span class=\"editlinktip hasTip\" title=\"Subject::".JText::_("ARRA_TIP_SUBJECT") ."\" >".
											JText::_("ARRA_EMAIL_IMPORT_SUBJECT").
									   "</span>";
		$emailSettings .=         "</td>";
		$emailSettings .=         "<td class=\"td_settings\">";
		$emailSettings .=             "<textarea rows=\"1\" cols=\"50\" name=\"subject_template\" onkeyup=\"this.style.border='1px solid silver'\"  id=\"subject_template\">";
		if($settings_saved == true){
			$emailSettings .=				 $this->checked("subject_template",$result);
		}
		else{
			$emailSettings .=				 $default_subject_template;
		}
        $emailSettings .=             "</textarea>";
		$emailSettings .=         "</td>";
		$emailSettings .=     "</tr>";
			
		$emailSettings .=     "<tr>";
		$emailSettings .=         "<td class=\"td_settings_options\">";
		$emailSettings .=              "<span class=\"editlinktip hasTip\" title=\"{from_email}::".JText::_("ARRA_TIP_FROM_EMAIL") ."\" >".
											"{from_email}" .
									   "</span>";
		$emailSettings .=         "</td>";
		$emailSettings .=         "<td class=\"td_settings\">";
		$emailSettings .=             "<input type=\"text\" name=\"from_email\" onkeyup=\"this.style.border='1px solid silver'\" value=\"";
		
		if($settings_saved == true){
			$emailSettings .=				 $this->checked("from_email",$result);
		}
		else{
			$emailSettings .=				 $config->mailfrom;
		}
		
		$emailSettings .=         "\" id=\"from_email\" size=\"40\">";
		$emailSettings .=         "</td>";
		$emailSettings .=     "</tr>";		
		$emailSettings .=     "<tr>";
		$emailSettings .=         "<td class=\"td_settings_options\">";
		$emailSettings .=              "<span class=\"editlinktip hasTip\" title=\"{from_name}::".JText::_("ARRA_TIP_FROM_NAME") ."\" >".
											"{from_name}" .
									   "</span>";
		$emailSettings .=         "</td>";
		$emailSettings .=         "<td class=\"td_settings\">";
		$emailSettings .=             "<input type=\"text\" name=\"from_name\" value=\"";
		
		if($settings_saved == true){
			$emailSettings .=				 $this->checked("from_name",$result);
		}
		else{
			$emailSettings .=				 $config->fromname;
		}
		
		$emailSettings .=         "\" onkeyup=\"this.style.border='1px solid silver'\"  id=\"from_name\"  size=\"40\">";				
		$emailSettings .=         "</td>";
		$emailSettings .=     "</tr>";		
		$emailSettings .=     "<tr>";
		$emailSettings .=         "<td class=\"td_settings_options\">";
		$emailSettings .=              "<span class=\"editlinktip hasTip\" title=\"{sitename}::".JText::_("ARRA_TIP_SITE_NAME") ."\" >".
											"{sitename}" .
									   "</span>";
		$emailSettings .=         "</td>";
		$emailSettings .=         "<td class=\"td_settings\">";
		$emailSettings .=             "<input type=\"text\" name=\"sitename\" value=\"";
		
		if($settings_saved == true){
			$emailSettings .=				 $this->checked("sitename",$result);
		}
		else{
			$emailSettings .=				 $config->sitename;
		}
		
		$emailSettings .= 		"\" onkeyup=\"this.style.border='1px solid silver'\"  id=\"sitename\"  size=\"40\">";       
		$emailSettings .=         "</td>";
		$emailSettings .=     "</tr>";		
		$emailSettings .=     "<tr>";
		$emailSettings .=         "<td class=\"td_settings_2\">";
		$emailSettings .=             "{username}";
		$emailSettings .=         "</td>";
		$emailSettings .=         "<td class=\"td_settings_2\">";
		$emailSettings .=              JText::_("ARRA_EMAILS_USERNAME"); 
		$emailSettings .=         "</td>";
		$emailSettings .=     "</tr>";		
		$emailSettings .=     "<tr>";
		$emailSettings .=         "<td class=\"td_settings_2\">";
		$emailSettings .=             "{name}";
		$emailSettings .=         "</td>";
		$emailSettings .=         "<td class=\"td_settings_2\">";
		$emailSettings .=              JText::_("ARRA_EMAILS_NAME"); 
		$emailSettings .=         "</td>";
		$emailSettings .=     "</tr>";		
		$emailSettings .=     "<tr>";
		$emailSettings .=         "<td class=\"td_settings_2\">";
		$emailSettings .=             "{usertype}";
		$emailSettings .=         "</td>";
		$emailSettings .=         "<td class=\"td_settings_2\">";
		$emailSettings .=              JText::_("ARRA_EMAILS_USERTYPE"); 
		$emailSettings .=         "</td>";
		$emailSettings .=     "</tr>";		
		$emailSettings .=     "<tr>";
		$emailSettings .=         "<td class=\"td_settings_2\">";
		$emailSettings .=             "{password}";
		$emailSettings .=         "</td>";
		$emailSettings .=         "<td class=\"td_settings_2\">";
		$emailSettings .=              JText::_("ARRA_EMAILS_PASSWORD"); 
		$emailSettings .=         "</td>";
		$emailSettings .=     "</tr>";			
		$emailSettings .= "</table>";		
		$emailSettings .=       "</td>";		
		$emailSettings .=       "<td valign=\"top\" align=\"center\">";
		$emailSettings .=          "<table>";		
		$emailSettings .=              "<tr>";
		$emailSettings .=                 "<td class=\"td_settings_2\">";
		$emailSettings .=                     JText::_("ARRA_IMPORT_BODY_EMAIL");
		$emailSettings .=                 "</td>";
		$emailSettings .=              "</tr>";		
		$emailSettings .=              "<tr>";
		$emailSettings .=                 "<td>";
		$emailSettings .=                     "<textarea rows=\"16\" cols=\"50\" name=\"email_template\" onkeyup=\"this.style.border='1px solid silver'\"  id=\"email_template\">";
		
		if($settings_saved == true){
			$emailSettings .=				 $this->checked("email_template",$result);
		}
		else{
			$emailSettings .=				 $defaul_email_template;
		}
		
        $emailSettings .=                     "</textarea>";
		$emailSettings .=                 "</td>";
		$emailSettings .=              "</tr>";
		$emailSettings .=          "</table>";  
		$emailSettings .=       "</td>";		 
		$emailSettings .=    "</tr>";
		$emailSettings .=    "<tr>";		
		$emailSettings .=       "<td class=\"td_settings_2\">&nbsp;&nbsp;";
		$emailSettings .=           JText::_("ARRA_EMAIL_TEMPLATE_NOTE");
		$emailSettings .=       "</td>";		
		$emailSettings .=    "</tr>";
		$emailSettings .= "</table>";
		
		return $emailSettings;
	}
	
	function allSettings(){
		$settings_saved = false;    	  
	    $db =& JFactory::getDBO();
	    $sql= "select params from #__components where link='option=com_arrauserexportimport'";
	    $db->setQuery($sql);
	    $all_result = $db->loadResult();
		$result = "";	  
		
	    if(strlen($all_result) != 0){
		    $all_array = unserialize($all_result);				    				
			if(isset($all_array["JoomlaImport"]) && strlen(trim($all_array["JoomlaImport"])) != 0){								
			   $result = $all_array["JoomlaImport"];			
		       $settings_saved = true;  
			}
			else{
			   $settings_saved = false;  
			}		
		}
	
	    $allSettings = "";
		$allSettings .= "<table cellspacing=\"5\">";
		$allSettings .=     "<tr>";
		$allSettings .=         "<td class=\"td_settings_options\">";
		$allSettings .=        "<span class=\"editlinktip hasTip\" title=\"" . JText::_("ARRA_SEPARATOR")."::".JText::_("ARRA_TIP_SEPARATOR") ."\" >".
					                JText::_("ARRA_SEPARATOR")."<br/>".
						       "</span>";
		$allSettings .=        "<span><a style=\"color:red;\" href=\"#\" onclick=\"javascript:hide_show('separator_div2'); return false;\">".JText::_("ARRA_SEPARATOR_2_HEADER")."</a></span><div id=\"separator_div2\" style=\"display:none; color:red;\">".JText::_("ARRA_SEPARATOR_22")."</div>";					   
		$allSettings .=         "</td>";
		$allSettings .=        "<td class=\"td_settings\" valign=\"top\">";
		$allSettings .=             "<select name=\"separator\">";		
		$select = "";
		if($this->checked("separator", $result)== ","){
			$select = ' selected="selected" ';
		}
		$allSettings .=     			"<option value=\",\" ".$select."> , " .JText::_("ARRA_COMMA") . "</option>";
		$select = "";
		if($this->checked("separator", $result) == ";" || $this->checked("separator", $result) == ""){
			$select = ' selected="selected" ';
		}
		$allSettings .=    				"<option value=\";\" ".$select."> ; " . JText::_("ARRA_SEMICOLON") . "</option>";
		$select = "";
		if($this->checked("separator", $result) == "|"){			
			$select = ' selected="selected" ';
		}
		$allSettings .= 				"<option value=\"|\" ".$select."> | " . JText::_("ARRA_VERTICAL_BAR") . "</option>";
		$select = "";
		if($this->checked("separator", $result)== "."){
			$select = ' selected="selected" ';
		}
		$allSettings .= 				"<option value=\".\" ".$select."> . " . JText::_("ARRA_DOT") . "</option>";
		$select = "";				 
		$allSettings .= 			"</select>";
		$allSettings .=        "</td>";
		$allSettings .=     "</tr>";		
		$allSettings .=     "<tr>";
		$allSettings .=         "<td class=\"td_settings_options\">"; 
		$allSettings .=        "<span class=\"editlinktip hasTip\" title=\"" . JText::_("ARRA_OVERWRITE_ALL_TO_EXISTING_USER")."::".JText::_("ARRA_TIP_OWRITE_ALL") ."\" >".
					                JText::_("ARRA_OVERWRITE_ALL_TO_EXISTING_USER") .
						       "</span>";
		$allSettings .=         "</td>";
		$allSettings .=         "<td class=\"td_settings\">";
		$allSettings .=             "<input type=\"checkbox\" name=\"same_user_option_checkbox\" value=\"change_all\"> "; 
		$allSettings .=         "</td>";		
		$allSettings .=     "</tr>";
		$allSettings .=     "<tr>";
		$allSettings .=         "<td class=\"td_settings_options\">";
		$allSettings .=        "<span class=\"editlinktip hasTip\" title=\"" . JText::_("ARRA_OVERWRITE_USERTYPE_TO_EXISTING_USER")."::".JText::_("ARRA_TIP_OWRITE_USERTYPE") ."\" >".
					                 JText::_("ARRA_OVERWRITE_USERTYPE_TO_EXISTING_USER") .
						       "</span>";
		$allSettings .=         "</td>";
		$allSettings .=         "<td class=\"td_settings\">";
		if($settings_saved == false){
			$allSettings .=           JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_usertype\" value=\"0\"> ";
			$allSettings .=           JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_usertype\" value=\"1\" checked> ";
		}
		else{
		    if($this->checked("same_user_option_radio_usertype",$result)==0){
			   $allSettings .=        JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_usertype\" value=\"0\" checked> ";
			   $allSettings .=        JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_usertype\" value=\"1\"> ";
			}
			else{
			   $allSettings .=        JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_usertype\" value=\"0\"> ";
			   $allSettings .=        JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_usertype\" value=\"1\" checked> "; 
			}		    
		}
		$allSettings .=         "</td>";
		$allSettings .=     "</tr>";
		$allSettings .=     "<tr>";
		$allSettings .=         "<td class=\"td_settings_options\">";
		$allSettings .=        "<span class=\"editlinktip hasTip\" title=\"" . JText::_("ARRA_OVERWRITE_PASSWORD_TO_EXISTING_USER")."::".JText::_("ARRA_TIP_OWRITE_PASSWORD") ."\" >".
					                  JText::_("ARRA_OVERWRITE_PASSWORD_TO_EXISTING_USER") .
						       "</span>";
		$allSettings .=         "</td>";
		$allSettings .=         "<td class=\"td_settings\">";
		if($settings_saved == false){
			$allSettings .=           JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_password\" value=\"0\"> ";
			$allSettings .=           JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_password\" value=\"1\" checked> ";
		}
		else{
		    if($this->checked("same_user_option_radio_password",$result)==0){
			    $allSettings .=       JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_password\" value=\"0\" checked> ";
			    $allSettings .=       JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_password\" value=\"1\"> ";
			}
			else{
			    $allSettings .=       JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_password\" value=\"0\"> ";
			    $allSettings .=       JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_password\" value=\"1\" checked> ";
			}
		}
		$allSettings .=         "</td>";
		$allSettings .=     "</tr>";
		
		$allSettings .=     "<tr>";
		$allSettings .=         "<td class=\"td_settings_options\">";
		$allSettings .=        "<span class=\"editlinktip hasTip\" title=\"" . JText::_("ARRA_OVERWRITE_EMAIL_TO_EXISTING_USER")."::".JText::_("ARRA_TIP_OWRITE_EMAIL") ."\" >".
					                  JText::_("ARRA_OVERWRITE_EMAIL_TO_EXISTING_USER") .
						       "</span>";
		$allSettings .=         "</td>";
		$allSettings .=         "<td class=\"td_settings\">";
		if($settings_saved == false){
			$allSettings .=           JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_email\" value=\"0\"> ";
			$allSettings .=           JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_email\" value=\"1\" checked> ";
		}
		else{
		    if($this->checked("same_user_option_radio_email",$result)==0){
			     $allSettings .=      JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_email\" value=\"0\" checked> ";
			     $allSettings .=      JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_email\" value=\"1\" > ";
			}
			else{
			     $allSettings .=      JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_email\" value=\"0\"> ";
			     $allSettings .=      JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_email\" value=\"1\" checked> ";
			}
		}
		$allSettings .=         "</td>";
		$allSettings .=     "</tr>";
		 
		$allSettings .=     "<tr>";
		$allSettings .=         "<td class=\"td_settings_options\">";
		$allSettings .=        "<span class=\"editlinktip hasTip\" title=\"" . JText::_("ARRA_OVERWRITE_BLOCK_TO_EXISTING_USER")."::".JText::_("ARRA_TIP_OWRITE_BLOCK") ."\" >".
					                 JText::_("ARRA_OVERWRITE_BLOCK_TO_EXISTING_USER") .
						       "</span>";
		$allSettings .=         "</td>";
		$allSettings .=         "<td class=\"td_settings\">";
		if($settings_saved == false){
			$allSettings .=          JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_block\" value=\"0\"> ";
			$allSettings .=          JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_block\" value=\"1\" checked> ";
		}
		else{
		     if($this->checked("same_user_option_radio_block",$result)==0){
			     $allSettings .=     JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_block\" value=\"0\" checked> ";
			     $allSettings .=     JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_block\" value=\"1\"> ";
			 }
			 else{
			     $allSettings .=     JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_block\" value=\"0\"> ";
			     $allSettings .=     JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_block\" value=\"1\" checked> ";
			 }
		}
		$allSettings .=         "</td>";
		$allSettings .=     "</tr>";
		
		$allSettings .=     "<tr>";
		$allSettings .=         "<td class=\"td_settings_options\">";
		$allSettings .=        "<span class=\"editlinktip hasTip\" title=\"" . JText::_("ARRA_OVERWRITE_PARAMS_TO_EXISTING_USER")."::".JText::_("ARRA_TIP_OWRITE_PARAMS") ."\" >".
					                 JText::_("ARRA_OVERWRITE_PARAMS_TO_EXISTING_USER") .
						       "</span>";
		$allSettings .=         "</td>";
		$allSettings .=         "<td class=\"td_settings\">";
		if($settings_saved == false){
			$allSettings .=          JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_params\" value=\"0\"> ";
			$allSettings .=          JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_params\" value=\"1\" checked> ";
		}
		else{
		     if($this->checked("same_user_option_radio_params",$result)==0){
			     $allSettings .=     JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_params\" value=\"0\" checked> ";
			     $allSettings .=     JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_params\" value=\"1\"> ";
			 }
			 else{
			     $allSettings .=     JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_params\" value=\"0\"> ";
			     $allSettings .=     JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_params\" value=\"1\" checked> ";
			 }
		}
		$allSettings .=         "</td>";
		$allSettings .=     "</tr>";
		
		$allSettings .=     "<tr>";
		$allSettings .=         "<td class=\"td_settings_options\">";
		$allSettings .=        "<span class=\"editlinktip hasTip\" title=\"" . JText::_("ARRA_ENCRYPTED_PASSWORD")."::".JText::_("ARRA_TIP_ENCRYPT_PASS") ."\" >".
					                JText::_("ARRA_ENCRYPTED_PASSWORD")."<br/>".									
						       "</span>".
							   "<span><a  style=\"color:red;\" href=\"#\" onclick=\"javascript:hide_show('encripted_div'); return false;\">".JText::_("ARRA_ENCRYPTED_PASSWORD_2_HEADER")."</a></span>".
							   "<div id=\"encripted_div\" style=\"display:none; color:red;\">".JText::_("ARRA_ENCRYPTED_PASSWORD_2")."</div>";
		$allSettings .=         "</td>";
		$allSettings .=         "<td class=\"td_settings\" valign=\"top\">";
		if($settings_saved == false){
			$allSettings .=         JText::_("ARRA_YES") . "<input type=\"radio\" name=\"encripted_password_radio\" value=\"0\" checked> ";
			$allSettings .=         JText::_("ARRA_NO") . "<input type=\"radio\" name=\"encripted_password_radio\" value=\"1\"> ";
		}
		else{
		    if($this->checked("encripted_password_radio",$result)==0){
			     $allSettings .=    JText::_("ARRA_YES") . "<input type=\"radio\" name=\"encripted_password_radio\" value=\"0\" checked> ";
			     $allSettings .=    JText::_("ARRA_NO") . "<input type=\"radio\" name=\"encripted_password_radio\" value=\"1\"> ";
			}
			else{
			    $allSettings .=     JText::_("ARRA_YES") . "<input type=\"radio\" name=\"encripted_password_radio\" value=\"0\"> ";
			    $allSettings .=     JText::_("ARRA_NO") . "<input type=\"radio\" name=\"encripted_password_radio\" value=\"1\" checked> ";
			}
		}
		$allSettings .=         "</td>";
		$allSettings .=     "</tr>";		
		$allSettings .=     "<tr>";
		$allSettings .=         "<td class=\"td_settings_options\">";
		$allSettings .=        "<span class=\"editlinktip hasTip\" title=\"" . JText::_("ARRA_GENERATE_PASSWORD")."::".JText::_("ARRA_TIP_GENERATE_PASS") ."\" >".
					                JText::_("ARRA_GENERATE_PASSWORD")."<br/>".									
						       "</span>".
							    "<span><a  style=\"color:red;\" href=\"#\" onclick=\"javascript:hide_show('generate_div'); return false;\">".JText::_("ARRA_GENERATE_PASSWORD_2_HEADER")."</a></span>".
							   "<div id=\"generate_div\" style=\"display:none; color:red;\">".JText::_("ARRA_GENERATE_PASSWORD_2")."</div>";
		$allSettings .=         "</td>";
		$allSettings .=         "<td class=\"td_settings\" valign=\"top\">";
		if($settings_saved == false){
			$allSettings .=         JText::_("ARRA_YES") . "<input type=\"radio\" name=\"generate_password_radio\" value=\"0\" checked> ";
			$allSettings .=         JText::_("ARRA_NO") . "<input type=\"radio\" name=\"generate_password_radio\" value=\"1\"> ";
		}
		else{
		    if($this->checked("generate_password_radio",$result)==0){
			    $allSettings .=     JText::_("ARRA_YES") . "<input type=\"radio\" name=\"generate_password_radio\" value=\"0\" checked> ";
			    $allSettings .=     JText::_("ARRA_NO") . "<input type=\"radio\" name=\"generate_password_radio\" value=\"1\"> ";
			}
			else{
				$allSettings .=     JText::_("ARRA_YES") . "<input type=\"radio\" name=\"generate_password_radio\" value=\"0\"> ";
			    $allSettings .=     JText::_("ARRA_NO") . "<input type=\"radio\" name=\"generate_password_radio\" value=\"1\" checked> ";
 			}
		}
		$allSettings .=         "</td>";
		$allSettings .=     "</tr>";		
		$allSettings .=     "<tr>";
		$allSettings .=         "<td class=\"td_settings_options\">";
		$allSettings .=        "<span class=\"editlinktip hasTip\" title=\"" . JText::_("ARRA_DEFAULT_PASSWORD")."::".JText::_("ARRA_TIP_DEFAULT_PASS") ."\" >".
					                JText::_("ARRA_DEFAULT_PASSWORD") .
						       "</span>";
		$allSettings .=         "</td>";
		$allSettings .=         "<td class=\"td_settings\">";
		$allSettings .=             "<input type=\"text\" name=\"default_password\" size=\"30px\">";
		$allSettings .=         "</td>";
		$allSettings .=     "</tr>";		
		$allSettings .= $this->setDefaultUsertype();				
		$allSettings .= "</table>";
		
		return $allSettings;
	}
	
	function checked($radio_name, $result){
	    $rows = explode(";", $result);
		foreach($rows as $key=>$value){
		     $value=explode("=", $value);
			 if($radio_name == trim($value[0])){
			     return trim($value[1]);
			 }
		} 
	}
	
	function setDefaultUsertype(){
	    JHTML::_('behavior.combobox');
		$all_user_type = $this->get('UserType2');
	
	    $encripted  = "";	
		$encripted .=   "<tr>";
		$encripted .=   	"<td class=\"td_settings_options\">";
		$encripted .=       	"<span class=\"editlinktip hasTip\" title=\"" . JText::_("ARRA_DEFAULT_USERTYPE")."::".JText::_("ARRA_TIP_USERTYPE") ."\" >". JText::_("ARRA_DEFAULT_USERTYPE") .
						    	"</span>";
		$encripted .=       "</td>";
		$encripted .=       "<td  class=\"td_settings\">";
		if(isset($all_user_type) && is_array($all_user_type) && count($all_user_type)!=0){
		    $encripted .=  		"<input type=\"text\" value=\"".$all_user_type[0]['name']."\" name=\"position\" class=\"combobox\" id=\"position\" style=\"position: absolute; left: 1064px !important; width: 58px; z-index: 1000; height: 12px; top:auto !important;\">";
			$encripted .=  		"<ul style=\"display: none;\" id=\"combobox-position\">";
			foreach($all_user_type as $key=>$value){
				 $encripted .= "<li>" .  $value['name'] . "</li>";
			}
		}
		else{
			$encripted .=  "<input type=\"text\" name=\"position\" class=\"combobox\" id=\"position\" style=\"position: absolute; top: 412px; left: 521px; width: 58px; z-index: 1000; height: 12px;\">";	
		}
		$encripted .=      "</td>";
		$encripted .=  "</tr>";
		
		return $encripted;
	}	
	
	function notice(){
		$return  = "<table width=\"100%\" cellpadding=0 cellspacing=0>";
		$return .= 	  "<tr>";
		$return .= 	     "<td class=\"td_utf_notice\" align=\"center\">";
		$return .=           '<img src="'.JUri::base().'components/com_arrauserexportimport/images/icons/notice_note.png">';
		$return .= 	     "</td>";
		$return .= 	     "<td class=\"td_utf_notice\">";
		$return .=           JText::_("ARRA_UTF_NOTICE");
		$return .= 	     "</td>";
		$return .= 	  "</tr>";
		$return .= "</table>";
		return $return;		
	}	

}