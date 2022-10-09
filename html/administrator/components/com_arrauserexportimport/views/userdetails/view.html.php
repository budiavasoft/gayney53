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
 * file: view.html.php
 *
 **** class 
     ArrausersexportimportViewExport 
	 
 **** functions
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );
JHTML::_( 'behavior.modal' );
/**
 * ArrausersexportimportViewExport View
 *
 */
class ArrausersexportimportViewUserdetails extends JView{
	/**
	 * display method 
	 * @return void
	 **/
	function display($tpl = null){						
		$task = JRequest::getVar("task");
		if($task == "new"){
			$edit = false;
		}
		else{
			$edit = true;
		}
		JRequest::setVar('edit', $edit);
		$cid		= JRequest::getVar( 'cid', array(0), '', 'array' );
		//$edit		= JRequest::getVar('edit',true);
		$me 		= JFactory::getUser();
		JArrayHelper::toInteger($cid, array(0));

		$db 		=& JFactory::getDBO();
		
		if($edit){
			$user 		=& JUser::getInstance( $cid[0] );
		}	
		else{
			$user 		=& JUser::getInstance();
		}	

		$myuser		=& JFactory::getUser();
		$acl		=& JFactory::getACL();

		// Check for post data in the event that we are returning
		// from a unsuccessful attempt to save data
		$post = JRequest::get('post');
		if ( $post ) {
			$user->bind($post);
		}

		if ( $user->get('id')){
			$query = 'SELECT *'
			. ' FROM #__contact_details'
			. ' WHERE user_id = '.(int) $cid[0]
			;
			$db->setQuery( $query );
			$contact = $db->loadObjectList();
		}
		else{
			$contact 	= NULL;
			// Get the default group id for a new user
			$config		= &JComponentHelper::getParams( 'com_users' );
			$newGrp		= $config->get( 'new_usertype' );
			$user->set( 'gid', $acl->get_group_id( $newGrp, null, 'ARO' ) );
		}

		$userObjectID 	= $acl->get_object_id( 'users', $user->get('id'), 'ARO' );
		$userGroups 	= $acl->get_object_groups( $userObjectID, 'ARO' );
		$userGroupName 	= strtolower( $acl->get_group_name( $userGroups[0], 'ARO' ) );

		$myObjectID 	= $acl->get_object_id( 'users', $myuser->get('id'), 'ARO' );
		$myGroups 		= $acl->get_object_groups( $myObjectID, 'ARO' );
		$myGroupName 	= strtolower( $acl->get_group_name( $myGroups[0], 'ARO' ) );;


		if ( $userGroupName == $myGroupName && $myGroupName == 'administrator' ){
			// administrators can't change each other
			$lists['gid'] = '<input type="hidden" name="gid" value="'. $user->get('gid') .'" /><strong>'. JText::_( 'Administrator' ) .'</strong>';
		}
		else{
			$gtree = $acl->get_group_children_tree( null, 'USERS', false );

			$lists['gid'] 	= JHTML::_('select.genericlist',   $gtree, 'gid', 'size="10"', 'value', 'text', $user->get('gid') );
		}

		// build the html select list
		$lists['block'] 	= JHTML::_('select.booleanlist',  'block', 'class="inputbox" size="1"', $user->get('block') );

		// build the html select list
		$lists['sendEmail'] = JHTML::_('select.booleanlist',  'sendEmail', 'class="inputbox" size="1"', $user->get('sendEmail') );
		
		$this->assignRef('me', 		$me);
		$this->assignRef('lists',	$lists);
		$this->assignRef('user',	$user);
		$this->assignRef('contact',	$contact);

		parent::display($tpl);
	}
}