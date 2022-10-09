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
 * file: arrausers.php
 *
 **** class 
     ArrausersexportimportModelArrausers 
	 
 **** functions
     __construct()
     getUsersCount();
     getActualVersion();
	 getLatestVersion();
     getUserType();
     getNoTypeCount();
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

/**
 * ArrausersexportimportModelArrausers Model
 */
class ArrausersexportimportModelArrausersexportimport extends JModel{	

	function __construct() {	  
		parent::__construct();
	}
	 
	// method return a number af all users 
	function getUsersCount(){
        $db =& JFactory::getDBO();
		$sql = "select count(*) from #__users ";
		$db->setQuery($sql);
		$content = $db->loadResult(); 		
		return $content;
	}
	
	//select number of rows where is not user type
	function getNoTypeCount(){
        $db =& JFactory::getDBO();		
		$sql = "select count(*) from #__users where usertype = ''";
		$db->setQuery($sql);
		$content = $db->loadResult(); 
		
		return $content;
	}
		
	// method return actual version from xml config
	function getActualVersion(){	
	     $version = "";	
	     $mosConfig_absolute_path = JPATH_SITE;
		 require_once( $mosConfig_absolute_path.DS.'includes'.DS.'domit'.DS.'xml_domit_lite_include.php' );	   
		 $moduleBaseDir	   = JPath::clean( JPath::clean( JPATH_SITE ).DS."administrator".DS."components".DS."com_arrauserexportimport");
		 // xml file for module
		 $xmlfilecomp = $moduleBaseDir.DS."install.xml"; 		 		
		 if (file_exists( $xmlfilecomp )) {
			$xmlDoc = new DOMIT_Lite_Document();
			$xmlDoc->resolveErrors( true );
			if (!$xmlDoc->loadXML( $xmlfilecomp, false, true )) {
				print "err1";
				continue;
			}	
			$root = &$xmlDoc->documentElement;	
			if ($root->getTagName() != 'install') {
				print "err2";
				continue;
			}	
			if ($root->getAttribute( "type" ) != "component") {
				print "err3";
				continue;
			}						
			if($element = &$root->getElementsByPath( 'version', 1 )){				
				$version  .= $element->getText();
			}
			else{
				$version .= "N/A";		
			}						
		}		
		return $version;
	}
	
	// method return tha last version from www.joomlarra.com site
	function getLatestVersion(){		
		$version = "";
		if($this->isCurlInstalled() == true){		
			$data = 'http://www.joomlarra.com/downloads/Free%20Joomla%20Extensions/latest_version.txt';		
			$ch = @curl_init($data);
			@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			@curl_setopt($ch, CURLOPT_TIMEOUT, 10); 							
			
			//try{
				$version = @curl_exec($ch);
				$patern = "/([0-9])(\.[0-9])(\.[0-9])/";		
				if(preg_match($patern, $version)){
					return trim($version);
				}
				else{
					return "ERROR";
				}
			//}
			//catch (Exception $e){
				//return "ERROR";
			//}
		}
		else{
			return "ERROR";
		}
			
	}
	
	function isCurlInstalled() {
	    $array = get_loaded_extensions();
		if(in_array("curl", $array)){
			return true;
		}
		else{
			return false;
		}
	}
	
	// return all types by users
	function getUserType(){
	    $db =& JFactory::getDBO();		
		$sql = "SELECT DISTINCT usertype
		        FROM #__users
                WHERE usertype <> ''";
		$db->setQuery($sql);
		$result = $db->loadAssocList();

		return $result;
	}
	
	function getExitJomsocial(){
		$db =& JFactory::getDBO();		
		$sql = "SELECT count(*)
		        FROM #__components
                WHERE link='option=com_community'";
		$db->setQuery($sql);
		$result = $db->loadResult();
		if($result=="0"){
			return false;
		}
		else{
			return true;
		}
	}
	
	function getExitComBuilder(){
		$db =& JFactory::getDBO();		
		$sql = "SELECT count(*)
		        FROM #__components
                WHERE link='option=com_comprofiler'";
		$db->setQuery($sql);
		$result = $db->loadResult();
		if($result=="0"){
			return false;
		}
		else{
			return true;
		}
	}
	
	function getGroupCategory(){
		$db =& JFactory::getDBO();		
		$sql = "SELECT gc.name, count(g.id) as total from #__community_groups_category gc LEFT OUTER JOIN #__community_groups g on gc.id=g.categoryid group by gc.id";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getGroups(){
		$db =& JFactory::getDBO();		
		$sql = "SELECT g.published, g.name, g.membercount, gc.name as cat_name from #__community_groups g, #__community_groups_category gc where gc.id=g.categoryid";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getUsers(){
		$db =& JFactory::getDBO();		
		$sql = "SELECT sum(membercount) from #__community_groups";
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}
	
	function getBlockUsers(){
		$db =& JFactory::getDBO();
		$sql = "SELECT u.block, count(u.id) as total from #__community_users cu  LEFT OUTER JOIN #__users u on u.id=cu.userid where u.block=1 group by u.block";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getUnblockUsers(){
		$db =& JFactory::getDBO();
		$sql = "SELECT u.block, count(u.id) as total from #__community_users cu  LEFT OUTER JOIN #__users u on u.id=cu.userid where u.block=0 group by u.block";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getJomSocialUsers(){
		$db =& JFactory::getDBO();
		$sql = "SELECT count(*) from #__community_users cu, #__users u where u.id=cu.userid";
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}	
	
}