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
 * file: import.php
 *
 **** class 
     ArrausersexportimportControllImport 
	 
 **** functions
     __construct();
	 importFile();
	 import();
	 cancel();
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * ArrausersexportimportControllerImport Controller
 */
class ArrausersexportimportControllerImport extends ArrausersexportimportController{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct() {	  
		parent::__construct();
		// Register Extra tasks				
		$this->registerTask( 'import', 'import' );
		$this->registerTask( 'import_button', 'importFile' );
		$this->registerTask( 'import_file', 'importFile' );
		$this->registerTask( 'back_up', 'backUp' );
	}	    
	
	//set view for export tab
    function import(){		
		JRequest::setVar( 'view', 'import' );
		JRequest::setVar( 'layout', 'default'  );		
		$model = $this->getModel('import');
		
		parent::display();
	}
	
	function backUp(){
		$model = $this->getModel('import');
		$model->backUp();
		$config = new JConfig();
		$this->setRedirect(Juri::base()."components/com_arrauserexportimport/files/".$config->db."_usersBK.zip");
	}
	
	//set model end request method for export command
	function importFile(){
	    $model = $this->getModel('import');		
		$message_completed = $model->import();		
		$message_array = explode("+", $message_completed);
		if(isset($message_array)){		    
			if(isset($message_array[0]) && $message_array[0]=="ERROR"){
				$this->setRedirect(JURI::base()."index.php?option=com_arrauserexportimport&task=import&controller=import", $message_array[1], 'notice');
			}
			else{
				$this->setRedirect(JURI::base()."index.php?option=com_arrauserexportimport&task=import&controller=import", $message_array[1]);
			}   
		}
		
	}

    //out from export tab
    function cancel(){
		$msg = JText::_( 'ARRA_OPERATION_CANCELED' );
		$this->setRedirect( 'index.php?option=com_arrauserexportimport', $msg );
	}	
	
}