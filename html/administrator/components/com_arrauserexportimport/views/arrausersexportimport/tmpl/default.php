
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
	$document->addStyleSheet("components/com_arrauserexportimport/css/arra_about.css");
	$document->addStyleSheet("components/com_arrauserexportimport/css/arra_statistics.css");
	$document->addScript(JURI::base()."components/com_arrauserexportimport/includes/js/ajax.js");
?>

<form action="index2.php" method="post" name="adminForm"> 
	<table width="100%">
	    <tr>
		<td width="20%" valign="top" style="padding-top:30px; padding-left:15px;">
				 <!-- image with package with component -->				
				 <a href="http://www.joomlarra.com" title="joomla components" align="center" target="_blank"> <img src="components/com_arrauserexportimport/images/logo_arra_user_export.png" alt="ARRA User Export Import" title="ARRA User Export Import" height="168"/> </a>				 			
		</td>
		<td width="40%" valign="top" align="center">
		    <table>
			    <tr>
				   <td align="center">
						 <!-- start menu --> 			 
						<a href="index.php?option=com_arrauserexportimport&task=export&controller=export">
							<img src="components/com_arrauserexportimport/images/icons/export_menu.png"
							alt="Export" align="middle" name="" border="0" title="Arra User Export" />
							<span> <?php echo JText::_('ARRA_USER_EXPORT_MENU'); ?> </span>
						</a>
					</td>
					<td align="center">	
						<a href="index.php?option=com_arrauserexportimport&task=import&controller=import">
							<img src="components/com_arrauserexportimport/images/icons/import_menu.png"
							alt="Import" align="middle" name="" border="0" title="Arra User Import" />
							<span><?php echo JText::_('ARRA_IMPORT_MENU'); ?></span>
						</a>
					</td>
					<td align="center">	
						<a href="index.php?option=com_arrauserexportimport&task=language&controller=language">
							<img src="components/com_arrauserexportimport/images/icons/language_menu.png"
							alt="Language" align="middle" name="" border="0" title="Arra User Language"/>
							<span><?php echo JText::_('ARRA_LANGUAGE_MENU'); ?></span>
						</a>
					 </td>	
				</tr>
				<tr>
				    <td colspan="3">
						<p>For any questions you may have please use our forum: <a href="http://www.joomlarra.com/forum" target="_blank">http://www.joomlarra.com/forum</a></p>
						<p>ARRA User Export documentation: <a href="http://www.joomlarra.com/user-export-import-documentation/" target="_blank">http://www.joomlarra.com/user-export-import-documentation/</a></p>
						<p>To report bugs use this link: <a href="http://www.joomlarra.com/4-report-bugs/" target="_blank">http://www.joomlarra.com/4-report-bugs/</a></p>
						<p>Feature request link: <a href="http://www.joomlarra.com/6-feature-request/" target="_blank">http://www.joomlarra.com/6-feature-request/</a></p>	
						<p>General Discussions: <a href="http://www.joomlarra.com/8-general-discussions-about-component/" target="_blank">http://www.joomlarra.com/8-general-discussions-about-component/</a></p>	
					</td>
				</tr>
				<tr>
				  <!--  <td align="center" colspan="3">
					    <b>If you find  our extension useful, please vote for it on:<br/>
								Extensions.joomla.org</b>
					</td>-->
				</tr>								
			</table>	
				 <!-- end menu -->
		</td>
		<td width="40%" valign="top">
				 <!-- start sliders  -->
			<?php 
	
				if (!class_exists('JPane')){ 
				   include(JPATH_LIBRARIES.DS.'joomla'.DS.'html'.DS.'pane.php');
				}
				 
				// start with requested tab
				$tab = JRequest::getVar("tab", "", "post", "string"); 
				if($tab != ""){ 
					 $tab=JRequest::getVar("tab", "", "post", "string");
				}	 
				else{ 
					$tab=0;
				}	
			 
				jimport( 'joomla.html.pane' );
				$tabs =& JPane::getInstance('Sliders');
							
				echo $tabs->startPane('Sliders');
				
				echo $tabs->startPanel(JText::_('ARRA_ABOUT'), JText::_('ARRA_ABOUT'));
			?>	
					<div>                     
						<?php echo $this->about; ?>					                   					
					</div>
			<?php		   
				echo $tabs->endPanel();	
				// start tab with Users Statistics			
				echo $tabs->startPanel(JText::_('ARRA_STATISTICS'), JText::_('ARRA_STATISTICS'));	
			?>
					<!-- content for Users Statistics tab -->
					<div class="user_statistic">                     
						<?php echo $this->listUsers; ?>										                   					
					</div> 
					<!-- <hr></hr>
					<div class="users_criteria">
						<?php //echo $this->criteria; ?>	
					</div>
					-->				
			<?php   
				// end tab with Users Statistics		  									
				echo $tabs->endPanel();
				if($this->existJomSocial()){
					echo $tabs->startPanel(JText::_('ARRA_ABOUT_JOMSOCIAL'), JText::_('ARRA_ABOUT_JOMSOCIAL'));
					echo "<div>";
					echo $this->jomSocialStatistics();
					echo "</div>";
					echo $tabs->endPanel();	
				}							
														
				echo $tabs->endPane();
			?>
			<!-- stop sliders  -->
		</td>
		</tr>
	</table>	
	
</form>