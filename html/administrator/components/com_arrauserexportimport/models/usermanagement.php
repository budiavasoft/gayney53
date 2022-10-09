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
     ArrausersexportimportModelUtf
	 
 **** functions
     __construct();
	 export();
	 getUserType();
	 setExportType();	 
	 sendMail();
	 mkfile();
	 checked();
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * ArrausersexportimportModelExport Model
 *
 */
class ArrausersexportimportModelUsermanagement extends JModel{
	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct(){		
		parent::__construct();
	}
		
	function block(){
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$db 			=& JFactory::getDBO();		
		$acl			=& JFactory::getACL();
		$currentUser 	=& JFactory::getUser();
		$cid 	= JRequest::getVar( 'cid', array(), '', 'array' );
		$task = JRequest::getVar("task");	
		$block  = $task == 'block' ? 1 : 0;				
		JArrayHelper::toInteger( $cid );
		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select a User to '.$task, true ) );
		}
		foreach ($cid as $id){		
			// check for a super admin ... can't delete them
			$objectID 	= $acl->get_object_id( 'users', $id, 'ARO' );
			$groups 	= $acl->get_object_groups( $objectID, 'ARO' );
			$this_group = strtolower( $acl->get_group_name( $groups[0], 'ARO' ) );

			$msg = '';
			$success = false;
			if ( $this_group == 'super administrator' ){
				$msg = JText::_( 'You cannot block a Super Administrator' );
			}
			else if ( $id == $currentUser->get( 'id' ) ){
				$msg = JText::_( 'You cannot block Yourself!' );
			}
			else if ( ( $this_group == 'administrator' ) && ( $currentUser->get( 'gid' ) == 24 ) ){
				$msg = JText::_( 'WARNBLOCK' );
			}
			else{
				$user =& JUser::getInstance((int)$id);
				$count = 2;
				if ( $user->get( 'gid' ) == 25 ){
					// count number of active super admins
					$query = 'SELECT COUNT( id )' . ' FROM #__users' . ' WHERE gid = 25' . ' AND block = 0';
					$db->setQuery( $query );
					$count = $db->loadResult();
				}

				if ( $count <= 1 && $user->get( 'gid' ) == 25 ){
					// cannot delete Super Admin where it is the only one that exists
					$msg = "You cannot block this Super Administrator as it is the only active Super Administrator for your site";
				}
				else{
					$user =& JUser::getInstance((int)$id);
					$user->block = $block;
					$user->save();

					if($block){
						JRequest::setVar( 'task', 'block' );
						JRequest::setVar( 'cid', array($id) );
						// delete user acounts active sessions
						$this->logout();
					}
				}
			}
		}
		return $msg;			
	}
	
	function logout(){
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		global $mainframe;

		$db		=& JFactory::getDBO();
		$task = JRequest::getVar("task");
		$cids 	= JRequest::getVar( 'cid', array(), '', 'array' );
		$client = JRequest::getVar( 'client', 0, '', 'int' );
		$id 	= JRequest::getVar( 'id', 0, '', 'int' );

		JArrayHelper::toInteger($cids);

		if ( count( $cids ) < 1 ) {
			$this->setRedirect( 'index.php?option=com_arrauserexportimport&controller=usermanagement', JText::_( 'User Deleted' ) );
			return false;
		}

		foreach($cids as $cid){
			$options = array();
			if ($task == 'logout' || $task == 'block') {
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
				$this->setRedirect( 'index.php?option=com_arrauserexportimport&controller=usermanagement', $msg );
				break;
		}
	}

}