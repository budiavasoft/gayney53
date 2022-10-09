<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JApplicationHelper::getPath('admin_html'));
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jcsv'.DS.'tables');

$task	= JRequest::getCmd('task');

switch($task){
	case 'add':
		uploadFile();
		break;
		
	case 'cancel':		
		showFile($option);
		break;
	
	case 'save':		
		saveFile();
		break;	
		
	default:
		showFile($option);
		break;

}

function uploadFile(){
	//$lists['published'] = JHTML::_('select.booleanlist', 'published' , 'class="inputbox"', $row->published);
	HTML_jcsv::uploadFile();
}

function showFile($option){
	global $mainframe;
	
   $db =& JFactory::getDBO();
	
	$filter_order= $mainframe->getUserStateFromRequest( $option.'filter_order','filter_order','id','cmd' );

	$filter_order_Dir= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir','filter_order_Dir','','word' );

	$filter_state = $mainframe->getUserStateFromRequest( $option.'filter_state','filter_state', '','word' );

	$search = $mainframe->getUserStateFromRequest( $option.'search','search','','string' );

	$search = JString::strtolower( $search );

	$limit= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');

	$limitstart= $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');

	$where = array();

	if ( $search ) {
		$where[] = 'upfile LIKE "%'.$db->getEscaped($search).'%"';
	}	
	
	$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

	if ($filter_order == 'id'){
		$orderby 	= ' ORDER BY id';
	} else {
		$orderby 	= ' ORDER BY `'. $filter_order .'` '. $filter_order_Dir .'';
	}	
	
	$query = 'SELECT COUNT(*)'
	. ' FROM #__jcsv_files'
	. $where;

	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );	
		
	$query = "SELECT * FROM #__jcsv_files". $where. $orderby;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $db->loadObjectList();
	if($db->getErrorNum()){
		echo $db->stderr();
		return false;
	}
	
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;	

    $lists['search']= $search;	
   
	HTML_jcsv::showFile($rows, $pageNav, $option, $lists);
}

