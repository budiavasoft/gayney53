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
      $document->addStyleSheet("components/com_arrauserexportimport/css/arra_import_layout.css");
	  $document->addScript(JURI::base()."components/com_arrauserexportimport/includes/js/ajax.js");
	  $document->addScript(JURI::base()."components/com_arrauserexportimport/includes/js/validations.js");	     
?>

<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm" id="adminForm" style="width:100%;">
     <table style="text-align: left; color:#FF0000; width: 100%;">
		<tr>
			<td>
				<a class="modal link_arra_video"  rel="{handler: 'iframe', size: {x: 740, y: 425}}" style="font-size: 16px;" target="_blank" 
					href="index.php?option=com_arrauserexportimport&controller=export&task=video&id=14383561">
					<img src="<?php echo JURI::base(); ?>components/com_arrauserexportimport/images/icons/icon_video.gif" class="video_img" />
					<?php echo JText::_("ARRA_VIDEO_IMPORT"); ?>
                </a>
			</td>
		</tr>
	</table>
	
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
							<legend>
								 <span class="editlinktip hasTip" title="<?php JHTML::_('behavior.tooltip'); echo JText::_("ARRA_SQL_ZIP_PANEL_IMPORT") ."::". JText::_("ARRA_TIP_SQL_ZIP_PANEL_IMPORT") ; ?>" >
									   <?php  echo JText::_("ARRA_SQL_ZIP_PANEL_IMPORT"); ?>
								 </span>								    				     
							</legend>
								<?php echo $this->upload_file_sql_zip; ?> 					   
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
    <input type="hidden" name="back_up" value="" />				  
	<input type="hidden" name="option" value="com_arrauserexportimport" />
	<input type="hidden" name="task" value="import_file" />
	<input type="hidden" name="controller" value="import" />
</form>
