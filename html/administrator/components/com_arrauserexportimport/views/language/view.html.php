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
     ArrausersexportimportViewLanguage
	 
 **** functions
     display();
     languageFile();
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * ArrausersexportimportViewLanguage View
 *
 */
class ArrausersexportimportViewLanguage extends JView{
	/**
	 * display method 
	 * @return void
	 **/
	function display($tpl = null){		
		
		// make ToolBarHelper with name of component.
		JToolBarHelper::title(   JText::_( 'ARRA_USER_EXPORT' ), 'generic.png' );
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel ('cancel', 'Cancel');		
		//make drop down with user types for export
		$language_file = $this->languageFile();		
		$this->assignRef('language_file', $language_file);     						
		parent::display($tpl);
	}
	
	//return language content
	function languageFile(){
	    $filename = JPATH_ROOT.DS."administrator".DS."language".DS."en-GB".DS."en-GB.com_arrauserexportimport.ini";
		$handle = fopen ( $filename, 'r' );
		$language_file = fread ( $handle, filesize ( $filename ) );
		fclose( $handle );		
		return $language_file;
	}
	
}