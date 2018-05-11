<?php
/**
* @copyright (C) 2017 JoomlArt, Inc. - All rights reserved.
* @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
* @author iJoomla.com <webmaster@ijoomla.com>
* @url https://www.jomsocial.com/license-agreement
* The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
* More info at https://www.jomsocial.com/license-agreement
*/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

require_once( JPATH_ROOT . '/components/com_community/libraries/core.php' );

/**
 * JomSocial Component Controller
 */
class CommunityControllerMigrators extends CommunityController{
	
	var $limit = 25;

	public function __construct()
	{
		parent::__construct();
		$lang = JFactory::getLanguage();
        $locale = $lang->getLocale();
        $lang->load( 'com_easysocial');

        $this->statusFile = JPATH_ROOT.'/tmp/migratorstatus.txt';
        if(!JFile::exists($this->statusFile)){
        	JFile::write($this->statusFile,'');
        }

	}

	public function __getMigrateStatus($param){
		$data = JFile::read($this->statusFile);
		$dataArr = json_decode($data,true);

		return isset($dataArr[$param])?$dataArr[$param]:0;
	}

	public function __updateMigrateStatus($param,$value){
		$data = JFile::read($this->statusFile);
		$dataArr = json_decode($data,true);

		$dataArr[$param] = $value;
		$dataUpdated = json_encode($dataArr);

		JFile::write($this->statusFile,$dataUpdated);
	}

