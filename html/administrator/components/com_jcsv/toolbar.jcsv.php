<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

switch ( $task )
{
	case 'add'  :
		TOOLBAR_jcsv::_NEW();
		break;

	default:
		TOOLBAR_jcsv::_DEFAULT();
		break;
}

?>