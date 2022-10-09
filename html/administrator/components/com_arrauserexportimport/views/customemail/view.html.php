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
     ArrausersexportimportViewModal
	 
 **** functions
     display();
	 custom_email();
	 checked();
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * ArrausersexportimportViewLanguage View
 *
 */
class ArrausersexportimportViewCustomemail extends JView{
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
	    $settings_saved = false;  
		$config = new JConfig();  	  
	    $db =& JFactory::getDBO();
	    $sql= "select params from #__components where link='option=com_arrauserexportimport'";
	    $db->setQuery($sql);
	    $all_result = $db->loadResult();
		$result = "";	  
		
	    if(strlen($all_result) != 0){
			$all_array = unserialize($all_result);			
			if(isset($all_array["JoomlaExport"]) && strlen(trim($all_array["JoomlaExport"]))>0){
				$result = $all_array["JoomlaExport"];
				$settings_saved = true;  
			}
			else{
				$settings_saved = false;  
			}		
		}
	    
		$default_subject_template  = "Users export from ".$config->sitename;
	    $default_email_template  = "You will find attached the file with the users exported.";
		
	    $config = new JConfig();
	    $emailSettings = "";
		
		$emailSettings .= "<table width=\"100%\" cellspacing=\"5\">";		
		$emailSettings .=     "<tr>";
		$emailSettings .=        "<td width=\"55%\" valign=\"top\">";
				
		$emailSettings .= "<table>";
		$emailSettings .=     "<tr>";
		$emailSettings .=        "<td colspan=\"2\">";
		$emailSettings .=           "<input type=\"button\" name=\"save_email_export\" value=\"".JText::_("ARRA_SAVE_EMAIL_EXPORT_BUTTON")."\" onClick=\"javascript:saveEmailExportSettings();\">";
		$emailSettings .=        "</td>";
		$emailSettings .=     "</tr>";
		$emailSettings .=     "<tr>";
		$emailSettings .=         "<td  class=\"td_settings_options\">";
		$emailSettings .=              "<span class=\"editlinktip hasTip\" title=\"Subject::".JText::_("ARRA_TIP_SUBJECT") ."\" >".
											JText::_("ARRA_EMAIL_IMPORT_SUBJECT").
									   "</span>";
		$emailSettings .=         "</td>";
		$emailSettings .=         "<td class=\"td_settings\">";
		if($settings_saved == false){
		     $emailSettings .=        "<textarea rows=\"1\" cols=\"50\" id=\"subject_template\" name=\"subject_template\" onkeyup=\"this.style.border='1px solid silver'\"  id=\"subject_template\">";
			 $emailSettings .=			  $default_subject_template;
			 $emailSettings .=        "</textarea>";
		}
		else{
		    $emailSettings .=         "<textarea rows=\"1\" cols=\"50\" id=\"subject_template\" name=\"subject_template\" onkeyup=\"this.style.border='1px solid silver'\"  id=\"subject_template\">";
			$emailSettings .=			 $this->checked("subject_template",$result);
			$emailSettings .=         "</textarea>"; 
		}
		$emailSettings .=         "</td>";
		$emailSettings .=     "</tr>";
			
