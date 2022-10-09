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

	JHtml::_('behavior.modal');
    JHTML::_('behavior.tooltip');

    $document =& JFactory::getDocument();
    $document->addStyleSheet("components/com_arrauserexportimport/css/arra_admin_layout.css");
	$document->addStyleSheet("components/com_arrauserexportimport/css/arra_export_layout.css");
	$document->addScript(JURI::base()."components/com_arrauserexportimport/includes/js/validations.js");
?>

<form action="index.php" method="post" name="adminForm" id="adminForm"> 
    <table style="text-align: left; color:#FF0000; width: 100%;">
		<tr>
			<td>
				<a class="modal link_arra_video"  rel="{handler: 'iframe', size: {x: 740, y: 425}}" style="font-size: 16px;" target="_blank" 
					href="index.php?option=com_arrauserexportimport&controller=export&task=video&id=14383397">
					<img src="<?php echo JURI::base(); ?>components/com_arrauserexportimport/images/icons/icon_video.gif" class="video_img" />
					<?php echo JText::_("ARRA_VIDEO_EXPORT"); ?>
                </a>
			</td>
		</tr>
	</table>
	
	<table>
	     <tr>	
		    <td valign="top">
		 
		 	<table> 
				<tr>		 		 
					<td width="33%" valign="top" align="center">
						<fieldset class="adminform">
						<legend>
							  <span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_SIMPLE_USER_EXPORT") . "::" .JText::_("ARRA_TOOLTIP_SIMPLE_USER_EXPORT"); ?>" >
								   <?php echo JText::_("ARRA_SIMPLE_USER_EXPORT"); ?>
							  </span>
						</legend>				
							 <table>
								<tr>
								   <td>				   				     
									<?php // first columns name/username/email 
										echo $this->first_columns_export; ?>
								   </td>
								   <td valign="top">
									   <table>
										  <tr>
											 <td valign="top">
												<input type="checkbox" name="split_name">
											 </td>
											 <td valign="top" class="td_export_definitions">
												<span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_SPLIT_NAME") . "::" .JText::_("ARRA_TOOLTIP_SPLIT_NAME"); ?>">
													<?php echo JText::_("ARRA_SPLIT_NAME"); ?>
												</span>	 									    
											 </td>
										  </tr>
										  <tr>
                                             <td valign="top">
												<input type="checkbox" name="remove_header">
											 </td>												
                                             <td class="td_export_definitions">
											    <span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_REMOVE_HEADER") . "::" .JText::_("ARRA_TOOLTIP_ARRA_REMOVE_HEADER"); ?>">
                                                    <?php echo JText::_("ARRA_REMOVE_HEADER"); ?>
                                                </span>
                                             </td>                                             	                                           		
                                           </tr>
										</table> 	 	
								   </td>
								</tr>   		
								<tr>
									<td align="right">
										<input type="submit" name="export" value=" <?php echo JText::_("ARRA_EXPORT_BUTTON"); ?> ">
									</td>
									<td></td>
								</tr>
							 </table>				 
						</fieldset>
					 </td>
				</tr>
				<tr>
				    <td width="100%" align="center" style="padding-top:100px;">	
						 <!-- image with package with component -->
					 <a href="http://www.joomlarra.com" title="joomla components" align="center" target="_blank"> <img src="components/com_arrauserexportimport/images/logo_arra_user_export.png" alt="ARRA User Export Import" title="ARRA User Export Import" /> </a>
					</td> 
				</tr>
			</table>
			
			</td>
		    <td>
			   <table  width="100%">
			    <tr>
				   <td valign="top">
						<fieldset class="adminform"">
							 <legend> 
							    <span class="editlinktip hasTip" title=" <?php echo JText::_("ARRA_FIELDS_TO_EXPORT")."::".JText::_("ARRA_TOOLTIP_FIELDS_TO_EXPORT"); ?> " > 
							         <?php  echo JText::_("ARRA_FIELDS_TO_EXPORT"); ?> 
								</span>
							</legend>
							 <table >
								<tr>
								   <td width="60%">				   				     
									<?php // second columns password/user type/blocked 
										   echo $this->second_columns_export1; ?>
								   </td>
								    <td width="60%" align="right" valign="top">		   				     
									<?php // second columns register date/last visit/activation 
										   echo $this->second_columns_export2; ?>
								   </td>								  
								</tr>
								<?php								
									if($this->second_columns_export3 != ""){
								?>	
								<tr>								  
									<td width="60%" align="left" valign="top">		   				     
									<?php // additional columns 
										 echo $this->second_columns_export3; ?>
								   </td>
								   <td width="60%" align="right" valign="top">
								   </td>								  
								</tr>	
								<?php	
									}
								?>
							</table>				 
						</fieldset>
					</td>
					<td  valign="top"> 		
						<fieldset class="adminform"">				     							  
							  <legend> 
									<span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_USER_TYPE")."::".JText::_("ARRA_TOOLTIP_USER_TYPE_PANEL"); ?>"> 
										 <?php  echo JText::_("ARRA_USER_TYPE"); ?> 
									</span>							 
							  </legend>  
							  <table >
								 <tr>
								   <td valign="top">
									   <?php // user type 
											 echo $this->user_type; ?>
									</td>	 
								</tr>
							 </table>
						</fieldset>
				    </td>
				</tr>
				</table>
				<fieldset class="adminform">
					  <legend> 
					 	<span class="editlinktip hasTip" title=" <?php echo JText::_("ARRA_EXPORT_FILE_TYPE")."::".JText::_("ARRA_TOOLTIP_EXPORT_FILE_TYPE_PANEL");?>"> 
								 <?php  echo JText::_("ARRA_EXPORT_FILE_TYPE"); ?> 
						 </span>							 
					  </legend> 					
					  <table>
					    <tr>	
							<td colspan="2" align="left">
							   <div id="file_type_id">
							       <table> 
								       <tr>
									       <td class="td_class">
												<span class="editlinktip hasTip" title=" <?php echo JText::_("ARRA_SEPARATOR")."::".JText::_("ARRA_TIP_SEPARATOR") ?> ">
												       <?php echo JText::_("ARRA_SEPARATOR");?>
												</span>
												<span>
													<a style="color:red;" href="#" onclick="javascript:hide_show('separator_div'); return false;"><?php echo JText::_("ARRA_SEPARATOR_2_HEADER"); ?></a></span>
							   					<div id="separator_div" style="display:none; color:red;"><?php echo JText::_("ARRA_SEPARATOR_2"); ?></div>						
				                          </td>
									      <td valign="top">
								              <?php  echo $this->separators; ?> 
								          </td> 
								       </tr>
								   </table>
							   </div>
							   <div id="ordering_export">
							       <table> 
								       <tr>
									       <td class="td_class">
												<span class="editlinktip hasTip" title=" <?php echo JText::_("ARRA_ORDERING")."::".JText::_("ARRA_TIP_ORDERING") ?> ">
												       <?php echo JText::_("ARRA_ORDERING");?>
												</span>						
				                          </td>
									      <td>
								              <?php  echo $this->ordering; ?> 
								          </td> 
								       </tr>
								   </table>
							   </div>
						    </td>
						</tr>
						<tr>
							<td>
							   <?php  echo $this->file_type; ?>
							</td>
						</tr>													 								
					 </table>				 
				</fieldset>
				
				<fieldset class="adminform"">
				     <legend>
					      <span class="editlinktip hasTip" title=" <?php echo JText::_("ARRA_EXPORT_USER_TABLE")."::".JText::_("ARRA_TOOLTIP_EXPORT_USER_TABLE"); ?> " >
					          <?php echo JText::_("ARRA_EXPORT_USER_TABLE"); ?> 
						  </span>
					 </legend>                     
						<?php  echo $this->table_file_type; ?>											 
				</fieldset>
				
				<fieldset class="adminform">
				     <legend>
					      <span class="editlinktip hasTip" title=" <?php echo JText::_("ARRA_SEND_TO_EMAIL").":".JText::_("ARRA_TOOLTIP_SEND_TO_EMAIL"); ?> " >
					      <?php  echo JText::_("ARRA_SEND_TO_EMAIL"); ?>
					 </legend>
                     <table cellspacing="5">
					    <?php  echo $this->email_to; ?>
					 </table>				 
				</fieldset>
				
			</td> 
		 </tr>					 	 
	</table>
		  		  
<input type="hidden" name="option" value="com_arrauserexportimport" />
<input type="hidden" name="task" value="export_file" />
<input type="hidden" name="controller" value="export" />
</form>
