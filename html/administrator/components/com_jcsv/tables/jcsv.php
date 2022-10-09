<?php
defined('_JEXEC') or die('Restricted Access');

class TableJcsv extends JTable
{
	var $id 			= null;
	var $upfile 		= null;	
	var $update	 		= null;	
	

	function __construct(&$db)
	{
		parent::__construct( '#__jcsv_files', 'id', $db );
	}	
	
}

?>