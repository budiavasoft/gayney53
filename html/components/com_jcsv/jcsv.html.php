<?php
class HTML_jcsv
{
  
  function showAdmin($rows,$rows1,$rows2,$rows3,$tid)
  {
  	if ($tid == "")
	{
  ?>
  	<div id="bill" style="margin-bottom:20px;"><h1><b>User List Billing Statement</b></h1></div>
  <?php
	 foreach($rows as $row)
     {
	 	if ($row->usertype != "Super Administrator")
		{
	 ?>
	 	<div style="width:50%; float:left; margin-left:20px; margin-bottom:5px;"><a href="index.php?option=com_jcsv&Itemid=2&tid=<?=$row->username?>"><?php echo $row->username;?> &nbsp; &nbsp; <?php echo $row->name;?></a></div>
     <?php
	 	}
	 }
	}
	else{
		$sum = 0;
		$sumpast = 0;
		$sumcurr = 0;
		
		$sumann = 0;
		$sumpastann = 0;
		$sumcurrann = 0;
		$sumann2 = 0;
		$sumpastann2 = 0;
		$sumcurrann2 = 0;
		
		$sumcap = 0;
		$sumpastcap = 0;
		$sumcurrcap = 0;
		$sumcap2 = 0;
		$sumpastcap2 = 0;
		$sumcurrcap2 = 0;
		
		$sumcou = 0;
		$sumpastcou = 0;
		$sumcurrcou = 0;
		$sumcou2 = 0;
		$sumpastcou2 = 0;
		$sumcurrcou2 = 0;
		
		$sumgue = 0;
		$sumpastgue = 0;
		$sumcurrgue = 0;
		$sumgue2 = 0;
		$sumpastgue2 = 0;
		$sumcurrgue2 = 0;
		
		$sumlate1 = 0;
		$sumpastlate = 0;
		$sumcurrlate = 0;
		$sumlate2 = 0;
		$sumpastlate2 = 0;
		$sumcurrlate2 = 0;
		
		$sumleg = 0;
		$sumpastleg = 0;
		$sumcurrleg = 0;
		$sumleg2 = 0;
		$sumpastleg2 = 0;
		$sumcurrleg2 = 0;
		
		$strpay = 0;
		$strpay2 = 0;
		
		$sumres = 0;
		$sumpastres = 0;
		$sumcurrres = 0;
		$sumres2 = 0;
		$sumpastres2 = 0;
		$sumcurrres2 = 0;
		
		$sumro = 0;
		$sumpastro = 0;
		$sumcurrro = 0;
		$sumro2 = 0;
		$sumpastro2 = 0;
		$sumcurrro2 = 0;
		
		$sumsec = 0;
		$sumpastsec = 0;
		$sumcurrsec = 0;
		$sumsec2 = 0;
		$sumpastsec2 = 0;
		$sumcurrsec2 = 0;
		
		$sumspe = 0;
		$sumpastspe = 0;
		$sumcurrspe = 0;
		$sumspe2 = 0;
		$sumpastspe2 = 0;
		$sumcurrspe2 = 0;
		
		$mon = JRequest::getVar('month', '','post','string', JREQUEST_ALLOWRAW );
		$yr = JRequest::getVar('year', '','post','string', JREQUEST_ALLOWRAW );
		
		if($mon == "" || $yr == ""){
			if (isset($rows1[0]->month) == ""){
				$mon = isset($rows2[0]->month);
				$yr = isset($rows2[0]->year);
			}
			else{
				$mon = $rows1[0]->month;
				$yr = $rows1[0]->year;
			}
		}
	?>
    	<div id="bill" align="center" style="text-align:center; margin-bottom:20px;"><h1><b>BILLING STATEMENT</b></h1></div>
        <form action="index.php?option=com_jcsv&Itemid=2&tid=<?=$tid?>" method="post" name="adminForm"> 
        <div id="search">
            Please Select 
            <select name="month" id="month">
              <option value=" ">-Select Month-</option>
              <option value="01" <?php if($mon == "01" ){echo 'selected="selected"';} ?>>January</option>
              <option value="02" <?php if($mon == "02" ){echo 'selected="selected"';} ?>>February</option>
              <option value="03" <?php if($mon == "03" ){echo 'selected="selected"';} ?>>March</option>
              <option value="04" <?php if($mon == "04" ){echo 'selected="selected"';} ?>>April</option>
              <option value="05" <?php if($mon == "05" ){echo 'selected="selected"';} ?>>May</option>
              <option value="06" <?php if($mon == "06" ){echo 'selected="selected"';} ?>>June</option>
              <option value="07" <?php if($mon == "07" ){echo 'selected="selected"';} ?>>July</option>
              <option value="08" <?php if($mon == "08" ){echo 'selected="selected"';} ?>>August</option>
              <option value="09" <?php if($mon == "09" ){echo 'selected="selected"';} ?>>September</option>
              <option value="10" <?php if($mon == "10" ){echo 'selected="selected"';} ?>>October</option>
              <option value="11" <?php if($mon == "11" ){echo 'selected="selected"';} ?>>November</option>
              <option value="12" <?php if($mon == "12" ){echo 'selected="selected"';} ?>>December</option>
            </select>
            
            <select name="year" id="year">
              <option value=" ">-Select Year-</option>
            <?php
            for ($i=date("Y"); $i>2011; $i--)
            {
            ?>
              <option value="<?php echo $i; ?>" <?php if($yr == $i ){echo 'selected="selected"';} ?>><?php echo $i; ?></option>
            <?php
            } 
            ?>
            </select>
            <input name="search" type="submit" value="Select" style="color:#FFFFFF;">
        </div>
        </form>
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <?php
        if (isset($rows1[0]->Satellite) == "" && isset($rows2[0]->Satellite) == ""){
            echo '<div align="center" style="width:100%; margin-left:20px;">You do not have any bills..</div>';
        }
		else if(isset($rows1[0]->Satellite) == "" && isset($rows2[0]->Satellite) != ""){
		?>
        	<div>
                <div style="width:30%; float:left; margin-left:20px;">
                    <div>Account: <?php echo $rows2[0]->Account;  ?></div>
                    <div><?php echo $rows2[0]->Name;  ?></div>
                    <div><?php echo $rows2[0]->Address1;  ?></div>
                    <div><?php echo $rows2[0]->City_State_Zip;  ?></div>
                    <div>&nbsp;</div>
                </div>
                 <div style="width:25%; float:right;">
                    <div>Date		: <?php echo $rows2[0]->month; ?>/01/<?php echo $rows2[0]->year; ?></div>
                    <div>Due		: Within 30 Days</div>
                    <?php
						$exp = explode("-",$rows3[0]->update);
                        $tgl = $exp[0];
                    ?>
                    <div>Last Update 	: <?php echo $rows2[0]->month; ?>/<?php echo $tgl; ?>/<?php echo $rows2[0]->year; ?></div>
                    <div>Unit		: <?php echo $rows2[0]->Unit;  ?></div>
                    <div>&nbsp;</div>
                </div>
                <div>&nbsp;</div>
            </div>
            <div style="clear:both;">&nbsp;</div>
        	<div style="margin-left:20px; margin-top:-10px;">Information for online payments through <a href="http://www.mutualofomahabank.com/" target="_blank">www.mutualofomahabank.com</a></div>
            <table width="94%" align="center" cellpadding="2" cellspacing="3" style="border-collapse: collapse;">
                <tr style="border:solid #000000 2px;">
                    <td width="180">1156</td>
                    <td width="100">990</td>
                    <td><?php echo $rows2[0]->Unit;  ?></td>
                </tr>
                <tr style="border:none;">
                    <td>Management Company ID</td>
                    <td>Association ID</td>
                    <td>Property Account Number</td>
                </tr>
            </table>
            <div>&nbsp;</div>
            <table width="95%" align="center" cellpadding="2" cellspacing="3">
            <tr style="background-color:#4B7B6F; color:#FFFFFF; height:40px;">
                <td width="40%">Description</td>
                <td width="15%">&nbsp;</td>
                <td width="15%" align="center">Past Due</td>
                <td width="15%" align="center">Current</td>
                <td width="15%" align="center">Total Due</td>
            </tr>
            <tr style="height:30px;">
                <td><b>Master Association Activity</b></td>
                <td>&nbsp;</td>
                <td colspan="3" align="center">Statement Date : <?php echo $rows2[0]->month; ?>/01/<?php echo $rows2[0]->year; ?></td>
            </tr>
    
            <?php
            
            $it2 = 1;
            foreach($rows2 as $row2)
            {
                
                if (stristr($row2->Description,"Annual Assessment"))
                {
                    $sumpast = $sumpast + $row2->Past_Months;
                    $sumcurr = $sumcurr + $row2->Current_Month;
                    $total2 = $row2->Past_Months + $row2->Current_Month; 
                    $sum = $sum + $total2;
                    
                    $sumpastann2 = $sumpastann2 + $row2->Past_Months;
                    $sumcurrann2 = $sumcurrann2 + $row2->Current_Month;
                    $tann2 = $row2->Past_Months + $row2->Current_Month;  
                    $sumann2 = $sumann2 + $tann2; 
                    
                    if($sumann2 != 0 && stristr(isset($rows2[$it2]->Description),"Annual Assessment")== ""){
                    ?>
                        <tr>
                            <td><?php echo "Annual Assessment" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastann2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrann2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumann2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumpastann2 = 0;
                        $sumcurrann2 = 0;
                        $sumann2 = 0;
                    }
                }
                
                else if (stristr($row2->Description,"Capital"))
                {
                    $sumpast = $sumpast + $row2->Past_Months;
                    $sumcurr = $sumcurr + $row2->Current_Month;
                    $total2 = $row2->Past_Months + $row2->Current_Month; 
                    $sum = $sum + $total2;
                    
                    $sumpastcap2 = $sumpastcap2 + $row2->Past_Months;
                    $sumcurrcap2 = $sumcurrcap2 + $row2->Current_Month;
                    $tcap2 = $row2->Past_Months + $row2->Current_Month;  
                    $sumcap2 = $sumcap2 + $tcap2; 
                    
                    if($sumcap2 != 0 && stristr(isset($rows2[$it2]->Description),"Capital")== ""){
                    ?>
                        <tr>
                            <td><?php echo "Capital Assessment" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastcap2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrcap2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcap2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumpastcap2 = 0;
                        $sumcurrcap2 = 0;
                        $sumcap2 = 0;
                    }
                }
                
                else if (stristr($row2->Description,"Court Fees"))
                {
                    $sumpast = $sumpast + $row2->Past_Months;
                    $sumcurr = $sumcurr + $row2->Current_Month;
                    $total2 = $row2->Past_Months + $row2->Current_Month; 
                    $sum = $sum + $total2;
                    
                    $sumpastcou2 = $sumpastcou2 + $row2->Past_Months;
                    $sumcurrcou2 = $sumcurrcou2 + $row2->Current_Month;
                    $tcou2 = $row1->Past_Months + $row2->Current_Month;  
                    $sumcou2 = $sumcou2 + $tcou2; 
                    
                    if($sumcou2 != 0 && stristr(isset($rows2[$it2]->Description),"Court Fees")== ""){
                    ?>
                        <tr>
                            <td><?php echo "Court Fees" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastcou2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrcou2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcou2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumpastcou2 = 0;
                        $sumcurrcou2 = 0;
                        $sumcou2 = 0;
                    }
                }
                
                else if (stristr($row2->Description,"Guest Fees-Out"))
                {
                    $sumpast = $sumpast + $row2->Past_Months;
                    $sumcurr = $sumcurr + $row2->Current_Month;
                    $total2 = $row2->Past_Months + $row2->Current_Month; 
                    $sum = $sum + $total2;
                    
                    $sumpastgue2 = $sumpastgue2 + $row2->Past_Months;
                    $sumcurrgue2 = $sumcurrgue2 + $row2->Current_Month;
                    $tgue2 = $row2->Past_Months + $row2->Current_Month;  
                    $sumgue2 = $sumgue2 + $tgue2; 
                    
                    if($sumgue2 != 0 && stristr(isset($rows2[$it2]->Description),"Guest Fees-Out")== ""){
                    ?>
                        <tr>
                            <td><?php echo "Guest Fees-Out of Town" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastgue2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrgue2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumgue2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumpastgue2 = 0;
                        $sumcurrgue2 = 0;
                        $sumgue2 = 0;
                    }
                }
                
                else if (stristr($row2->Description,"Late Assessment"))
                {
                    $sumpast = $sumpast + $row2->Past_Months;
                    $sumcurr = $sumcurr + $row2->Current_Month;
                    $total2 = $row2->Past_Months + $row2->Current_Month; 
                    $sum = $sum + $total2;
                    
                    $sumpastlate2 = $sumpastlate2 + $row2->Past_Months;
                    $sumcurrlate2 = $sumcurrlate2 + $row2->Current_Month;
                    $tls2 = $row2->Past_Months + $row2->Current_Month;  
                    $sumlate2 = $sumlate2 + $tls2; 
                    
                    if($sumlate2 != 0 && stristr(isset($rows2[$it2]->Description),"Late Assessment")== ""){
                    ?>
                        <tr>
                            <td><?php echo "Late Assessment" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastlate2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrlate2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumlate2, 2); ?></b></td>
                        </tr>
                        <!--<tr>
                            <td><span style="margin-left:20px;">Late Assessment</span></td>
                            <td align="right" style="border-bottom:solid 1px #000000;"><?php //echo number_format($sumlate2, 2); ?></td>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                         <tr>
                            <td>&nbsp;</td>
                            <td align="right"><?php //echo number_format($sumlate2, 2); ?></td>
                            <td colspan="3">&nbsp;</td>
                        </tr>-->
                <?php
                        $sumlate2 = 0;
                        $sumpastlate2 = 0;
                        $sumcurrlate2 = 0;
                    }
                }
                
                else if (stristr($row2->Description,"Legal Fees"))
                {
                    $sumpast = $sumpast + $row2->Past_Months;
                    $sumcurr = $sumcurr + $row2->Current_Month;
                    $total2 = $row2->Past_Months + $row2->Current_Month; 
                    $sum = $sum + $total2;
                    
                    $sumpastleg2 = $sumpastleg2 + $row2->Past_Months;
                    $sumcurrleg2 = $sumcurrleg2 + $row2->Current_Month;
                    $tleg2 = $row2->Past_Months + $row2->Current_Month;  
                    $sumleg2 = $sumleg2 + $tleg2; 
                    
                    if($sumleg2 != 0  && stristr(isset($rows2[$it2]->Description),"Legal Fees")== ""){
                    ?>
                        <tr>
                            <td><?php echo "Legal Fees-Inv" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastleg2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrleg2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumleg2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumleg2 = 0;
                        $sumpastleg2 = 0;
                        $sumcurrleg2 = 0;
                    }
                    
                }
                
				else if (stristr($row2->Description,"Payment"))
				{
					$sumpast = $sumpast + $row2->Past_Months;
					$sumcurr = $sumcurr + $row2->Current_Month;
					$total2 = $row2->Past_Months + $row2->Current_Month; 
					$sum = $sum + $total2;
					
					$tpay2 = $row2->Past_Months + $row2->Current_Month;
					/*$sumpastpay = $sumpastpay + $row1->Past_Months;
					$sumcurrpay = $sumcurrpay + $row1->Current_Month;  
					$sumpay = $sumpay + $tpay; */
		
					$strpay2 = '<tr>
							<td>Payment</td>
							<td>&nbsp;</td>
							<td align="right"><b>'.number_format($row2->Past_Months, 2).'</b></td>
							<td align="right"><b>'.number_format($row2->Current_Month, 2).'</b></td>
							<td align="right"><b>'.number_format($tpay2, 2).'</b></td>
						</tr>';
		
				}
				
                else if (stristr($row2->Description,"Reserve Assessment"))
                {
                    $sumpast = $sumpast + $row2->Past_Months;
                    $sumcurr = $sumcurr + $row2->Current_Month;
                    $total2 = $row2->Past_Months + $row2->Current_Month; 
                    $sum = $sum + $total2;
                    
                    $sumpastres2 = $sumpastres2 + $row2->Past_Months;
                    $sumcurrres2 = $sumcurrres2 + $row2->Current_Month;
                    $tres2 = $row2->Past_Months + $row2->Current_Month;  
                    $sumres2 = $sumres2 + $tres2; 
                    
                    if($sumres2 != 0 && stristr(isset($rows2[$it2]->Description),"Reserve Assessment")== ""){
                    ?>
                        <tr>
                            <td><?php echo "Reserve Assessment" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastres2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrres2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumres2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumpastres2 = 0;
                        $sumcurrres2 = 0;
                        $sumres2 = 0;
                    }
                }
                
                else if (stristr($row2->Description,"Roof Reserve"))
                {
                    $sumpast = $sumpast + $row2->Past_Months;
                    $sumcurr = $sumcurr + $row2->Current_Month;
                    $total2 = $row2->Past_Months + $row2->Current_Month; 
                    $sum = $sum + $total2;
                    
                    $sumpastro2 = $sumpastro2 + $row2->Past_Months;
                    $sumcurrro2 = $sumcurrro2 + $row2->Current_Month;
                    $tro2 = $row2->Past_Months + $row2->Current_Month;  
                    $sumro2 = $sumro2 + $tro2; 
                    
                    if($sumro2 != 0 && stristr(isset($rows2[$it2]->Description),"Roof Reserve")== ""){
                    ?>
                        <tr>
                            <td><?php echo "Roof Reserve" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastro2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrro2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumro2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumpastro2 = 0;
                        $sumcurrro2 = 0;
                        $sumro2 = 0;
                    }
                }
                
                else if (stristr($row2->Description,"Security Assessment"))
                {
                    $sumpast = $sumpast + $row2->Past_Months;
                    $sumcurr = $sumcurr + $row2->Current_Month;
                    $total2 = $row2->Past_Months + $row2->Current_Month; 
                    $sum = $sum + $total2;
                    
                    $sumpastsec2 = $sumpastsec2 + $row2->Past_Months;
                    $sumcurrsec2 = $sumcurrsec2 + $row2->Current_Month;
                    $tsec2 = $row2->Past_Months + $row2->Current_Month;  
                    $sumsec2 = $sumsec2 + $tsec2; 
                    
                    if($sumsec2 != 0 && stristr(isset($rows2[$it2]->Description),"Security Assessment")== ""){
                    ?>
                        <tr>
                            <td><?php echo "Security Assessment" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastsec2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrsec2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumsec2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumpastsec2 = 0;
                        $sumcurrsec2 = 0;
                        $sumsec2 = 0;
                    }
                }
                
                else if (stristr($row2->Description,"Special Assessment"))
                {
                    $sumpast = $sumpast + $row2->Past_Months;
                    $sumcurr = $sumcurr + $row2->Current_Month;
                    $total2 = $row2->Past_Months + $row2->Current_Month; 
                    $sum = $sum + $total2;
                    
                    $sumpastspe2 = $sumpastspe2 + $row2->Past_Months;
                    $sumcurrspe2 = $sumcurrspe2 + $row2->Current_Month;
                    $tspe2 = $row2->Past_Months + $row2->Current_Month;  
                    $sumspe2 = $sumspe2 + $tspe2; 
                    
                    if($sumspe2 != 0 && stristr(isset($rows2[$it2]->Description),"Special Assessment")== ""){
                    ?>
                        <tr>
                            <td><?php echo "Special Assessment" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastspe2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrspe2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumspe2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumpastspe2 = 0;
                        $sumcurrspe2 = 0;
                        $sumspe2 = 0;
                    }
                }
                
                else {
                    if($sumann2 != 0){
                    ?>
                        <tr>
                            <td><?php echo "Annual Assessment" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastann2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrann2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumann2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumpastann2 = 0;
                        $sumcurrann2 = 0;
                        $sumann2 = 0;
                    }
                    
                    if($sumcap2 != 0){
                    ?>
                        <tr>
                            <td><?php echo "Capital Assessment" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastcap2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrcap2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcap2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumpastcap2 = 0;
                        $sumcurrcap2 = 0;
                        $sumcap2 = 0;
                    }
                    
                    if($sumcou2 != 0){
                    ?>
                        <tr>
                            <td><?php echo "Court Fees" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastcou2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrcou2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcou2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumpastcou2 = 0;
                        $sumcurrcou2 = 0;
                        $sumcou2 = 0;
                    }
                    
                    if($sumgue2 != 0){
                    ?>
                        <tr>
                            <td><?php echo "Guest Fees-Out of Town" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastgue2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrgue2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumgue2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumpastgue2 = 0;
                        $sumcurrgue2 = 0;
                        $sumgue2 = 0;
                    }
                    
                    if($sumlate2 != 0){
                    ?>
                        <tr>
                            <td><?php echo "Late Assessment" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastlate2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrlate2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumlate2, 2); ?></b></td>
                        </tr>
                        <!--<tr>
                            <td><span style="margin-left:20px;">Late Assessment</span></td>
                            <td align="right" style="border-bottom:solid 1px #000000;"><?php //echo number_format($sumlate2, 2); ?></td>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                         <tr>
                            <td>&nbsp;</td>
                            <td align="right"><?php //echo number_format($sumlate2, 2); ?></td>
                            <td colspan="3">&nbsp;</td>
                        </tr>-->
                <?php
                        $sumlate2 = 0;
                        $sumpastlate2 = 0;
                        $sumcurrlate2 = 0;
                    }
                    
                    if($sumleg2 != 0){
                    ?>
                        <tr>
                            <td><?php echo "Legal Fees-Inv" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastleg2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrleg2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumleg2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumleg2 = 0;
                        $sumpastleg2 = 0;
                        $sumcurrleg2 = 0;
                    }
                    
                    if($sumres2 != 0){
                    ?>
                        <tr>
                            <td><?php echo "Reserve Assessment" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastres2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrres2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumres2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumpastres2 = 0;
                        $sumcurrres2 = 0;
                        $sumres2 = 0;
                    }
                    
                    if($sumro2 != 0){
                    ?>
                        <tr>
                            <td><?php echo "Roof Reserve" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastro2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrro2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumro2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumpastro2 = 0;
                        $sumcurrro2 = 0;
                        $sumro2 = 0;
                    }
                    
                    if($sumsec2 != 0){
                    ?>
                        <tr>
                            <td><?php echo "Security Assessment" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastsec2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrsec2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumsec2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumpastsec2 = 0;
                        $sumcurrsec2 = 0;
                        $sumsec2 = 0;
                    }
                    
                    if($sumspe2 != 0){
                    ?>
                        <tr>
                            <td><?php echo "Special Assessment" ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><b><?php echo number_format($sumpastspe2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumcurrspe2, 2); ?></b></td>
                            <td align="right"><b><?php echo number_format($sumspe2, 2); ?></b></td>
                        </tr>
                <?php
                        $sumpastspe2 = 0;
                        $sumcurrspe2 = 0;
                        $sumspe2 = 0;
                    }
            ?>
                    <tr>
                        <td><?php echo $row2->Description; ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><b><?php $sumpast = $sumpast + $row2->Past_Months; echo number_format($row2->Past_Months, 2); ?></b></td>
                        <td align="right"><b><?php $sumcurr = $sumcurr + $row2->Current_Month; echo number_format($row2->Current_Month, 2); ?></b></td>
                        <td align="right"><b><?php $total2 = $row2->Past_Months + $row2->Current_Month; $sum = $sum + $total2; echo number_format($total2, 2);?></b></td>
                    </tr>
                
            <?php
                
                }
                $it2++;		
            }
			
			if($strpay2 != ""){
				echo $strpay2;
			}
            ?>
            <tr>
               <td>&nbsp;</td>
                <td style="border-top:#000000 dashed 1px;"><b>Total Activity</b></td>
                <td align="right" style="border-top:#000000 dashed 1px;"><b><?php if (number_format($sumpast,2) == -0.00) { echo "0.00"; } else {echo number_format($sumpast,2);} ?></b></td>
            <td align="right" style="border-top:#000000 dashed 1px;"><b><?php if (number_format($sumcurr,2) == -0.00) { echo "0.00"; } else {echo number_format($sumcurr,2);} ?></b></td>
            <td align="right" style="border-top:#000000 dashed 1px;"><b><?php if (number_format($sum,2) == -0.00) { echo "0.00"; } else {echo number_format($sum,2);} ?></b></td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
                <td style="background:#FFFFFF; height:30px;"><b>Total Due</b></td>
               <td align="right" style="background:#FFFFFF; height:30px;"><b style="text-decoration:underline;"><?php if (number_format($sum,2) == -0.00) { echo "0.00"; } else {echo number_format($sum,2);} ?></b></td>
            </tr>
            <?php
                if (isset($rows1[0]->SurePay) == "Y" || isset($rows2[0]->SurePay) == "Y"){
            ?>
            <tr>
                <td colspan="5" align="center">
                    <div style="margin-top:10px; text-align:center; border: 1px solid #000000; width:50%; padding:5px;"> ** Do not pay - Sure Pay Account ** </div>
                </td>
            </tr>
            <?php
                }
            ?>
            <tr>
                <td colspan="5"><div style="margin-top:10px; text-align:justify;">Should the Gainey Ranch Community Association assessment not be received within 30 days from the statement date, a late fee in the amount of $15.00 shall be charged for the master association. The late fees relating to any delinquent satellite association assessment may vary between associations. In addition to late fees, any balances remaining delinquent after 60 days will be assessed an interest charge at the rate of 18% per annum. Interest charges may vary on satellite delinquent balances. </div></td>
            </tr>
          </table>
        <?php	
		}
        else{
        ?>
        <div>
            <div style="width:30%; float:left; margin-left:20px;">
                <div>Account: <?php echo $rows1[0]->Account;  ?></div>
                <div><?php echo $rows1[0]->Name;  ?></div>
                <div><?php echo $rows1[0]->Address1;  ?></div>
                <div><?php echo $rows1[0]->City_State_Zip;  ?></div>
                <div>&nbsp;</div>
            </div>
             <div style="width:25%; float:right;">
                <div>Date		: <?php echo $mon; ?>/01/<?php echo $yr; ?></div>
                <div>Due		: Within 30 Days</div>
                <?php
                    $exp = explode("-",$rows3[0]->update);
                    $tgl = $exp[0];
                ?>
                <div>Last Update 	: <?php echo $mon; ?>/<?php echo $tgl; ?>/<?php echo $yr; ?></div>
                <div>Unit		: <?php echo $rows1[0]->Unit;  ?></div>
                <div>&nbsp;</div>
            </div>
            <div>&nbsp;</div>
        </div>
        <div style="clear:both;">&nbsp;</div>
        <div style="margin-left:20px; margin-top:-10px;">Information for online payments through <a href="http://www.mutualofomahabank.com/" target="_blank">www.mutualofomahabank.com</a></div>
        <table width="94%" align="center" cellpadding="2" cellspacing="3" style="border-collapse: collapse;">
            <tr style="border:solid #000000 2px;">
                <td width="180">1156</td>
                <td width="100">990</td>
                <td><?php echo $rows1[0]->Unit;  ?></td>
            </tr>
            <tr style="border:none;">
                <td>Management Company ID</td>
                <td>Association ID</td>
                <td>Property Account Number</td>
            </tr>
        </table>
        <div>&nbsp;</div>
        <table width="95%" align="center" cellpadding="2" cellspacing="3">
        <tr style="background-color:#4B7B6F; color:#FFFFFF; height:40px;">
            <td width="40%">Description</td>
            <td width="15%">&nbsp;</td>
            <td width="15%" align="center">Past Due</td>
            <td width="15%" align="center">Current</td>
            <td width="15%" align="center">Total Due</td>
        </tr>
        <tr style="height:30px;">
            <td><b>Satellite Activity</b></td>
            <td>&nbsp;</td>
            <td colspan="3" align="center">Statement Date : <?php echo $mon; ?>/01/<?php echo $yr; ?></td>
        </tr>
        <?php
        $it = 1;
    foreach($rows1 as $row1)
    {
		
		if (stristr($row1->Description,"Annual Assessment"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastann = $sumpastann + $row1->Past_Months;
			$sumcurrann = $sumcurrann + $row1->Current_Month;
			$tann = $row1->Past_Months + $row1->Current_Month;  
			$sumann = $sumann + $tann; 
			
			if($sumann != 0 && stristr(isset($rows1[$it]->Description),"Annual Assessment")== ""){
			?>
            	<tr>
                    <td><?php echo "Annual Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastann, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrann, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumann, 2); ?></b></td>
				</tr>
        <?php
				$sumpastann = 0;
				$sumcurrann = 0;
				$sumann = 0;
			}
		}
		
		else if (stristr($row1->Description,"Capital"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastcap = $sumpastcap + $row1->Past_Months;
			$sumcurrcap = $sumcurrcap + $row1->Current_Month;
			$tcap = $row1->Past_Months + $row1->Current_Month;  
			$sumcap = $sumcap + $tcap; 
			
			if($sumcap != 0 && stristr(isset($rows1[$it]->Description),"Capital")== ""){
			?>
            	<tr>
                    <td><?php echo "Capital Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastcap, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrcap, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcap, 2); ?></b></td>
				</tr>
        <?php
				$sumpastcap = 0;
				$sumcurrcap = 0;
				$sumcap = 0;
			}
		}
		
		else if (stristr($row1->Description,"Court Fees"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastcou = $sumpastcou + $row1->Past_Months;
			$sumcurrcou = $sumcurrcou + $row1->Current_Month;
			$tcou = $row1->Past_Months + $row1->Current_Month;  
			$sumcou = $sumcou + $tcou; 
			
			if($sumcou != 0 && stristr(isset($rows1[$it]->Description),"Court Fees")== ""){
			?>
            	<tr>
                    <td><?php echo "Court Fees" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastcou, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrcou, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcou, 2); ?></b></td>
				</tr>
        <?php
				$sumpastcou = 0;
				$sumcurrcou = 0;
				$sumcou = 0;
			}
		}
		
		else if (stristr($row1->Description,"Guest Fees-Out"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastgue = $sumpastgue + $row1->Past_Months;
			$sumcurrgue = $sumcurrgue + $row1->Current_Month;
			$tgue = $row1->Past_Months + $row1->Current_Month;  
			$sumgue = $sumgue + $tgue; 
			
			if($sumgue != 0 && stristr(isset($rows1[$it]->Description),"Guest Fees-Out")== ""){
			?>
            	<tr>
                    <td><?php echo "Guest Fees-Out of Town" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastgue, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrgue, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumgue, 2); ?></b></td>
				</tr>
        <?php
				$sumpastgue = 0;
				$sumcurrgue = 0;
				$sumgue = 0;
			}
		}
		
		else if (stristr($row1->Description,"Late Assessment"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastlate = $sumpastlate + $row1->Past_Months;
			$sumcurrlate = $sumcurrlate + $row1->Current_Month;
			$tls = $row1->Past_Months + $row1->Current_Month;  
			$sumlate1 = $sumlate1 + $tls; 
			
			if($sumlate1 != 0 && stristr(isset($rows1[$it]->Description),"Late Assessment")== ""){
			?>
            	<tr>
                    <td><?php echo "Late Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastlate, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrlate, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumlate1, 2); ?></b></td>
				</tr>
				<!--<tr>
                    <td><span style="margin-left:20px;">Late Assessment</span></td>
                    <td align="right" style="border-bottom:solid 1px #000000;"><?php //echo number_format($sumlate1, 2); ?></td>
                    <td colspan="3">&nbsp;</td>
                </tr>
                 <tr>
                    <td>&nbsp;</td>
                    <td align="right"><?php //echo number_format($sumlate1, 2); ?></td>
                    <td colspan="3">&nbsp;</td>
                </tr>-->
        <?php
				$sumlate1 = 0;
				$sumpastlate = 0;
				$sumcurrlate = 0;
			}
		}
		
		else if (stristr($row1->Description,"Legal Fees"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastleg = $sumpastleg + $row1->Past_Months;
			$sumcurrleg = $sumcurrleg + $row1->Current_Month;
			$tleg = $row1->Past_Months + $row1->Current_Month;  
			$sumleg = $sumleg + $tleg; 
			
			if($sumleg != 0  && stristr(isset($rows1[$it]->Description),"Legal Fees")== ""){
			?>
            	<tr>
                    <td><?php echo "Legal Fees-Inv" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastleg, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrleg, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumleg, 2); ?></b></td>
				</tr>
        <?php
				$sumleg = 0;
				$sumpastleg = 0;
				$sumcurrleg = 0;
			}
			
		}
		
		else if (stristr($row1->Description,"Payment"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$tpay = $row1->Past_Months + $row1->Current_Month;
			/*$sumpastpay = $sumpastpay + $row1->Past_Months;
			$sumcurrpay = $sumcurrpay + $row1->Current_Month;  
			$sumpay = $sumpay + $tpay; */

            $strpay = '<tr>
                    <td>Payment</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>'.number_format($row1->Past_Months, 2).'</b></td>
                    <td align="right"><b>'.number_format($row1->Current_Month, 2).'</b></td>
                    <td align="right"><b>'.number_format($tpay, 2).'</b></td>
				</tr>';

		}
		
		else if (stristr($row1->Description,"Reserve Assessment"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastres = $sumpastres + $row1->Past_Months;
			$sumcurrres = $sumcurrres + $row1->Current_Month;
			$tres = $row1->Past_Months + $row1->Current_Month;  
			$sumres = $sumres + $tres; 
			
			if($sumres != 0 && stristr(isset($rows1[$it]->Description),"Reserve Assessment")== ""){
			?>
            	<tr>
                    <td><?php echo "Reserve Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastres, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrres, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumres, 2); ?></b></td>
				</tr>
        <?php
				$sumpastres = 0;
				$sumcurrres = 0;
				$sumres = 0;
			}
		}
		
		else if (stristr($row1->Description,"Roof Reserve"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastro = $sumpastro + $row1->Past_Months;
			$sumcurrro = $sumcurrro + $row1->Current_Month;
			$tro = $row1->Past_Months + $row1->Current_Month;  
			$sumro = $sumro + $tro; 
			
			if($sumro != 0 && stristr(isset($rows1[$it]->Description),"Roof Reserve")== ""){
			?>
            	<tr>
                    <td><?php echo "Roof Reserve" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastro, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrro, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumro, 2); ?></b></td>
				</tr>
        <?php
				$sumpastro = 0;
				$sumcurrro = 0;
				$sumro = 0;
			}
		}
		
		else if (stristr($row1->Description,"Security Assessment"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastsec = $sumpastsec + $row1->Past_Months;
			$sumcurrsec = $sumcurrsec + $row1->Current_Month;
			$tsec = $row1->Past_Months + $row1->Current_Month;  
			$sumsec = $sumsec + $tsec; 
			
			if($sumsec != 0 && stristr(isset($rows1[$it]->Description),"Security Assessment")== ""){
			?>
            	<tr>
                    <td><?php echo "Security Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastsec, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrsec, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumsec, 2); ?></b></td>
				</tr>
        <?php
				$sumpastsec = 0;
				$sumcurrsec = 0;
				$sumsec = 0;
			}
		}
		
		else if (stristr($row1->Description,"Special Assessment"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastspe = $sumpastspe + $row1->Past_Months;
			$sumcurrspe = $sumcurrspe + $row1->Current_Month;
			$tspe = $row1->Past_Months + $row1->Current_Month;  
			$sumspe = $sumspe + $tspe; 
			
			if($sumspe != 0 && stristr(isset($rows1[$it]->Description),"Special Assessment")== ""){
			?>
            	<tr>
                    <td><?php echo "Special Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastspe, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrspe, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumspe, 2); ?></b></td>
				</tr>
        <?php
				$sumpastspe = 0;
				$sumcurrspe = 0;
				$sumspe = 0;
			}
		}
		
		else {
			if($sumann != 0){
			?>
            	<tr>
                    <td><?php echo "Annual Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastann, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrann, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumann, 2); ?></b></td>
				</tr>
        <?php
				$sumpastann = 0;
				$sumcurrann = 0;
				$sumann = 0;
			}
			
			if($sumcap != 0){
			?>
            	<tr>
                    <td><?php echo "Capital Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastcap, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrcap, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcap, 2); ?></b></td>
				</tr>
        <?php
				$sumpastcap = 0;
				$sumcurrcap = 0;
				$sumcap = 0;
			}
			
			if($sumcou != 0){
			?>
            	<tr>
                    <td><?php echo "Court Fees" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastcou, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrcou, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcou, 2); ?></b></td>
				</tr>
        <?php
				$sumpastcou = 0;
				$sumcurrcou = 0;
				$sumcou = 0;
			}
			
			if($sumgue != 0){
			?>
            	<tr>
                    <td><?php echo "Guest Fees-Out of Town" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastgue, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrgue, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumgue, 2); ?></b></td>
				</tr>
        <?php
				$sumpastgue = 0;
				$sumcurrgue = 0;
				$sumgue = 0;
			}
			
			if($sumlate1 != 0){
			?>
            	<tr>
                    <td><?php echo "Late Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastlate, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrlate, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumlate1, 2); ?></b></td>
				</tr>
				<!--<tr>
                    <td><span style="margin-left:20px;">Late Assessment</span></td>
                    <td align="right" style="border-bottom:solid 1px #000000;"><?php //echo number_format($sumlate1, 2); ?></td>
                    <td colspan="3">&nbsp;</td>
                </tr>
                 <tr>
                    <td>&nbsp;</td>
                    <td align="right"><?php //echo number_format($sumlate1, 2); ?></td>
                    <td colspan="3">&nbsp;</td>
                </tr>-->
        <?php
				$sumlate1 = 0;
				$sumpastlate = 0;
				$sumcurrlate = 0;
			}
			
			if($sumleg != 0){
			?>
            	<tr>
                    <td><?php echo "Legal Fees-Inv" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastleg, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrleg, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumleg, 2); ?></b></td>
				</tr>
        <?php
				$sumleg = 0;
				$sumpastleg = 0;
				$sumcurrleg = 0;
			}
			
			if($sumres != 0){
			?>
            	<tr>
                    <td><?php echo "Reserve Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastres, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrres, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumres, 2); ?></b></td>
				</tr>
        <?php
				$sumpastres = 0;
				$sumcurrres = 0;
				$sumres = 0;
			}
			
			if($sumro != 0){
			?>
            	<tr>
                    <td><?php echo "Roof Reserve" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastro, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrro, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumro, 2); ?></b></td>
				</tr>
        <?php
				$sumpastro = 0;
				$sumcurrro = 0;
				$sumro = 0;
			}
			
			if($sumsec != 0){
			?>
            	<tr>
                    <td><?php echo "Security Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastsec, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrsec, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumsec, 2); ?></b></td>
				</tr>
        <?php
				$sumpastsec = 0;
				$sumcurrsec = 0;
				$sumsec = 0;
			}
			
			if($sumspe != 0){
			?>
            	<tr>
                    <td><?php echo "Special Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastspe, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrspe, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumspe, 2); ?></b></td>
				</tr>
        <?php
				$sumpastspe = 0;
				$sumcurrspe = 0;
				$sumspe = 0;
			}
	?>
            <tr>
                <td><?php echo $row1->Description; ?></td>
                <td>&nbsp;</td>
                <td align="right"><b><?php $past = number_format($row1->Past_Months, 2); $sumpast = $sumpast + $row1->Past_Months; echo $past;?></b></td>
                <td align="right"><b><?php $charge = number_format($row1->Current_Month, 2); $sumcurr = $sumcurr + $row1->Current_Month; echo $charge ;?></b></td>
                <td align="right"><b><?php $total = $row1->Past_Months + $row1->Current_Month; $sum = $sum + $total; echo number_format($total, 2);?></b></td>
            </tr>
		
	<?php
		
		}
		$it++;
    }
		if($strpay != ""){
			echo $strpay;
		}
        
        if ($rows2[0]->Satellite != ""){
        ?>
        <tr>
            <td colspan="5" align="center">&nbsp;</td>
        </tr>
         <tr style="height:30px;">
            <td><b>Master Association Activity</b></td>
            <td>&nbsp;</td>
            <td colspan="3" align="center">&nbsp;</td>
        </tr>
        <?php
        }
        
		$it2 = 1;
        foreach($rows2 as $row2)
        {
            
			if (stristr($row2->Description,"Annual Assessment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastann2 = $sumpastann2 + $row2->Past_Months;
				$sumcurrann2 = $sumcurrann2 + $row2->Current_Month;
				$tann2 = $row2->Past_Months + $row2->Current_Month;  
				$sumann2 = $sumann2 + $tann2; 
				
				if($sumann2 != 0 && stristr(isset($rows2[$it2]->Description),"Annual Assessment")== ""){
				?>
					<tr>
						<td><?php echo "Annual Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastann2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrann2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumann2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastann2 = 0;
					$sumcurrann2 = 0;
					$sumann2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Capital"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastcap2 = $sumpastcap2 + $row2->Past_Months;
				$sumcurrcap2 = $sumcurrcap2 + $row2->Current_Month;
				$tcap2 = $row2->Past_Months + $row2->Current_Month;  
				$sumcap2 = $sumcap2 + $tcap2; 
				
				if($sumcap2 != 0 && stristr(isset($rows2[$it2]->Description),"Capital")== ""){
				?>
					<tr>
						<td><?php echo "Capital Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastcap2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrcap2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcap2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastcap2 = 0;
					$sumcurrcap2 = 0;
					$sumcap2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Court Fees"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastcou2 = $sumpastcou2 + $row2->Past_Months;
				$sumcurrcou2 = $sumcurrcou2 + $row2->Current_Month;
				$tcou2 = $row1->Past_Months + $row2->Current_Month;  
				$sumcou2 = $sumcou2 + $tcou2; 
				
				if($sumcou2 != 0 && stristr(isset($rows2[$it2]->Description),"Court Fees")== ""){
				?>
					<tr>
						<td><?php echo "Court Fees" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastcou2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrcou2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcou2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastcou2 = 0;
					$sumcurrcou2 = 0;
					$sumcou2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Guest Fees-Out"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastgue2 = $sumpastgue2 + $row2->Past_Months;
				$sumcurrgue2 = $sumcurrgue2 + $row2->Current_Month;
				$tgue2 = $row2->Past_Months + $row2->Current_Month;  
				$sumgue2 = $sumgue2 + $tgue2; 
				
				if($sumgue2 != 0 && stristr(isset($rows2[$it2]->Description),"Guest Fees-Out")== ""){
				?>
					<tr>
						<td><?php echo "Guest Fees-Out of Town" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastgue2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrgue2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumgue2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastgue2 = 0;
					$sumcurrgue2 = 0;
					$sumgue2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Late Assessment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastlate2 = $sumpastlate2 + $row2->Past_Months;
				$sumcurrlate2 = $sumcurrlate2 + $row2->Current_Month;
				$tls2 = $row2->Past_Months + $row2->Current_Month;  
				$sumlate2 = $sumlate2 + $tls2; 
				
				if($sumlate2 != 0 && stristr(isset($rows2[$it2]->Description),"Late Assessment")== ""){
				?>
					<tr>
						<td><?php echo "Late Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastlate2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrlate2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumlate2, 2); ?></b></td>
					</tr>
					<!--<tr>
						<td><span style="margin-left:20px;">Late Assessment</span></td>
						<td align="right" style="border-bottom:solid 1px #000000;"><?php //echo number_format($sumlate2, 2); ?></td>
						<td colspan="3">&nbsp;</td>
					</tr>
					 <tr>
						<td>&nbsp;</td>
						<td align="right"><?php //echo number_format($sumlate2, 2); ?></td>
						<td colspan="3">&nbsp;</td>
					</tr>-->
			<?php
					$sumlate2 = 0;
					$sumpastlate2 = 0;
					$sumcurrlate2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Legal Fees"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastleg2 = $sumpastleg2 + $row2->Past_Months;
				$sumcurrleg2 = $sumcurrleg2 + $row2->Current_Month;
				$tleg2 = $row2->Past_Months + $row2->Current_Month;  
				$sumleg2 = $sumleg2 + $tleg2; 
				
				if($sumleg2 != 0  && stristr(isset($rows2[$it2]->Description),"Legal Fees")== ""){
				?>
					<tr>
						<td><?php echo "Legal Fees-Inv" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastleg2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrleg2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumleg2, 2); ?></b></td>
					</tr>
			<?php
					$sumleg2 = 0;
					$sumpastleg2 = 0;
					$sumcurrleg2 = 0;
				}
				
			}
			
			else if (stristr($row2->Description,"Payment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$tpay2 = $row2->Past_Months + $row2->Current_Month;
				/*$sumpastpay = $sumpastpay + $row1->Past_Months;
				$sumcurrpay = $sumcurrpay + $row1->Current_Month;  
				$sumpay = $sumpay + $tpay; */
	
				$strpay2 = '<tr>
						<td>Payment</td>
						<td>&nbsp;</td>
						<td align="right"><b>'.number_format($row2->Past_Months, 2).'</b></td>
						<td align="right"><b>'.number_format($row2->Current_Month, 2).'</b></td>
						<td align="right"><b>'.number_format($tpay2, 2).'</b></td>
					</tr>';
	
			}
			
			else if (stristr($row2->Description,"Reserve Assessment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastres2 = $sumpastres2 + $row2->Past_Months;
				$sumcurrres2 = $sumcurrres2 + $row2->Current_Month;
				$tres2 = $row2->Past_Months + $row2->Current_Month;  
				$sumres2 = $sumres2 + $tres2; 
				
				if($sumres2 != 0 && stristr(isset($rows2[$it2]->Description),"Reserve Assessment")== ""){
				?>
					<tr>
						<td><?php echo "Reserve Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastres2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrres2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumres2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastres2 = 0;
					$sumcurrres2 = 0;
					$sumres2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Roof Reserve"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastro2 = $sumpastro2 + $row2->Past_Months;
				$sumcurrro2 = $sumcurrro2 + $row2->Current_Month;
				$tro2 = $row2->Past_Months + $row2->Current_Month;  
				$sumro2 = $sumro2 + $tro2; 
				
				if($sumro2 != 0 && stristr(isset($rows2[$it2]->Description),"Roof Reserve")== ""){
				?>
					<tr>
						<td><?php echo "Roof Reserve" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastro2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrro2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumro2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastro2 = 0;
					$sumcurrro2 = 0;
					$sumro2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Security Assessment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastsec2 = $sumpastsec2 + $row2->Past_Months;
				$sumcurrsec2 = $sumcurrsec2 + $row2->Current_Month;
				$tsec2 = $row2->Past_Months + $row2->Current_Month;  
				$sumsec2 = $sumsec2 + $tsec2; 
				
				if($sumsec2 != 0 && stristr(isset($rows2[$it2]->Description),"Security Assessment")== ""){
				?>
					<tr>
						<td><?php echo "Security Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastsec2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrsec2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumsec2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastsec2 = 0;
					$sumcurrsec2 = 0;
					$sumsec2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Special Assessment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastspe2 = $sumpastspe2 + $row2->Past_Months;
				$sumcurrspe2 = $sumcurrspe2 + $row2->Current_Month;
				$tspe2 = $row2->Past_Months + $row2->Current_Month;  
				$sumspe2 = $sumspe2 + $tspe2; 
				
				if($sumspe2 != 0 && stristr(isset($rows2[$it2]->Description),"Special Assessment")== ""){
				?>
					<tr>
						<td><?php echo "Special Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastspe2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrspe2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumspe2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastspe2 = 0;
					$sumcurrspe2 = 0;
					$sumspe2 = 0;
				}
			}
			
			else {
				if($sumann2 != 0){
				?>
					<tr>
						<td><?php echo "Annual Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastann2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrann2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumann2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastann2 = 0;
					$sumcurrann2 = 0;
					$sumann2 = 0;
				}
				
				if($sumcap2 != 0){
				?>
					<tr>
						<td><?php echo "Capital Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastcap2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrcap2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcap2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastcap2 = 0;
					$sumcurrcap2 = 0;
					$sumcap2 = 0;
				}
				
				if($sumcou2 != 0){
				?>
					<tr>
						<td><?php echo "Court Fees" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastcou2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrcou2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcou2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastcou2 = 0;
					$sumcurrcou2 = 0;
					$sumcou2 = 0;
				}
				
				if($sumgue2 != 0){
				?>
					<tr>
						<td><?php echo "Guest Fees-Out of Town" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastgue2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrgue2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumgue2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastgue2 = 0;
					$sumcurrgue2 = 0;
					$sumgue2 = 0;
				}
				
				if($sumlate2 != 0){
				?>
					<tr>
						<td><?php echo "Late Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastlate2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrlate2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumlate2, 2); ?></b></td>
					</tr>
					<!--<tr>
						<td><span style="margin-left:20px;">Late Assessment</span></td>
						<td align="right" style="border-bottom:solid 1px #000000;"><?php //echo number_format($sumlate2, 2); ?></td>
						<td colspan="3">&nbsp;</td>
					</tr>
					 <tr>
						<td>&nbsp;</td>
						<td align="right"><?php //echo number_format($sumlate2, 2); ?></td>
						<td colspan="3">&nbsp;</td>
					</tr>-->
			<?php
					$sumlate2 = 0;
					$sumpastlate2 = 0;
					$sumcurrlate2 = 0;
				}
				
				if($sumleg2 != 0){
				?>
					<tr>
						<td><?php echo "Legal Fees-Inv" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastleg2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrleg2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumleg2, 2); ?></b></td>
					</tr>
			<?php
					$sumleg2 = 0;
					$sumpastleg2 = 0;
					$sumcurrleg2 = 0;
				}
				
				if($sumres2 != 0){
				?>
					<tr>
						<td><?php echo "Reserve Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastres2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrres2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumres2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastres2 = 0;
					$sumcurrres2 = 0;
					$sumres2 = 0;
				}
				
				if($sumro2 != 0){
				?>
					<tr>
						<td><?php echo "Roof Reserve" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastro2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrro2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumro2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastro2 = 0;
					$sumcurrro2 = 0;
					$sumro2 = 0;
				}
				
				if($sumsec2 != 0){
				?>
					<tr>
						<td><?php echo "Security Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastsec2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrsec2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumsec2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastsec2 = 0;
					$sumcurrsec2 = 0;
					$sumsec2 = 0;
				}
				
				if($sumspe2 != 0){
				?>
					<tr>
						<td><?php echo "Special Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastspe2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrspe2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumspe2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastspe2 = 0;
					$sumcurrspe2 = 0;
					$sumspe2 = 0;
				}
		?>
				<tr>
					<td><?php echo $row2->Description; ?></td>
					<td>&nbsp;</td>
					<td align="right"><b><?php $past2 = number_format($row2->Past_Months, 2); $sumpast = $sumpast + $row2->Past_Months; echo $past2;?></b></td>
					<td align="right"><b><?php $charge2 = number_format($row2->Current_Month, 2); $sumcurr = $sumcurr + $row2->Current_Month; echo $charge2 ;?></b></td>
					<td align="right"><b><?php $total2 = $row2->Past_Months + $row2->Current_Month; $sum = $sum + $total2; echo number_format($total2, 2);?></b></td>
				</tr>
			
		<?php
			
			}
			$it2++;		
        }
		
		if($strpay2 != ""){
				echo $strpay2;
		}
        ?>
        <tr>
           <td>&nbsp;</td>
            <td style="border-top:#000000 dashed 1px;"><b>Total Activity</b></td>
            <td align="right" style="border-top:#000000 dashed 1px;"><b><?php if (number_format($sumpast,2) == -0.00) { echo "0.00"; } else {echo number_format($sumpast,2);} ?></b></td>
            <td align="right" style="border-top:#000000 dashed 1px;"><b><?php if (number_format($sumcurr,2) == -0.00) { echo "0.00"; } else {echo number_format($sumcurr,2);} ?></b></td>
            <td align="right" style="border-top:#000000 dashed 1px;"><b><?php if (number_format($sum,2) == -0.00) { echo "0.00"; } else {echo number_format($sum,2);} ?></b></td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
            <td style="background:#FFFFFF; height:30px;"><b>Total Due</b></td>
            <td align="right" style="background:#FFFFFF; height:30px;"><b style="text-decoration:underline;"><?php if (number_format($sum,2) == -0.00) { echo "0.00"; } else {echo number_format($sum,2);} ?></b></td>
        </tr>
        <?php
			if ($rows1[0]->SurePay == "Y" || $rows2[0]->SurePay == "Y"){
		?>
        <tr>
            <td colspan="5" align="center">
            	<div style="margin-top:10px; text-align:center; border: 1px solid #000000; width:50%; padding:5px;"> ** Do not pay - Sure Pay Account ** </div>
            </td>
        </tr>
        <?php
			}
		?>
        <tr>
            <td colspan="5"><div style="margin-top:10px; text-align:justify;">Should the Gainey Ranch Community Association assessment not be received within 30 days from the statement date, a late fee in the amount of $15.00 shall be charged for the master association. The late fees relating to any delinquent satellite association assessment may vary between associations. In addition to late fees, any balances remaining delinquent after 60 days will be assessed an interest charge at the rate of 18% per annum. Interest charges may vary on satellite delinquent balances. </div></td>
        </tr>
      </table>

    <?php
		}
	}
  }
  
  function showBilling($rows1, $rows2, $rows3)
  { 
  	$sum = 0;
	$sumpast = 0;
	$sumcurr = 0;
	
	$sumann = 0;
	$sumpastann = 0;
	$sumcurrann = 0;
	$sumann2 = 0;
	$sumpastann2 = 0;
	$sumcurrann2 = 0;
	
	$sumcap = 0;
	$sumpastcap = 0;
	$sumcurrcap = 0;
	$sumcap2 = 0;
	$sumpastcap2 = 0;
	$sumcurrcap2 = 0;
	
	$sumcou = 0;
	$sumpastcou = 0;
	$sumcurrcou = 0;
	$sumcou2 = 0;
	$sumpastcou2 = 0;
	$sumcurrcou2 = 0;
	
	$sumgue = 0;
	$sumpastgue = 0;
	$sumcurrgue = 0;
	$sumgue2 = 0;
	$sumpastgue2 = 0;
	$sumcurrgue2 = 0;
	
	$sumlate1 = 0;
	$sumpastlate = 0;
	$sumcurrlate = 0;
	$sumlate2 = 0;
	$sumpastlate2 = 0;
	$sumcurrlate2 = 0;
	
	$sumleg = 0;
	$sumpastleg = 0;
	$sumcurrleg = 0;
	$sumleg2 = 0;
	$sumpastleg2 = 0;
	$sumcurrleg2 = 0;
	
	$strpay = 0;
	$strpay2 = 0;
	
	$sumres = 0;
	$sumpastres = 0;
	$sumcurrres = 0;
	$sumres2 = 0;
	$sumpastres2 = 0;
	$sumcurrres2 = 0;
	
	$sumro = 0;
	$sumpastro = 0;
	$sumcurrro = 0;
	$sumro2 = 0;
	$sumpastro2 = 0;
	$sumcurrro2 = 0;
	
	$sumsec = 0;
	$sumpastsec = 0;
	$sumcurrsec = 0;
	$sumsec2 = 0;
	$sumpastsec2 = 0;
	$sumcurrsec2 = 0;
	
	$sumspe = 0;
	$sumpastspe = 0;
	$sumcurrspe = 0;
	$sumspe2 = 0;
	$sumpastspe2 = 0;
    $sumcurrspe2 = 0;
	
  	$mon = JRequest::getVar('month', '','post','string', JREQUEST_ALLOWRAW );
  	$yr = JRequest::getVar('year', '','post','string', JREQUEST_ALLOWRAW );
	
	if($mon == "" || $yr == ""){
		if (isset($rows1[0]->month) == ""){
			$mon = isset($rows2[0]->month);
			$yr = isset($rows2[0]->year);
		}
		else{
			$mon = $rows1[0]->month;
			$yr = $rows1[0]->year;
		}
	}
  ?>
  	<div id="bill" align="center" style="text-align:center; margin-bottom:20px;"><h1><b>BILLING STATEMENT</b></h1></div>
  	<form action="index.php?option=com_jcsv&Itemid=2" method="post" name="adminForm"> 
    <div id="search">
    	Please Select 
        <select name="month" id="month">
          <option value=" ">-Select Month-</option>
    	  <option value="01" <?php if($mon == "01" ){echo 'selected="selected"';} ?>>January</option>
    	  <option value="02" <?php if($mon == "02" ){echo 'selected="selected"';} ?>>February</option>
    	  <option value="03" <?php if($mon == "03" ){echo 'selected="selected"';} ?>>March</option>
    	  <option value="04" <?php if($mon == "04" ){echo 'selected="selected"';} ?>>April</option>
    	  <option value="05" <?php if($mon == "05" ){echo 'selected="selected"';} ?>>May</option>
    	  <option value="06" <?php if($mon == "06" ){echo 'selected="selected"';} ?>>June</option>
    	  <option value="07" <?php if($mon == "07" ){echo 'selected="selected"';} ?>>July</option>
    	  <option value="08" <?php if($mon == "08" ){echo 'selected="selected"';} ?>>August</option>
    	  <option value="09" <?php if($mon == "09" ){echo 'selected="selected"';} ?>>September</option>
    	  <option value="10" <?php if($mon == "10" ){echo 'selected="selected"';} ?>>October</option>
    	  <option value="11" <?php if($mon == "11" ){echo 'selected="selected"';} ?>>November</option>
    	  <option value="12" <?php if($mon == "12" ){echo 'selected="selected"';} ?>>December</option>
    	</select>
        
        <select name="year" id="year">
          <option value=" ">-Select Year-</option>
        <?php
		for ($i=date("Y"); $i>2011; $i--)
		{
		?>
    	  <option value="<?php echo $i; ?>" <?php if($yr == $i ){echo 'selected="selected"';} ?>><?php echo $i; ?></option>
        <?php
		} 
		?>
        </select>
        <input name="search" type="submit" value="Select" style="color:#FFFFFF;">
    </div>
    </form>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <?php
    if (isset($rows1[0]->Satellite) == "" && isset($rows2[0]->Satellite) == ""){
		echo '<div align="center" style="width:100%; margin-left:20px;">You do not have any bills..</div>';
	}
	else if(isset($rows1[0]->Satellite) == "" && isset($rows2[0]->Satellite) != ""){
	?>
    	 <div>
            <div style="width:30%; float:left; margin-left:20px;">
                <div>Account: <?php echo $rows2[0]->Account;  ?></div>
                <div><?php echo $rows2[0]->Name;  ?></div>
                <div><?php echo $rows2[0]->Address1;  ?></div>
                <div><?php echo $rows2[0]->City_State_Zip;  ?></div>
                <div>&nbsp;</div>
            </div>
             <div style="width:25%; float:right;">
                <div>Date		: <?php echo $rows2[0]->month; ?>/01/<?php echo $rows2[0]->year; ?></div>
                <div>Due		: Within 30 Days</div>
                <?php
                    $exp = explode("-",$rows3[0]->update);
                    $tgl = $exp[0];
                ?>
                <div>Last Update 	: <?php echo $rows2[0]->month; ?>/<?php echo $tgl; ?>/<?php echo $rows2[0]->year; ?></div>
                <div>Unit		: <?php echo $rows2[0]->Unit;  ?></div>
                <div>&nbsp;</div>
            </div>
            <div>&nbsp;</div>
        </div>
        <div style="clear:both;">&nbsp;</div>
        <div style="margin-left:20px; margin-top:-10px;">Information for online payments through <a href="http://www.mutualofomahabank.com/" target="_blank">www.mutualofomahabank.com</a></div>
        <table width="94%" align="center" cellpadding="2" cellspacing="3" style="border-collapse: collapse;">
            <tr style="border:solid #000000 2px;">
                <td width="180">1156</td>
                <td width="100">990</td>
                <td><?php echo $rows2[0]->Unit;  ?></td>
            </tr>
            <tr style="border:none;">
                <td>Management Company ID</td>
                <td>Association ID</td>
                <td>Property Account Number</td>
            </tr>
        </table>
        <div>&nbsp;</div>
        <table width="95%" align="center" cellpadding="2" cellspacing="3">
        <tr style="background-color:#4B7B6F; color:#FFFFFF; height:40px;">
            <td width="40%">Description</td>
            <td width="15%">&nbsp;</td>
            <td width="15%" align="center">Past Due</td>
            <td width="15%" align="center">Current</td>
            <td width="15%" align="center">Total Due</td>
        </tr>
        <tr style="height:30px;">
            <td><b>Master Association Activity</b></td>
            <td>&nbsp;</td>
            <td colspan="3" align="center">Statement Date : <?php echo $rows2[0]->month; ?>/01/<?php echo $rows2[0]->year; ?></td>
        </tr>

        <?php
        
		$it2 = 1;
        foreach($rows2 as $row2)
        {
            
			if (stristr($row2->Description,"Annual Assessment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastann2 = $sumpastann2 + $row2->Past_Months;
				$sumcurrann2 = $sumcurrann2 + $row2->Current_Month;
				$tann2 = $row2->Past_Months + $row2->Current_Month;  
				$sumann2 = $sumann2 + $tann2; 
				
				if($sumann2 != 0 && stristr(isset($rows2[$it2]->Description),"Annual Assessment")== ""){
				?>
					<tr>
						<td><?php echo "Annual Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastann2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrann2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumann2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastann2 = 0;
					$sumcurrann2 = 0;
					$sumann2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Capital"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastcap2 = $sumpastcap2 + $row2->Past_Months;
				$sumcurrcap2 = $sumcurrcap2 + $row2->Current_Month;
				$tcap2 = $row2->Past_Months + $row2->Current_Month;  
				$sumcap2 = $sumcap2 + $tcap2; 
				
				if($sumcap2 != 0 && stristr(isset($rows2[$it2]->Description),"Capital")== ""){
				?>
					<tr>
						<td><?php echo "Capital Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastcap2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrcap2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcap2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastcap2 = 0;
					$sumcurrcap2 = 0;
					$sumcap2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Court Fees"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastcou2 = $sumpastcou2 + $row2->Past_Months;
				$sumcurrcou2 = $sumcurrcou2 + $row2->Current_Month;
				$tcou2 = $row1->Past_Months + $row2->Current_Month;  
				$sumcou2 = $sumcou2 + $tcou2; 
				
				if($sumcou2 != 0 && stristr(isset($rows2[$it2]->Description),"Court Fees")== ""){
				?>
					<tr>
						<td><?php echo "Court Fees" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastcou2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrcou2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcou2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastcou2 = 0;
					$sumcurrcou2 = 0;
					$sumcou2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Guest Fees-Out"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastgue2 = $sumpastgue2 + $row2->Past_Months;
				$sumcurrgue2 = $sumcurrgue2 + $row2->Current_Month;
				$tgue2 = $row2->Past_Months + $row2->Current_Month;  
				$sumgue2 = $sumgue2 + $tgue2; 
				
				if($sumgue2 != 0 && stristr(isset($rows2[$it2]->Description),"Guest Fees-Out")== ""){
				?>
					<tr>
						<td><?php echo "Guest Fees-Out of Town" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastgue2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrgue2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumgue2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastgue2 = 0;
					$sumcurrgue2 = 0;
					$sumgue2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Late Assessment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastlate2 = $sumpastlate2 + $row2->Past_Months;
				$sumcurrlate2 = $sumcurrlate2 + $row2->Current_Month;
				$tls2 = $row2->Past_Months + $row2->Current_Month;  
				$sumlate2 = $sumlate2 + $tls2; 
				
				if($sumlate2 != 0 && stristr(isset($rows2[$it2]->Description),"Late Assessment")== ""){
				?>
					<tr>
						<td><?php echo "Late Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastlate2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrlate2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumlate2, 2); ?></b></td>
					</tr>
					<!--<tr>
						<td><span style="margin-left:20px;">Late Assessment</span></td>
						<td align="right" style="border-bottom:solid 1px #000000;"><?php //echo number_format($sumlate2, 2); ?></td>
						<td colspan="3">&nbsp;</td>
					</tr>
					 <tr>
						<td>&nbsp;</td>
						<td align="right"><?php //echo number_format($sumlate2, 2); ?></td>
						<td colspan="3">&nbsp;</td>
					</tr>-->
			<?php
					$sumlate2 = 0;
					$sumpastlate2 = 0;
					$sumcurrlate2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Legal Fees"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastleg2 = $sumpastleg2 + $row2->Past_Months;
				$sumcurrleg2 = $sumcurrleg2 + $row2->Current_Month;
				$tleg2 = $row2->Past_Months + $row2->Current_Month;  
				$sumleg2 = $sumleg2 + $tleg2; 
				
				if($sumleg2 != 0  && stristr(isset($rows2[$it2]->Description),"Legal Fees")== ""){
				?>
					<tr>
						<td><?php echo "Legal Fees-Inv" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastleg2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrleg2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumleg2, 2); ?></b></td>
					</tr>
			<?php
					$sumleg2 = 0;
					$sumpastleg2 = 0;
					$sumcurrleg2 = 0;
				}
				
			}
			
			else if (stristr($row2->Description,"Payment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$tpay2 = $row2->Past_Months + $row2->Current_Month;
				/*$sumpastpay = $sumpastpay + $row1->Past_Months;
				$sumcurrpay = $sumcurrpay + $row1->Current_Month;  
				$sumpay = $sumpay + $tpay; */
	
				$strpay2 = '<tr>
						<td>Payment</td>
						<td>&nbsp;</td>
						<td align="right"><b>'.number_format($row2->Past_Months, 2).'</b></td>
						<td align="right"><b>'.number_format($row2->Current_Month, 2).'</b></td>
						<td align="right"><b>'.number_format($tpay2, 2).'</b></td>
					</tr>';
	
			}
			
			else if (stristr($row2->Description,"Reserve Assessment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastres2 = $sumpastres2 + $row2->Past_Months;
				$sumcurrres2 = $sumcurrres2 + $row2->Current_Month;
				$tres2 = $row2->Past_Months + $row2->Current_Month;  
				$sumres2 = $sumres2 + $tres2; 
				
				if($sumres2 != 0 && stristr(isset($rows2[$it2]->Description),"Reserve Assessment")== ""){
				?>
					<tr>
						<td><?php echo "Reserve Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastres2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrres2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumres2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastres2 = 0;
					$sumcurrres2 = 0;
					$sumres2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Roof Reserve"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastro2 = $sumpastro2 + $row2->Past_Months;
				$sumcurrro2 = $sumcurrro2 + $row2->Current_Month;
				$tro2 = $row2->Past_Months + $row2->Current_Month;  
				$sumro2 = $sumro2 + $tro2; 
				
				if($sumro2 != 0 && stristr(isset($rows2[$it2]->Description),"Roof Reserve")== ""){
				?>
					<tr>
						<td><?php echo "Roof Reserve" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastro2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrro2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumro2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastro2 = 0;
					$sumcurrro2 = 0;
					$sumro2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Security Assessment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastsec2 = $sumpastsec2 + $row2->Past_Months;
				$sumcurrsec2 = $sumcurrsec2 + $row2->Current_Month;
				$tsec2 = $row2->Past_Months + $row2->Current_Month;  
				$sumsec2 = $sumsec2 + $tsec2; 
				
				if($sumsec2 != 0 && stristr(isset($rows2[$it2]->Description),"Security Assessment")== ""){
				?>
					<tr>
						<td><?php echo "Security Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastsec2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrsec2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumsec2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastsec2 = 0;
					$sumcurrsec2 = 0;
					$sumsec2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Special Assessment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastspe2 = $sumpastspe2 + $row2->Past_Months;
				$sumcurrspe2 = $sumcurrspe2 + $row2->Current_Month;
				$tspe2 = $row2->Past_Months + $row2->Current_Month;  
				$sumspe2 = $sumspe2 + $tspe2; 
				
				if($sumspe2 != 0 && stristr(isset($rows2[$it2]->Description),"Special Assessment")== ""){
				?>
					<tr>
						<td><?php echo "Special Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastspe2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrspe2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumspe2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastspe2 = 0;
					$sumcurrspe2 = 0;
					$sumspe2 = 0;
				}
			}
			
			else {
				if($sumann2 != 0){
				?>
					<tr>
						<td><?php echo "Annual Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastann2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrann2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumann2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastann2 = 0;
					$sumcurrann2 = 0;
					$sumann2 = 0;
				}
				
				if($sumcap2 != 0){
				?>
					<tr>
						<td><?php echo "Capital Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastcap2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrcap2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcap2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastcap2 = 0;
					$sumcurrcap2 = 0;
					$sumcap2 = 0;
				}
				
				if($sumcou2 != 0){
				?>
					<tr>
						<td><?php echo "Court Fees" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastcou2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrcou2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcou2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastcou2 = 0;
					$sumcurrcou2 = 0;
					$sumcou2 = 0;
				}
				
				if($sumgue2 != 0){
				?>
					<tr>
						<td><?php echo "Guest Fees-Out of Town" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastgue2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrgue2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumgue2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastgue2 = 0;
					$sumcurrgue2 = 0;
					$sumgue2 = 0;
				}
				
				if($sumlate2 != 0){
				?>
					<tr>
						<td><?php echo "Late Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastlate2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrlate2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumlate2, 2); ?></b></td>
					</tr>
					<!--<tr>
						<td><span style="margin-left:20px;">Late Assessment</span></td>
						<td align="right" style="border-bottom:solid 1px #000000;"><?php //echo number_format($sumlate2, 2); ?></td>
						<td colspan="3">&nbsp;</td>
					</tr>
					 <tr>
						<td>&nbsp;</td>
						<td align="right"><?php //echo number_format($sumlate2, 2); ?></td>
						<td colspan="3">&nbsp;</td>
					</tr>-->
			<?php
					$sumlate2 = 0;
					$sumpastlate2 = 0;
					$sumcurrlate2 = 0;
				}
				
				if($sumleg2 != 0){
				?>
					<tr>
						<td><?php echo "Legal Fees-Inv" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastleg2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrleg2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumleg2, 2); ?></b></td>
					</tr>
			<?php
					$sumleg2 = 0;
					$sumpastleg2 = 0;
					$sumcurrleg2 = 0;
				}
				
				if($sumres2 != 0){
				?>
					<tr>
						<td><?php echo "Reserve Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastres2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrres2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumres2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastres2 = 0;
					$sumcurrres2 = 0;
					$sumres2 = 0;
				}
				
				if($sumro2 != 0){
				?>
					<tr>
						<td><?php echo "Roof Reserve" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastro2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrro2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumro2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastro2 = 0;
					$sumcurrro2 = 0;
					$sumro2 = 0;
				}
				
				if($sumsec2 != 0){
				?>
					<tr>
						<td><?php echo "Security Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastsec2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrsec2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumsec2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastsec2 = 0;
					$sumcurrsec2 = 0;
					$sumsec2 = 0;
				}
				
				if($sumspe2 != 0){
				?>
					<tr>
						<td><?php echo "Special Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastspe2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrspe2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumspe2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastspe2 = 0;
					$sumcurrspe2 = 0;
					$sumspe2 = 0;
				}
		?>
				<tr>
					<td><?php echo $row2->Description; ?></td>
					<td>&nbsp;</td>
					<td align="right"><b><?php $past2 = number_format($row2->Past_Months, 2); $sumpast = $sumpast + $row2->Past_Months; echo $past2;?></b></td>
					<td align="right"><b><?php $charge2 = number_format($row2->Current_Month, 2); $sumcurr = $sumcurr + $row2->Current_Month; echo $charge2 ;?></b></td>
					<td align="right"><b><?php $total2 = $row2->Past_Months + $row2->Current_Month; $sum = $sum + $total2; echo number_format($total2, 2);?></b></td>
				</tr>
			
		<?php
			
			}
			$it2++;		
        }
		
		if($strpay2 != ""){
				echo $strpay2;
		}
		
        ?>
        <tr>
           <td>&nbsp;</td>
            <td style="border-top:#000000 dashed 1px;"><b>Total Activity</b></td>
           <td align="right" style="border-top:#000000 dashed 1px;"><b><?php if (number_format($sumpast,2) == -0.00) { echo "0.00"; } else {echo number_format($sumpast,2);} ?></b></td>
            <td align="right" style="border-top:#000000 dashed 1px;"><b><?php if (number_format($sumcurr,2) == -0.00) { echo "0.00"; } else {echo number_format($sumcurr,2);} ?></b></td>
            <td align="right" style="border-top:#000000 dashed 1px;"><b><?php if (number_format($sum,2) == -0.00) { echo "0.00"; } else {echo number_format($sum,2);} ?></b></td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
            <td style="background:#FFFFFF; height:30px;"><b>Total Due</b></td>
            <td align="right" style="background:#FFFFFF; height:30px;"><b style="text-decoration:underline;"><?php if (number_format($sum,2) == -0.00) { echo "0.00"; } else {echo number_format($sum,2);} ?></b></td>
        </tr>
        <?php
			if ($rows1[0]->SurePay == "Y" || $rows2[0]->SurePay == "Y"){
		?>
        <tr>
            <td colspan="5" align="center">
            	<div style="margin-top:10px; text-align:center; border: 1px solid #000000; width:50%; padding:5px;"> ** Do not pay - Sure Pay Account ** </div>
            </td>
        </tr>
        <?php
			}
		?>
        <tr>
            <td colspan="5"><div style="margin-top:10px; text-align:justify;">Should the Gainey Ranch Community Association assessment not be received within 30 days from the statement date, a late fee in the amount of $15.00 shall be charged for the master association. The late fees relating to any delinquent satellite association assessment may vary between associations. In addition to late fees, any balances remaining delinquent after 60 days will be assessed an interest charge at the rate of 18% per annum. Interest charges may vary on satellite delinquent balances. </div></td>
        </tr>
      </table>

<?php
	}
	else{
	?>
    <div>
        <div style="width:30%; float:left; margin-left:20px;">
            <div>Account: <?php echo $rows1[0]->Account;  ?></div>
            <div><?php echo $rows1[0]->Name;  ?></div>
            <div><?php echo $rows1[0]->Address1;  ?></div>
            <div><?php echo $rows1[0]->City_State_Zip;  ?></div>
            <div>&nbsp;</div>
        </div>
         <div style="width:25%; float:right;">
            <div>Date		: <?php echo $mon; ?>/01/<?php echo $yr; ?></div>
            <div>Due		: Within 30 Days</div>
            <?php
				$exp = explode("-",$rows3[0]->update);
                $tgl = $exp[0];
			?>
            <div>Last Update 	: <?php echo $mon; ?>/<?php echo $tgl; ?>/<?php echo $yr; ?></div>
            <div>Unit		: <?php echo $rows1[0]->Unit;  ?></div>
            <div>&nbsp;</div>
        </div>
    	<div>&nbsp;</div>
    </div>
    <div style="clear:both;">&nbsp;</div>
    <div style="margin-left:20px; margin-top:-10px;">Information for online payments through <a href="http://www.mutualofomahabank.com/" target="_blank">www.mutualofomahabank.com</a></div>
    <table width="45%" align="center" cellpadding="2" cellspacing="3" style="border-collapse: collapse;">
        <tr style="border:solid #000000 2px;">
            <td width="180">1156</td>
            <td width="100">990</td>
            <td><?php echo $rows1[0]->Unit;  ?></td>
        </tr>
        <tr style="border:none;">
            <td>Management Company ID</td>
            <td>Association ID</td>
            <td>Property Account Number</td>
        </tr>
    </table>
    <div>&nbsp;</div>
    <table width="95%" align="center" cellpadding="2" cellspacing="3">
    <tr style="background-color:#4B7B6F; color:#FFFFFF; height:40px;">
        <td width="40%">Description</td>
        <td width="15%">&nbsp;</td>
        <td width="15%" align="center">Past Due</td>
        <td width="15%" align="center">Current</td>
        <td width="15%" align="center">Total Due</td>
  	</tr>
    <tr style="height:30px;">
        <td><b>Satellite Activity</b></td>
        <td>&nbsp;</td>
        <td colspan="3" align="center">Statement Date : <?php echo $mon; ?>/01/<?php echo $yr; ?></td>
    </tr>
    <?php
	$it = 1;
    foreach($rows1 as $row1)
    {
		
		if (stristr($row1->Description,"Annual Assessment"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastann = $sumpastann + $row1->Past_Months;
			$sumcurrann = $sumcurrann + $row1->Current_Month;
			$tann = $row1->Past_Months + $row1->Current_Month;  
			$sumann = $sumann + $tann; 
			
			if($sumann != 0 && stristr(isset($rows1[$it]->Description),"Annual Assessment")== ""){
			?>
            	<tr>
                    <td><?php echo "Annual Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastann, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrann, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumann, 2); ?></b></td>
				</tr>
        <?php
				$sumpastann = 0;
				$sumcurrann = 0;
				$sumann = 0;
			}
		}
		
		else if (stristr($row1->Description,"Capital"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastcap = $sumpastcap + $row1->Past_Months;
			$sumcurrcap = $sumcurrcap + $row1->Current_Month;
			$tcap = $row1->Past_Months + $row1->Current_Month;  
			$sumcap = $sumcap + $tcap; 
			
			if($sumcap != 0 && stristr(isset($rows1[$it]->Description),"Capital")== ""){
			?>
            	<tr>
                    <td><?php echo "Capital Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastcap, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrcap, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcap, 2); ?></b></td>
				</tr>
        <?php
				$sumpastcap = 0;
				$sumcurrcap = 0;
				$sumcap = 0;
			}
		}
		
		else if (stristr($row1->Description,"Court Fees"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastcou = $sumpastcou + $row1->Past_Months;
			$sumcurrcou = $sumcurrcou + $row1->Current_Month;
			$tcou = $row1->Past_Months + $row1->Current_Month;  
			$sumcou = $sumcou + $tcou; 
			
			if($sumcou != 0 && stristr(isset($rows1[$it]->Description),"Court Fees")== ""){
			?>
            	<tr>
                    <td><?php echo "Court Fees" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastcou, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrcou, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcou, 2); ?></b></td>
				</tr>
        <?php
				$sumpastcou = 0;
				$sumcurrcou = 0;
				$sumcou = 0;
			}
		}
		
		else if (stristr($row1->Description,"Guest Fees-Out"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastgue = $sumpastgue + $row1->Past_Months;
			$sumcurrgue = $sumcurrgue + $row1->Current_Month;
			$tgue = $row1->Past_Months + $row1->Current_Month;  
			$sumgue = $sumgue + $tgue; 
			
			if($sumgue != 0 && stristr(isset($rows1[$it]->Description),"Guest Fees-Out")== ""){
			?>
            	<tr>
                    <td><?php echo "Guest Fees-Out of Town" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastgue, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrgue, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumgue, 2); ?></b></td>
				</tr>
        <?php
				$sumpastgue = 0;
				$sumcurrgue = 0;
				$sumgue = 0;
			}
		}
		
		else if (stristr($row1->Description,"Late Assessment"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastlate = $sumpastlate + $row1->Past_Months;
			$sumcurrlate = $sumcurrlate + $row1->Current_Month;
			$tls = $row1->Past_Months + $row1->Current_Month;  
			$sumlate1 = $sumlate1 + $tls; 
			
			if($sumlate1 != 0 && stristr(isset($rows1[$it]->Description),"Late Assessment")== ""){
			?>
            	<tr>
                    <td><?php echo "Late Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastlate, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrlate, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumlate1, 2); ?></b></td>
				</tr>
				<!--<tr>
                    <td><span style="margin-left:20px;">Late Assessment</span></td>
                    <td align="right" style="border-bottom:solid 1px #000000;"><?php //echo number_format($sumlate1, 2); ?></td>
                    <td colspan="3">&nbsp;</td>
                </tr>
                 <tr>
                    <td>&nbsp;</td>
                    <td align="right"><?php //echo number_format($sumlate1, 2); ?></td>
                    <td colspan="3">&nbsp;</td>
                </tr>-->
        <?php
				$sumlate1 = 0;
				$sumpastlate = 0;
				$sumcurrlate = 0;
			}
		}
		
		else if (stristr($row1->Description,"Legal Fees"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastleg = $sumpastleg + $row1->Past_Months;
			$sumcurrleg = $sumcurrleg + $row1->Current_Month;
			$tleg = $row1->Past_Months + $row1->Current_Month;  
			$sumleg = $sumleg + $tleg; 
			
			if($sumleg != 0  && stristr(isset($rows1[$it]->Description),"Legal Fees")== ""){
			?>
            	<tr>
                    <td><?php echo "Legal Fees-Inv" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastleg, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrleg, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumleg, 2); ?></b></td>
				</tr>
        <?php
				$sumleg = 0;
				$sumpastleg = 0;
				$sumcurrleg = 0;
			}
			
		}
		
		else if (stristr($row1->Description,"Payment"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$tpay = $row1->Past_Months + $row1->Current_Month;
			/*$sumpastpay = $sumpastpay + $row1->Past_Months;
			$sumcurrpay = $sumcurrpay + $row1->Current_Month;  
			$sumpay = $sumpay + $tpay; */

			$strpay = '<tr>
					<td>Payment</td>
					<td>&nbsp;</td>
					<td align="right"><b>'.number_format($row1->Past_Months, 2).'</b></td>
					<td align="right"><b>'.number_format($row1->Current_Month, 2).'</b></td>
					<td align="right"><b>'.number_format($tpay, 2).'</b></td>
				</tr>';

		}
		
		else if (stristr($row1->Description,"Reserve Assessment"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastres = $sumpastres + $row1->Past_Months;
			$sumcurrres = $sumcurrres + $row1->Current_Month;
			$tres = $row1->Past_Months + $row1->Current_Month;  
			$sumres = $sumres + $tres; 
			
			if($sumres != 0 && stristr(isset($rows1[$it]->Description),"Reserve Assessment")== ""){
			?>
            	<tr>
                    <td><?php echo "Reserve Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastres, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrres, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumres, 2); ?></b></td>
				</tr>
        <?php
				$sumpastres = 0;
				$sumcurrres = 0;
				$sumres = 0;
			}
		}
		
		else if (stristr($row1->Description,"Roof Reserve"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastro = $sumpastro + $row1->Past_Months;
			$sumcurrro = $sumcurrro + $row1->Current_Month;
			$tro = $row1->Past_Months + $row1->Current_Month;  
			$sumro = $sumro + $tro; 
			
			if($sumro != 0 && stristr(isset($rows1[$it]->Description),"Roof Reserve")== ""){
			?>
            	<tr>
                    <td><?php echo "Roof Reserve" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastro, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrro, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumro, 2); ?></b></td>
				</tr>
        <?php
				$sumpastro = 0;
				$sumcurrro = 0;
				$sumro = 0;
			}
		}
		
		else if (stristr($row1->Description,"Security Assessment"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastsec = $sumpastsec + $row1->Past_Months;
			$sumcurrsec = $sumcurrsec + $row1->Current_Month;
			$tsec = $row1->Past_Months + $row1->Current_Month;  
			$sumsec = $sumsec + $tsec; 
			
			if($sumsec != 0 && stristr(isset($rows1[$it]->Description),"Security Assessment")== ""){
			?>
            	<tr>
                    <td><?php echo "Security Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastsec, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrsec, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumsec, 2); ?></b></td>
				</tr>
        <?php
				$sumpastsec = 0;
				$sumcurrsec = 0;
				$sumsec = 0;
			}
		}
		
		else if (stristr($row1->Description,"Special Assessment"))
		{
			$sumpast = $sumpast + $row1->Past_Months;
			$sumcurr = $sumcurr + $row1->Current_Month;
			$total = $row1->Past_Months + $row1->Current_Month; 
			$sum = $sum + $total;
			
			$sumpastspe = $sumpastspe + $row1->Past_Months;
			$sumcurrspe = $sumcurrspe + $row1->Current_Month;
			$tspe = $row1->Past_Months + $row1->Current_Month;  
			$sumspe = $sumspe + $tspe; 
			
			if($sumspe != 0 && stristr(isset($rows1[$it]->Description),"Special Assessment")== ""){
			?>
            	<tr>
                    <td><?php echo "Special Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastspe, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrspe, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumspe, 2); ?></b></td>
				</tr>
        <?php
				$sumpastspe = 0;
				$sumcurrspe = 0;
				$sumspe = 0;
			}
		}
		
		else {
			if($sumann != 0){
			?>
            	<tr>
                    <td><?php echo "Annual Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastann, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrann, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumann, 2); ?></b></td>
				</tr>
        <?php
				$sumpastann = 0;
				$sumcurrann = 0;
				$sumann = 0;
			}
			
			if($sumcap != 0){
			?>
            	<tr>
                    <td><?php echo "Capital Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastcap, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrcap, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcap, 2); ?></b></td>
				</tr>
        <?php
				$sumpastcap = 0;
				$sumcurrcap = 0;
				$sumcap = 0;
			}
			
			if($sumcou != 0){
			?>
            	<tr>
                    <td><?php echo "Court Fees" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastcou, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrcou, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcou, 2); ?></b></td>
				</tr>
        <?php
				$sumpastcou = 0;
				$sumcurrcou = 0;
				$sumcou = 0;
			}
			
			if($sumgue != 0){
			?>
            	<tr>
                    <td><?php echo "Guest Fees-Out of Town" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastgue, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrgue, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumgue, 2); ?></b></td>
				</tr>
        <?php
				$sumpastgue = 0;
				$sumcurrgue = 0;
				$sumgue = 0;
			}
			
			if($sumlate1 != 0){
			?>
            	<tr>
                    <td><?php echo "Late Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastlate, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrlate, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumlate1, 2); ?></b></td>
				</tr>
				<!--<tr>
                    <td><span style="margin-left:20px;">Late Assessment</span></td>
                    <td align="right" style="border-bottom:solid 1px #000000;"><?php //echo number_format($sumlate1, 2); ?></td>
                    <td colspan="3">&nbsp;</td>
                </tr>
                 <tr>
                    <td>&nbsp;</td>
                    <td align="right"><?php //echo number_format($sumlate1, 2); ?></td>
                    <td colspan="3">&nbsp;</td>
                </tr>-->
        <?php
				$sumlate1 = 0;
				$sumpastlate = 0;
				$sumcurrlate = 0;
			}
			
			if($sumleg != 0){
			?>
            	<tr>
                    <td><?php echo "Legal Fees-Inv" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastleg, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrleg, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumleg, 2); ?></b></td>
				</tr>
        <?php
				$sumleg = 0;
				$sumpastleg = 0;
				$sumcurrleg = 0;
			}
			
			if($sumres != 0){
			?>
            	<tr>
                    <td><?php echo "Reserve Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastres, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrres, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumres, 2); ?></b></td>
				</tr>
        <?php
				$sumpastres = 0;
				$sumcurrres = 0;
				$sumres = 0;
			}
			
			if($sumro != 0){
			?>
            	<tr>
                    <td><?php echo "Roof Reserve" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastro, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrro, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumro, 2); ?></b></td>
				</tr>
        <?php
				$sumpastro = 0;
				$sumcurrro = 0;
				$sumro = 0;
			}
			
			if($sumsec != 0){
			?>
            	<tr>
                    <td><?php echo "Security Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastsec, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrsec, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumsec, 2); ?></b></td>
				</tr>
        <?php
				$sumpastsec = 0;
				$sumcurrsec = 0;
				$sumsec = 0;
			}
			
			if($sumspe != 0){
			?>
            	<tr>
                    <td><?php echo "Special Assessment" ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><?php echo number_format($sumpastspe, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumcurrspe, 2); ?></b></td>
                    <td align="right"><b><?php echo number_format($sumspe, 2); ?></b></td>
				</tr>
        <?php
				$sumpastspe = 0;
				$sumcurrspe = 0;
				$sumspe = 0;
			}
	?>
            <tr>
                <td><?php echo $row1->Description; ?></td>
                <td>&nbsp;</td>
                <td align="right"><b><?php $past = number_format($row1->Past_Months, 2); $sumpast = $sumpast + $row1->Past_Months; echo $past;?></b></td>
                <td align="right"><b><?php $charge = number_format($row1->Current_Month, 2); $sumcurr = $sumcurr + $row1->Current_Month; echo $charge ;?></b></td>
                <td align="right"><b><?php $total = $row1->Past_Months + $row1->Current_Month; $sum = $sum + $total; echo number_format($total, 2);?></b></td>
            </tr>
		
	<?php
		
		}
		$it++;
    }
	
	if($strpay != ""){
			echo $strpay;
	}
	
	if ($rows2[0]->Satellite != ""){
    ?>
    <tr>
        <td colspan="5" align="center">&nbsp;</td>
    </tr>
     <tr style="height:30px;">
        <td><b>Master Association Activity</b></td>
        <td>&nbsp;</td>
        <td colspan="3" align="center">&nbsp;</td>
    </tr>
    <?php
	}
	
    $it2 = 1;
        foreach($rows2 as $row2)
        {
            
			if (stristr($row2->Description,"Annual Assessment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastann2 = $sumpastann2 + $row2->Past_Months;
				$sumcurrann2 = $sumcurrann2 + $row2->Current_Month;
				$tann2 = $row2->Past_Months + $row2->Current_Month;  
				$sumann2 = $sumann2 + $tann2; 
				
				if($sumann2 != 0 && stristr(isset($rows2[$it2]->Description),"Annual Assessment")== ""){
				?>
					<tr>
						<td><?php echo "Annual Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastann2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrann2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumann2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastann2 = 0;
					$sumcurrann2 = 0;
					$sumann2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Capital"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastcap2 = $sumpastcap2 + $row2->Past_Months;
				$sumcurrcap2 = $sumcurrcap2 + $row2->Current_Month;
				$tcap2 = $row2->Past_Months + $row2->Current_Month;  
				$sumcap2 = $sumcap2 + $tcap2; 
				
				if($sumcap2 != 0 && stristr(isset($rows2[$it2]->Description),"Capital")== ""){
				?>
					<tr>
						<td><?php echo "Capital Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastcap2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrcap2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcap2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastcap2 = 0;
					$sumcurrcap2 = 0;
					$sumcap2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Court Fees"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastcou2 = $sumpastcou2 + $row2->Past_Months;
				$sumcurrcou2 = $sumcurrcou2 + $row2->Current_Month;
				$tcou2 = $row1->Past_Months + $row2->Current_Month;  
				$sumcou2 = $sumcou2 + $tcou2; 
				
				if($sumcou2 != 0 && stristr(isset($rows2[$it2]->Description),"Court Fees")== ""){
				?>
					<tr>
						<td><?php echo "Court Fees" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastcou2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrcou2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcou2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastcou2 = 0;
					$sumcurrcou2 = 0;
					$sumcou2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Guest Fees-Out"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastgue2 = $sumpastgue2 + $row2->Past_Months;
				$sumcurrgue2 = $sumcurrgue2 + $row2->Current_Month;
				$tgue2 = $row2->Past_Months + $row2->Current_Month;  
				$sumgue2 = $sumgue2 + $tgue2; 
				
				if($sumgue2 != 0 && stristr(isset($rows2[$it2]->Description),"Guest Fees-Out")== ""){
				?>
					<tr>
						<td><?php echo "Guest Fees-Out of Town" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastgue2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrgue2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumgue2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastgue2 = 0;
					$sumcurrgue2 = 0;
					$sumgue2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Late Assessment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastlate2 = $sumpastlate2 + $row2->Past_Months;
				$sumcurrlate2 = $sumcurrlate2 + $row2->Current_Month;
				$tls2 = $row2->Past_Months + $row2->Current_Month;  
				$sumlate2 = $sumlate2 + $tls2; 
				
				if($sumlate2 != 0 && stristr(isset($rows2[$it2]->Description),"Late Assessment")== ""){
				?>
					<tr>
						<td><?php echo "Late Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastlate2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrlate2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumlate2, 2); ?></b></td>
					</tr>
					<!--<tr>
						<td><span style="margin-left:20px;">Late Assessment</span></td>
						<td align="right" style="border-bottom:solid 1px #000000;"><?php //echo number_format($sumlate2, 2); ?></td>
						<td colspan="3">&nbsp;</td>
					</tr>
					 <tr>
						<td>&nbsp;</td>
						<td align="right"><?php //echo number_format($sumlate2, 2); ?></td>
						<td colspan="3">&nbsp;</td>
					</tr>-->
			<?php
					$sumlate2 = 0;
					$sumpastlate2 = 0;
					$sumcurrlate2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Legal Fees"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastleg2 = $sumpastleg2 + $row2->Past_Months;
				$sumcurrleg2 = $sumcurrleg2 + $row2->Current_Month;
				$tleg2 = $row2->Past_Months + $row2->Current_Month;  
				$sumleg2 = $sumleg2 + $tleg2; 
				
				if($sumleg2 != 0  && stristr(isset($rows2[$it2]->Description),"Legal Fees")== ""){
				?>
					<tr>
						<td><?php echo "Legal Fees-Inv" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastleg2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrleg2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumleg2, 2); ?></b></td>
					</tr>
			<?php
					$sumleg2 = 0;
					$sumpastleg2 = 0;
					$sumcurrleg2 = 0;
				}
				
			}
			
			else if (stristr($row2->Description,"Payment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$tpay2 = $row2->Past_Months + $row2->Current_Month;
				/*$sumpastpay = $sumpastpay + $row1->Past_Months;
				$sumcurrpay = $sumcurrpay + $row1->Current_Month;  
				$sumpay = $sumpay + $tpay; */
	
				$strpay2 = '<tr>
						<td>Payment</td>
						<td>&nbsp;</td>
						<td align="right"><b>'.number_format($row2->Past_Months, 2).'</b></td>
						<td align="right"><b>'.number_format($row2->Current_Month, 2).'</b></td>
						<td align="right"><b>'.number_format($tpay2, 2).'</b></td>
					</tr>';
	
			}
			
			else if (stristr($row2->Description,"Reserve Assessment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastres2 = $sumpastres2 + $row2->Past_Months;
				$sumcurrres2 = $sumcurrres2 + $row2->Current_Month;
				$tres2 = $row2->Past_Months + $row2->Current_Month;  
				$sumres2 = $sumres2 + $tres2; 
				
				if($sumres2 != 0 && stristr(isset($rows2[$it2]->Description),"Reserve Assessment")== ""){
				?>
					<tr>
						<td><?php echo "Reserve Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastres2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrres2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumres2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastres2 = 0;
					$sumcurrres2 = 0;
					$sumres2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Roof Reserve"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastro2 = $sumpastro2 + $row2->Past_Months;
				$sumcurrro2 = $sumcurrro2 + $row2->Current_Month;
				$tro2 = $row2->Past_Months + $row2->Current_Month;  
				$sumro2 = $sumro2 + $tro2; 
				
				if($sumro2 != 0 && stristr(isset($rows2[$it2]->Description),"Roof Reserve")== ""){
				?>
					<tr>
						<td><?php echo "Roof Reserve" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastro2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrro2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumro2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastro2 = 0;
					$sumcurrro2 = 0;
					$sumro2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Security Assessment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastsec2 = $sumpastsec2 + $row2->Past_Months;
				$sumcurrsec2 = $sumcurrsec2 + $row2->Current_Month;
				$tsec2 = $row2->Past_Months + $row2->Current_Month;  
				$sumsec2 = $sumsec2 + $tsec2; 
				
				if($sumsec2 != 0 && stristr(isset($rows2[$it2]->Description),"Security Assessment")== ""){
				?>
					<tr>
						<td><?php echo "Security Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastsec2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrsec2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumsec2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastsec2 = 0;
					$sumcurrsec2 = 0;
					$sumsec2 = 0;
				}
			}
			
			else if (stristr($row2->Description,"Special Assessment"))
			{
				$sumpast = $sumpast + $row2->Past_Months;
				$sumcurr = $sumcurr + $row2->Current_Month;
				$total2 = $row2->Past_Months + $row2->Current_Month; 
				$sum = $sum + $total2;
				
				$sumpastspe2 = $sumpastspe2 + $row2->Past_Months;
				$sumcurrspe2 = $sumcurrspe2 + $row2->Current_Month;
				$tspe2 = $row2->Past_Months + $row2->Current_Month;  
				$sumspe2 = $sumspe2 + $tspe2; 
				
				if($sumspe2 != 0 && stristr(isset($rows2[$it2]->Description),"Special Assessment")== ""){
				?>
					<tr>
						<td><?php echo "Special Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastspe2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrspe2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumspe2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastspe2 = 0;
					$sumcurrspe2 = 0;
					$sumspe2 = 0;
				}
			}
			
			else {
				if($sumann2 != 0){
				?>
					<tr>
						<td><?php echo "Annual Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastann2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrann2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumann2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastann2 = 0;
					$sumcurrann2 = 0;
					$sumann2 = 0;
				}
				
				if($sumcap2 != 0){
				?>
					<tr>
						<td><?php echo "Capital Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastcap2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrcap2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcap2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastcap2 = 0;
					$sumcurrcap2 = 0;
					$sumcap2 = 0;
				}
				
				if($sumcou2 != 0){
				?>
					<tr>
						<td><?php echo "Court Fees" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastcou2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrcou2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcou2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastcou2 = 0;
					$sumcurrcou2 = 0;
					$sumcou2 = 0;
				}
				
				if($sumgue2 != 0){
				?>
					<tr>
						<td><?php echo "Guest Fees-Out of Town" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastgue2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrgue2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumgue2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastgue2 = 0;
					$sumcurrgue2 = 0;
					$sumgue2 = 0;
				}
				
				if($sumlate2 != 0){
				?>
					<tr>
						<td><?php echo "Late Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastlate2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrlate2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumlate2, 2); ?></b></td>
					</tr>
					<!--<tr>
						<td><span style="margin-left:20px;">Late Assessment</span></td>
						<td align="right" style="border-bottom:solid 1px #000000;"><?php //echo number_format($sumlate2, 2); ?></td>
						<td colspan="3">&nbsp;</td>
					</tr>
					 <tr>
						<td>&nbsp;</td>
						<td align="right"><?php //echo number_format($sumlate2, 2); ?></td>
						<td colspan="3">&nbsp;</td>
					</tr>-->
			<?php
					$sumlate2 = 0;
					$sumpastlate2 = 0;
					$sumcurrlate2 = 0;
				}
				
				if($sumleg2 != 0){
				?>
					<tr>
						<td><?php echo "Legal Fees-Inv" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastleg2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrleg2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumleg2, 2); ?></b></td>
					</tr>
			<?php
					$sumleg2 = 0;
					$sumpastleg2 = 0;
					$sumcurrleg2 = 0;
				}
				
				if($sumres2 != 0){
				?>
					<tr>
						<td><?php echo "Reserve Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastres2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrres2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumres2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastres2 = 0;
					$sumcurrres2 = 0;
					$sumres2 = 0;
				}
				
				if($sumro2 != 0){
				?>
					<tr>
						<td><?php echo "Roof Reserve" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastro2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrro2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumro2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastro2 = 0;
					$sumcurrro2 = 0;
					$sumro2 = 0;
				}
				
				if($sumsec2 != 0){
				?>
					<tr>
						<td><?php echo "Security Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastsec2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrsec2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumsec2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastsec2 = 0;
					$sumcurrsec2 = 0;
					$sumsec2 = 0;
				}
				
				if($sumspe2 != 0){
				?>
					<tr>
						<td><?php echo "Special Assessment" ?></td>
						<td>&nbsp;</td>
						<td align="right"><b><?php echo number_format($sumpastspe2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumcurrspe2, 2); ?></b></td>
						<td align="right"><b><?php echo number_format($sumspe2, 2); ?></b></td>
					</tr>
			<?php
					$sumpastspe2 = 0;
					$sumcurrspe2 = 0;
					$sumspe2 = 0;
				}
		?>
				<tr>
					<td><?php echo $row2->Description; ?></td>
					<td>&nbsp;</td>
					<td align="right"><b><?php $past2 = number_format($row2->Past_Months, 2); $sumpast = $sumpast + $row2->Past_Months; echo $past2;?></b></td>
					<td align="right"><b><?php $charge2 = number_format($row2->Current_Month, 2); $sumcurr = $sumcurr + $row2->Current_Month; echo $charge2 ;?></b></td>
					<td align="right"><b><?php $total2 = $row2->Past_Months + $row2->Current_Month; $sum = $sum + $total2; echo number_format($total2, 2);?></b></td>
				</tr>
			
		<?php
			
			}
			$it2++;		
    }
	
	if($strpay2 != ""){
			echo $strpay2;
	}
	
    ?>
    <tr>
       <td>&nbsp;</td>
        <td style="border-top:#000000 dashed 1px;"><b>Total Activity</b></td>
        <td align="right" style="border-top:#000000 dashed 1px;"><b><?php if (number_format($sumpast,2) == -0.00) { echo "0.00"; } else {echo number_format($sumpast,2);} ?></b></td>
            <td align="right" style="border-top:#000000 dashed 1px;"><b><?php if (number_format($sumcurr,2) == -0.00) { echo "0.00"; } else {echo number_format($sumcurr,2);} ?></b></td>
            <td align="right" style="border-top:#000000 dashed 1px;"><b><?php if (number_format($sum,2) == -0.00) { echo "0.00"; } else {echo number_format($sum,2);} ?></b></td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
        <td style="background:#FFFFFF; height:30px;"><b>Total Due</b></td>
        <td align="right" style="background:#FFFFFF; height:30px;"><b style="text-decoration:underline;"><?php if (number_format($sum,2) == -0.00) { echo "0.00"; } else {echo number_format($sum,2);} ?></b></td>
    </tr>
    <?php
			if ($rows1[0]->SurePay == "Y" || $rows2[0]->SurePay == "Y"){
		?>
        <tr>
            <td colspan="5" align="center">
            	<div style="margin-top:10px; text-align:center; border: 1px solid #000000; width:50%; padding:5px;"> ** Do not pay - Sure Pay Account ** </div>
            </td>
        </tr>
        <?php
			}
		?>
    <tr>
        <td colspan="5"><div style="margin-top:10px; text-align:justify;">Should the Gainey Ranch Community Association assessment not be received within 30 days from the statement date, a late fee in the amount of $15.00 shall be charged for the master association. The late fees relating to any delinquent satellite association assessment may vary between associations. In addition to late fees, any balances remaining delinquent after 60 days will be assessed an interest charge at the rate of 18% per annum. Interest charges may vary on satellite delinquent balances. </div></td>
   </tr>
  </table>
  
  <?php
  }
  }
}
?>