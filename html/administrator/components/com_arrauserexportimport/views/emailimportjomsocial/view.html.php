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
     ArrausersexportimportViewEmailimportjomsocial
	 
 **** functions
     display();
	 custom_email();
	 checked();
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * ArrausersexportimportViewEmailimportjomsocial View
 *
 */
class ArrausersexportimportViewEmailimportjomsocial extends JView{
	/**
	 * display method 
	 * @return void
	 **/
	function display($tpl = null){				
		// make ToolBarHelper with name of component.
		JToolBarHelper::title(   JText::_( 'ARRA_USER_EXPORT' ), 'generic.png' );
		
		$custom_email = $this->custom_email();		
		$this->assignRef('custom_email', $custom_email);
				
		parent::display($tpl);
	}
	
	function custom_email(){
	    $db =& JFactory::getDBO();
	    $sql= "select params from #__components where link='option=com_arrauserexportimport'";
	    $db->setQuery($sql);
	    $all_result = $db->loadResult();
		$result = "";
		$settings_saved = false;	  
		
	    if(strlen($all_result) != 0){
			$all_array = unserialize($all_result);			
			if(isset($all_array["JomSocialImport"]) && strlen(trim($all_array["JomSocialImport"]))>0){
				$result = $all_array["JomSocialImport"];
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
		$defaul_email_template .= "email: {email}\n";
		$defaul_email_template .= "usertype: {usertype}\n";
		$defaul_email_template .= "password: {password}\n";
		$defaul_email_template .= "group name: {group_name}\n";
		$checked = "checked";
		
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
		if($settings_saved == true){
			if($this->checked("send_email_to_import",$result) == "true"){
				$checked = "checked";
			}
		}
		else{
			$checked = "checked";
		}
		$emailSettings .=             "<span><a style=\"color:red;\" href=\"#\" onclick=\"javascript:hide_show('email_div')\">".JText::_("ARRA_EMAILS_TO_NEW_USERS_2_HEADER")."</a></span>".
							   			"<div id=\"email_div\" style=\"display:none; color:red;\">".JText::_("ARRA_EMAILS_TO_NEW_USERS_2")."</div><br/>";										
		$emailSettings .=             "<input type=\"checkbox\" name=\"send_email_to_import\" id=\"send_email_to_import\" ".$checked.">" . "&nbsp;&nbsp;" . JText::_("ARRA_EMAILS_TO_NEW_USERS");
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
		$emailSettings .=             "{email}";
		$emailSettings .=         "</td>";
		$emailSettings .=         "<td class=\"td_settings_2\">";
		$emailSettings .=              JText::_("ARRA_EMAILS_EMAIL"); 
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
		$emailSettings .=     "<tr>";
		$emailSettings .=         "<td class=\"td_settings_2\">";
		$emailSettings .=             "{group_name}";
		$emailSettings .=         "</td>";
		$emailSettings .=         "<td class=\"td_settings_2\">";
		$emailSettings .=              JText::_("ARRA_EMAILS_GROUP_NAME"); 
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
		$emailSettings .=       "<td>";
		$emailSettings .=           "<input type=\"button\" name=\"save_email_jomsocial\" value=\"".JText::_("ARRA_SAVE_EMAIL_EXPORT_BUTTON")."\" onClick=\"javascript:saveEmailJomSocial();\"> <div id=\"message_error\"></div>";
		$emailSettings .=       "</td>";
		$emailSettings .=       "<td class=\"td_settings_2\">&nbsp;&nbsp;";
		$emailSettings .=           JText::_("ARRA_EMAIL_TEMPLATE_NOTE");
		$emailSettings .=       "</td>";
		$emailSettings .=    "</tr>";
		$emailSettings .= "</table>";
		
		return $emailSettings;
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
		
}