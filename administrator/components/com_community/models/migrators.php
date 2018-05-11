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

jimport( 'joomla.application.component.model' );

class CommunityModelMigrators extends JModelLegacy
{
	var $db;

	public function __construct()
	{
		$mainframe	= JFactory::getApplication();
		$jinput     = $mainframe->input;
		$this->db = JFactory::getDBO();

		// Call the parents constructor
		parent::__construct();
	}

	/**
	* store record profile to Jomsocial table
	*
	*/
	public function storeFriends($from,$to,$status,$message){
		
		$date	= JDate::getInstance(); //get the time without any offset!

		$query		= 'SELECT * FROM ' . $this->db->quoteName( '#__community_connection' )
				.' WHERE  ' . $this->db->quoteName('connect_from').' = '.$this->db->Quote($from)
            	. ' AND '. $this->db->quoteName('connect_to').' = '.$this->db->Quote($to);

		$this->db->setQuery($query);
		$isExist = $this->db->loadObjectList();

		if(empty($isExist)){
			$query	= 'INSERT INTO '. $this->db->quoteName('#__community_connection')
	            .' SET ' . $this->db->quoteName('connect_from').' = '.$this->db->Quote($from)
	            . ', '. $this->db->quoteName('connect_to').' = '.$this->db->Quote($to)
	            . ', '. $this->db->quoteName('status').' = '. $this->db->Quote($status)
	            . ', '. $this->db->quoteName('msg').' = '. $this->db->Quote($message)
	            . ', '. $this->db->quoteName('created').' = ' . $this->db->Quote($date->toSql());

	        return $this->db->setQuery($query)->execute();
		}

		return true;
		
	}

	/**
	* store record avatar to Jomsocial table
	*
	*/
	public function storeAvatar($userid,$avatar,$thumb){
		$user = CFactory::getUser($userid);
		
		if($user->id){
			$query	= 'UPDATE  '. $this->db->quoteName('#__community_users')
	            .' SET ' . $this->db->quoteName('avatar').' = '.$this->db->Quote($avatar)
	            . ', '. $this->db->quoteName('thumb').' = '.$this->db->Quote($thumb)
	            . ' WHERE '. $this->db->quoteName('userid').' = '.$this->db->Quote($userid);
		}else{
			$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_users')
	            .' SET ' . $this->db->quoteName('avatar').' = '.$this->db->Quote($avatar)
	            . ', '. $this->db->quoteName('thumb').' = '.$this->db->Quote($thumb)
	            . ', '. $this->db->quoteName('userid').' = '.$this->db->Quote($userid);
		}
		

        return $this->db->setQuery($query)->execute();
	}

	/**
	* store record cover to Jomsocial table
	*
	*/
	public function storeCover($userid,$cover){
		$user = CFactory::getUser($userid);
		
		
		if($user->id){
			$query	= 'UPDATE  '. $this->db->quoteName('#__community_users')
	            .' SET ' . $this->db->quoteName('cover').' = '.$this->db->Quote($cover)
	            . ' WHERE '. $this->db->quoteName('userid').' = '.$this->db->Quote($userid);
		}else{
			$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_users')
	            .' SET ' . $this->db->quoteName('cover').' = '.$this->db->Quote($cover)
	            . ', '. $this->db->quoteName('userid').' = '.$this->db->Quote($userid);
		}

        return $this->db->setQuery($query)->execute();
	}

