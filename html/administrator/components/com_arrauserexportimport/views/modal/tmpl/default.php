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
?>
     <table width="60%" class="adminlist">
        <?php foreach($this->columns as $key=>$value){
		           echo "<tr>";
				   echo    "<td width=\"15%\">";
				   echo        "<b>".$value."</b>";
				   echo    "</td>";
				   echo    "<td width=\"85%\">";
				   echo        JText::_("ARRA_MODAL_TIP_".strtoupper($value));
				   echo    "</td>";
				   echo "</tr>";
		      }
		?>	
	</table> 	 