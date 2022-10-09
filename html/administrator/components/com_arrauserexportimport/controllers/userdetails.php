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
     ArrausersexportimportControllEditUser 
	 
 **** functions
     __construct();
	 export();
	 exportFile();
	 cancel();
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * ArrausersexportimportControllerEditUser Controller
 */
class ArrausersexportimportControllerUserdetails extends ArrausersexportimportController{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct() {	  
		parent::__construct();
		// Register Extra tasks
		$this->registerTask( 'edit', 'edit' );
		$this->registerTask( 'new', 'edit' );
		$this->registerTask( 'save', 'save' );
		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'cancel', 'cancel' );		
	}	    
	
	//set view for export tab
    function edit(){
		JRequest::setVar( 'view', 'userdetails' );
		JRequest::setVar( 'layout', 'default'  );		
		//$model = $this->getModel('utf');		
		parent::display();
	}	
	
	function save(){
		$model = $this->getModel('Userdetails');	
		$result = $model->save(); 
		
		if($result["0"] === TRUE){
			$msg = JText::sprintf( 'Successfully Saved changes to User', $result["1"] );
			//$this->setRedirect( 'index.php?option=com_arrauserexportimport&task=usermanagement&controller=usermanagement', $msg );
		}
		else{
			$msg = JText::sprintf('Can\'t save this user!');
			//$this->setRedirect( 'index.php?option=com_arrauserexportimport&task=usermanagement&controller=usermanagement', $msg ); 
		}
		echo '<script type="text/javascript">';
		echo 'window.parent.document.getElementById(\'sbox-window\').close();';
		echo 'window.parent.location.href=window.parent.location.href;';
		echo '</script>';		
		$tmpl = JRequest::getVar("tmpl", "");
		if($tmpl == ""){
			$this->setRedirect( 'index.php?option=com_arrauserexportimport&task=usermanagement&controller=usermanagement', $msg );
		}	
	}	
	
	function cancel(){
		echo '<script type="text/javascript">';
		echo 'window.parent.document.getElementById(\'sbox-window\').close();';
		echo '</script>'; 
		$tmpl = JRequest::getVar("tmpl", "");
		if($tmpl == ""){
			$this->setRedirect( 'index.php?option=com_arrauserexportimport&task=usermanagement&controller=usermanagement');
		}	
	}			
}