	public function easysocial(){
		$mainframe	= JFactory::getApplication();
		$jinput     = $mainframe->input;

		$step = $jinput->request->get('step', 'avatars');
		$model		= $this->getModel( 'MigratorEasySocial' );

		$a=0;

		switch ($step) {
			case 'avatars':
				$lastId = $this->__getMigrateStatus($step);
				$list = $model->getAvatar($lastId,$this->limit);
				$count = $model->getCountAvatar();
				
				if(!empty($list)){
					
					foreach ($list as $row) {
						// copy the file 
						$oldPath = JPATH_ROOT.'/media/com_easysocial/avatars/users/'.$row->uid.'/'.$row->avatar;

						$avatarPath = '/images/avatar/'.$row->avatar;
						$avatarThumbPath = '/images/avatar/thumb_'.$row->avatar;

						$newPath = JPATH_ROOT.$avatarPath;
						$newPathThumb = JPATH_ROOT.$avatarThumbPath;

						// get the file type
						if(JFile::exists($oldPath)){
							$info = getimagesize($oldPath);
	                        $destType = image_type_to_mime_type($info[2]);

	                        // copy to jomsocial folder
	                       $saveSucess = JFile::copy($oldPath,$newPath);
							if($saveSucess){
								$a++;
								CImageHelper::createThumb($newPath, $newPathThumb, $destType, 128, 128);

								$userId = $row->uid;

								// save to jomsocial table
								$model->storeAvatar($userId,$avatarPath,$newPathThumb);
							}
						}
					}
					// flag last migrated data
					$this->__updateMigrateStatus($step,$row->id);
					$countLeft = $model->getCountAvatar($row->id);
				}else{
					$countLeft  = $count;
				}

				break;
			case 'covers':
				$lastId = $this->__getMigrateStatus($step);
				$list = $model->getCover($lastId,$this->limit);
				$count = $model->getCountCover();
				if(!empty($list)){
					foreach ($list as $row) {
						// copy the file 
						$oldPath = JPATH_ROOT.$row->cover;
						$coverPath = '/images/cover/'.uniqid().'.jpg';
						$newPath = JPATH_ROOT.$coverPath;
						
						// get the file type
						if(JFile::exists($oldPath)){
						    // copy to jomsocial folder
							$saveSucess = JFile::copy($oldPath,$newPath);
							if($saveSucess){
								$a++;
								
								$userId = $row->uid;

								// save to jomsocial table
								$model->storeCover($userId,$coverPath);
							}
						}
					}
					// flag last migrated data
					$this->__updateMigrateStatus($step,$row->id);
					$countLeft = $model->getCountCover($row->id);
				}else{
					$countLeft = $count;
				}

				
				break;
			case 'multiprofile':
				// save at once
				$list = $model->getMultiProfile();
				if(!empty($list)){
					foreach ($list as $row) {
						$name = $row->title;
						$description = $row->description;
						$published = $row->state;
						$id = $row->id;

						$model->storeMultiProfile($id,$name,$description,$published);
					}
				}
				
				// get the mapping fields and profile
				$list3 = $model->getFieldProfileType();
				foreach ($list3 as $row3) {
					$parent = $row3->uid;
					$field_id = $row3->field_id;

					$model->storeProfileFields($parent,$field_id);
				}

				// split this
				$lastId = $this->__getMigrateStatus($step);
				$list2 = $model->getMembersMultiProfile($lastId,$this->limit);
				$count = $model->getCountMembersMultiProfile();
				if(!empty($list2)){
					// store to user members
					foreach ($list2 as $row2) {
						$model->storeMembersMultiProfile($row2->user_id,$row2->profile_id);
						$a++;
					}

					$this->__updateMigrateStatus($step,$row2->user_id);
					$countLeft = $model->getCountMembersMultiProfile($row2->user_id);
				}else{
					$countLeft = $count;
				}
				break;
			case 'fields':
				$list = $model->getFieldsEasySocial();
				$lastId = $this->__getMigrateStatus($step);
				
				if(!isset($lastId)){
					foreach ($list as $row) {
						$id = $row->id;
						$type = $row->element;
						$name = JText::_($row->title);

						// type selection
						switch ($type) {
							case 'textbox':
							case 'autocomplete':
								$type = 'text';
								break;
							case 'dropdown':
								$type = 'select';
								break;
							case 'multidropdown':
							case 'multitextbox':
							case 'multitextbox':
								$type = 'list';
								break;
							case 'birthday':
								$type = 'birthdate';
								break;
							case 'datetime':
								$type = 'date';
								break;
							case 'permalink':
								$type = 'url';
								break;
						}
						
						$fieldCode = 'FIELDS_'.$row->unique_key;
						$published = $row->state;
						$min = '';
						$max = '';
						$visible = $row->visible_display;
						$required = $row->required;
						$searchable = $row->searchable;
						$ordering = $row->ordering;
						$registration = $row->visible_registration;
						
						$options = '';
						$optionsValueList = $model->getFieldsOptions($id);
						if(!empty($optionsValueList)){
							foreach ($optionsValueList as $rowOptions) {
								$options[] = $rowOptions->title;
							}
						}
						if(!empty($options)){
							$options = array_filter($options);
							$options = implode("\n", $options);
						}

						$params = '';

						// save to jomsocial fields
						$fieldid = $model->storeFields($id,$name,$type,$fieldCode,$published,$ordering,$min,$max,$visible,$required,$searchable,$registration,$options,$params='');
						if($fieldid) 
							$a++;
					}
				}

				// save profile data
				$listData = $model->getProfileData('user',$lastId,$this->limit);
				$count = $model->getCountProfileData('user');
				if(!empty($listData)){
					foreach ($listData as $data) {
						$user_id = $data->uid;
						$field_id = $data->field_id;
						$value = $data->data;
						$id = $data->id;

						$value = str_replace('["', '', $value);
						$value = str_replace('"]', '', $value);
						$value = str_replace('","', ',', $value);

						$model->storeFieldValues($user_id,$field_id,$value,$id);
					}
					$this->__updateMigrateStatus($step,$id);
					$countLeft = $model->getCountProfileData('user',$id);
				}else{
					$countLeft = $count;
				}
				
				$model->removeEmptyValue();
				break;
			case 'friends':
				$lastId = $this->__getMigrateStatus($step);
				$list = $model->getFriends($lastId,$this->limit);
				$count = $model->getCountFriends($lastId);
				if(!empty($list)){
					foreach ($list as $row) {
						$id = $row->id;
						$from = $row->actor_id;
						$to = $row->target_id;
						$status = $row->state;
						$message = $row->message;

						$isSuccess = $model->storeFriends($from,$to,$status,$message);
						if($isSuccess){
							$isSuccess = $model->storeFriends($to,$from,$status,$message);
							$a++;
						}
					}
					$this->__updateMigrateStatus($step,$id);
					$countLeft = $model->getCountFriends($id);
				}else{
					$countLeft = $count;
				}
				
				break;
			case 'photos':
				$lastId = $this->__getMigrateStatus($step);
				if(!empty($lastId)){
					$listAlbum = $model->getAlbumsEasySocial();
					if(!empty($listAlbum)){
						foreach ($listAlbum as $album) {
							$id = $album->id;
							$photoId = $album->cover_id;
							$groupId = $eventId = 0;
							if($album->type=='group'){
								$groupId = $album->uid;
							}elseif($album->type=='event'){
								$eventId = $album->uid;
							}
							$creator = $album->user_id;
							$name = JText::_($album->title);
							$description = JText::_($album->caption);
							$permission = 10;
							$created = $album->created;
							
							// find type
							$type = '';
							if(strpos($album->title,'COVER') && $album->type=='user'){
								$type = 'profile.cover';
							}elseif(strpos($album->title,'AVATAR') && $album->type=='user'){
								$type = 'profile.avatar';
							}elseif(strpos($album->title,'COVER') && $album->type=='group'){
								$type = 'group.cover';
							}elseif(strpos($album->title,'AVATAR') && $album->type=='group'){
								$type = 'group.avatar';
							}elseif(strpos($album->title,'COVER') && $album->type=='event'){
								$type = 'event.cover';
							}elseif(strpos($album->title,'AVATAR') && $album->type=='event'){
								$type = 'event.avatar';
							}else{
								$type = $album->type;
							}
							
							// find path
							$path = '';
							switch ($type=='user') {
								case 'user':
									JFolder::create(JPATH_ROOT.'/images/photos/'.$creator);
									JFolder::create(JPATH_ROOT.'/images/photos/'.$creator.'/'.$album->id);
									$path = '/images/photos/'.$creator.'/'.$album->id;
									break;
								case 'group':
									JFolder::create(JPATH_ROOT.'/images/groupphotos/'.$groupId);
									$path = '/images/groupphotos/'.$groupId;
									break;
								case 'event':
									JFolder::create(JPATH_ROOT.'/images/eventphotos/'.$eventId);
									$path = '/images/eventphotos/'.$eventId;
									break;
								case 'profile.cover':
									JFolder::create(JPATH_ROOT.'/images/cover/profile/'.$creator);
									$path = '/images/cover/profile/'.$creator;
									break;
								case 'event.cover':
									JFolder::create(JPATH_ROOT.'/images/cover/event/'.$eventId);
									$path = '/images/cover/event/'.$eventId;
									break;
								case 'group.cover':
									JFolder::create(JPATH_ROOT.'/images/cover/group/'.$groupId);
									$path = '/images/cover/group/'.$groupId;
									break;
							}

							$hits = $album->hits;
							// params 
							// get total photos
							$params = array();
							$params['count'] = $model->getCountPhotosEasySocial($id);

							$params['lastupdated'] = date('Y-m-d h:i:s');
							$params = json_encode($params);
							$default = 0;
							$albumid = $id;
							$isSuccess = $model->storeAlbums($id,$photoId,$creator,$name,$description,$permission,$created,$path,$type,$groupId,$eventId,$hits,$default,$params);
						}
					}
				}
				

				$listPhotos = $model->getPhotos($lastId,$this->limit);
				$count = $model->getCountPhotos();
				if(!empty($listPhotos)){
					// migrate photos now
					foreach ($listPhotos as $photo) {
						$id = $photo->id;
						$albumid = $photo->album_id;
						$caption = 	$photo->caption;
						$published = $photo->state==1?1:0;
						$creator = $photo->user_id;
						$permissions = 10;


						$folderPhoto = 'images/photos/'.$creator;
						if(!JFile::exists($folderPhoto))
							JFolder::create($folderPhoto);

						$folderPhoto = 'images/photos/'.$creator.'/'.$albumid;
						if(!JFile::exists($folderPhoto))
							JFolder::create($folderPhoto);

						

						$imageLarge = $model->getPhotoDetail($id,'large');
						$imageThumb = $model->getPhotoDetail($id,'thumb');
						//http://localhost/jomsocial/media/com_easysocial/photos/9/16/2017-04-16-06-03-39-hdr_large.jpg
						$photoExt = explode('.', $imageLarge);
						$ext = $photoExt[count($photoExt)-1];

						if(!JFile::exists(JPATH_ROOT.'/'.$imageLarge)){
							$imageLargeOld = 'media/com_easysocial/photos'.$imageLarge;
							$imageThumbOld = 'media/com_easysocial/photos'.$imageThumb;
						}else{
							$imageLargeOld = $imageLarge;
							$imageThumbOld = $imageThumb;
						}
						

						$uniqid = uniqid();
						$image = $folderPhoto.'/'.$uniqid.'.'.$ext;
						$saveSucess = JFile::copy(JPATH_ROOT.'/'.$imageLargeOld,JPATH_ROOT.'/'.$image);
						if($saveSucess){
							// thumb now
							$thumbnail = $folderPhoto.'/thumb_'.$uniqid.'.'.$ext;
							JFile::copy(JPATH_ROOT.'/'.$imageLargeOld,JPATH_ROOT.'/'.$thumbnail);

							$original = $image;
							$created = $photo->created;
							$filesize = $photo->total_size;

							$isSuccess = $model->storeAlbumPhotos($id,$albumid,$caption,$published,$creator,$permissions,$image,$thumbnail,$original,$created,$filesize);
							if($isSuccess)
								$a++;
						}
					}
					$this->__updateMigrateStatus($step,$id);
					$countLeft = $model->getCountPhotos($id);
				}
				else{
					$countLeft = $count;
				}
				break;
			case 'videos':
				// migrate category first

				$listCategory = $model->getVideoCategories();
				foreach ($listCategory as $category) {
					$parent = 0;
					$id = $category->id;
					$name = $category->title;
					$description = $category->description;
					$published = $category->state;

					$model->storeVideosCategory($id,$parent,$name,$description,$published);
				}

				// migrate videos data
				$lastId = $this->__getMigrateStatus($step);
				$list = $model->getVideos($lastId,$this->limit);
				$count = $model->getCountVideos();
				if(!empty($list)){
					foreach ($list as $row) {
						$id = $row->id;
						$title = $row->title;
						$creator = $row->user_id;
				        
						// get video id from url
						if($row->source=='link'){
							try{
								$videoLib   = new CVideoLibrary();
								$provider = $videoLib->getProvider($row->path);
						        try {
						            $isValid = $provider->isValid();
						        } catch (Exception $e) {
						            continue;
						        }
						        if($isValid)
						        {
						            $video_id = $provider->getId();
						        }else{
						        	continue;
						        }
						        $type = $provider->getType();
						        $path = $row->path;
							}catch(exception $e){
								continue;
							}
							
						}else{
							$type = 'file';
							$videoPath = $row->path;

							// copy video
							$videoFolder = 'images/videos/'.$creator;
				        	if(!JFolder::exists(JPATH_ROOT.'/'.$videoFolder))
				        		JFolder::create(JPATH_ROOT.'/'.$videoFolder);

				        	$path = $videoFolder.'/'.uniqid().'.mp4';
				        	$video_id = '';
				        	JFile::copy(JPATH_ROOT.'/'.$videoPath,JPATH_ROOT.'/'.$path);
						}
						

				        $description = $row->description;
				        $eventid = $groupid = '';
				        if($row->type=='event'){
				        	$eventid  = $row->uid;
				        }elseif($row->type=='group'){
				        	$groupid  = $row->uid;
				        }
				        $creator_type = $row->type;	
				        $created = $row->created;
				        $permission = 10;
				        $category_id = $row->category_id;
				        $hits = $row->hits;
				        $featured = 0;
				        $duration = $row->duration;
				        $status = $row->state==1?'ready':'pending';

				        // copy thumb
				        $thumbPath = 	$row->thumbnail;
				        $thumbNew = 'images/videos/'.$creator;
				        if(!JFolder::exists(JPATH_ROOT.'/'.$thumbNew))
				        	JFolder::create(JPATH_ROOT.'/'.$thumbNew);
				        
				        if(!JFolder::exists(JPATH_ROOT.'/'.$thumbNew.'/thumb'))
				        	JFolder::create(JPATH_ROOT.'/'.$thumbNew.'/thumb');

				        $thumbNew = 'images/videos/'.$creator.'/thumb/'.uniqid().'.jpg';
				        
				        JFile::copy(JPATH_ROOT.'/'.$thumbPath, JPATH_ROOT.'/'.$thumbNew);
				        
				        
				        $params = '';

						$isSuccess = $model->storeVideos($id,$title,$type,$video_id,$description,$creator,$creator_type,$created,$permission,$category_id,$hits,$featured,$duration,$status,$thumbNew,$path,$groupid,$eventid,$params);

						if($isSuccess)
							$a++;
					}
					$this->__updateMigrateStatus($step,$id);
					$countLeft = $model->getCountVideos($lastId);
				}else{
					$countLeft = $count;
				}
				
				break;
			case 'groups':
				// migrate category first
				$listCategory = $model->getGroupCategories();
				
				foreach ($listCategory as $category) {
					$parent = 0;
					$id = $category->id;
					$name = $category->title;
					$description = $category->description;
					
					$model->storeGroupsCategory($id,$parent,$name,$description);
				}

				// get events 
				$lastId = $this->__getMigrateStatus($step);
				$list = $model->getGroups($lastId,$this->limit);
				$count = $model->getCountGroups();

				$a = 0;
				if(!empty($list)){
					foreach($list as $row){
						$id = $row->id;
						$categoryid = $row->category_id;
						$type = $row->creator_type;
						$name = $row->title;
						$summary = '';
						$description = $row->description;
						$ownerid = $row->creator_uid;
						$published = $row->state;
						
						$created = $row->created;
						$hits = $row->hits;
						$published = $row->state;
						$unlisted = $approval = 0;
						if($row->type==1){
							$approval = 0;
						}elseif($row->type==2){
							$approval = 1;
						}elseif($row->type==3){
							$approval = 1;
							$unlisted = 1;
						}


						// save param
						$paramArr = json_decode($row->params);
						$params = array();
						$params['photopermission'] = $params['videopermission'] = '';
						$params['photopermission'] = @$paramArr->photo->albums==true?'2':'-1';
						$params['videopermission'] = @$paramArr->videos==true?'2':'-1';
						$params = json_encode($params);

						// cover saving
						$cover = $model->getGroupCover($id);

						$oldPath = JPATH_ROOT.$cover;
						$coverPath = '/images/cover/'.uniqid().'.jpg';
						$newPath = JPATH_ROOT.$coverPath;
						
						// get the file type
						if(JFile::exists($oldPath)){
							if(JFile::copy($oldPath,$newPath)){
								$cover = $coverPath;
							}
						}

						$avatar = $thumb = $discusscount = '';
						$avatarList = $model->getGroupAvatar($id);
						if(!empty($avatarList)){
							$oldThumbAvatar = JPATH_ROOT.'/media/com_easysocial/avatars/group/'.$id.'/'.$avatarList[0]->medium;
							$oldLargeAvatar = JPATH_ROOT.'/media/com_easysocial/avatars/group/'.$id.'/'.$avatarList[0]->large;

							$avatar = '/images/avatar/'.uniqid().'.jpg';
							$thumb = '/images/avatar/'.uniqid().'_thumb.jpg';
								

							if(JFile::exists($oldThumbAvatar)){
								JFile::copy($oldThumbAvatar,JPATH_ROOT.$thumb);
							}

							if(JFile::exists($oldLargeAvatar)){
								JFile::copy($oldLargeAvatar,JPATH_ROOT.$avatar);
							}
						}
						
						// now migrate the members
						// 1 goind, 2 cant go, 3 maybe
						$listMembers = $model->getEventMembers($id);
						$membercount = $discusscount = 0;
						if(!empty($listMembers)){
							foreach ($listMembers as $member) {
								if($member->state!=4){
									$membercount++;
									$approved = 1;
								}else{
									$approved = 0;
								}

								$groupid = $id;
								$memberid = $member->uid;

								if($memberid==$ownerid)
									$permissionGroup = 1;
								else
									$permissionGroup = 3;
								$model->storeGroupMembers($groupid,$memberid,$approved,$permissionGroup);
							}
						}

						// migrate discussion 
						$listDiscussions = $model->getGroupDiscussion($id);
						if(!empty($listDiscussions)){
							foreach ($listDiscussions as $discussion) {
								$idDiscussion = $discussion->id;
								$groupid = $discussion->uid;
								$parentid = $discussion->parent_id;
								$creator = $discussion->created_by;
								$title = $discussion->title;
								$message = $discussion->content;
								$lastreplied = $discussion->last_replied;
								$lock = $discussion->lock;

	 							$model->storeGroupDiscussions($idDiscussion,$parentid,$groupid,$creator,$created,$title,$message,$lastreplied,$lock,'');
								$discusscount++;
							}
						}

						// migrate bulletin
						$listBulletin = $model->getGroupBulletin($id);
						if(!empty($listBulletin)){
							foreach ($listBulletin as $bulletin) {
								$idBulletin = $bulletin->id;
								$groupid = $bulletin->cluster_id;
								$created_by = $bulletin->created_by;
								$title = $bulletin->title;
								$message = $bulletin->content;
								$published = $bulletin->state;
								$date = $bulletin->created;

	 							$model->storeGroupBulletin($idBulletin,$groupid,$created_by,$published,$title,$message,$date,$params='');
								$discusscount++;
							}
						}

						
						$isSuccess = $model->storeGroups($id,$published,$ownerid,$categoryid,$name,$description,$summary,$approval,$unlisted,$created,$avatar,$thumb,$cover,$discusscount,0,$membercount,$params);
						$a++;
					}
					$this->__updateMigrateStatus($step,$id);
					$countLeft = $model->getCountGroups($id);
				}
				else{
					$countLeft = $count;
				}
				break;
			case 'events':
				// migrate category first
				$listCategory = $model->getEventCategories();
				
				foreach ($listCategory as $category) {
					$parent = 0;
					$id = $category->id;
					$name = $category->title;
					$description = $category->description;
					
					$model->storeEventsCategory($id,$parent,$name,$description);
				}

				// get events 
				$lastId = $this->__getMigrateStatus($step);
				$list = $model->getEvents($lastId,$this->limit);
				$count = $model->getCountEvents();
				
				$a = 0;
				if(!empty($list)){
					foreach($list as $row){
						$id = $row->id;
						$catid = $row->category_id;
						$type = $row->creator_type;
						$title = $row->title;
						$location = $row->address;
						$summary = '';
						$description = $row->description;
						$creator = $row->creator_uid;
						$startdate = $row->start;
						if($row->end=='0000-00-00 00:00:00'){
							$dateOnly = date('Y-m-d',strtotime($row->start));
							$enddate = $dateOnly.' 23:59:59';
						}else{
							$enddate = $row->end;
						}
						
						$created = $row->created;
						$hits = $row->hits;
						$published = $row->state;
						$latitude = $row->latitude;
						$longitude = $row->longitude;
						$allday = $row->all_day;
						$unlisted = $permission = 0;
						if($row->type==1){
							$permission = 0;
						}elseif($row->type==2){
							$permission = 1;
						}elseif($row->type==3){
							$permission = 1;
							$unlisted = 1;
						}

						$contentid = $row->group_id;

						// save param
						$paramArr = json_decode($row->params);
						$params = array();
						$params['photopermission'] = $params['videopermission'] = '';
						$params['photopermission'] = @$paramArr->photo->albums==true?'2':'-1';
						$params['videopermission'] = @$paramArr->videos==true?'2':'-1';
						$params['timezone'] = '';
						$params = json_encode($params);

						// cover saving
						$cover = $model->getEventCover($id);

						$oldPath = JPATH_ROOT.$cover;
						$coverPath = '/images/cover/'.uniqid().'.jpg';
						$newPath = JPATH_ROOT.$coverPath;
						
						// get the file type
						if(JFile::exists($oldPath)){
							if(JFile::copy($oldPath,$newPath)){
								$cover = $coverPath;
							}
						}
						
						// now migrate the members
						// 1 goind, 2 cant go, 3 maybe
						$listMembers = $model->getEventMembers($id);
						$countGoing = $countCant = $countMaybe = 0;
						if(!empty($listMembers)){
							foreach ($listMembers as $member) {
								switch ($member->state) {
									case 1:
										$countGoing++;
										break;
									case 4:
										$countCant++;
										$status = 2;
										break;
									case 3:
										$countMaybe++;
										break;
								}

								$eventid = $id;
								$memberid = $member->uid;
								$status = $member->state;
								///$permission = 3;
								$approval = 0;
								$createdMember = $member->created;

								if($memberid==$creator)
									$permissionEvent = 1;
								else
									$permissionEvent = 3;

								$model->storeEventMembers($id,$eventid,$memberid,$status,$permissionEvent,$approval,$createdMember);
							}
						}

						$isSuccess = $model->storeEvents($id,$catid,$contentid,$type,$title,$permission,$unlisted,$location,$summary,$description,$creator,$startdate,$enddate,$cover,$created,$hits,$published,$latitude,$longitude,$allday,$params,$countGoing,$countCant,$countMaybe);
						$a++;
					}
					$this->__updateMigrateStatus($step,$id);
					$countLeft = $model->getCountEvents($id);
				}else{
					$countLeft = $count;
				}

				break;
		}
		
		$result['status'] = 'done';
		$result['count'] = $a;
		$result['countData'] = $count;
		$result['countLeft'] = $countLeft;
		echo json_encode($result);
		exit;	
		
	}