		$emailSettings .=     "<tr>";
		$emailSettings .=         "<td class=\"td_settings_options\">";
		$emailSettings .=              "<span class=\"editlinktip hasTip\" title=\"{from_email}::".JText::_("ARRA_TIP_FROM_EMAIL") ."\" >".
											"{from_email}" .
									   "</span>";
		$emailSettings .=         "</td>";
		$emailSettings .=         "<td class=\"td_settings\">";
		if($settings_saved == false){
		      $emailSettings .=             "<input type=\"text\" id=\"from_email\" name=\"from_email\" onkeyup=\"this.style.border='1px solid silver'\" value=\"".$config->mailfrom."\" id=\"from_email\" size=\"40\">";
		}
		else{
		      $emailSettings .=             "<input type=\"text\" id=\"from_email\" name=\"from_email\" onkeyup=\"this.style.border='1px solid silver'\" value=\"".$this->checked("from_email",$result)."\" id=\"from_email\" size=\"40\">";
		}
		$emailSettings .=         "</td>";
		$emailSettings .=     "</tr>";		
		$emailSettings .=     "<tr>";
		$emailSettings .=         "<td class=\"td_settings_options\">";
		$emailSettings .=              "<span class=\"editlinktip hasTip\" title=\"{from_name}::".JText::_("ARRA_TIP_FROM_NAME") ."\" >".
											"{from_name}" .
									   "</span>";
		$emailSettings .=         "</td>";
		$emailSettings .=         "<td class=\"td_settings\">";
		if($settings_saved == false){
		      $emailSettings .=         "<input type=\"text\" id=\"from_name\" name=\"from_name\" value=\"".$config->fromname."\" onkeyup=\"this.style.border='1px solid silver'\"  id=\"from_name\"  size=\"40\">";
		}
		else{
 	          $emailSettings .=         "<input type=\"text\" id=\"from_name\" name=\"from_name\" value=\"".$this->checked("from_name",$result)."\" onkeyup=\"this.style.border='1px solid silver'\"  id=\"from_name\"  size=\"40\">";
		}
		$emailSettings .=         "</td>";
		$emailSettings .=     "</tr>";		
		$emailSettings .=     "<tr>";
		$emailSettings .=         "<td class=\"td_settings_options\">";
		$emailSettings .=              "<span class=\"editlinktip hasTip\" title=\"{sitename}::".JText::_("ARRA_TIP_SITE_NAME") ."\" >".
											"{sitename}" .
									   "</span>";
		$emailSettings .=         "</td>";
		$emailSettings .=         "<td class=\"td_settings\">";
		if($settings_saved == false){
		       $emailSettings .=          "<input type=\"text\" id=\"sitename\" name=\"sitename\" value=\"".$config->sitename."\" onkeyup=\"this.style.border='1px solid silver'\"  id=\"sitename\"  size=\"40\">";
		}
		else{
		       $emailSettings .=          "<input type=\"text\" id=\"sitename\" name=\"sitename\" value=\"".$this->checked("sitename",$result)."\" onkeyup=\"this.style.border='1px solid silver'\"  id=\"sitename\"  size=\"40\">";
		}
		$emailSettings .=         "</td>";		
		$emailSettings .=     "</tr>";
		$emailSettings .=     "<tr>";
		$emailSettings .=         "<td class=\"td_settings_2\"  colspan='2'><b>";
		$emailSettings .=             JText::_("ARRA_EMAIL_TEMPLATE_NOTE");
		$emailSettings .=         "</b></td>";
		$emailSettings .=     "</tr>";
		
		$emailSettings .=     "<tr>";
		$emailSettings .=        "<td colspan=\"2\">";
		$emailSettings .=          "<div id=\"message_error\"></div>";
		$emailSettings .=        "</td>";
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
		if($settings_saved == false){
			$emailSettings .=                  "<textarea rows=\"16\" cols=\"50\" id=\"email_template\" name=\"email_template\" onkeyup=\"this.style.border='1px solid silver'\"  id=\"email_template\">";
			$emailSettings .=				        $default_email_template;
			$emailSettings .=                  "</textarea>";
		}
		else{
		    $emailSettings .=                  "<textarea rows=\"16\" cols=\"50\" id=\"email_template\" name=\"email_template\" onkeyup=\"this.style.border='1px solid silver'\"  id=\"email_template\">";
			$emailSettings .=				        $this->checked("email_template",$result);
			$emailSettings .=                  "</textarea>";
		}
		$emailSettings .=                 "</td>";
		$emailSettings .=              "</tr>";
		$emailSettings .=          "</table>";  
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