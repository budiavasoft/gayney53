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
 * file: usermanagement.php
 *
 **** class 
     ArrausersexportimportControllUsermanagement
	 
 **** functions
     __construct(); 
     save();
	 cancel();	  
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * ArrausersexportimportControllerUsermanagement Controller
 */
class ArrausersexportimportControllerUsermanagement extends ArrausersexportimportController{
    var $_model = null;
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct() {   
		parent::__construct();
		//Register Extra tasks
	    $this->registerTask( 'usermanagement', 'usermanagement' );
		$this->registerTask( 'logout', 'logout' );
		$this->registerTask( 'remove', 'remove' );
		$this->registerTask( 'add', 'newUser' );
		$this->registerTask( 'edit', 'newUser' );
		$this->registerTask( 'unblock', 'block' );
		$this->registerTask( 'block', 'block' );
	}
	
	function usermanagement(){
		JRequest::setVar( 'view', 'usermanagement' );
		JRequest::setVar( 'layout', 'default'  );		
		parent::display();
	}
	
	function newUser(){
		switch($this->getTask()){
			case 'add' : {	
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'layout', 'default'  );
				JRequest::setVar( 'view', 'editUser' );
				JRequest::setVar( 'edit', false );
			} 
			break;
			case 'edit' : {
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'layout', 'default'  );
				JRequest::setVar( 'view', 'editUser' );
				JRequest::setVar( 'edit', true );
			} 
			break;
		}
		$this->setRedirect('index.php?option=com_arrauserexportimport&controller=editUser&task=new');
	}
	
	function logout(){
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		global $mainframe;

		$db		=& JFactory::getDBO();
		$task 	= $this->getTask();
		$cids 	= JRequest::getVar( 'cid', array(), '', 'array' );
		$client = JRequest::getVar( 'client', 0, '', 'int' );
		$id 	= JRequest::getVar( 'id', 0, '', 'int' );
		JArrayHelper::toInteger($cids);
		if ( count( $cids ) < 1 ){
			$this->setRedirect('index.php?option=com_users', JText::_( 'User Deleted' ) );
			return false;
		}

		foreach($cids as $cid){
			$options = array();
			if ($task == 'logout' || $task == 'block'){
				$options['clientid'][] = 0; //site
				$options['clientid'][] = 1; //administrator
			} 
			else if ($task == 'flogout') {
				$options['clientid'][] = $client;
			}
			$mainframe->logout((int)$cid, $options);
		}
		
		$msg = JText::_( 'User Session Ended' );
		switch ( $task ){
			case 'flogout':
				$this->setRedirect( 'index.php', $msg );
				break;
			case 'remove':
			case 'block':
				return;
				break;
			default:
				$this->setRedirect( 'index.php?option=com_users', $msg );
				break;
		}
	}
	
	function remove(){
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$db 			=& JFactory::getDBO();
		$currentUser 	=& JFactory::getUser();
		$acl			=& JFactory::getACL();
		$cid 			= JRequest::getVar( 'cid', array(), '', 'array' );
		JArrayHelper::toInteger( $cid );

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select a User to delete', true ) );
		}

		foreach ($cid as $id){
			// check for a super admin ... can't delete them
			$objectID 	= $acl->get_object_id( 'users', $id, 'ARO' );
			$groups 	= $acl->get_object_groups( $objectID, 'ARO' );
			$this_group = strtolower( $acl->get_group_name( $groups[0], 'ARO' ) );

			$success = false;
			if ( $this_group == 'super administrator' ){
				$msg = JText::_( 'You cannot delete a Super Administrator' );
			}
			else if ( $id == $currentUser->get( 'id' ) ){
				$msg = JText::_( 'You cannot delete Yourself!' );
			}
			else if ( ( $this_group == 'administrator' ) && ( $currentUser->get( 'gid' ) == 24 ) ){
				$msg = JText::_( 'WARNDELETE' );
			}
			else{
				$user =& JUser::getInstance((int)$id);
				$count = 2;

				if ( $user->get( 'gid' ) == 25 ){
					// count number of active super admins
					$query = 'SELECT COUNT( id )'.' FROM #__users'.' WHERE gid = 25'.' AND block = 0';
					$db->setQuery( $query );
					$count = $db->loadResult();
				}

				if ( $count <= 1 && $user->get( 'gid' ) == 25 ) {
					// cannot delete Super Admin where it is the only one that exists
					$msg = "You cannot delete this Super Administrator as it is the only active Super Administrator for your site";
				}
				else {
					// delete user
					$user->delete();
					$msg = '';
					JRequest::setVar( 'task', 'remove' );
					JRequest::setVar( 'cid', $id );
					// delete user acounts active sessions
					$this->logout();
				}
			}
		}
		$this->setRedirect( 'index.php?option=com_arrauserexportimport&task=usermanagement&controller=usermanagement', $msg);
	}
	
    //out from language tab
    function cancel(){
		$msg = JText::_( 'ARRA_OPERATION_CANCELED' );
		$this->setRedirect( 'index.php?option=com_arrauserexportimport', $msg );
	}	
	
	function block(){
		$model = $this->getModel('Usermanagement');		
		$result = $model->block();
		$this->setRedirect('index.php?option=com_arrauserexportimport&task=usermanagement&controller=usermanagement', $result);
	}
	
}