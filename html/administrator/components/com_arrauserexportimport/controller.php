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
 * file: controller.php
 *
 **** class 
     ArrausersexportimportController 
 **** functions
     display();
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class ArrausersexportimportController extends JController{
	/**
	 * Method to display the view
	 * @access	public
	 */
	function display(){		
	    $controller = JRequest::getVar("controller", "");
		$true_export = false;
		$true_import = false;
		$true_language = false;
		$true_about = false;		
		$true_jomsocial = false;
		$true_main = true;
		$true_additional_columns = false;
		$true_utf = false;
		$true_usermanagement = false;
		
		if($controller == "export"){
		    $true_export = true;
			$true_main = false;
		}
		elseif($controller == "import"){
		    $true_import = true;
			$true_main = false;
		}
		elseif($controller == "language"){
		    $true_language = true;
			$true_main = false;
		}
		elseif($controller == "about"){
		    $true_about = true;
			$true_main = false;
		}
		elseif($controller == "jomsocial"){
		    $true_jomsocial = true;
			$true_main = false;
		}		
		elseif($controller == "modal"){
			$true_main = false;
		}
		elseif($controller == "additionalcolumns"){
			$true_additional_columns = true;
			$true_main = false;
		}
		elseif($controller == "utf"){
			$true_utf = true;
			$true_main = false;
		}
		elseif($controller == "usermanagement"){
			$true_usermanagement = true;
			$true_main = false;
		}	
		else{
		    $true_main = true;
		}
		
		JSubMenuHelper::addEntry(JText::_('ARRA_MAIN_MENU'), 'index.php?option=com_arrauserexportimport', $true_main);
		JSubMenuHelper::addEntry(JText::_('ARRA_USER_EXPORT_MENU'), 'index.php?option=com_arrauserexportimport&task=export&controller=export', $true_export);
		JSubMenuHelper::addEntry(JText::_('ARRA_USER_IMPORT_MENU'), 'index.php?option=com_arrauserexportimport&task=import&controller=import', $true_import);	     
		if($this->existJomSocial()){
			JSubMenuHelper::addEntry(JText::_('ARRA_JOMSOCIAL_MENU'), 'index.php?option=com_arrauserexportimport&task=jomsocial&controller=jomsocial', $true_jomsocial);
		}		
		JSubMenuHelper::addEntry(JText::_('ARRA_ADDITIONALCOLUMNS_MENU'), 'index.php?option=com_arrauserexportimport&task=newcolumns&controller=additionalcolumns', $true_additional_columns);
		JSubMenuHelper::addEntry(JText::_('ARRA_UTF_MENU'), 'index.php?option=com_arrauserexportimport&task=utf&controller=utf', $true_utf);
		JSubMenuHelper::addEntry(JText::_('ARRA_USERMANAGEMENT_MENU'), 'index.php?option=com_arrauserexportimport&task=usermanagement&controller=usermanagement', $true_usermanagement);
		JSubMenuHelper::addEntry(JText::_('ARRA_LANGUAGE_MENU'), 'index.php?option=com_arrauserexportimport&task=language&controller=language', $true_language);			
		
		echo '<span style="padding: 10px; background-color:yellow; font-family:Arial; font-weight:bold; float: left;">Migrate/Move your Joomla! 1.5.x users to Joomla! 2.5.x <a  target="_blank" href="http://www.joomlarra.com/joomla-1.7-extensions/arra-user-export-import-for-joomla-1.72.5.html">BUY NOW</a> our commercial version!</span><br/><br/><br/>';
		
		parent::display();
	}
	
	function existJomSocial(){
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
}