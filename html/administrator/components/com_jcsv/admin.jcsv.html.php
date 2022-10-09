<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class HTML_jcsv
{

 function uploadFile(){
  JRequest::setVar( 'hidemainmenu', 1 );
?>
        
  <script language="javascript" type="text/javascript">
  <!--
    function submitbutton(pressbutton) {
	   var form = document.adminForm;
	   if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	   }
				
	   submitform( pressbutton );
	}
  //-->
  </script>        
        
  <form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">
	<table class="admintable">
      <tr>
	    <td class="key">
		<label for="message">
		  <?php echo JText::_( 'Month of Reports' ); ?>:
		</label>
	   </td>
	   <td>
	     <select name="month" id="month">
	       <option value=" ">-Select Month-</option>
	       <option value="01">January</option>
    	   <option value="02">February</option>
    	   <option value="03">March</option>
    	   <option value="04">April</option>
    	   <option value="05">May</option>
    	   <option value="06">June</option>
    	   <option value="07">July</option>
    	   <option value="08">August</option>
    	   <option value="09">September</option>
    	   <option value="10">October</option>
    	   <option value="11">November</option>
    	   <option value="12">December</option>
         </select>
	   </td>
	 </tr>
     <tr>
	    <td class="key">
		<label for="message">
		  <?php echo JText::_( 'Year of Reports' ); ?>:
		</label>
	   </td>
	   <td >
	     <select name="year" id="year">
         <option value=" ">-Select Year-</option>
         <?php
			for ($i=date("Y"); $i>2011; $i--)
			{
		?>
    	  	<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
        <?php
			} 
		?>
         </select>
	   </td>
	 </tr>        
	  <tr>
	    <td class="key">
		<label for="message">
		  <?php echo JText::_( 'Upload CSV File' ); ?>:
		</label>
	   </td>
	   <td >
	     <input class="text_area" type="file" name="upfile" id="upfile" size="100" maxlength="255">
	   </td>
	 </tr>     
  </table> 
<input type="hidden" name="update" value="<?php echo date("d-m-Y"); ?>" />
       
<input type="hidden" name="option" value="com_jcsv" />
<input type="hidden" name="task" value="" />        
</form>
<?php
		
	}
	
	function showFile(&$rows, &$pageNav, $option, &$lists){	?>
		<form action="index.php?option=com_jcsv" method="post" name="adminForm"> 
        <table>
          <tr>
            <td align="left" width="100%">
            <?php echo JText::_( 'Filter' ); ?>:
            <input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
            <button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
            <button onclick="document.getElementById('search').value=''; this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
            </td>
        </tr>
  	   </table>
              
		<table class="adminlist">
		<thead>
			<tr>
				<th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows)?>)"></th>
				<th width="50" class="title"><?php echo JHTML::_('grid.sort','ID','id',@$lists['order_Dir'], @$lists['order'] ); ?></td>
				<th><?php echo JHTML::_('grid.sort','File Name','upfile',@$lists['order_Dir'],@$lists['order']); ?></th>
				<th><?php echo JHTML::_('grid.sort','Date','update',@$lists['order_Dir'],@$lists['order']); ?></th>            
			</tr>
		</thead>
        
        <tfoot>
            <tr>
             <td colspan="11">
                <?php echo $pageNav->getListFooter();?>
              </td>
           </tr>
		</tfoot>
        
		<?php
		$k = 0;
		for($i=0, $n=count($rows); $i < $n ; $i++)
		{
			$row = &$rows[$i];
			$checked 	= JHTML::_('grid.id', $i, $row->id); 
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $checked?></td>
				<td><?php echo $row->id?></td>
				<td><?php echo $row->upfile?></td> 
				<td><?php echo $row->update?></td>        	                     
			</tr>
			<?
			$k = 1 - $k;
		}
		?>
		</table>
		<input type="hidden" name="option" value="com_jcsv">
		<input type="hidden" name="task" value="">    
		<input type="hidden" name="boxchecked" value="0">
        <input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>    
		</form>
		<?php
	}
}
?>