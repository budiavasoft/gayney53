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
     ArrausersexportimportControllLanguage
	 
 **** functions
     __construct(); 
     save();
	 cancel();	  
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * ArrausersexportimportControllerLanguage Controller
 */
class ArrausersexportimportControllerLanguage extends ArrausersexportimportController{
    var $_model = null;
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct() {   
		parent::__construct();
		//Register Extra tasks
	    $this->registerTask( 'language', 'language' );
	}
	
	function language(){
		JRequest::setVar( 'view', 'language' );
		JRequest::setVar( 'layout', 'default'  );		
		$model =& $this->getModel('language');		
		parent::display();
	}
	
	function apply(){
		$model = $this->getModel('language'); 
		if ($model->store() ) {
			$msg = "OK+".JText::_('ARRA_LANGUAGE_SAVED');
		} 
		else{
			$msg = "ERROR+".JText::_('ARRA_LANGUAGE_NOT_SAVED');
		}
		
		$message_array = explode("+", $msg);
		if(isset($message_array)){		    
			if(isset($message_array[0]) && $message_array[0]=="ERROR"){
				$this->setRedirect("index.php?option=com_arrauserexportimport&task=language&controller=language", $message_array[1], 'notice');			  
			}
			else{
				$this->setRedirect("index.php?option=com_arrauserexportimport&task=language&controller=language", $message_array[1]);
			}   
		}	
	}
	
	// save language file
	function save(){
	    $model = $this->getModel('language'); 
		if ($model->store() ) {
			$msg = "OK+".JText::_('ARRA_LANGUAGE_SAVED');
		} 
		else{
			$msg = "ERROR+".JText::_('ARRA_LANGUAGE_NOT_SAVED');
		}
		
		$message_array = explode("+", $msg);
		if(isset($message_array)){		    
			if(isset($message_array[0]) && $message_array[0]=="ERROR"){
				$this->setRedirect("index.php?option=com_arrauserexportimport", $message_array[1], 'notice');			  
			}
			else{
				$this->setRedirect("index.php?option=com_arrauserexportimport", $message_array[1]);
			}   
		}		
	}

    //out from language tab
    function cancel(){
		$msg = JText::_( 'ARRA_OPERATION_CANCELED' );
		$this->setRedirect( 'index.php?option=com_arrauserexportimport', $msg );
	}	
	
}