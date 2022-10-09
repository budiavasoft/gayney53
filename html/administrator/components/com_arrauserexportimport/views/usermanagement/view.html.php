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
     ArrausersexportimportViewUsermanagement
	 
 **** functions
     display();
     languageFile();
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * ArrausersexportimportViewUsermanagement View
 *
 */
class ArrausersexportimportViewUsermanagement extends JView{
	/**
	 * display method 
	 * @return void
	 **/
	function display($tpl = null){		
		
		// make ToolBarHelper with name of component.
		JToolBarHelper::title(   JText::_( 'ARRA_USER_EXPORT' ), 'generic.png' );
		JToolBarHelper::title( JText::_( 'User Manager' ), 'user.png' );
		JToolBarHelper::custom( 'logout', 'cancel.png', 'cancel_f2.png', 'Logout' );
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();
		JToolBarHelper::help( 'screen.users' );
		JToolBarHelper::cancel ('cancel', 'Cancel');		
		//make drop down with user types for export
     	
		global $mainframe, $option;

		$db				=& JFactory::getDBO();
		$currentUser	=& JFactory::getUser();
		$acl			=& JFactory::getACL();

		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'a.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'',			'word' );
		$filter_type		= $mainframe->getUserStateFromRequest( "$option.filter_type",		'filter_type', 		0,			'string' );
		$filter_logged		= $mainframe->getUserStateFromRequest( "$option.filter_logged",		'filter_logged', 	0,			'int' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search",			'search', 			'',			'string' );
		if (strpos($search, '"') !== false) {
			$search = str_replace(array('=', '<'), '', $search);
		}
		$search = JString::strtolower($search);

		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

		$where = array();
		if(isset( $search ) && $search!= ''){
			$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where[] = 'a.username LIKE '.$searchEscaped.' OR a.email LIKE '.$searchEscaped.' OR a.name LIKE '.$searchEscaped;
		}
		if($filter_type){
			if($filter_type == 'Public Frontend'){
				$where[] = ' a.usertype = \'Registered\' OR a.usertype = \'Author\' OR a.usertype = \'Editor\' OR a.usertype = \'Publisher\' ';
			}
			else if($filter_type == 'Public Backend'){
				$where[] = 'a.usertype = \'Manager\' OR a.usertype = \'Administrator\' OR a.usertype = \'Super Administrator\' ';
			}
			else{
				$where[] = 'a.usertype = LOWER('.$db->Quote($filter_type).')';
			}
		}
		if($filter_logged == 1 ){
			$where[] = 's.userid = a.id';
		}
		elseif($filter_logged == 2){
			$where[] = 's.userid IS NULL';
		}

		// exclude any child group id's for this user
		$pgids = $acl->get_group_children($currentUser->get('gid'), 'ARO', 'RECURSE');

		if(is_array( $pgids ) && count( $pgids ) > 0){
			JArrayHelper::toInteger($pgids);
			$where[] = 'a.gid NOT IN (' . implode( ',', $pgids ) . ')';
		}
		$filter = '';
		if($filter_logged == 1 || $filter_logged == 2){
			$filter = ' INNER JOIN #__session AS s ON s.userid = a.id';
		}

		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		$where = (count($where) ? ' WHERE ('.implode( ') AND (', $where ) . ')' : '');

		$query = 'SELECT COUNT(a.id)'
		. ' FROM #__users AS a'
		. $filter
		. $where;
		$db->setQuery( $query );
		$total = $db->loadResult();

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );

		$query = 'SELECT a.*, g.name AS groupname'
			. ' FROM #__users AS a'
			. ' INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id'
			. ' INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id'
			. ' INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id'
			. $filter
			. $where
			. ' GROUP BY a.id'
			. $orderby;
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();

		$n = count( $rows );
		$template = 'SELECT COUNT(s.userid)'
			. ' FROM #__session AS s'
			. ' WHERE s.userid = %d';
		for ($i = 0; $i < $n; $i++){
			$row = &$rows[$i];
			$query = sprintf($template, intval($row->id));
			$db->setQuery($query);
			$row->loggedin = $db->loadResult();
		}

		// get list of Groups for dropdown filter
		$query = 'SELECT name AS value, name AS text'
			. ' FROM #__core_acl_aro_groups'
			. ' WHERE name != "ROOT"'
			. ' AND name != "USERS"';
		$db->setQuery( $query );
		$types[] = JHTML::_('select.option',  '0', '- '. JText::_( 'Select Group' ) .' -' );
		foreach($db->loadObjectList() as $obj){
			$types[] = JHTML::_('select.option',  $obj->value, JText::_($obj->text));
		}
		$lists['type'] = JHTML::_('select.genericlist',   $types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_type" );

		// get list of Log Status for dropdown filter
		$logged[] = JHTML::_('select.option',  0, '- '. JText::_( 'Select Log Status' ) .' -');
		$logged[] = JHTML::_('select.option',  1, JText::_( 'Logged In' ) );
		$lists['logged'] = JHTML::_('select.genericlist',   $logged, 'filter_logged', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_logged");

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search']= $search;

		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$rows);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
	}		
}