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
 * file: SqlJomSocialImport.php
 *
 **** class 
     JomSocialImport 
	 
 **** functions
     __construct();	
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
//ini_set( 'display_errors', true ); error_reporting( E_ALL );

class JomSocialImport{
	
	function getAllGroups(){
		$db =& JFactory::getDBO();
		$sql = "select id, name from #__community_groups";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		return $result;	
	}

	function updateUserJomSocial($columns_imported, $row, $user_id, $fields_details, $videos_category, $photos_albums){	
		$db =& JFactory::getDBO();			
		foreach($columns_imported as $key=>$value){			
			$sql = "";
			$field_id = "";
			if($value == "id"){
				// do nothing
			}
			elseif($value == "name" || $value == "username" || $value == "email" || $value == "password" || $value == "group" || $value == "usertype" || $value == "block" || $value == "registerDate" || $value == "lastvisitDate"){
				unset($columns_imported[$key]);
			}
			elseif($value == "status"){
				$sql = "update #__community_users set `status`= '".addslashes($row[$key])."' where userid=".$user_id;				
				$db->setQuery($sql);
				$db->query();
			}
			elseif($value == "videos"){
				$sql = "select * from #__community_videos where creator=".intval($user_id);
				$db->setQuery($sql);
				$db->query();
				$existing_videos_for_user = $db->loadAssocList("video_id");

				$arrays_category_old_new = array();//we will save old ids as array keys and new ids as values for this array
				$encripted_videos = $row[$key];
				$encripted_videos = str_replace("***", ",", $encripted_videos);
				$decripted_videos = json_decode($encripted_videos);
				
				$encripted_video_category = $fields_details["videos"];
				$decripted_video_category = json_decode($encripted_video_category);
				
				if(isset($videos_category) && count($videos_category) > 0){
					if(isset($decripted_video_category) && count($decripted_video_category) > 0){
						foreach($decripted_video_category as $key=>$value){
							if(isset($videos_category[trim($key)])){
								//this category already exist
								$arrays_category_old_new[$value->id] = $value->id;
							}
							else{
								//this is a new category and must insert this
								$sql = "insert into #__community_videos_category(`parent`, `name`, `description`, `published`) values (".$value->parent.", '".addslashes(trim($value->name))."', '".addslashes(trim($value->description))."', ".$value->published.")";
								$db->setQuery($sql);
								if($db->query()){
									//get new category id
									$sql = "select id from #__community_videos_category where name='".addslashes(trim($value->name))."'";
									$db->setQuery($sql);
									$db->query();
									$new_cat_id = $db->loadResult();
									$arrays_category_old_new[$value->id] = $new_cat_id;
								}
							}
						}//foreach
					}//if exist category
				}
				else{
					//no category in database, so insert with-out verifications					
					foreach($decripted_video_category as $key=>$value){
						$sql = "insert into #__community_videos_category(`id`, `name`, `description`, `published`) values (".$value->id.", '".addslashes(trim($value->name))."', '".addslashes(trim($value->description))."', ".$value->published.")";
						$db->setQuery($sql);
						$db->query();
						$arrays_category_old_new[$value->id] = $value->id;
					}
				}
				//we have categories saved and now insert user videos in database				
				if(isset($decripted_videos) && count($decripted_videos) > 0){
					foreach($decripted_videos as $key=>$value){
						if(!isset($existing_videos_for_user[$value->video_id])){
							//if this video is not in database
							$sql = "insert into #__community_videos (`title`, `type`, `video_id`, `description`, `creator`, `creator_type`, `created`, `permissions`, `category_id`, `hits`, `published`, `featured`, `duration`, `status`, `thumb`, `path`, `groupid`, `filesize`, `storage`) values ('".addslashes(trim($value->title))."', '".addslashes(trim($value->type))."', '".addslashes(trim($value->video_id))."', '".addslashes(trim($value->description))."', ".intval($user_id).", '".addslashes(trim($value->creator_type))."', '".trim($value->created)."', '".addslashes($value->permissions)."', ".$arrays_category_old_new[$value->category_id].", ".$value->hits.", ".$value->published.", ".$value->featured.", ".$value->duration.", '".$value->status."', '".addslashes(trim($value->thumb))."', '".addslashes(trim($value->path))."', ".$value->groupid.", ".$value->filesize.", '".addslashes(trim($value->storage))."')";
							$db->setQuery($sql);
							$db->query();
						}
					}
				}				
			}
			elseif($value == "photos"){
				$sql = "select * from #__community_photos where creator=".intval($user_id);
				$db->setQuery($sql);
				$db->query();
				$existing_photos_for_user = $db->loadAssocList("caption");

				$arrays_category_old_new = array();//we will save old ids as array keys and new ids as values for this array
				$encripted_photos = $row[$key];
				$encripted_photos = str_replace("***", ",", $encripted_photos);
				$decripted_photos = json_decode($encripted_photos);
				
				$encripted_photos_category = $fields_details["photos"];
				$decripted_photos_category = json_decode($encripted_photos_category);
				
				if(isset($photos_albums) && count($photos_albums) > 0){
					if(isset($decripted_photos_category) && count($decripted_photos_category) > 0){
						foreach($decripted_photos_category as $key=>$value){
							if(isset($photos_albums[trim($key)])){
								//this album already exist
								$arrays_category_old_new[$value->id] = $value->id;
							}
							else{
								//this is a new album and must insert this
								$sql = "insert into #__community_photos_albums(`photoid`, `creator`, `name`, `description`, `permissions`, `created`, `path`, `type`, `groupid`) values (".$value->photoid.", '".$user_id."', '".addslashes(trim($value->name))."', '".addslashes(trim($value->description))."', '".addslashes(trim($value->permissions))."', '".trim($value->created)."', '".addslashes(trim($value->path))."', '".addslashes(trim($value->type))."', ".$value->groupid.")";
								$db->setQuery($sql);
								if($db->query()){
									//get new category id
									$sql = "select id from #__community_photos_albums where name='".addslashes(trim($value->name))."'";
									$db->setQuery($sql);
									$db->query();
									$new_cat_id = $db->loadResult();
									$arrays_category_old_new[$value->id] = $new_cat_id;
								}
							}
						}//foreach
					}//if exist category
				}
				else{
					//no albums in database, so insert with-out verifications
					foreach($decripted_photos_category as $key=>$value){
						$sql = "insert into #__community_photos_albums(`id`, `photoid`, `creator`, `name`, `description`, `permissions`, `created`, `path`, `type`, `groupid`) values (".$value->id.", ".$value->photoid.", '".$user_id."', '".addslashes(trim($value->name))."', '".addslashes(trim($value->description))."', '".addslashes(trim($value->permissions))."', '".trim($value->created)."', '".addslashes(trim($value->path))."', '".addslashes(trim($value->type))."', ".$value->groupid.")";						
						$db->setQuery($sql);
						$db->query();
						$arrays_category_old_new[$value->id] = $value->id;
					}
				}
				//we have categories saved and now insert user videos in database
				if(isset($decripted_photos) && count($decripted_photos) > 0){
					foreach($decripted_photos as $key=>$value){
						if(!isset($existing_photos_for_user[$value->caption])){
							//if this video is not in database
							$sql = "insert into #__community_photos (`albumid`, `caption`, `published`, `creator`, `permissions`, `image`, `thumbnail`, `original`, `filesize`, `storage`, `created`, `ordering`) values (".$arrays_category_old_new[$value->albumid].", '".addslashes(trim($value->caption))."', ".$value->published.", ".$user_id.", '".addslashes(trim($value->permissions))."', '".addslashes(trim($value->image))."', '".addslashes(trim($value->thumbnail))."', '".addslashes(trim($value->original))."', ".$value->filesize.", '".addslashes(trim($value->storage))."', '".$value->created."', ".$value->ordering.")";
							$db->setQuery($sql);
							$db->query();
						}
					}
				}				
			}
			else{											
				$temp = json_decode(trim($fields_details[$value]));				
				$type = $temp["0"]->type;	
							
				if($this->fieldExist($temp["0"]->fieldcode)){
					$sql = "update #__community_fields set type='".addslashes($temp["0"]->type)."', ordering=".addslashes($temp["0"]->ordering).", published=".addslashes($temp["0"]->published).",	min=".addslashes($temp["0"]->min).", max=".addslashes($temp["0"]->max).", tips='".addslashes($temp["0"]->tips)."', visible=".addslashes($temp["0"]->visible).", required=".addslashes($temp["0"]->required).", searchable=".addslashes($temp["0"]->searchable).", registration=".addslashes($temp["0"]->registration).", options='".addslashes($temp["0"]->options)."', fieldcode='".addslashes($temp["0"]->fieldcode)."' where name='".addslashes($temp["0"]->name)."'";
				}
				else{
					$sql = "insert into #__community_fields (`type`,`ordering`,`published`,`min`,`max`,`name`,`tips`,`visible`,`required`,`searchable`,`registration` 	`options`,`fieldcode`) values ('".addslashes($temp["0"]->type)."', ".addslashes($temp["0"]->ordering).", ".addslashes($temp["0"]->published).", ".addslashes($temp["0"]->min).", ".addslashes($temp["0"]->max).", '".addslashes($temp["0"]->name)."', '".addslashes($temp["0"]->tips)."', ".addslashes($temp["0"]->visible).", ".addslashes($temp["0"]->required).", ".addslashes($temp["0"]->searchable).", ".addslashes($temp["0"]->registration).", '".addslashes($temp["0"]->options)."', '".addslashes($temp["0"]->fieldcode)."')";
				}				
				$db->setQuery($sql);								
				if($db->query()){
					$sql = "select id from #__community_fields where `fieldcode`='".addslashes($temp["0"]->fieldcode)."'";
					$db->setQuery($sql);
					$db->query();
					$field_id = $db->loadResult();
				}				
				if($this->existFieldValue($user_id, $field_id)){
					if(trim($row[$key]) != ""){
						$sql = "update #__community_fields_values set value='".addslashes($row[$key])."' where user_id=".$user_id." and field_id=".$field_id;
						$db->setQuery($sql);
						$db->query();
					}
				}
				else{
					if(trim($row[$key]) != ""){
						$sql = "insert into #__community_fields_values (`user_id`, `field_id`, `value`) values (".$user_id.", ".$field_id.", '".addslashes($row[$key])."')";
						$db->setQuery($sql);
						$db->query();
					}
				}
			}
		}
	}
	