	public function cb(){
		$mainframe	= JFactory::getApplication();
		$jinput     = $mainframe->input;

		$step = $jinput->request->get('step', 'avatars');
		$model	= $this->getModel( 'MigratorCB' );
		$a=0;
		switch ($step) {
			case 'avatars':
				$lastId = $this->__getMigrateStatus($step);
				$list = $model->getAvatar($lastId,$this->limit);
				$count = $model->getCountAvatar();

				if(!empty($list)){
					foreach ($list as $row) {
						// copy the file 
						$oldPath = JPATH_ROOT.'/images/comprofiler/'.$row->avatar;
						$userId = $row->user_id;


						// check if this issue gallery  system from CB
						if(strpos($row->avatar, '/')){
							// skip this if user use default avatar
							//continue;
							$row->avatar = uniqid($row->user_id.'_').'.png';
						}

						$avatarPath = '/images/avatar/'.$row->avatar;
						$avatarThumbPath = '/images/avatar/thumb_'.$row->avatar;

						$newPath = JPATH_ROOT.$avatarPath;
						$newPathThumb = JPATH_ROOT.$avatarThumbPath;

						// get the file type
						if(JFile::exists($oldPath)){
							$info = getimagesize($oldPath);
	                        $destType = image_type_to_mime_type($info[2]);

	                        // copy to jomsocial folder
	                       $saveSucess = JFile::copy($oldPath,$newPath);
							if($saveSucess){
								$a++;
								CImageHelper::createThumb($newPath, $newPathThumb, $destType, 128, 128);

								// save to jomsocial table
								$model->storeAvatar($userId,$avatarPath,$newPathThumb);
							}
						}
					}
					$this->__updateMigrateStatus($step,$userId);
					$countLeft = $model->getCountAvatar($userId);
				}else{
					$countLeft = $count;
				}
				break;
			case 'covers':
				$lastId = $this->__getMigrateStatus($step);
				$list = $model->getCover($lastId,$this->limit);
				$count = $model->getCountCover();
				if(!empty($list)){
					foreach ($list as $row) {
						// copy the file 
						$oldPath = JPATH_ROOT.'/images/comprofiler/'.$row->canvas;
						$userId = $row->user_id;

						// check if this issue gallery  system from CB
						if(strpos($row->canvas, '/')){
							// skip this if user use default avatar
							//continue;
							$row->canvas = str_replace('gallery/', '', $row->canvas);
							$oldPath = JPATH_ROOT.'/images/comprofiler/gallery/canvas/'.$row->canvas;
							$row->canvas = 'canvas_'.uniqid($row->user_id.'_').'.png';
						}

						$coverPath = '/images/cover/'.$row->canvas;
						$newPath = JPATH_ROOT.$coverPath;
						
						// get the file type
						if(JFile::exists($oldPath)){
						    // copy to jomsocial folder
							$saveSucess = JFile::copy($oldPath,$newPath);
							if($saveSucess){
								$a++;
								
								// save to jomsocial table
								$model->storeCover($userId,$coverPath);
							}
						}
					}
					$this->__updateMigrateStatus($step,$userId);
					$countLeft = $model->getCountCover($userId);
				}else{
					$countLeft = $count;
				}
				break;
			case 'fields':
				$lastId = $this->__getMigrateStatus($step);
				$list = $model->getFields();
				
				$tablecolumns = '';
				foreach ($list as $row) {
					$id = $row->fieldid;
					$name = $row->title;
					$type = $row->type;
					$published = $row->type;
					$fieldCode = $row->tablecolumns;
					$published = $row->published;
					$ordering = $row->ordering;
					$visible = 1;
					$required = $row->required;
					$searchable = $row->searchable;
					$registration = $row->registration;

					$options = '';
					$tablecolumns[] = $row->tablecolumns;
					//$tablecolumns['type'] = $row->type;

					// search if value is available
					$optionsValueList = $model->getFieldsValueCB($id);
					if(!empty($optionsValueList)){
						foreach ($optionsValueList as $rowOptions) {
							$options[] = $rowOptions->fieldtitle;
						}
					}
					if(!empty($options)){
						$options = array_filter($options);
						$options = implode("\n", $options);
					}
					
					
					// params for Jomsocial
					$paramsCB['readonly'] = $row->readonly;
					$params = json_encode($paramsCB);

					// params from CB
					$min = $max = '';
					/*if(!empty($maxlength)){
						$params = json_decode($row->params);
						$min = isset($params->fieldMinLength)?$params->fieldMinLength:0;
						$max = isset($params->fieldMaxLength)?$params->fieldMaxLength:'';
					}*/
					

					// migrate based on type
					switch ($row->type) {
						case 'text':
							$type = 'text';
							break;
						case 'emailaddress':
							$type = 'email';
							break;
						case 'multicheckbox':
							$type = 'checkbox';
							break;
						case 'multiselect':
							$type = 'list';
							break;
						case 'datetime':
							$type = 'time';
							break;
						case 'editorta':
							$type = 'textarea';
							break;
						case 'integer':
							$type = 'text';
							break;
						case 'webaddress':
							$type = 'url';
							break;
						
					}

					// save to jomsocial fields
					$fieldid = $model->storeFields($id,$name,$type,$fieldCode,$published,$ordering,$min,$max,$visible,$required,$searchable,$registration,$options,$params='');
					if($fieldid) 
						$a++;
				}
				

				//  save to  jomsocial fields value record
				if(!empty($tablecolumns)){
					$listValues = $model->getProfile(implode(',', $tablecolumns),$lastId,$this->limit);
					$count = $model->getCountProfile();

					if(!empty($listValues)){
						foreach ($listValues as $row) {
							// get field id
							$userid = $row->user_id;

							foreach($tablecolumns as $colomn){
								$fieldid = $model->checkFields($colomn);
								$value = $row->$colomn;
								
								if(!empty($value) && $value!='0000-00-00 00:00:00'){
									$value = str_replace('|*|', ',', $value);
									$model->storeFieldValues($userid,$fieldid,$value);
								}
							}
						}
						$countLeft = $model->getCountProfile($userid);
						$this->__updateMigrateStatus($step,$userid);
					}else{
						$countLeft = $count;
					}
				}

				break;
			case 'friends':
				$lastId = $this->__getMigrateStatus($step);
				$count = $model->getCountFriends();
				$list = $model->getFriends($lastId,$this->limit);
				if(!empty($list)){
					foreach ($list as $row) {
						$connect_from = $row->referenceid;
						$connect_to = $row->memberid;
						$status = $row->accepted;
						$message = $row->reason;

						if($model->storeFriends($connect_from,$connect_to,$status,$message)){
							$a++;
						} 
						
					}
					$countLeft = $model->getCountFriends($lastId);
					$this->__updateMigrateStatus($step,$connect_from);
				}else{
					$countLeft = $count;
				}
				break;
		}
		
		$result['status'] = 'done';
		$result['count'] = $a;
		$result['countData'] = $count;
		$result['countLeft'] = $countLeft;
		echo json_encode($result);
		
		exit;
	}
}