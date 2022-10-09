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
     ArrausersexportimportViewJomsocial 
	 
 **** functions  
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );
JHTML::_( 'behavior.modal' );
/**
 * ArrausersexportimportViewJomsocial View
 *
 */
class ArrausersexportimportViewJomsocial extends JView{
	/**
	 * display method 
	 * @return void
	 **/
	function display($tpl = null){						
		
		// make ToolBarHelper with name of component.
		JToolBarHelper::custom('export_button', 'export.png', 'export.png', 'Export', false, false);
		JToolBarHelper::custom('import_button', 'import.png', 'import.png', 'Import', false, false);
		JToolBarHelper::title(JText::_('ARRA_USER_EXPORT'), 'generic.png');		
		JToolBarHelper::cancel('cancel', 'Cancel');
		
		$columns = $this->get("Columns");
		$this->total_columns = count($columns)+12;
		
		$groups = $this->get("Groups");
		$this->total_groups = count($groups);
		
		$jomsocial = $this->JoomSocial();		
		$this->assignRef('jomsocial', $jomsocial);
		
		$separators_export = $this->setSeparators("export");		
		$this->assignRef('separators_export', $separators_export);		
		
		$ordering = $this->setOrdering();		
		$this->assignRef('ordering', $ordering);
		
		$file_type = $this->fileType();		
		$this->assignRef('file_type', $file_type);
		
		$table_file_type = $this->tableFileType();		
		$this->assignRef('table_file_type', $table_file_type);
		
		$email_to = $this->setEmailTo();		
		$this->assignRef('email_to', $email_to);
		
		$email_to_import = $this->setEmailToImport();		
		$this->assignRef('email_to_import', $email_to_import);
		
		$gruop_type = $this->getGroups();
		$this->assignRef('gruop_type', $gruop_type);
		
		$upload_file = $this->uploadFile();
		$this->assignRef('upload_file', $upload_file);
		
		$allSettings = $this->allSettings();		
		$this->assignRef('allSettings', $allSettings);
		
		$error_message = $this->errorMessage();
		$this->assignRef('error_message', $error_message);
		
		parent::display($tpl);
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
	
	function getGroups(){
		$groups = $this->get("Groups");
		$columns_value = array();
		$columns_name = array();
		
		foreach($groups as $key=>$value){
			$columns_value[] = $value["id"];
			$columns_name[] = $value["name"];
		}
		
		$columns = $this->joomcomCheckboxGroups("checkbox", "bottom_columns_for_export", "header_colum", "td_class", "group_type_checkbox", $columns_value, $columns_name, "", "all_groups_type");
		return $columns;
	}
	
	function tableFileType(){		
		$columns_value = array("sql", "zip");
		$columns_name = array("SQL", "ZIP");
		$columns = $this->generateCheckbox("radio", "bottom_columns_for_export", "header_colum", "td_class", "radio_type_export", $columns_value, $columns_name);
		return $columns;
	}
	
	function fileType(){		
		$columns_value = array("csv", "txt", "html");
		$columns_name = array("CSV", "TXT", "HTML");
		$columns = $this->generateCheckbox("radio", "bottom_columns_for_export", "header_colum", "td_class", "radio_type_export", $columns_value, $columns_name);
		return $columns;
	}
	
	function JoomSocial(){
		$columns_name = array("Name", "Username", "Email", "Password", "User Type", "Activation", "Register Date", "Last Visit Date", "Status");
		$columns_value = array("name", "username", "email", "password", "usertype", "block", "registerDate", "lastvisitDate", "status");
		
		$groups = $this->get("Groups");
		$total_groups = count($groups);
		if($total_groups > 0){
			$columns_name[] = ("Group");
			$columns_value[] = ("group_name");
		}
				
		$columns = $this->get("Columns");
		
		foreach($columns as $key=>$value){
			$columns_value[] = $value['fieldcode'];
			$value['name'] = str_replace("_", " ", $value['name']);
			$columns_name[] = ucfirst($value['name']);			
		}
		
		$columns_value[] = "photos";
		$columns_name[] = "Photos";
		$columns_value[] = "videos";
		$columns_name[] = "Videos";
							
		$columns = $this->joomcomCheckbox("checkbox", "top_columns_for_export", "header_colum", "td_class", "top_column_checkbox", $columns_value, $columns_name, "");
		return $columns;	
	}
	
	function joomcomCheckboxGroups($type, $class, $header_column_class, $td_class, $element_name, $columns_value, $columns_name, $checked="", $all_user_type=""){
		$columns = "";
		$read_only = "";
		$stop = false;
		$br = "";
		$i=0;
		 
		$columns .= "<table class=\"" . $class . "\" width=\"100%\">"; 			
		 	
		while(!$stop){			
			$br = "<br/>";
			$columns .= "<tr>";
				for($j=0; $j<4; $j++){
					if(isset($columns_name[$i])){
						$name = $element_name . "[" . $columns_value[$i] . "] ";						
						$columns .= "<td width=\"4%\">";  
						$columns .= 	"<input type=\"" . $type . "\" name=\"" . $name . "\"  id=\"".$element_name."[".$i."]\"  value=\"" .  $columns_value[$i] . "\"  checked>" . $br;						
						$columns .= "</td>";
						$columns .= "<td>";
						$columns .= 	$columns_name[$i];						
						$columns .= "</td>";						
						$i++;
					}
					else{
						$stop = true;						
					}
				}
			$columns .= "</tr>";
		}			  	 	
		$columns .= "</table>";
		
		return $columns;
		
	}
	
	function joomcomCheckbox($type, $class, $header_column_class, $td_class, $element_name, $columns_value, $columns_name, $checked="", $all_user_type=""){
		$columns = "";
		$read_only = "";
		$stop = false;
		$br = "";
		$i=0;
		 
		$columns .= "<table class=\"" . $class . "\" width=\"100%\">"; 			
		 	
		while(!$stop){			
			$br = "<br/>";
			$columns .= "<tr>";
				for($j=0; $j<4; $j++){
					if(isset($columns_name[$i])){
						$name = $element_name . "[" . $columns_value[$i] . "] ";
						if($columns_value[$i]=="name" || $columns_value[$i]=="username" || $columns_value[$i]=="email"){
							$checked = "checked";
							$read_only = " onclick=\"return false;\" ";
						}						
						else{
							$checked = "";
							$read_only = "";
						}
						$columns .= "<td width=\"4%\">";  
						$columns .= 	"<input type=\"" . $type . "\" name=\"" . $name . "\" ".$read_only." id=\"".$element_name."[".$i."]\"  value=\"" .  $columns_value[$i] . "\"" . $checked . ">" . $br;						
						$columns .= "</td>";
						$columns .= "<td>";
						$columns .= 	$columns_name[$i];						
						$columns .= "</td>";						
						$i++;
					}
					else{
						$stop = true;						
					}
				}
			$columns .= "</tr>";
		}			  	 	
		$columns .= "</table>";
		
		return $columns;
		
	}
	
	function generateCheckbox($type, $class, $header_column_class, $td_class, $element_name, $columns_value, $columns_name, $checked="", $all_user_type=""){
	     $columns = "";
		 $header = false;
		 $br = "";
		 
		 $columns .= "<table class=\"" . $class . "\">"; 			
		 		 
		 for($j=0; $j<count($columns_name); $j++){			   				
			if($columns_value[$j] == "csv" || $columns_value[$j]=="txt" || $columns_value[$j]=="html"){				
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
		$email_to .=    	"<a rel=\"{handler: 'iframe', size: {x: 900, y: 350}}\"  class=\"modal\"  href=\"index.php?option=com_arrauserexportimport&controller=jomsocialemailexport&task=email_export&tmpl=component\">".JText::_('VIEW_CUSTOM_EMAIL_BUTTON')."</a>";
		$email_to .= 	"</td>"; 
		$email_to .= "</tr>";	
		return $email_to;
	}	
	
	function setEmailToImport(){
		$email_to = "";
		$email_to .= "<tr>";
		$email_to .= 	"<td>";
		$email_to .=    	"<a rel=\"{handler: 'iframe', size: {x: 870, y: 350}}\"  class=\"modal\"  href=\"index.php?option=com_arrauserexportimport&controller=emailimportjomsocial&task=email_import&tmpl=component\">".JText::_('VIEW_CUSTOM_EMAIL_BUTTON')."</a>";
		$email_to .= 	"</td>"; 
		$email_to .= "</tr>";	
		return $email_to;
	}
	
	function setSeparators($type){
		$name = "separator_export";
		if($type=="import"){
			$name = "separator";
		}
		$combo = "";
		$combo .= "<select name=\"".$name."\">";
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
		$combo .= 		"<option value=\"registerDate\">" . JText::_("ARRA_ORDER_BY_REGISTER_DATE") . "</option>";
		$combo .= "</select>";
		$combo .= "&nbsp;&nbsp;"."<input type=\"radio\" name=\"mode_order\" value=\"asc\" checked> ".JText::_("ARRA_ORDER_ASC");
		$combo .= "&nbsp;&nbsp;"."<input type=\"radio\" name=\"mode_order\" value=\"desc\"> ".JText::_("ARRA_ORDER_DESC");
		return $combo;
	}
	
	function uploadFile(){
	    $upload_file = "";
		$upload_file .= "<table width=\"100%\">";
		$upload_file .= 		"<tr>";
		$upload_file .= 			"<td width=\"25%\">";		   
		$upload_file .= 				"<input name=\"file_upload\" type=\"FILE\" id=\"file_upload\" size=\"50px\" >";				  			  
		$upload_file .= 			"</td>"; 
		$upload_file .= 			"<td align=\"left\"  width=\"75%\">";
		$upload_file .= 			"<input type=\"submit\" name=\"import_button\" value=\"Import\" onClick=\" document.adminForm.task.value='import'; return validateJomSocialImport();\">";	
		$upload_file .= 			"</td>";
		$upload_file .= 		"</tr>";		
		$upload_file .= "</table>";
		
		return $upload_file; 
	}	
	
	function allSettings(){   	  
	    $db =& JFactory::getDBO();
	    $sql= "select params from #__components where link='option=com_arrauserexportimport'";
	    $db->setQuery($sql);
	    $all_result = $db->loadResult();
		$result = "";		 
		
		$same_user_option_radio_basic_informations = 1;
		$same_user_option_radio_usertype = 1;
		$same_user_option_radio_password = 1;
		$same_user_option_radio_email = 1;
		$same_user_option_radio_block = 1;
		$encripted_password_radio = 0;
		$generate_password_radio = 0;
		
	    if(strlen($all_result) != 0){
			$all_array = unserialize($all_result);				    				
			if(isset($all_array["JomSocialOptions"]) && strlen(trim($all_array["JomSocialOptions"])) != 0){								
			   	$result = $all_array["JomSocialOptions"];
			   	$same_user_option_radio_basic_informations = $this->checked("same_user_option_radio_basic_informations",$result);
				$same_user_option_radio_usertype = $this->checked("same_user_option_radio_usertype",$result);
				$same_user_option_radio_password = $this->checked("same_user_option_radio_password",$result);
				$same_user_option_radio_email = $this->checked("same_user_option_radio_email",$result);
				$same_user_option_radio_block = $this->checked("same_user_option_radio_block",$result);
				$encripted_password_radio = $this->checked("encripted_password_radio",$result);
				$generate_password_radio = $this->checked("generate_password_radio",$result);		       	 
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
		$allSettings .=     			"<option value=\",\"> , " .JText::_("ARRA_COMMA") . "</option>";
		$allSettings .=    				"<option value=\";\"> ; " . JText::_("ARRA_SEMICOLON") . "</option>";
		$allSettings .= 				"<option value=\"|\"> | " . JText::_("ARRA_VERTICAL_BAR") . "</option>";
		$allSettings .= 				"<option value=\".\"> . " . JText::_("ARRA_DOT") . "</option>";				 
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
		$allSettings .=        "<td class=\"td_settings_options\">";
		$allSettings .=        "<span class=\"editlinktip hasTip\" title=\"" . JText::_("ARRA_OVERWRITE_ALL_FIELDS_TO_EXISTING_USER")."::".JText::_("ARRA_TIP_OWRITE_ALL_FIELDS") ."\" >".
					                 JText::_("ARRA_OVERWRITE_ALL_FIELDS_TO_EXISTING_USER") .
						       "</span>";
		$allSettings .=        "</td>";
		$allSettings .=        "<td class=\"td_settings\">";
		if($same_user_option_radio_basic_informations == 0){
		   $allSettings .=        JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_basic_informations\" value=\"0\" checked> ";
		   $allSettings .=        JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_basic_informations\" value=\"1\"> ";
		}
		else{
		   $allSettings .=        JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_basic_informations\" value=\"0\"> ";
		   $allSettings .=        JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_basic_informations\" value=\"1\" checked> "; 
		}		
		$allSettings .=        "</td>";
		$allSettings .=     "</tr>";			
		$allSettings .=     "<tr>";
		$allSettings .=         "<td class=\"td_settings_options\">";
		$allSettings .=        "<span class=\"editlinktip hasTip\" title=\"" . JText::_("ARRA_OVERWRITE_USERTYPE_TO_EXISTING_USER")."::".JText::_("ARRA_TIP_OWRITE_USERTYPE") ."\" >".
					                 JText::_("ARRA_OVERWRITE_USERTYPE_TO_EXISTING_USER") .
						       "</span>";
		$allSettings .=         "</td>";
		$allSettings .=         "<td class=\"td_settings\">";		
		if($same_user_option_radio_usertype == 0){
		   $allSettings .=        JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_usertype\" value=\"0\" checked> ";
		   $allSettings .=        JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_usertype\" value=\"1\"> ";
		}
		else{
		   $allSettings .=        JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_usertype\" value=\"0\"> ";
		   $allSettings .=        JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_usertype\" value=\"1\" checked> "; 
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
		if($same_user_option_radio_password == 0){
			$allSettings .=       JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_password\" value=\"0\" checked> ";
			$allSettings .=       JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_password\" value=\"1\"> ";
		}
		else{
			$allSettings .=       JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_password\" value=\"0\"> ";
			$allSettings .=       JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_password\" value=\"1\" checked> ";
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
		if($same_user_option_radio_email == 0){
			 $allSettings .=      JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_email\" value=\"0\" checked> ";
			 $allSettings .=      JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_email\" value=\"1\" > ";
		}
		else{
			 $allSettings .=      JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_email\" value=\"0\"> ";
			 $allSettings .=      JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_email\" value=\"1\" checked> ";
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
		if($same_user_option_radio_block == 0){
			 $allSettings .=     JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_block\" value=\"0\" checked> ";
			 $allSettings .=     JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_block\" value=\"1\"> ";
		 }
		 else{
			 $allSettings .=     JText::_("ARRA_YES") . "<input type=\"radio\" name=\"same_user_option_radio_block\" value=\"0\"> ";
			 $allSettings .=     JText::_("ARRA_NO") . "<input type=\"radio\" name=\"same_user_option_radio_block\" value=\"1\" checked> ";
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
		if($encripted_password_radio == 0){
			 $allSettings .=    JText::_("ARRA_YES") . "<input type=\"radio\" name=\"encripted_password_radio\" value=\"0\" checked> ";
			 $allSettings .=    JText::_("ARRA_NO") . "<input type=\"radio\" name=\"encripted_password_radio\" value=\"1\"> ";
		}
		else{
			$allSettings .=     JText::_("ARRA_YES") . "<input type=\"radio\" name=\"encripted_password_radio\" value=\"0\"> ";
			$allSettings .=     JText::_("ARRA_NO") . "<input type=\"radio\" name=\"encripted_password_radio\" value=\"1\" checked> ";
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
		if($generate_password_radio == 0){
			$allSettings .=     JText::_("ARRA_YES") . "<input type=\"radio\" name=\"generate_password_radio\" value=\"0\" checked> ";
			$allSettings .=     JText::_("ARRA_NO") . "<input type=\"radio\" name=\"generate_password_radio\" value=\"1\"> ";
		}
		else{
			$allSettings .=     JText::_("ARRA_YES") . "<input type=\"radio\" name=\"generate_password_radio\" value=\"0\"> ";
			$allSettings .=     JText::_("ARRA_NO") . "<input type=\"radio\" name=\"generate_password_radio\" value=\"1\" checked> ";
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
		$all_user_type = $this->get('UserType');
	
	    $encripted  = "";	
		$encripted .=   "<tr>";
		$encripted .=   	"<td class=\"td_settings_options\">";
		$encripted .=       	"<span class=\"editlinktip hasTip\" title=\"" . JText::_("ARRA_DEFAULT_USERTYPE")."::".JText::_("ARRA_TIP_USERTYPE") ."\" >". JText::_("ARRA_DEFAULT_USERTYPE") .
						    	"</span>";
		$encripted .=       "</td>";
		$encripted .=       "<td  class=\"td_settings\">";
		if(isset($all_user_type) && is_array($all_user_type) && count($all_user_type)!=0){
		    $encripted .=  		"<input type=\"text\" value=\"".$all_user_type[0]['name']."\" name=\"position\" class=\"combobox\" id=\"position\" style=\"position: absolute; top: 412px; left: 521px; width: 58px; z-index: 1000; height: 12px;\">";
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
	
	function existJomSocial(){
		$return = $this->get("joomSocial");
		return $return;
	}
	
	function existComBuilder(){
		$return = $this->get("comBuilder");
		return $return;
	}		

}