	public function checkMultiProfile($name,$description){
		
		$query		= 'SELECT id FROM ' . $this->db->quoteName( '#__community_profiles' )
				.' WHERE  ' . $this->db->quoteName('name').' = '.$this->db->Quote($name)
				.'AND ' . $this->db->quoteName('description').' = '.$this->db->Quote($description);

		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	/**
	* store multi profile to jomsocial table
	*
	*/
	public function storeMultiProfile($id,$name,$description,$published){
		
		$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_profiles')
            .' SET ' . $this->db->quoteName('id').' = '.$this->db->Quote($id)
            .', ' . $this->db->quoteName('name').' = '.$this->db->Quote($name)
            .', ' . $this->db->quoteName('description').' = '.$this->db->Quote($description)
            .',  ' . $this->db->quoteName('published').' = '.$this->db->Quote($published);

        $isSuccess = $this->db->setQuery($query)->execute();

        if($isSuccess)
        	return $this->checkMultiProfile($name,$description);
	}

	/**
	* store to multimembers jomsocial record
	*
	*/
	public function storeMembersMultiProfile($userid,$profile_id){
		$user = CFactory::getUser($userid);
		
		if($user->id){
			$query	= 'UPDATE  '. $this->db->quoteName('#__community_users')
	            .' SET ' . $this->db->quoteName('profile_id').' = '.$this->db->Quote($profile_id)
	            . ' WHERE '. $this->db->quoteName('userid').' = '.$this->db->Quote($userid);
		}else{
			$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_users')
	            .' SET ' . $this->db->quoteName('profile_id').' = '.$this->db->Quote($profile_id)
	            . ', '. $this->db->quoteName('userid').' = '.$this->db->Quote($userid);
		}
		

        return $this->db->setQuery($query)->execute();
	}

	public function checkFields($fieldCode){
		
		$query		= 'SELECT id FROM ' . $this->db->quoteName( '#__community_fields' )
				.' WHERE  ' . $this->db->quoteName('fieldcode').' = '.$this->db->Quote($fieldCode);

		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	/**
	* store profile fields to Jomsocial table
	*
	*/
	public function storeFields($id,$name,$type,$fieldCode,$published,$ordering,$min,$max,$visible,$required,$searchable,$registration,$options,$params=''){
		
		// check the current field is availabe
		/*$id = $this->checkFields($fieldCode);
		
		if(!empty($id))
			return $id;*/

		$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_fields')
            .' SET ' . $this->db->quoteName('type').' = '.$this->db->Quote($type)
            .', ' . $this->db->quoteName('id').' = '.$this->db->Quote($id)
            .', ' . $this->db->quoteName('fieldcode').' = '.$this->db->Quote($fieldCode)
            .',  ' . $this->db->quoteName('published').' = '.$this->db->Quote($published)
            .',  ' . $this->db->quoteName('ordering').' = '.$this->db->Quote($ordering)
            .',  ' . $this->db->quoteName('min').' = '.$this->db->Quote($min)
            .',  ' . $this->db->quoteName('max').' = '.$this->db->Quote($max)
            .',  ' . $this->db->quoteName('name').' = '.$this->db->Quote($name)
            .',  ' . $this->db->quoteName('visible').' = '.$this->db->Quote($visible)
            .',  ' . $this->db->quoteName('required').' = '.$this->db->Quote($required)
            .',  ' . $this->db->quoteName('registration').' = '.$this->db->Quote($registration)
            .',  ' . $this->db->quoteName('options').' = '.$this->db->Quote($options)
            .',  ' . $this->db->quoteName('params').' = '.$this->db->Quote($params);

        $isSuccess = $this->db->setQuery($query)->execute();
        //echo $query.'<hr>';
        if($isSuccess) 
        	return $this->checkFields($fieldCode);
	}

	public function storeProfileFields($parent,$field_id){
		$query = 'SELECT * FROM '.$this->db->quoteName('#__community_profiles_fields')
			.' WHERE  ' . $this->db->quoteName('parent').' = '.$this->db->Quote($parent)
			.'AND ' . $this->db->quoteName('field_id').' = '.$this->db->Quote($field_id);

		$this->db->setQuery($query);
		$isExist = $this->db->loadResult();
		if(!empty($isExist))
			return true;



		$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_profiles_fields')
            .'SET ' . $this->db->quoteName('parent').' = '.$this->db->Quote($parent)
            .', ' . $this->db->quoteName('field_id').' = '.$this->db->Quote($field_id);

        $isSuccess = $this->db->setQuery($query)->execute();
		
	}

	/**
	* store fields value to Jomsocial table
	*
	*/
	public function storeFieldValues($user_id,$field_id,$value,$id=''){
		if($id==''){
			$query		= 'SELECT id FROM ' . $this->db->quoteName( '#__community_fields_values' )
				.' WHERE  ' . $this->db->quoteName('field_id').' = '.$this->db->Quote($field_id)
				.' AND '. $this->db->quoteName('user_id').' = '.$this->db->Quote($user_id)
				.' AND '. $this->db->quoteName('value').' = '.$this->db->Quote($value);

			$isEmpty = $this->db->setQuery($query)->loadResult();
			
			if(!empty($isEmpty))
				return false;
		}

		$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_fields_values')
            .' SET  '. $this->db->quoteName('field_id').' = '.$this->db->Quote($field_id)
            .',   ' . $this->db->quoteName('value').' = '.$this->db->Quote($value)
            . ',  '. $this->db->quoteName('user_id').' = '.$this->db->Quote($user_id);

       	if($id){
       		$query .= ', '. $this->db->quoteName('id').' = '.$this->db->Quote($id);
       	}

        return $this->db->setQuery($query)->execute();
	}

	public function removeEmptyValue(){
		$query	= 'DELETE FROM  '. $this->db->quoteName('#__community_fields_values')
            .' WHERE   ' . $this->db->quoteName('value').' = "" ';

        return $this->db->setQuery($query)->execute();
	}

	public function checkAlbum($creator, $name){
		$query		= 'SELECT id FROM ' . $this->db->quoteName( '#__community_photos_albums' )
				.' WHERE  ' . $this->db->quoteName('creator').' = '.$this->db->Quote($creator)
				.' AND '. $this->db->quoteName('name').' = '.$this->db->Quote($name);

		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	/**
	* store albums record to Jomsocial table
	*
	*/
	public function storeAlbums($id,$photoId,$creator,$name,$description,$permissions,$created,$path,$type,$groupId,$eventId,$hits,$default,$params){
		/*$id = $this->checkAlbum($creator,$name);
		if($id)
			return $id;*/

		$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_photos_albums')
            .' SET ' . $this->db->quoteName('photoid').' = '.$this->db->Quote($photoId)
            .', ' . $this->db->quoteName('id').' = '.$this->db->Quote($id)
            .', ' . $this->db->quoteName('creator').' = '.$this->db->Quote($creator)
            .',  ' . $this->db->quoteName('name').' = '.$this->db->Quote($name)
            .',  ' . $this->db->quoteName('description').' = '.$this->db->Quote($description)
            .',  ' . $this->db->quoteName('permissions').' = '.$this->db->Quote($permissions)
            .',  ' . $this->db->quoteName('created').' = '.$this->db->Quote($created)
            .',  ' . $this->db->quoteName('path').' = '.$this->db->Quote($path)
            .',  ' . $this->db->quoteName('type').' = '.$this->db->Quote($type)
            .',  ' . $this->db->quoteName('groupid').' = '.$this->db->Quote($groupId)
            .',  ' . $this->db->quoteName('eventid').' = '.$this->db->Quote($eventId)
            .',  ' . $this->db->quoteName('hits').' = '.$this->db->Quote($hits)
            .',  ' . $this->db->quoteName('default').' = '.$this->db->Quote($default)
            .',  ' . $this->db->quoteName('params').' = '.$this->db->Quote($params);

        $isSuccess = $this->db->setQuery($query)->execute();
        //echo $query;
        if($isSuccess) 
        	return $this->checkAlbum($creator,$name);

	}

	/**
	* store photo album record to Jomsocial table
	*
	*/
	public function storeAlbumPhotos($id,$albumid,$caption,$published,$creator,$permissions,$image,$thumbnail,$original,$created,$filesize){
		
		$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_photos')
            .' SET ' . $this->db->quoteName('albumid').' = '.$this->db->Quote($albumid)
            .', ' . $this->db->quoteName('id').' = '.$this->db->Quote($id)
            .', ' . $this->db->quoteName('caption').' = '.$this->db->Quote($caption)
            .',  ' . $this->db->quoteName('published').' = '.$this->db->Quote($published)
            .',  ' . $this->db->quoteName('creator').' = '.$this->db->Quote($creator)
            .',  ' . $this->db->quoteName('permissions').' = '.$this->db->Quote($permissions)
            .',  ' . $this->db->quoteName('image').' = '.$this->db->Quote($image)
            .',  ' . $this->db->quoteName('thumbnail').' = '.$this->db->Quote($thumbnail)
            .',  ' . $this->db->quoteName('original').' = '.$this->db->Quote($original)
            .',  ' . $this->db->quoteName('created').' = '.$this->db->Quote($created)
            .',  ' . $this->db->quoteName('filesize').' = '.$this->db->Quote($filesize);

        $isSuccess = $this->db->setQuery($query)->execute();
        //echo $query;

        return $isSuccess;
	}		


	public function checkVideo($creator, $video_id){
		$query		= 'SELECT id FROM ' . $this->db->quoteName( '#__community_videos' )
				.' WHERE  ' . $this->db->quoteName('creator').' = '.$this->db->Quote($creator)
				.' AND '. $this->db->quoteName('video_id').' = '.$this->db->Quote($video_id);

		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	/**
	* store videos category record to Jomsocial table
	*
	*/
	public function storeVideosCategory($id,$parent,$name,$description,$published){
		$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_videos_category')
            .' SET ' . $this->db->quoteName('parent').' = '.$this->db->Quote($parent)
            .', ' . $this->db->quoteName('id').' = '.$this->db->Quote($id)
            .', ' . $this->db->quoteName('name').' = '.$this->db->Quote($name)
            .',  ' . $this->db->quoteName('description').' = '.$this->db->Quote($description)
            .',  ' . $this->db->quoteName('published').' = '.$this->db->Quote($published);

        $isSuccess = $this->db->setQuery($query)->execute();
        //echo $query;

        return $isSuccess;
	}

	/**
	* store videos record to Jomsocial table
	*
	*/
	public function storeVideos($id,$title,$type,$video_id,$description,$creator,$creator_type,$created,$permissions,$category_id,$hits,$featured,$duration,$status,$thumb,$path,$groupid,$eventid,$params){
		$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_videos')
            .' SET ' . $this->db->quoteName('id').' = '.$this->db->Quote($id)
            .', ' . $this->db->quoteName('title').' = '.$this->db->Quote($title)
            .', ' . $this->db->quoteName('type').' = '.$this->db->Quote($type)
            .',  ' . $this->db->quoteName('video_id').' = '.$this->db->Quote($video_id)
            .',  ' . $this->db->quoteName('description').' = '.$this->db->Quote($description)
            .',  ' . $this->db->quoteName('creator').' = '.$this->db->Quote($creator)
            .',  ' . $this->db->quoteName('creator_type').' = '.$this->db->Quote($creator_type)
            .',  ' . $this->db->quoteName('created').' = '.$this->db->Quote($created)
            .',  ' . $this->db->quoteName('permissions').' = '.$this->db->Quote($permissions)
            .',  ' . $this->db->quoteName('category_id').' = '.$this->db->Quote($category_id)
            .',  ' . $this->db->quoteName('hits').' = '.$this->db->Quote($hits)
            .',  ' . $this->db->quoteName('featured').' = '.$this->db->Quote($featured)
            .',  ' . $this->db->quoteName('status').' = '.$this->db->Quote($status)
            .',  ' . $this->db->quoteName('thumb').' = '.$this->db->Quote($thumb)
            .',  ' . $this->db->quoteName('path').' = '.$this->db->Quote($path)
            .',  ' . $this->db->quoteName('groupid').' = '.$this->db->Quote($groupid)
            .',  ' . $this->db->quoteName('eventid').' = '.$this->db->Quote($eventid)
            .',  ' . $this->db->quoteName('storage').' = '.$this->db->Quote('file')
            .',  ' . $this->db->quoteName('params').' = '.$this->db->Quote($params);

        $isSuccess = $this->db->setQuery($query)->execute();
        //echo $query;

        return $isSuccess;
	}	

	public function checkGroup($ownerid, $name){
		$query		= 'SELECT id FROM ' . $this->db->quoteName( '#__community_groups' )
				.' WHERE  ' . $this->db->quoteName('ownerid').' = '.$this->db->Quote($ownerid)
				.' AND '. $this->db->quoteName('name').' = '.$this->db->Quote($name);

		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	/**
	* store groups record to Jomsocial table
	*
	*/
	public function storeGroups($id,$published,$ownerid,$categoryid,$name,$description,$summary,$approvals,$unlisted,$created,$avatar,$thumb,$cover,$discusscount,$wallcount,$membercount,$params){
		
		$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_groups')
            .' SET ' . $this->db->quoteName('published').' = '.$this->db->Quote($published)
            .', ' . $this->db->quoteName('id').' = '.$this->db->Quote($id)
            .', ' . $this->db->quoteName('ownerid').' = '.$this->db->Quote($ownerid)
            .',  ' . $this->db->quoteName('categoryid').' = '.$this->db->Quote($categoryid)
            .',  ' . $this->db->quoteName('name').' = '.$this->db->Quote($name)
            .',  ' . $this->db->quoteName('description').' = '.$this->db->Quote($description)
            .',  ' . $this->db->quoteName('summary').' = '.$this->db->Quote($summary)
            .',  ' . $this->db->quoteName('approvals').' = '.$this->db->Quote($approvals)
            .',  ' . $this->db->quoteName('unlisted').' = '.$this->db->Quote($unlisted)
            .',  ' . $this->db->quoteName('created').' = '.$this->db->Quote($created)
            .',  ' . $this->db->quoteName('avatar').' = '.$this->db->Quote($avatar)
            .',  ' . $this->db->quoteName('thumb').' = '.$this->db->Quote($thumb)
            .',  ' . $this->db->quoteName('cover').' = '.$this->db->Quote($cover)
            .',  ' . $this->db->quoteName('discusscount').' = '.$this->db->Quote($discusscount)
            .',  ' . $this->db->quoteName('wallcount').' = '.$this->db->Quote($wallcount)
            .',  ' . $this->db->quoteName('membercount').' = '.$this->db->Quote($membercount)
            .',  ' . $this->db->quoteName('params').' = '.$this->db->Quote($params);

        $isSuccess = $this->db->setQuery($query)->execute();
        //echo $query;

        return $isSuccess;
	}	

	public function checkGroupsCategory($parent, $name){
		$query		= 'SELECT id FROM ' . $this->db->quoteName( '#__community_groups_category' )
				.' WHERE  ' . $this->db->quoteName('parent').' = '.$this->db->Quote($parent)
				.' AND '. $this->db->quoteName('name').' = '.$this->db->Quote($name);

		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	/**
	* store groups category record to Jomsocial table
	*
	*/
	public function storeGroupsCategory($id,$parent,$name,$description){
		
		$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_groups_category')
            .' SET ' . $this->db->quoteName('parent').' = '.$this->db->Quote($parent)
            .', ' . $this->db->quoteName('id').' = '.$this->db->Quote($id)
            .', ' . $this->db->quoteName('name').' = '.$this->db->Quote($name)
            .',  ' . $this->db->quoteName('description').' = '.$this->db->Quote($description);

        $isSuccess = $this->db->setQuery($query)->execute();
        //echo $query;

        return $isSuccess;
	}	

	public function checkGroupMembers($groupid,$memberid){
		$query = 'SELECT * FROM '.$this->db->quoteName('#__community_groups_members')
			.' WHERE  ' . $this->db->quoteName('groupid').' = '.$this->db->Quote($groupid)
			.'AND ' . $this->db->quoteName('memberid').' = '.$this->db->Quote($memberid);

		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	public function storeGroupMembers($groupid,$memberid,$approved,$permissions){
		if(!empty($this->checkGroupMembers($groupid,$memberid))){
			return true;
		}

		$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_groups_members')
            .' SET ' . $this->db->quoteName('groupid').' = '.$this->db->Quote($groupid)
            .', ' . $this->db->quoteName('memberid').' = '.$this->db->Quote($memberid)
            .',  ' . $this->db->quoteName('approved').' = '.$this->db->Quote($approved)
            .',  ' . $this->db->quoteName('permissions').' = '.$this->db->Quote($permissions);

        $isSuccess = $this->db->setQuery($query)->execute();
        //echo $query;

        return $isSuccess;
	}

	public function storeGroupDiscussions($id,$parentid,$groupid,$creator,$created,$title,$message,$lastreplied,$lock,$params=''){
		$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_groups_discuss')
            .' SET ' . $this->db->quoteName('id').' = '.$this->db->Quote($id)
            .', ' . $this->db->quoteName('parentid').' = '.$this->db->Quote($parentid)
            .', ' . $this->db->quoteName('groupid').' = '.$this->db->Quote($groupid)
            .',  ' . $this->db->quoteName('creator').' = '.$this->db->Quote($creator)
            .',  ' . $this->db->quoteName('created').' = '.$this->db->Quote($created)

            .',  ' . $this->db->quoteName('title').' = '.$this->db->Quote($title)
            .',  ' . $this->db->quoteName('message').' = '.$this->db->Quote($message)
            .',  ' . $this->db->quoteName('lastreplied').' = '.$this->db->Quote($lastreplied)
            .',  ' . $this->db->quoteName('lock').' = '.$this->db->Quote($lock);

        $isSuccess = $this->db->setQuery($query)->execute();
        //echo $query;

        return $isSuccess;
	}

	public function storeGroupBulletin($id,$groupid,$created_by,$published,$title,$message,$date,$params=''){
		$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_groups_bulletins')
            .' SET ' . $this->db->quoteName('id').' = '.$this->db->Quote($id)
            .', ' . $this->db->quoteName('groupid').' = '.$this->db->Quote($groupid)
            .', ' . $this->db->quoteName('created_by').' = '.$this->db->Quote($created_by)
            .',  ' . $this->db->quoteName('published').' = '.$this->db->Quote($published)
            .',  ' . $this->db->quoteName('title').' = '.$this->db->Quote($title)

            .',  ' . $this->db->quoteName('message').' = '.$this->db->Quote($message)
            .',  ' . $this->db->quoteName('date').' = '.$this->db->Quote($date);

        $isSuccess = $this->db->setQuery($query)->execute();
        //echo $query;

        return $isSuccess;
	}

	public function checkEventCategory($parent, $name){
		$query		= 'SELECT id FROM ' . $this->db->quoteName( '#__community_events_category' )
				.' WHERE  ' . $this->db->quoteName('parent').' = '.$this->db->Quote($parent)
				.' AND '. $this->db->quoteName('name').' = '.$this->db->Quote($name);

		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	/**
	* store groups category record to Jomsocial table
	*
	*/
	public function storeEventsCategory($id,$parent,$name,$description){
		$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_events_category')
            .' SET ' . $this->db->quoteName('parent').' = '.$this->db->Quote($parent)
            .', ' . $this->db->quoteName('id').' = '.$this->db->Quote($id)
            .', ' . $this->db->quoteName('name').' = '.$this->db->Quote($name)
            .',  ' . $this->db->quoteName('description').' = '.$this->db->Quote($description);

        $isSuccess = $this->db->setQuery($query)->execute();
        //echo $query;

        return $isSuccess;
	}

	/**
	* store events 
	*
	*/
	public function storeEvents($id,$catid,$contentid,$type,$title,$permission,$unlisted,$location,$summary,$description,$creator,$startdate,$enddate,$cover,$created,$hits,$published,$latitude,$longitude,$allday,$params,$confirmedcount,$declinedcount,$maybecount){
		$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_events')
            .' SET ' . $this->db->quoteName('id').' = '.$this->db->Quote($id)
            .', ' . $this->db->quoteName('catid').' = '.$this->db->Quote($catid)
            .', ' . $this->db->quoteName('type').' = '.$this->db->Quote($type)
            .',  ' . $this->db->quoteName('title').' = '.$this->db->Quote($title)
            .',  ' . $this->db->quoteName('permission').' = '.$this->db->Quote($permission)
            .',  ' . $this->db->quoteName('unlisted').' = '.$this->db->Quote($unlisted)
            .',  ' . $this->db->quoteName('creator').' = '.$this->db->Quote($creator)
            .', '. $this->db->quoteName('contentid').' = '.$this->db->Quote($contentid)

            .',  ' . $this->db->quoteName('location').' = '.$this->db->Quote($location)
            .',  ' . $this->db->quoteName('summary').' = '.$this->db->Quote($summary)
            .',  ' . $this->db->quoteName('description').' = '.$this->db->Quote($description)
            .',  ' . $this->db->quoteName('startdate').' = '.$this->db->Quote($startdate)
            .',  ' . $this->db->quoteName('enddate').' = '.$this->db->Quote($enddate)

            .',  ' . $this->db->quoteName('cover').' = '.$this->db->Quote($cover)
            .',  ' . $this->db->quoteName('created').' = '.$this->db->Quote($created)
            .',  ' . $this->db->quoteName('hits').' = '.$this->db->Quote($hits)
            .',  ' . $this->db->quoteName('published').' = '.$this->db->Quote($published)
            .',  ' . $this->db->quoteName('latitude').' = '.$this->db->Quote($latitude)
            .',  ' . $this->db->quoteName('longitude').' = '.$this->db->Quote($longitude)

            .',  ' . $this->db->quoteName('allday').' = '.$this->db->Quote($allday)
            .',  ' . $this->db->quoteName('params').' = '.$this->db->Quote($params)
            .',  ' . $this->db->quoteName('confirmedcount').' = '.$this->db->Quote($confirmedcount)
            .',  ' . $this->db->quoteName('declinedcount').' = '.$this->db->Quote($declinedcount)
            .',  ' . $this->db->quoteName('maybecount').' = '.$this->db->Quote($maybecount);

        $isSuccess = $this->db->setQuery($query)->execute();
        //echo $query;

        return $isSuccess;
	}

	public function checkEventMembers($eventid,$memberid){
		$query = 'SELECT * FROM '.$this->db->quoteName('#__community_events_members')
			.' WHERE  ' . $this->db->quoteName('eventid').' = '.$this->db->Quote($eventid)
			.'AND ' . $this->db->quoteName('memberid').' = '.$this->db->Quote($memberid);

		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	public function storeEventMembers($id,$eventid,$memberid,$status,$permission,$approval,$created){
		if(!empty($this->checkEventMembers($eventid,$memberid))){
			return true;
		}

		$query	= 'REPLACE INTO '. $this->db->quoteName('#__community_events_members')
            .' SET ' . $this->db->quoteName('eventid').' = '.$this->db->Quote($eventid)
            .', ' . $this->db->quoteName('id').' = '.$this->db->Quote($id)
            .', ' . $this->db->quoteName('memberid').' = '.$this->db->Quote($memberid)
            .',  ' . $this->db->quoteName('status').' = '.$this->db->Quote($status)
            .',  ' . $this->db->quoteName('permission').' = '.$this->db->Quote($permission)
            .',  ' . $this->db->quoteName('approval').' = '.$this->db->Quote($approval)
            .',  ' . $this->db->quoteName('created').' = '.$this->db->Quote($created)
            ;

        $isSuccess = $this->db->setQuery($query)->execute();
        

        return $isSuccess;
	}
	
}