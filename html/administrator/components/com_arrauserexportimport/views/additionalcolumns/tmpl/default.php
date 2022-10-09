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
jimport('joomla.html.pane');

      $document =& JFactory::getDocument();
      $document->addStyleSheet("components/com_arrauserexportimport/css/arra_import_layout.css");
	  $document->addScript(JURI::base()."components/com_arrauserexportimport/includes/js/ajax.js");
	  $document->addScript(JURI::base()."components/com_arrauserexportimport/includes/js/validations.js");	     
?>

<?php
	$tab = JRequest::getVar("tab", 0, "post");
	if($tab == 0){
		$tab = JRequest::getVar("tab", 0, "get");
	}
	$tabs =& JPane::getInstance('Tabs', array('startOffset'=>$tab));
	echo $tabs->startPane("ARRA Profile Pane");
	
	echo $tabs->startPanel(JText::_("ARRA_EDIT_FIELDS_PROFILE"), 'edit-panel');
?>

<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm" id="adminForm" style="width:100%;">
    <table width="90%" align="center"> 
	   <tr width="90%">
	      <td width="40%" valign="top">
			  <table width="100%">
			      <tr>
					 <td valign="top" width="35%">
					    <div id="error_fieldset" style=" <?php if(isset($_SESSION['link_eror']) && $_SESSION['link_eror']=="error"){
						                                     		echo "display:block";
																    unset($_SESSION['link_eror']);
						                                       }
															   elseif(isset($_SESSION['error_empty_column']) && $_SESSION['error_empty_column']=="error_empty_column"){
															   		echo "display:block";
																	unset($_SESSION['error_empty_column']);
															   }
															   else{
															       echo "display:none";
															   }
						                                 ?>">
							<fieldset class="adminform">
								<legend class="errors_legend">
									 <span class="editlinktip hasTip" title="<?php JHTML::_('behavior.tooltip'); echo JText::_("ARRA_ERRORS_PANEL_IMPORT")."::". JText::_("ARRA_TIP_ERRORS_PANEL_IMPORT") ; ?>" >
										   <?php  echo JText::_("ARRA_ERRORS_PANEL_IMPORT"); ?>
									 </span>								    				     
								</legend>
									<?php echo $this->error_message; ?> 					   
							</fieldset>
						</div> 					 
					 </td>
				  </tr>
				   <tr>
					 <td valign="top" width="35%">					     
						<fieldset class="adminform">
							<legend>
								 <span class="editlinktip hasTip" title="<?php JHTML::_('behavior.tooltip'); echo JText::_("ARRA_ADDITIONALCOLUMNS_PANEL_IMPORT") ."::". JText::_("ARRA_TIP_ADDITIONALCOLUMNS_PANEL_IMPORT") ; ?>" >
									   <?php  echo JText::_("ARRA_ADDITIONALCOLUMNS_PANEL_IMPORT"); ?>
								 </span>								    				     
							</legend>
								<?php echo $this->additional_columns; ?> 					   
						</fieldset>						 
					 </td>
				  </tr>				  				 
				  <tr>
					 <td valign="top" width="35%">
					    <fieldset class="adminform">
							<legend>
								 <span class="editlinktip hasTip" title="<?php JHTML::_('behavior.tooltip'); echo JText::_("ARRA_CSV_TXT_PANEL_IMPORT")."::". JText::_("ARRA_TIP_CSV_TXT_PANEL_IMPORT") ; ?>" >
									   <?php  echo JText::_("ARRA_CSV_TXT_PANEL_IMPORT"); ?>
								 </span>								    				     
							</legend>
								<?php echo $this->upload_file_csv_txt; ?> 					   
						</fieldset> 					 
					 </td>
				  </tr>
				   <tr>
					 <td valign="top" width="35%">					     
						<fieldset class="adminform">
						         <table>
								 	<tr>
										<td>
										    <span style="color:#FF0000;"><b>	
												<?php  echo JText::_("ARRA_UPLOAD_FILE_LIMIT_SIZE"); ?>
												<?php  echo @ini_get('upload_max_filesize')."B"; ?>	</b>
											</span>		   
								 		</td>
								 	</tr>
								 </table>
						</fieldset>						 
					 </td>
				  </tr>				 				   
			  </table>
	      </td>
	      <td valign="top" width="60%">
		     <table width="100%">
			     <tr>
				    <td valign="top" width="35%">
					</td>
				 </tr>
			     <tr>
				    <td>
						<fieldset class="adminform">
							<legend>
								 <span class="editlinktip hasTip" title="<?php JHTML::_('behavior.tooltip'); echo JText::_("ARRA_OPTIONS_FOR_EXISTING_USERS_PANEL") . "::".JText::_("ARRA_TOOLTIP_OPTIONS_FOR_EXISTING_USERS_PANEL"); ?>" >
									   <?php  echo JText::_("ARRA_OPTIONS_FOR_EXISTING_USERS_PANEL"); ?>
								 </span>								    				     
							</legend>
								<?php echo $this->allSettings; ?>					   
						</fieldset>
				   </td>	
				</tr>				 
			 </table>	
		 </td>
	   </tr>
	   <tr  width="90%">
	      <td colspan="2">
				<fieldset class="adminform">
					<legend>
						 <span class="editlinktip hasTip" title="<?php JHTML::_('behavior.tooltip'); echo JText::_("ARRA_EMAILS_OPTIONS_PANEL")."::".JText::_("ARRA_TOOLTIP_EMAILS_OPTIONS_PANEL"); ?>" >
							   <?php  echo JText::_("ARRA_EMAILS_OPTIONS_PANEL"); ?>
						 </span>								    				     
					</legend>
						<?php echo $this->emailSettings; ?>					   
				</fieldset>
		 </td>	
	   </tr>
	</table>
	<input type="hidden" name="file_import" value="" />			  
	<input type="hidden" name="option" value="com_arrauserexportimport" />
	<input type="hidden" name="task" value="import_file" />
	<input type="hidden" name="controller" value="additionalcolumns" />
