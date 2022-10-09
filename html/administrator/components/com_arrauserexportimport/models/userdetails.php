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
class ArrausersexportimportModelUserdetails extends JModel{
	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct(){		
		parent::__construct();
	}	
	
	function save(){
		global $mainframe;	
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$option = JRequest::getCmd( 'option');

		// Initialize some variables
		$db			= & JFactory::getDBO();
		$me			= & JFactory::getUser();
		$acl			=& JFactory::getACL();
		$MailFrom	= $mainframe->getCfg('mailfrom');
		$FromName	= $mainframe->getCfg('fromname');
		$SiteName	= $mainframe->getCfg('sitename');

 		// Create a new JUser object
		$user = new JUser(JRequest::getVar( 'id', 0, 'post', 'int'));
		$original_gid = $user->get('gid');

		$post = JRequest::get('post');
		$post['username']	= JRequest::getVar('username', '', 'post', 'username');
		$post['password']	= JRequest::getVar('password', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['password2']	= JRequest::getVar('password2', '', 'post', 'string', JREQUEST_ALLOWRAW);

		if (!$user->bind($post)){
			$mainframe->enqueueMessage(JText::_('CANNOT SAVE THE USER INFORMATION'), 'message');
			$mainframe->enqueueMessage($user->getError(), 'error');
			//$mainframe->redirect( 'index.php?option=com_users', $user->getError() );
			//return false;
			return $this->execute('edit');
		}

		$objectID 	= $acl->get_object_id( 'users', $user->get('id'), 'ARO' );
		$groups 	= $acl->get_object_groups( $objectID, 'ARO' );
		$this_group = strtolower( $acl->get_group_name( $groups[0], 'ARO' ) );


		if ( $user->get('id') == $me->get( 'id' ) && $user->get('block') == 1 ){
			$msg = JText::_( 'You cannot block Yourself!' );
			$mainframe->enqueueMessage($msg, 'message');
			return $this->execute('edit');
		}
		else if ( ( $this_group == 'super administrator' ) && $user->get('block') == 1 ) {
			$msg = JText::_( 'You cannot block a Super Administrator' );
			$mainframe->enqueueMessage($msg, 'message');
			return $this->execute('edit');
		}
		else if ( ( $this_group == 'administrator' ) && ( $me->get( 'gid' ) == 24 ) && $user->get('block') == 1 ){
			$msg = JText::_( 'WARNBLOCK' );
			$mainframe->enqueueMessage($msg, 'message');
			return $this->execute('edit');
		}
		else if ( ( $this_group == 'super administrator' ) && ( $me->get( 'gid' ) != 25 ) ){
			$msg = JText::_( 'You cannot edit a super administrator account' );
			$mainframe->enqueueMessage($msg, 'message');
			return $this->execute('edit');
		}
		// Are we dealing with a new user which we need to create?
		$isNew 	= ($user->get('id') < 1);
		if (!$isNew){
			// if group has been changed and where original group was a Super Admin
			if ( $user->get('gid') != $original_gid && $original_gid == 25 ){
				// count number of active super admins
				$query = 'SELECT COUNT( id )' . ' FROM #__users' . ' WHERE gid = 25' . ' AND block = 0';
				$db->setQuery( $query );
				$count = $db->loadResult();

				if ( $count <= 1 ){
					// disallow change if only one Super Admin exists
					$this->setRedirect( 'index.php?option=com_arrauserexportimport&task=usermanagement&controller=usermanagement', JText::_('WARN_ONLY_SUPER') );
					return false;
				}
			}
		}

		/*
	 	 * Lets save the JUser object
	 	 */
		if (!$user->save()){
			$mainframe->enqueueMessage(JText::_('CANNOT SAVE THE USER INFORMATION'), 'message');
			$mainframe->enqueueMessage($user->getError(), 'error');
			return $this->execute('edit');
		}

		/*
	 	 * Time for the email magic so get ready to sprinkle the magic dust...
	 	 */
		if ($isNew){
			$adminEmail = $me->get('email');
			$adminName	= $me->get('name');

			$subject = JText::_('NEW_USER_MESSAGE_SUBJECT');
			$message = sprintf ( JText::_('NEW_USER_MESSAGE'), $user->get('name'), $SiteName, JURI::root(), $user->get('username'), $user->password_clear );

			if ($MailFrom != '' && $FromName != ''){
				$adminName 	= $FromName;
				$adminEmail = $MailFrom;
			}
			JUtility::sendMail( $adminEmail, $adminName, $user->get('email'), $subject, $message );
		}

		// If updating self, load the new user object into the session
		if ($user->get('id') == $me->get('id')){			
			// Get an ACL object
			$acl = &JFactory::getACL();
			// Get the user group from the ACL
			$grp = $acl->getAroGroup($user->get('id'));
			// Mark the user as logged in
			$user->set('guest', 0);
			$user->set('aid', 1);
			// Fudge Authors, Editors, Publishers and Super Administrators into the special access group
			if ($acl->is_group_child_of($grp->name, 'Registered')      ||
			    $acl->is_group_child_of($grp->name, 'Public Backend'))    {
				$user->set('aid', 2);
			}
			// Set the usertype based on the ACL group name
			$user->set('usertype', $grp->name);

			$session = &JFactory::getSession();
			$session->set('user', $user);
		}
		$task = JRequest::getVar("task", "cancel");
		$return  = array(true, $user->get('name'), $user->get('id'));
		return $return;		
	}			

}