function saveFile(){

  global $mainframe;
  $db =& JFactory::getDBO();
  
  	// get month and year
  	$month = JRequest::getVar('month', '','post','string', JREQUEST_ALLOWRAW );
	$year = JRequest::getVar('year', '','post','string', JREQUEST_ALLOWRAW );
  
  	//Retrieve file details from uploaded file, sent from upload form
	$file = JRequest::getVar('upfile', null, 'files', 'array');
	
	//Import filesystem libraries. Perhaps not necessary, but does not hurt
	jimport('joomla.filesystem.file');
	
	//Clean up filename to get rid of strange characters like spaces etc
	$filename = JFile::makeSafe($file['name']);
	
	//Set up the source and destination of the file
	$src = $file['tmp_name'];
	$dest = JPATH_COMPONENT . DS . "uploads" . DS . $filename;
	
	if($month == "")
	{
		 $mainframe->redirect('index.php?option=com_jcsv&task=add', 'Month must be choose');
	}
	
	else if($year == "")
	{
		 $mainframe->redirect('index.php?option=com_jcsv&task=add', 'Year must be choose');
	}
	
	else{
	//First check if the file has the right extension, we need csv only
	 if ( strtolower(JFile::getExt($filename) ) == 'csv') {
	   if ( JFile::upload($src, $dest) ) {
	   
	   	  //upload csv in database
		  $fieldseparator = ",";
		  $lineseparator = "\n";
		  $csvfile = $dest;
			/********************************/
			/* Would you like to add an ampty field at the beginning of these records?
			/* This is useful if you have a table with the first field being an auto_increment integer
			/* and the csv file does not have such as empty field before the records.
			/* Set 1 for yes and 0 for no. ATTENTION: don't set to 1 if you are not sure.
			/* This can dump data in the wrong fields if this extra field does not exist in the table
			/********************************/
			$addauto = 1;
			
			
			if(!file_exists($csvfile)) {
				$mainframe->redirect('index.php?option=com_jcsv&task=add', 'File not found. Make sure you specified the correct path.');
				exit;
			}
			
			$file = fopen($csvfile,"r");
			
			if(!$file) {
				$mainframe->redirect('index.php?option=com_jcsv&task=add', 'Error opening data file. Please try again..');
				exit;
			}
			
			$size = filesize($csvfile);
			
			if(!$size) {
				$mainframe->redirect('index.php?option=com_jcsv&task=add', 'File is empty');
				exit;
			}
			
			$csvcontent = fread($file,$size);
			
			fclose($file);
			
			// check new csv or not, if not delete data before
			$query = "SELECT COUNT(*) as cou FROM #__jcsv WHERE month = '$month' and year = '$year'" ;
			$db->setQuery( $query );
			$result = $db->loadObjectList();
			
			if($result[0]->cou != 0)
			{
				$query = "DELETE FROM #__jcsv WHERE month = '$month' AND year = '$year'";
				$db->setQuery($query);
				$db->query();
			}
			
			$lines = 0;
			$queries = "";
			$linearray = array();
			
			foreach(explode($lineseparator,$csvcontent) as $line) {
			
				$lines++;
			
				$line = trim($line," \t");
				
				$line = str_replace("\r","",$line);
				
				/************************************
				This line escapes the special character. remove it if entries are already escaped in the csv file
				************************************/
				$line = str_replace("'","\'",$line);
				/*************************************/
				
				//echo $line.'<br>';
				//$line = preg_replace ( '/([^"])\,([^"])/' , "$1$2" , $line);
				 
				$cou = 0;
				if (preg_match_all('/"([^"]+)"/', $line, $m)) {
					$cou = preg_match_all('/"([^"]+)"/', $line, $m);
					if($cou != 0){
						for($i=0;$i<$cou;$i++){
							$m[0][$i] = isset($m[0][$i]) ? $m[0][$i] : null;
							$line = str_replace($m[0][$i],"text123".$i,$line);
							$m[0][$i] = str_replace(",","",$m[0][$i]);
						}
					}
				}
				
				//print_r($m);
				//echo $m[1]."<br/>";
				//echo '<br>'.$cou.'<br>';
				//echo $line.'<br>';
								
				$linearray = explode($fieldseparator,$line);
				
				//print_r($linearray);
				
				if($cou != 0){
					for($i=0;$i<$cou;$i++){
						$linearray = str_replace("text123".$i,$m[0][$i],$linearray); 
					}
				}
				
				$linearray = str_replace('"','',$linearray);
			
				$linemysql = implode("','",$linearray);
				
				if($addauto)
					$query = "insert into #__jcsv values('','$linemysql','$month','$year');";
				else
					$query = "insert into #__jcsv values('','$linemysql','$month','$year');";
				
				//$queries .= $query . "\n";
			
				$db->setQuery($query);
				$db->query();
				
				//echo $queries.'<br>';
				//echo $linemysql.'<br>';
				//print_r($linearray);
			}
			
		  
		  // list file name
	   	  $row =& JTable::getInstance('jcsv', 'Table');
  
		  if(!$row->bind(JRequest::get('post')))
		  {
			JError::raiseError(500, $row->getError() );
		  }
		  
		  $row->upfile = $filename;
		  $row->update = JRequest::getVar( 'update', '','post','string', JREQUEST_ALLOWRAW );
			
		  if(!$row->store()){
			JError::raiseError(500, $row->getError() );
		  }
		  
		  // send email to customer which approved and not blocked when new csv uploaded
		  //get all email customer
		  $query = 'SELECT DISTINCT u.email, u.name'
					. ' FROM #__jcsv j, #__users u, #__comprofiler c'
					. ' WHERE j.Unit = u.username AND u.id = c.user_id AND u.block = 0 AND c.approved = 1' ;
				
		  $db->setQuery( $query );
		  $result = $db->loadObjectList();
		  
		  foreach($result as $res)
     	  {
			  // call Joomla mailer configuration
			  $mailer =& JFactory::getMailer();
			  $config =& JFactory::getConfig();
			  $sender = array( 
						$config->getValue( 'config.mailfrom' ),
						$config->getValue( 'config.fromname' ) );
	
			  $mailer->setSender($sender);
			  
			  $recipient = "";
			  $recipient = $res->email;
			  $mailer->addRecipient($recipient);
			  
			  $mailer->setSubject('New Billing Statement');
			  
			  $body   = '<h3>Hi, '.$res->name.' </h3><br />'
						. '<div> The most recent statement has been uploaded. To view your statement, click on the following link,<a href="http://billing.gaineyranchca.com" target="_blank">http://billing.gaineyranchca.com</a>, or go to the Gainey Ranch Community Association website and click on the eStatements button. </div>'
						. '<br> Thank you, <br><br> Gainey Ranch Community Association Team ';
					
			  $mailer->isHTML(true);
			  $mailer->Encoding = 'base64';
			  $mailer->setBody($body);
			  
			  $send =& $mailer->Send();
			  
				if ( $send !== true ) {
					echo 'Error sending email: ' . $send->message;
				} 
		  }
		  
		  //send email to admin for notification that new csv uploaded
		  // call Joomla mailer configuration
		  $mailer2 =& JFactory::getMailer();
		  $cfg =& JFactory::getConfig();
		  $sender2 = array( 
				$cfg->getValue( 'config.mailfrom' ),
				$cfg->getValue( 'config.fromname' ) );

		  $mailer2->setSender($sender2);
			  
		  $admrecipient = "dmerrill@gaineyranchca.com";
		  $mailer2->addRecipient($admrecipient);
		  $mailer2->setSubject('New Billing Statement');
		  
		  $body2   = '<h3>Hi Administrator </h3><br />'
					. '<div> The most recent statement has been uploaded. To view your statement, click on the following link,<a href="http://billing.gaineyranchca.com" target="_blank">http://billing.gaineyranchca.com</a>, or go to the Gainey Ranch Community Association website and click on the eStatements button. </div>'
					. '<br> Thank you, <br><br> Gainey Ranch Community Association Team ';
				
		  $mailer2->isHTML(true);
		  $mailer2->Encoding = 'base64';
		  $mailer2->setBody($body2);
		  
		  $send2 =& $mailer2->Send();
		  
			if ( $send2 !== true ) {
				echo 'Error sending email: ' . $send2->message;
			} 
		  
		  //redirect
		  $mainframe->redirect('index.php?option=com_jcsv', 'File Uploaded');
		  
	   } 
	   else {
		  $mainframe->redirect('index.php?option=com_jcsv', 'File can not uploaded. Please Try again');
	   }
	} 
	else {
	   $mainframe->redirect('index.php?option=com_jcsv&task=add', 'File must be .csv');
	}
   }

}

?>