</form>

<?php
	echo $tabs->endPanel();
	echo $tabs->startPanel(JText::_("ARRA_FILTER_FIELDS_PROFILE"), 'filter-panel');
?>
	<form method="post" name="adminForm2" id="adminForm2">
		<fieldset class="adminform">
			<legend>
				  <span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_USER_PROFILE_FILTER") . "::" .JText::_("ARRA_TOOLTIP_USER_PROFILE_FILTER"); ?>" >
					   <?php echo JText::_("ARRA_USER_PROFILE_FILTER"); ?>
				  </span>
			</legend>
			<table>
				<tr>
					<td class="td_settings_options">
						<?php
							echo JText::_("ARRA_SEPARATOR");
						?>
					</td>
					<td>
						<select name="separator">
							<option value=","> , comma</option>
							<option value=";"> ; semicolon</option>
							<option value="|"> | vertical bar</option>
							<option value="."> . dot</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="td_settings_options">
						<?php 
							echo JText::_("ARRA_USER_SELECT_FIELD");
						?>
					</td>
					<td>
						<?php
							echo $this->getFields();
						?>
					</td>
					<td>
						<select name="filteroptions" id="filteroptions">
							<option value="0"><?php echo JText::_("ARRA_USER_COMPLETED_FIELD"); ?></option>
							<option value="1"><?php echo JText::_("ARRA_USER_FIELD_BLANK"); ?></option>
						</select>
						
						<input onfocus="if (this.value=='Keyword...') this.value='';" onblur="if (this.value=='') this.value='Keyword...';" value="Keyword..." type="text" name="keyword" id="keyword">
					</td>
					<td id="divsearch">
						<input type="button" name="searchfield" value="<?php echo JText::_("ARRA_SEARCH_BUTTON"); ?>" onclick="javascrip:searchByFilters();" />
					</td>
					<td id="imagewait" style="display:none; text-align: center; width:50%;">
						<img style="float: none !important;" src="<?php echo JURI::root()."administrator/components/com_arrauserexportimport/images/icons/pleasewait.gif"; ?>" alt="pleasewait" />
					</td>
				</tr>
			</table>
		</fieldset>
		<input type="hidden" name="file_import" value="" />			  
		<input type="hidden" name="option" value="com_arrauserexportimport" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="additionalcolumns" />
	</form>		
<?php	
	echo $tabs->endPanel();
	echo $tabs->endPane();
?>