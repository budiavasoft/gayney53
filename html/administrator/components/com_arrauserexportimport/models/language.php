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
 * file: language.php
 *
 **** class 
     ArrausersexportimportModelLanguage 
	 
 **** functions
     __construct();
     store();
     fk_slashes();
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * ArrausersexportimportModelLanguageModel
 */
class ArrausersexportimportModelLanguage extends JModel{
	/**
	 * Constructor that retrieves the ID from the request
	 * @access	public
	 * @return	void
	 */
	function __construct(){		
		parent::__construct();
	}

	//save language file 
    function store(){
        $ok = false; 
		$data = JRequest::get( 'post', JREQUEST_ALLOWHTML );	
		$language = JPATH_ROOT.DS."administrator".DS."language".DS."en-GB".DS."en-GB.com_arrauserexportimport.ini";		
        $textbe = $this->fk_slashes($data["language_file"]);	
		$g = fopen ($language, "w");
		if(fwrite ($g, $textbe)){
		    $ok = true; 
		}		
		fclose ($g);
		return $ok;
    } 
	
	function fk_slashes($string){	
		while(strstr($string, '\\')) {
			$string=stripslashes($string);
		}
		return $string;
	}
}