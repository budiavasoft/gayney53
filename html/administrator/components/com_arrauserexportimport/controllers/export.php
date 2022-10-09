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
     ArrausersexportimportControllExport 
	 
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
class ArrausersexportimportControllerExport extends ArrausersexportimportController{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct() {	  
		parent::__construct();
		// Register Extra tasks
		$this->registerTask( 'export', 'export' );
		$this->registerTask( 'export_button', 'exportFile' );
		$this->registerTask( 'export_file', 'exportFile' );
		$this->registerTask( 'video', 'video' );
	}
	
	function video(){
   		JRequest::setVar( 'view', 'export' );
		JRequest::setVar( 'layout', 'video'  );
        $view = $this->getView("export", "html");
		$view->setLayout("video");
        $view->video();
        die();
    }	    
	
	//set view for export tab
    function export(){
		JRequest::setVar( 'view', 'export' );
		JRequest::setVar( 'layout', 'default'  );		
		$model = $this->getModel('export');		
		parent::display();
	}
	
	//set model end request method for export command
	function exportFile(){
	    $model = $this->getModel('export');
		$model->export();
		$config = new JConfig();
		$this->setRedirect(Juri::base()."components/com_arrauserexportimport/files/".$config->db."_users.zip");
	}

    //out from export tab
    function cancel(){
		$msg = JText::_( 'ARRA_OPERATION_CANCELED' );
		$this->setRedirect( 'index.php?option=com_arrauserexportimport', $msg );
	}	
	
}