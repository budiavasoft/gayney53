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
    JHTML::_('behavior.tooltip');
    $document =& JFactory::getDocument();
    $document->addStyleSheet("components/com_arrauserexportimport/css/jomsocial.css");
	$document->addScript(JURI::base()."components/com_arrauserexportimport/includes/js/validations.js");		
?>

<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm" id="adminForm" onsubmit="return validateJomSocialExport(<?php echo $this->total_groups; ?>);"> 

	<table width="100%"> 
		<tr>		 		 
			<td width="33%" valign="top" align="center">
				<fieldset class="adminform">
				<legend>
					  <span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_JOMSOCIAL_EXPORT") . "::" .JText::_("ARRA_TOOLTIP_JOMSOCIAL_EXPORT"); ?>" >
						   <?php echo JText::_("ARRA_JOMSOCIAL_EXPORT"); ?>
					  </span>					  
					  <table width="100%">
							<tr>
								<td class="legend_column">
									<span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_JOMSOCIAL_FIELDS") . "::" .JText::_("ARRA_TOOLTIP_JOMSOCIAL_FIELDS"); ?>" >
										<?php echo JText::_("ARRA_JOMSOCIAL_FIELDS"); ?>										
									</span>
									<?php										
										if($this->existJomSocial()){
									?>
									<table width="100%" class="fieldset">
										<tr>
											<td class="td_export_definitions">
												<input type="checkbox" name="toggle1" id="toggle1" onclick="javascript:checkAllFields(<?php echo $this->total_columns; ?>);"/>&nbsp;
												<span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_JOMSOCIAL_CHECK_ALL_FIELDS") . "::" .JText::_("ARRA_TOOLTIP_JOMSOCIAL_CHECK_ALL_FIELDS"); ?>" ><?php echo JText::_("ARRA_JOMSOCIAL_CHECK_ALL_FIELDS"); ?>
												</span>
											</td>
										</tr>
										<tr>
										   <td>				   				     
											<?php
												echo $this->jomsocial; ?>
										   </td>											
										</tr>
                                        <tr>
                                        	<td>
                                            	<input type="submit" name="export" value=" <?php echo JText::_("ARRA_EXPORT_BUTTON"); ?> " onclick="document.adminForm.task.value='export'">
                                            </td>
                                        </tr>
                                        <tr>
                                             <td colspan="2" class="td_class">
                                                <input type="checkbox" name="split_name">
                                                <span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_SPLIT_NAME") . "::" .JText::_("ARRA_TOOLTIP_SPLIT_NAME"); ?>">
                                                    <?php echo JText::_("ARRA_SPLIT_NAME"); ?>
                                                </span>
                                             </td>                                             	                                           		
                                        </tr>
										<tr>
                                             <td colspan="2" class="td_class">
                                                <input type="checkbox" name="remove_header">
                                                <span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_REMOVE_HEADER") . "::" .JText::_("ARRA_TOOLTIP_ARRA_REMOVE_HEADER"); ?>">
                                                    <?php echo JText::_("ARRA_REMOVE_HEADER"); ?>
                                                </span>
                                             </td>                                             	                                           		
                                        </tr>
									</table>									
									<?php
										}
									?>
								</td>
							</tr>
							
                            <tr>
								<td class="legend_column">
									<span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_JOMSOCIAL_GROUP_TYPE") . "::" .JText::_("ARRA_TOOLTIP_JOMSOCIAL_GROUP_TYPE"); ?>" >
										<?php echo JText::_("ARRA_JOMSOCIAL_GROUP_TYPE"); ?>										
									</span>
									<?php										
										if($this->existJomSocial()){
									?>
									<table width="100%" class="fieldset">
										<tr>
											<td class="td_export_definitions">
												<input type="checkbox" name="toggle2" id="toggle2" onclick="javascript:checkAllGroups(<?php echo $this->total_groups; ?>);" checked="checked"/>&nbsp;
												<span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_JOMSOCIAL_CHECK_ALL_GROUPS") . "::" .JText::_("ARRA_TOOLTIP_JOMSOCIAL_CHECK_ALL_GROUPS"); ?>" ><?php echo JText::_("ARRA_JOMSOCIAL_CHECK_ALL_GROUPS"); ?>
												</span>
											</td>
										</tr>
										<tr>
										   <td>				   				     
											<?php
												echo $this->gruop_type; ?>
										   </td>											
										</tr>
									</table>
									<?php
										}
									?>
								</td>
							</tr>
                            
							<tr>
								<td class="legend_column">
									<span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_EXPORT_FILE_TYPE") . "::" .JText::_("ARRA_TOOLTIP_EXPORT_FILE_TYPE_PANEL"); ?>" >
										<?php echo JText::_("ARRA_EXPORT_FILE_TYPE"); ?>										
									</span>
									<table class="fieldset" width="100%">
										<tr>	
											<td colspan="2" align="left">
											   <div id="file_type_id">
												   <table> 
													   <tr>
														   <td class="td_class">
																<span class="editlinktip hasTip" title=" <?php echo JText::_("ARRA_SEPARATOR")."::".JText::_("ARRA_TIP_SEPARATOR") ?> ">
																	   <?php echo JText::_("ARRA_SEPARATOR")."<br/>";?>
																</span>
																<span>
																	<a style="color:red;" href="#" onclick="javascript:hide_show('separator_div'); return false;"><?php echo JText::_("ARRA_SEPARATOR_2_HEADER"); ?></a></span>
							   					<div id="separator_div" style="display:none; color:red;"><?php echo JText::_("ARRA_SEPARATOR_2"); ?></div>						
														  </td>
														  <td valign="top">
															  <?php  echo $this->separators_export; ?> 
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
								</td>
							</tr>
							
							<!-- <tr>                            	
								<td class="legend_column">
									<span class="editlinktip hasTip" title="<?php //echo JText::_("ARRA_EXPORT_USER_TABLE") . "::" .JText::_("ARRA_TOOLTIP_EXPORT_USER_TABLE"); ?>" >
										<?php //echo JText::_("ARRA_EXPORT_USER_TABLE"); ?>										
									</span>
                                    <table class="fieldset">
                                    	<tr>
                                        	<td>
									<?php // echo $this->table_file_type; ?>
                                    		</td>
                                        </tr>
                                    </table>     
								</td>                                	
							</tr> -->
							
							<tr>
								<td class="legend_column">
									<span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_SEND_TO_EMAIL") . "::" .JText::_("ARRA_TOOLTIP_SEND_TO_EMAIL"); ?>" >
										<?php echo JText::_("ARRA_SEND_TO_EMAIL"); ?>										
									</span>
									<table class="fieldset">
										<tr>	
											<td colspan="2" align="left">
												<?php  echo $this->email_to; ?>
											</td>
										</tr>
									</table>		
								</td>
							</tr>
							
					  </table>					  
				</legend>
			</td>
			
			<td width="33%" valign="top" align="center">
				<fieldset class="adminform">
				<legend>
					  <span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_JOMSOCIAL_IMPORT") . "::" .JText::_("ARRA_TOOLTIP_JOMSOCIAL_IMPORT"); ?>" >
						   <?php echo JText::_("ARRA_JOMSOCIAL_IMPORT"); ?>
					  </span>
                      <table>					  		
							<tr>
								 <td valign="top">
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
                            	<td class="legend_column">
									<span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_UPLOAD_FILE") . "::" .JText::_("ARRA_TOOLTIP_UPLOAD_FILE"); ?>" >
										<?php echo JText::_("ARRA_UPLOAD_FILE"); ?>										
									</span>
									<table class="fieldset" width="100%">
										<tr width="100%">	
											<td colspan="2" align="left">
												<?php echo $this->upload_file; ?>
											</td>
										</tr>
										<tr width="100%">
											<td class="td_settings_options" width="100%">
												<?php 
													echo JText::_("ARRA_UPLOAD_FILE_NOTE"); 													
												?>												
											</td>
										</tr>
										<tr>
											<td class="td_settings_options" style="text-align:center;" width="100%">
												<?php
													echo JText::_("ARRA_FROM")."&nbsp;&nbsp;".'<input type="text" name="min_value" id="min_value" size="5"/>'; 
													echo "&nbsp;&nbsp;&nbsp;&nbsp;";
													echo JText::_("ARRA_TO")."&nbsp;&nbsp;".'<input type="text" name="max_value" id="max_value" size="5"/>'; 
												?>
												<div style="float:right;">
													<a href="<?php echo Juri::base()."components/com_arrauserexportimport/files/details.txt"; ?>" target=\"_blank\"><?php echo JText::_("ARRA_MORE_DETAILS"); ?></a>
												</div>												
											</td>
										</tr>
									</table>		
								</td>
                            </tr>
                            
                            <tr>
                            	<td class="legend_column">
									<span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_JOMSOCIAL_IMPORT_SETTINGS") . "::" .JText::_("ARRA_TOOLTIP_JOMSOCIAL_IMPORT_SETTINGS"); ?>" >
										<?php echo JText::_("ARRA_JOMSOCIAL_IMPORT_SETTINGS"); ?>										
									</span>
									<table class="fieldset">
										<tr>	
											<td colspan="2" align="left">
												<?php echo $this->allSettings; ?>
											</td>
										</tr>
									</table>		
								</td>
                            </tr>
							
							<tr>
								<td class="legend_column">
									<span class="editlinktip hasTip" title="<?php echo JText::_("ARRA_SEND_TO_EMAIL") . "::" .JText::_("ARRA_TOOLTIP_EMAILS_OPTIONS_PANEL"); ?>" >
										<?php echo JText::_("ARRA_SEND_TO_EMAIL"); ?>										
									</span>
									<table class="fieldset" width="100%">
										<tr>	
											<td colspan="2" align="left">
												<?php  echo $this->email_to_import; ?>
											</td>
										</tr>
									</table>		
								</td>
							</tr>
                            
                      </table>
				</legend>
			</td>
		</tr>
	</table>			
		  		  				  
<input type="hidden" name="option" value="com_arrauserexportimport" />
<input type="hidden" name="task" value="jomsocial" />
<input type="hidden" name="controller" value="jomsocial" />
<input type="hidden" name="boxchecked" value="" />
<input type="hidden" name="type_file" value="" />

</form>
