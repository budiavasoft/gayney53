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
 * file: default.php
 *
 **** class     
 **** functions
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

    $document =& JFactory::getDocument();
    $document->addStyleSheet("components/com_arrauserexportimport/css/arra_admin_layout.css");
	$document->addScript(JURI::base()."components/com_arrauserexportimport/includes/js/validations.js");
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">   
    
    <table width="100%" class="adminlist">
		<tr>
			<td width="100%" valign="top">
				<h2><?php echo JText::_( 'ARRA_LANGUAGE_PANEL', true );?> </h2>
				<table width="100" class="adminform"> 
					<tr>
						<td> 
							<textarea cols="100" rows="25" name="language_file"><?php echo $this->language_file ?></textarea>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table> 	 
   
<input type="hidden" name="option" value="com_arrauserexportimport" />
<input type="hidden" name="task" value="language" />
<input type="hidden" name="controller" value="language" />
</form>
