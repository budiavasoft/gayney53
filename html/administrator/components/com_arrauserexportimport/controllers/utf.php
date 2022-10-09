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
 * file: utf.php
 *
 **** class 
     ArrausersexportimportControllUtf 
	 
 **** functions
     __construct();
	 export();
	 exportFile();
	 cancel();
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * ArrausersexportimportControllerExport Controller
 */
class ArrausersexportimportControllerUtf extends ArrausersexportimportController{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct() {	  
		parent::__construct();
		// Register Extra tasks
		$this->registerTask( 'utf', 'utf' );
		$this->registerTask( 'export_button', 'utf' );
		$this->registerTask( 'export_file', 'utf' );
		$this->registerTask( 'import', 'import' );
		$this->registerTask( 'import_button', 'import' );
		$this->registerTask( 'import_file', 'import' );
	}	    
	
	//set view for export tab
    function utf(){
		JRequest::setVar( 'view', 'utf' );
		JRequest::setVar( 'layout', 'default'  );		
		$model = $this->getModel('utf');		
		parent::display();
	}
	
	function import(){
	    $model = $this->getModel('utf');		
		$message_completed = $model->import();		
		$message_array = explode("+", $message_completed);
		if(isset($message_array)){		    
			if(isset($message_array[0]) && $message_array[0]=="ERROR"){
				$this->setRedirect(JURI::base()."index.php?option=com_arrauserexportimport&task=utf&controller=utf&tab=1", $message_array[1], 'notice');
			}
			else{
				$this->setRedirect(JURI::base()."index.php?option=com_arrauserexportimport&task=utf&controller=utf&tab=1", $message_array[1]);
			}   
		}
		
	}
		
    //out from utf tab
    function cancel(){
		$msg = JText::_( 'ARRA_OPERATION_CANCELED' );
		$this->setRedirect( 'index.php?option=com_arrauserexportimport', $msg );
	}	
	
}