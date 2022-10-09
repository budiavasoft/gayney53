<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class TOOLBAR_jcsv {

	function _NEW() {
		JToolBarHelper::title( JText::_('Upload CSV File'), 'generic.png' );
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();		
	}

	function _DEFAULT() {
		JToolBarHelper::title( JText::_('The CSV File'), 'generic.png' );
		/*JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();*/
		JToolBarHelper::addNewX();
	}
}

?>