	function saveUserInJomSocial($columns_imported, $row, $user_id, $fields_details, $array_from_joomla, $component_params, $category, $videos_category, $photos_albums){
		$component_params_all = unserialize($component_params);
		$component_params = $component_params_all["JomSocialImport"];					
		$db =& JFactory::getDBO();
		$saved = false;	
		
		foreach($columns_imported as $key=>$value){					
			if($value=="name" || $value=="username" || $value=="email" || $value=="password" || $value=="usertype" || $value=="block" || $value=="registerDate" || $value=="lastvisitDate"){
				unset($columns_imported[$key]);
				unset($row[$value]);
			}			
			elseif($value=="status"){				
				if($saved === false){
					$this->saveJomUsers($row[$value], $user_id);					
					$saved = true;										
				}
				unset($columns_imported[$key]);
				unset($row[$value]);
			}
			elseif($value == "videos"){
				$sql = "select * from #__community_videos where creator=".intval($user_id);
				$db->setQuery($sql);
				$db->query();
				$existing_videos_for_user = $db->loadAssocList("video_id");

				$arrays_category_old_new = array();//we will save old ids as array keys and new ids as values for this array
				$encripted_videos = $row[$key];
				$encripted_videos = str_replace("***", ",", $encripted_videos);
				$decripted_videos = json_decode($encripted_videos);
				
				$encripted_video_category = $fields_details["videos"];
				$decripted_video_category = json_decode($encripted_video_category);
				
				if(isset($videos_category) && count($videos_category) > 0){
					if(isset($decripted_video_category) && count($decripted_video_category) > 0){
						foreach($decripted_video_category as $key=>$value){
							if(isset($videos_category[trim($key)])){
								//this category already exist
								$arrays_category_old_new[$value->id] = $value->id;
							}
							else{
								//this is a new category and must insert this
								$sql = "insert into #__community_videos_category(`parent`, `name`, `description`, `published`) values (".$value->parent.", '".addslashes(trim($value->name))."', '".addslashes(trim($value->description))."', ".$value->published.")";
								$db->setQuery($sql);
								if($db->query()){
									//get new category id
									$sql = "select id from #__community_videos_category where name='".addslashes(trim($value->name))."'";
									$db->setQuery($sql);
									$db->query();
									$new_cat_id = $db->loadResult();
									$arrays_category_old_new[$value->id] = $new_cat_id;
								}
							}
						}//foreach
					}//if exist category
				}
				else{
					//no category in database, so insert with-out verifications					
					foreach($decripted_video_category as $key=>$value){
						$sql = "insert into #__community_videos_category(`id`, `name`, `description`, `published`) values (".$value->id.", '".addslashes(trim($value->name))."', '".addslashes(trim($value->description))."', ".$value->published.")";
						$db->setQuery($sql);
						$db->query();
						$arrays_category_old_new[$value->id] = $value->id;
					}
				}
				//we have categories saved and now insert user videos in database				
				if(isset($decripted_videos) && count($decripted_videos) > 0){
					foreach($decripted_videos as $key=>$value){
						if(!isset($existing_videos_for_user[$value->video_id])){
							//if this video is not in database
							$sql = "insert into #__community_videos (`title`, `type`, `video_id`, `description`, `creator`, `creator_type`, `created`, `permissions`, `category_id`, `hits`, `published`, `featured`, `duration`, `status`, `thumb`, `path`, `groupid`, `filesize`, `storage`) values ('".addslashes(trim($value->title))."', '".addslashes(trim($value->type))."', '".addslashes(trim($value->video_id))."', '".addslashes(trim($value->description))."', ".intval($user_id).", '".addslashes(trim($value->creator_type))."', '".trim($value->created)."', '".addslashes($value->permissions)."', ".$arrays_category_old_new[$value->category_id].", ".$value->hits.", ".$value->published.", ".$value->featured.", ".$value->duration.", '".$value->status."', '".addslashes(trim($value->thumb))."', '".addslashes(trim($value->path))."', ".$value->groupid.", ".$value->filesize.", '".addslashes(trim($value->storage))."')";
							$db->setQuery($sql);
							$db->query();
						}
					}
				}				
			}
			elseif($value == "photos"){
				$sql = "select * from #__community_photos where creator=".intval($user_id);
				$db->setQuery($sql);
				$db->query();
				$existing_photos_for_user = $db->loadAssocList("caption");

				$arrays_category_old_new = array();//we will save old ids as array keys and new ids as values for this array
				$encripted_photos = $row[$key];
				$encripted_photos = str_replace("***", ",", $encripted_photos);
				$decripted_photos = json_decode($encripted_photos);
				
				$encripted_photos_category = $fields_details["photos"];
				$decripted_photos_category = json_decode($encripted_photos_category);
				
				if(isset($photos_albums) && count($photos_albums) > 0){
					if(isset($decripted_photos_category) && count($decripted_photos_category) > 0){
						foreach($decripted_photos_category as $key=>$value){
							if(isset($photos_albums[trim($key)])){
								//this album already exist
								$arrays_category_old_new[$value->id] = $value->id;
							}
							else{
								//this is a new album and must insert this
								$sql = "insert into #__community_photos_albums(`photoid`, `creator`, `name`, `description`, `permissions`, `created`, `path`, `type`, `groupid`) values (".$value->photoid.", '".$user_id."', '".addslashes(trim($value->name))."', '".addslashes(trim($value->description))."', '".addslashes(trim($value->permissions))."', '".trim($value->created)."', '".addslashes(trim($value->path))."', '".addslashes(trim($value->type))."', ".$value->groupid.")";
								$db->setQuery($sql);
								if($db->query()){
									//get new category id
									$sql = "select id from #__community_photos_albums where name='".addslashes(trim($value->name))."'";
									$db->setQuery($sql);
									$db->query();
									$new_cat_id = $db->loadResult();
									$arrays_category_old_new[$value->id] = $new_cat_id;
								}
							}
						}//foreach
					}//if exist category
				}
				else{
					//no albums in database, so insert with-out verifications
					foreach($decripted_photos_category as $key=>$value){
						$sql = "insert into #__community_photos_albums(`id`, `photoid`, `creator`, `name`, `description`, `permissions`, `created`, `path`, `type`, `groupid`) values (".$value->id.", ".$value->photoid.", '".$user_id."', '".addslashes(trim($value->name))."', '".addslashes(trim($value->description))."', '".addslashes(trim($value->permissions))."', '".trim($value->created)."', '".addslashes(trim($value->path))."', '".addslashes(trim($value->type))."', ".$value->groupid.")";						
						$db->setQuery($sql);
						$db->query();
						$arrays_category_old_new[$value->id] = $value->id;
					}
				}
				//we have categories saved and now insert user videos in database
				if(isset($decripted_photos) && count($decripted_photos) > 0){
					foreach($decripted_photos as $key=>$value){
						if(!isset($existing_photos_for_user[$value->caption])){
							//if this video is not in database
							$sql = "insert into #__community_photos (`albumid`, `caption`, `published`, `creator`, `permissions`, `image`, `thumbnail`, `original`, `filesize`, `storage`, `created`, `ordering`) values (".$arrays_category_old_new[$value->albumid].", '".addslashes(trim($value->caption))."', ".$value->published.", ".$user_id.", '".addslashes(trim($value->permissions))."', '".addslashes(trim($value->image))."', '".addslashes(trim($value->thumbnail))."', '".addslashes(trim($value->original))."', ".$value->filesize.", '".addslashes(trim($value->storage))."', '".$value->created."', ".$value->ordering.")";
							$db->setQuery($sql);
							$db->query();
						}
					}
				}				
			}	
			elseif($value=="category"){
			}
			elseif($value=="group"){
				$user =& JFactory::getUser();
				$user_id_logged = $user->id;	
				$all_groups = json_decode($fields_details["group"]);							
				$groups = explode("***", $row[$key]);
											
				foreach($groups as $key=>$val){
					if($val != ""){							
						$group_id = $this->groupExist($val);
						if($group_id != NULL){
							$sql = "insert into #__community_groups_members(`groupid`, `memberid`, `approved`, `permissions`) values (".$group_id.", ".$user_id.", 1, 0)";								
							$db->setQuery($sql);
							if($db->query()){
								$this->incrementUsers($group_id);
								$send_email_to_import = $this->checked("send_email_to_import", $component_params);
								$encripted_password = JRequest::getVar("encripted_password_radio","1","post","string");										
								if($send_email_to_import != "false" && $encripted_password == "1"){	
									$this->sendEmail($array_from_joomla, $val, $component_params);
								}
							}
						}
						else{							
							$old_cat_id = $all_groups->$val->categoryid;
							$new_cat_id = "";
							if(!$this->existGroupsCategory($category[$old_cat_id]->name)){
								$sql = "insert into #__community_groups_category (`name`, `description`) values ('".addslashes($category[$old_cat_id]->name)."', '".addslashes($category[$old_cat_id]->description)."')";								
								$db->setQuery($sql);
								if($db->query()){
									$sql = "select id from #__community_groups_category where name='".addslashes($category[$old_cat_id]->name)."'";
									$db->setQuery($sql);
									$new_cat_id = $db->loadResult();
								}
							}
							else{
								$sql = "select id from #__community_groups_category where name='".addslashes($category[$old_cat_id]->name)."'";
								$db->setQuery($sql);
								$new_cat_id = $db->loadResult();
							}	
							
							$sql = "insert into #__community_groups(`published`, `ownerid`, `categoryid`, `name`, `description`, `email`, 	`website`, `approvals`, `created`, `avatar`, `thumb`, `discusscount`, `wallcount`, `membercount`, `params`
	) values (".$all_groups->$val->published.", ".$user_id_logged.", ".$new_cat_id.", '".addslashes($all_groups->$val->name)."', '".addslashes($all_groups->$val->description)."', '".addslashes($all_groups->$val->email)."', '".$all_groups->$val->website."', ".$all_groups->$val->approvals.", '".$all_groups->$val->created."', '".$all_groups->$val->avatar."', '".$all_groups->$val->thumb."', ".$all_groups->$val->discusscount.", ".$all_groups->$val->wallcount.", 0, '".$all_groups->$val->params."')";											
							$db->setQuery($sql);
							if($db->query()){
								$sql = "select id from #__community_groups where name='".addslashes($val)."'";
								$db->setQuery($sql);
								if($db->query()){
									$result = $db->loadResult();
									$group_id = $result;
									$sql = "insert into #__community_groups_members(`groupid`, `memberid`, `approved`, `permissions`) values (".$result.", ".$user_id.", 1, 0)";
									$db->setQuery($sql);
									if($db->query()){
										$this->incrementUsers($group_id);
										$send_email_to_import = $this->checked("send_email_to_import", $component_params);
										$encripted_password = JRequest::getVar("encripted_password_radio","1","post","string");										
										if($send_email_to_import != "false" && $encripted_password == "1"){	
											$this->sendEmail($array_from_joomla, $val, $component_params);
										}	
									}
								}
							}
						}
					}
				}
				unset($columns_imported[$key]);
				unset($row[$value]);			
			}
			else{				
				if($saved === false){
					if(isset($row["status"])){
						$this->saveJomUsers($row["status"], $user_id);
					}
					else{
						$this->saveJomUsers("", $user_id);
					}
					$saved = true;				
				}
				
				$this->saveJomFields($row[$key], $user_id, $fields_details[$value]);
				unset($columns_imported[$key]);
				unset($row[$value]);
			}
		}
	}
	
	function checked($radio_name, $result){
	    $rows = explode(";", $result);
		foreach($rows as $key=>$value){
		     $value=explode("=", $value);
			 if($radio_name == trim($value[0])){
			     return trim($value[1]);
			 }
		} 
	}
	
	function processText($text, $name, $username, $password, $usertype, $group_name, $from, $fromname, $sitename){		 
		if(preg_match("/{name}/", $text) ){
			$text = str_replace("{name}", $name, $text);
		}
		if(preg_match("/{username}/", $text) ){
			$text = str_replace("{username}", $username, $text);
		}
		if(preg_match("/{password}/", $text) ){
			$text = str_replace("{password}", $password, $text);
		}
		if(preg_match("/{usertype}/", $text) ){
			$text = str_replace("{usertype}", $usertype, $text);
		}
		if(preg_match("/{from_name}/", $text) ){
			$text = str_replace("{from_name}", $fromname, $text);
		}
		if(preg_match("/{sitename}/", $text) ){
			$text = str_replace("{sitename}", $sitename, $text);
		}
		if(preg_match("/{from_email}/", $text) ){
			$text = str_replace("{from_email}", $from, $text);
		}
		if(preg_match("/{group_name}/", $text) ){
			$text = str_replace("{group_name}", $group_name, $text);
		}		
		return $text;
	}
	
	function sendEmail($array_from_joomla, $group_name, $component_params){		
		$recipient = array(); 
		$recipient[] = $array_from_joomla["email"];
		
		$from = $this->checked("from_email", $component_params);
		$fromname = $this->checked("from_name", $component_params);
		$sitename = $this->checked("sitename", $component_params);		  		  
		$subject_mambot = $this->checked("subject_template", $component_params);
		$body_mambot = $this->checked("email_template", $component_params);
		
		$name = $array_from_joomla["name"];
		$username = $array_from_joomla["username"];
		$password = $array_from_joomla["password"];
		$usertype = $array_from_joomla["usertype"];
		
		$subject_procesed = $this->processText($subject_mambot, $name, $username, $password, $usertype, $group_name, $from, $fromname, $sitename); 
		$body_procesed = $this->processText($body_mambot, $name, $username, $password, $usertype, $group_name, $from, $fromname, $sitename);		
		$mode = false;		 	  
		JUtility::sendMail($from, $fromname, $recipient, $subject_procesed, $body_procesed, $mode);	    
	}
	
	function saveJomUsers($status="", $user_id){		
		$db =& JFactory::getDBO();
		$sql = "insert into #__community_users(`userid`, `status`, `points`, `posted_on`, `avatar`, `thumb`, `invite`, `params`, `view`, `friendcount`, `alias`, `latitude`, `longitude`) values(".$user_id.", '".$status."', 0, '0000-00-00 00:00:00', 'components/com_community/assets/default.jpg', 'components/com_community/assets/default_thumb.jpg', 0, 'notifyEmailSystem=1
privacyProfileView=0
privacyPhotoView=0
privacyFriendsView=0
privacyVideoView=1
notifyEmailMessage=1
notifyEmailApps=1
notifyWallComment=0
daylightsavingoffset=0
', 0, 0, '', 255, 255)";
		$db->setQuery($sql);
		$db->query();		
	}
	
	function saveJomFields($value, $user_id, $details_encoded){		
		$details = json_decode($details_encoded);		
		$poz = $details["0"]->fieldcode;
		
		$db =& JFactory::getDBO();
		$fields_id = $this->getFieldId($poz);				
		if($fields_id != NULL){
			if($value != ""){
				if(trim($value) != ""){
					$sql = "insert into #__community_fields_values(`user_id`, `field_id`, `value`) values(".$user_id.", ".$fields_id.", '".addslashes($value)."')";														
					$db->setQuery($sql);
					$db->query();
				}
			}
		}
		else{
			//$details = json_decode($details_encoded);			
			$sql = "insert into #__community_fields(`type`, `ordering`, `published`, `min`, `max`, `name`, `tips`, `visible`, `required`, 	`searchable`, `registration`, `options`, `fieldcode`) values ('".$details["0"]->type."', ".$details["0"]->ordering.", ".$details["0"]->published.", ".$details["0"]->min.", ".$details["0"]->max.", '".addslashes($details["0"]->name)."', '".$details["0"]->tips."', ".$details["0"]->visible.", ".$details["0"]->required.", ".$details["0"]->searchable.", ".$details["0"]->registration.", '".$details["0"]->options."', '".$details["0"]->fieldcode."')";											
			$db->setQuery($sql);			
			if($db->query()){
				if($value != ""){
					$sql = "select id from #__community_fields where fieldcode='".addslashes($poz)."'";
					$db->setQuery($sql);
					if($db->query()){
						$result = $db->loadResult();
						if(trim($value) != ""){
							$sql = "insert into #__community_fields_values(`user_id`, `field_id`, `value`) values(".$user_id.", ".$result.", '".addslashes($value)."')";
							$db->setQuery($sql);
							$db->query(); 
						}
					}				
				}
			}
		}	
	}
	
	function getFieldId($value){
		$db =& JFactory::getDBO();
		$sql = "select id from #__community_fields where fieldcode='".addslashes($value)."'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		return $result;	
	}
	
	function fieldExist($code){
		$db =& JFactory::getDBO();
		$sql = "select count(*) from #__community_fields where fieldcode='".addslashes($code)."'";		 
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		if($result > 0){
			return true;
		}
		return false;
	}
	
	function existFieldValue($user_id, $field_id){
		$db =& JFactory::getDBO();
		$sql = "select count(*) from #__community_fields_values where user_id=".$user_id." and field_id=".$field_id;		
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		if($result > 0){
			return true;
		}
		return false;
	}
	
	function groupExist($value){
		$db =& JFactory::getDBO();
		$sql = "select id from #__community_groups where name='".addslashes($value)."'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		return $result;
	}
	
	function incrementUsers($group_id){
		$db =& JFactory::getDBO();
		$sql = "update `jos_community_groups` set membercount=membercount+1 where id=".$group_id;
		$db->setQuery($sql);
		$db->query();		
	}
	
	function existGroupsCategory($value){
		$db =& JFactory::getDBO();
		$sql = "select count(*) from #__community_groups_category where name='".addslashes(trim($value))."'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		if($result>0){
			return true;
		}
		return false;
	}	
};
?>