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
 * file: additionalcolumns.php
 *
 **** class 
     ArrausersexportimportControllerAdditionalcolumns 
	 
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
class ArrausersexportimportControllerAdditionalcolumns extends ArrausersexportimportController{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct() {	  
		parent::__construct();
		// Register Extra tasks				
		$this->registerTask( 'newcolumns', 'newcolumns' );
		$this->registerTask( 'import_button', 'importFile' );
		$this->registerTask( 'import_file', 'importFile' );
		$this->registerTask( 'search', 'search' );
		$this->registerTask( 'export', 'export' );
	}	    
	
	//set view for export tab
    function newcolumns(){		
		JRequest::setVar( 'view', 'additionalcolumns' );
		JRequest::setVar( 'layout', 'default'  );		
		$model = $this->getModel('additionalcolumns');
		
		parent::display();
	}
		
	//set model end request method for export command
	function importFile(){
	    $model = $this->getModel('additionalcolumns');		
		$message_completed = $model->import();		
		$message_array = explode("+", $message_completed);
		if(isset($message_array)){		    
			if(isset($message_array[0]) && $message_array[0]=="ERROR"){
				$this->setRedirect(JURI::base()."index.php?option=com_arrauserexportimport&task=newcolumns&controller=additionalcolumns", $message_array[1], 'notice');
			}
			else{
				$this->setRedirect(JURI::base()."index.php?option=com_arrauserexportimport&task=newcolumns&controller=additionalcolumns", $message_array[1]);
			}   
		}
		
	}

    //out from export tab
    function cancel(){
		$msg = JText::_( 'ARRA_OPERATION_CANCELED' );
		$this->setRedirect( 'index.php?option=com_arrauserexportimport', $msg );
	}	
	
	function getSearchUsers(){
		$column = JRequest::getVar("fields", "");
		if($column != ""){
			$filteroptions = JRequest::getVar("filteroptions", "");
			$keyword = JRequest::getVar("keyword", "");
			
			$and = "";
			if($filteroptions == 0){
				$and .= " and u.".$column." <> '' ";
			}
			elseif($filteroptions == 1){
				$and .= " and u.".$column." = '' ";
			}
			
			if(trim($keyword) != "" && trim($keyword) != "Keyword..."){
				$and .= " and u.".$column." like '%".trim($keyword)."%' ";
			}
			
			$db =& JFactory::getDBO();
			$sql = "select u.id, u.name, u.username, u.email, u.registerDate, u.usertype, u.".$column." from #__users u where 1=1 ".$and." group by u.id";
			$db->setQuery($sql);
			$db->query();
			$result = $db->loadAssocList();
			return $result;
		}
		else{
			return array();
		}	
	}
	
	function search(){
		$result = $this->getSearchUsers();
		$count = intval(count($result));
		if($count > 0){
			echo '<span style="font-size: 14px; color:green; font-weight: bold;">'.JText::_("ARRA_RESULT").': '.$count.' users</span>. <a style="font-size: 14px;" href="#" onclick="document.adminForm2.task.value=\'export\'; document.adminForm2.submit();">'.JText::_("ARRA_CLICK_HERE").'</a> <span style="font-size: 14px; color:green; font-weight: bold;">'.JText::_("ARRA_TO_EXPORT_RESULT").'</span>';
		}
		else{
			echo "<span style=\"font-size: 14px; color:red; font-weight: bold;\">No Result</span>";
		}
	}
	
	function export(){
		$db =& JFactory::getDBO();
		$separator = JRequest::getVar("separator", ",");
		$data = $this->getSearchUsers();
		$content = "";
		$header = array();
		$column = JRequest::getVar("fields", "");
		
		if(isset($data) && count($data) > 0){
			$header[] = "name";
			$header[] = "username";
			$header[] = "email";
			$header[] = "registerDate";
			$header[] = "usertype";
			$header[] = $column;
			$content .= implode($separator, $header)."\n";
			
			foreach($data as $key=>$value){
				$row = array();
				$row[] = $value["name"];
				$row[] = $value["username"];
				$row[] = $value["email"];
				$row[] = $value["registerDate"];
				$row[] = $value["usertype"];
				$row[] = $value[$column];
				$content .= implode($separator, $row)."\n";
			}
		}
		
		$config = new JConfig();
		$filename = $config->db."_filters.csv";	
		header("Content-Type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=".$filename);
		header("Pragma: no-cache");
		header("Expires: 0");
		echo utf8_decode($content);
		exit();
	}
}

?>