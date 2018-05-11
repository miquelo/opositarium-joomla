<?php
/**
* @copyright (C) 2017 JoomlArt, Inc. - All rights reserved.
* @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
* @author iJoomla.com <contact@joomlart.com>
* @url https://www.jomsocial.com/license-agreement
* The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
* More info at https://www.jomsocial.com/license-agreement
*/
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );
require_once 'migrators.php';


class CommunityModelMigratorEasySocial extends CommunityModelMigrators
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

	public function checkTableExistEasySocial(){
		try{
			$this->getMultiProfile();
			return true;
		}catch(exception $e){
			return false;
		}
	}

	/**
	* get avatar from EasySocial
	*/
	public function getAvatar($lastid=0,$limit=10){
		$query		= 'SELECT '.$this->db->quoteName( 'id' ).','.$this->db->quoteName( 'uid' ).', '.$this->db->quoteName( 'square' ).' as avatar FROM ' . $this->db->quoteName( '#__social_avatars' ) .' as a ';
		$query .= ' WHERE '.$this->db->quoteName('id').'>'.$lastid;
		$query .= ' ORDER BY id ASC ';
		$query .= ' LIMIT '.$limit;
		
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function getCountAvatar($lastid=0){
		$query		= 'SELECT count(*) as count FROM ' . $this->db->quoteName( '#__social_avatars' ) .' as a ';
		if($lastid>0){
			$query .= ' WHERE '.$this->db->quoteName('id').'<='.$lastid;
		}
		

		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	/**
	* get cover from EasySocial
	*/
	public function getCover($lastid=0,$limit=10){
		$query		= 'SELECT '.$this->db->quoteName( 'a.id' ).','.$this->db->quoteName( 'a.uid' ).', '.$this->db->quoteName( 'b.value' ).' as cover FROM ' . $this->db->quoteName( '#__social_covers' ) .' as a INNER JOIN  '. $this->db->quoteName( '#__social_photos_meta' ) .' as b '
			.' ON a.photo_id=b.photo_id WHERE '.$this->db->quoteName('property').'='.$this->db->Quote('large');
		$query .= ' AND '.$this->db->quoteName('a.id').'>'.$lastid;
		$query .= ' ORDER BY a.id ASC ';
		$query .= ' LIMIT '.$limit;
		
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function getCountCover($lastid=0){
		$query		= 'SELECT count(*) as count FROM ' . $this->db->quoteName( '#__social_covers' ) .' as a INNER JOIN  '. $this->db->quoteName( '#__social_photos_meta' ) .' as b '
			.' ON a.photo_id=b.photo_id WHERE '.$this->db->quoteName('property').'='.$this->db->Quote('large');
		if($lastid>0){
			$query .= ' AND '.$this->db->quoteName('a.id').'<='.$lastid;
		}
		
		$query .= ' ORDER BY a.id ASC ';
		
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	public function getMultiProfile(){
		$query		= 'SELECT '.$this->db->quoteName( 'id' ).','.$this->db->quoteName( 'title' ).', '.$this->db->quoteName( 'description' ).', '.$this->db->quoteName( 'state' ).' FROM ' . $this->db->quoteName( '#__social_profiles' );
		//echo $query;	
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function getMembersMultiProfile($lastid=0,$limit=10){
		$query		= 'SELECT '.$this->db->quoteName( 'user_id' ).','.$this->db->quoteName( 'profile_id' ).' FROM ' . $this->db->quoteName( '#__social_profiles_maps' );
		$query .= ' WHERE '.$this->db->quoteName('user_id').'>'.$lastid;
		$query .= ' ORDER BY user_id ASC ';
		$query .= ' LIMIT '.$limit;
		
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function getCountMembersMultiProfile($lastid=0){
		$query		= 'SELECT count(*) FROM ' . $this->db->quoteName( '#__social_profiles_maps' );

		if($lastid>0){
			$query .= ' WHERE '.$this->db->quoteName('user_id').'<='.$lastid;
		}
		
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	/**
	* get fields from easysocial
	*
	*/
	public function getFieldsEasySocial(){
		$query = 'SELECT a.id,a.unique_key,a.ordering,a.visible_registration,a.visible_display,a.searchable,a.state,a.required,  a.title,b.type,b.element,a.visible_registration'.
				' FROM ' . $this->db->quoteName( '#__social_fields' ). ' as a '.
				' INNER JOIN ' . $this->db->quoteName( '#__social_apps' ). ' as b ON a.app_id=b.id '.
				' WHERE ' . $this->db->quoteName( 'b.group' ).' = ' . $this->db->Quote( 'user' ).
				' AND ' . $this->db->quoteName( 'b.type' ).' = ' . $this->db->Quote( 'fields' ).
				' AND ' . $this->db->quoteName( 'a.unique_key' ).' NOT LIKE ' . $this->db->Quote( '%JOOMLA%' ).
				' AND ( ' . $this->db->quoteName( 'b.element' ).' = ' . $this->db->Quote( 'address' ).
				' OR ' . $this->db->quoteName( 'b.element' ).' = ' . $this->db->Quote( 'birthday' ).
				' OR ' . $this->db->quoteName( 'b.element' ).' = ' . $this->db->Quote( 'checkbox' ).
				' OR ' . $this->db->quoteName( 'b.element' ).' = ' . $this->db->Quote( 'country' ).
				' OR ' . $this->db->quoteName( 'b.element' ).' = ' . $this->db->Quote( 'datetime' ).

				' OR ' . $this->db->quoteName( 'b.element' ).' = ' . $this->db->Quote( 'dropdown' ).
				' OR ' . $this->db->quoteName( 'b.element' ).' = ' . $this->db->Quote( 'multidropdown' ).
				' OR ' . $this->db->quoteName( 'b.element' ).' = ' . $this->db->Quote( 'multilist' ).
				' OR ' . $this->db->quoteName( 'b.element' ).' = ' . $this->db->Quote( 'autocomplete' ).
				' OR ' . $this->db->quoteName( 'b.element' ).' = ' . $this->db->Quote( 'permalink' ).

				' OR ' . $this->db->quoteName( 'b.element' ).' = ' . $this->db->Quote( 'text' ).
				' OR ' . $this->db->quoteName( 'b.element' ).' = ' . $this->db->Quote( 'textarea' ).
				' OR ' . $this->db->quoteName( 'b.element' ).' = ' . $this->db->Quote( 'textbox' ).
				' OR ' . $this->db->quoteName( 'b.element' ).' = ' . $this->db->Quote( 'url' ).
				' OR ' . $this->db->quoteName( 'b.element' ).' = ' . $this->db->Quote( 'email' ).

				') ORDER BY a.ordering ASC';
			
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	/**
	* get fields options from easysocial
	*
	*/
	public function getFieldsOptions($field_id){
		$query		= 'SELECT '.$this->db->quoteName( 'title' ).' FROM ' . $this->db->quoteName( '#__social_fields_options' ) .' WHERE '.$this->db->quoteName( 'parent_id' ).'='.$this->db->Quote( $field_id );
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	//**
	/* get mapping from profile with fields
	/*
	*/
	public function getFieldProfileType(){
		$query = 'SELECT c.uid,a.id as field_id FROM '.$this->db->quoteName('#__social_fields').' as a LEFT JOIN '.$this->db->quoteName('#__social_apps').' as b ON b.id=a.app_id ';
		$query .=  ' LEFT JOIN '.$this->db->quoteName('#__social_fields_steps').' as c ON a.step_id=c.id';

		$this->db->setQuery($query);
		return $this->db->loadObjectList();

	}
	/**
	* get profile date 
	*
	*/
	public function getProfileData($type,$lastid=0,$limit=0){
		$query		= 'SELECT '.$this->db->quoteName( 'id' ).','.$this->db->quoteName( 'field_id' ).','.$this->db->quoteName( 'uid' ).','.$this->db->quoteName( 'data' ).' FROM ' . $this->db->quoteName( '#__social_fields_data' ) .' WHERE '.$this->db->quoteName( 'type' ).'='.$this->db->Quote($type)
			.' AND '.$this->db->quoteName( 'datakey' ).'!='.$this->db->Quote('timezone');
		$query .= ' AND '.$this->db->quoteName('id').'>'.$lastid;
		$query .= ' ORDER BY id ASC ';
		$query .= ' LIMIT '.$limit;
		
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
		
	}

	public function getCountProfileData($type,$lastid=0){
		$query		= 'SELECT '.$this->db->quoteName( 'id' ).','.$this->db->quoteName( 'field_id' ).','.$this->db->quoteName( 'uid' ).','.$this->db->quoteName( 'data' ).' FROM ' . $this->db->quoteName( '#__social_fields_data' ) .' WHERE '.$this->db->quoteName( 'type' ).'='.$this->db->Quote($type)
			.' AND '.$this->db->quoteName( 'datakey' ).'!='.$this->db->Quote('timezone');

		if($lastid>0){
			$query .= ' AND '.$this->db->quoteName('id').'<='.$lastid;
		}
		
		
		$this->db->setQuery($query);
		return $this->db->loadResult();
		
	}

	public function getFriends($lastid=0,$limit=0){
		$query		= 'SELECT * FROM ' . $this->db->quoteName( '#__social_friends' );
		$query .= ' WHERE '.$this->db->quoteName('id').'>'.$lastid;
		$query .= ' ORDER BY id ASC ';
		$query .= ' LIMIT '.$limit;
		
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function getCountFriends($lastid=0){
		$query		= 'SELECT count(*) FROM ' . $this->db->quoteName( '#__social_friends' );
		if($lastid>0){
			$query .= ' WHERE '.$this->db->quoteName('id').'<='.$lastid;
		}
		
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	

	public function getCountPhotosEasySocial($album_id){
		$query		= 'SELECT count(*) as count FROM ' . $this->db->quoteName( '#__social_photos' )
					.' WHERE '.$this->db->quoteName('album_id').'='.$this->db->Quote($album_id);
		
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	public function getVideoCategories(){
		$query		= 'SELECT * FROM ' . $this->db->quoteName( '#__social_videos_categories' );
	
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
		
	}

	public function getVideos($lastid=0,$limit=0){
		$query		= 'SELECT * FROM ' . $this->db->quoteName( '#__social_videos' );
		$query .= ' WHERE '.$this->db->quoteName('id').'>'.$lastid;
		$query .= ' ORDER BY id ASC ';
		$query .= ' LIMIT '.$limit;
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function getCountVideos($lastid=0){
		$query		= 'SELECT * FROM ' . $this->db->quoteName( '#__social_videos' );
		if($lastid>0){
			$query .= ' WHERE '.$this->db->quoteName('id').'<='.$lastid;
		}
	
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	public function getAlbumsEasySocial(){
		$query		= 'SELECT * FROM ' . $this->db->quoteName( '#__social_albums' );
	
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function getPhotos($lastid=0,$limit=0){
		$query		= 'SELECT * FROM ' . $this->db->quoteName( '#__social_photos' );
		$query .= ' WHERE '.$this->db->quoteName('id').'>'.$lastid;
		$query .= ' ORDER BY id ASC ';
		$query .= ' LIMIT '.$limit;
	
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function getCountPhotos($lastid=0){
		$query		= 'SELECT count(*) FROM ' . $this->db->quoteName( '#__social_photos' );
		
		if($lastid>0){
			$query .= ' WHERE '.$this->db->quoteName('id').'<='.$lastid;
		}

		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	public function getPhotoDetail($photo_id,$property){
		$query		= 'SELECT value FROM ' . $this->db->quoteName( '#__social_photos_meta' )
					.' WHERE  '.$this->db->quoteName('photo_id').'='.$this->db->Quote($photo_id)
					.' AND '.$this->db->quoteName('property').'='.$this->db->Quote($property)
					. ' AND '.$this->db->quoteName('group').'='.$this->db->Quote('path');
	
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	/**
	* get event categories
	*
	*/
	public function getEventCategories(){
		$query		= 'SELECT * FROM ' . $this->db->quoteName( '#__social_clusters_categories' ). 
					' WHERE '.$this->db->quoteName('type').'='.$this->db->Quote('event');
	
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	/**
	* get event from easysocial
	*
	*/
	public function getEvents($lastid=0,$limit=0){
		$query = 'SELECT a.id,a.category_id,a.creator_type,a.type,a.creator_uid,a.title,a.description,a.state, a.created, a.hits,a.longitude,a.latitude,a.address,b.start,b.end,b.group_id,b.all_day,b.timezone,a.params FROM '.$this->db->quoteName('#__social_clusters') .' as a '
				.' INNER JOIN '.$this->db->quoteName('#__social_events_meta') .' as b ON a.id=b.cluster_id '
				.' WHERE '.$this->db->quoteName('cluster_type').'='.$this->db->Quote('event');
		$query .= ' AND '.$this->db->quoteName('a.id').'>'.$lastid;
		$query .= ' ORDER BY a.id ASC ';
		$query .= ' LIMIT '.$limit;

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function getCountEvents($lastid=0){
		$query = 'SELECT count(*) FROM '.$this->db->quoteName('#__social_clusters') .' as a '
				.' INNER JOIN '.$this->db->quoteName('#__social_events_meta') .' as b ON a.id=b.cluster_id '
				.' WHERE '.$this->db->quoteName('cluster_type').'='.$this->db->Quote('event');

		if($lastid>0){
			$query .= ' AND '.$this->db->quoteName('a.id').'<='.$lastid;
		}

		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	public function getEventCover($uid){
		$query		= 'SELECT '.$this->db->quoteName( 'b.value' ).' as cover FROM ' . $this->db->quoteName( '#__social_covers' ) .' as a INNER JOIN  '. $this->db->quoteName( '#__social_photos_meta' ) .' as b '
			.' ON a.photo_id=b.photo_id WHERE '.$this->db->quoteName('property').'='.$this->db->Quote('large')
			.' AND '.$this->db->quoteName('uid').'='.$this->db->Quote($uid);
		
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	public function getEventMembers($id){
		$query = 'SELECT * FROM '.$this->db->quoteName('#__social_clusters_nodes').' WHERE '.$this->db->quoteName('cluster_id').'='.$this->db->Quote($id);

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}


	/**
	* get group categories
	*
	*/
	public function getGroupCategories(){
		$query		= 'SELECT * FROM ' . $this->db->quoteName( '#__social_clusters_categories' ). 
					' WHERE '.$this->db->quoteName('type').'='.$this->db->Quote('group');
	
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	/**
	* get group from easysocial
	*
	*/
	public function getGroups($lastid=0,$limit=0){
		$query = 'SELECT a.id,a.category_id,a.creator_type,a.type,a.creator_uid,a.title,a.description,a.state, a.created, a.hits,a.longitude,a.latitude,a.address,a.params FROM '.$this->db->quoteName('#__social_clusters') .' as a '
				.' WHERE '.$this->db->quoteName('cluster_type').'='.$this->db->Quote('group');
		$query .= ' AND '.$this->db->quoteName('a.id').'>'.$lastid;
		$query .= ' ORDER BY a.id ASC ';
		$query .= ' LIMIT '.$limit;

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function getCountGroups($lastid=0){
		$query = 'SELECT count(*) FROM '.$this->db->quoteName('#__social_clusters') .' as a '
				.' WHERE '.$this->db->quoteName('cluster_type').'='.$this->db->Quote('group');
		if($lastid>0){
			$query .= ' AND '.$this->db->quoteName('a.id').'<='.$lastid;
		}
		//echo $query;
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	public function getGroupCover($uid){
		$query		= 'SELECT '.$this->db->quoteName( 'b.value' ).' as cover FROM ' . $this->db->quoteName( '#__social_covers' ) .' as a INNER JOIN  '. $this->db->quoteName( '#__social_photos_meta' ) .' as b '
			.' ON a.photo_id=b.photo_id WHERE '.$this->db->quoteName('property').'='.$this->db->Quote('large')
			.' AND '.$this->db->quoteName('uid').'='.$this->db->Quote($uid);
		
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	public function getGroupAvatar($uid){
		$query		= 'SELECT * FROM ' . $this->db->quoteName( '#__social_avatars' ) 
			.' WHERE '.$this->db->quoteName('uid').'='.$this->db->Quote($uid)
			.' AND '.$this->db->quoteName('type').'='.$this->db->Quote('group');
		
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function getGroupMembers($id){
		$query = 'SELECT * FROM '.$this->db->quoteName('#__social_clusters_nodes').' WHERE '.$this->db->quoteName('cluster_id').'='.$this->db->Quote($id);

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function getGroupDiscussion($groupid){
		$query = 'SELECT * FROM '.$this->db->quoteName('#__social_discussions')
				.' WHERE '.$this->db->quoteName('uid').'='.$this->db->Quote($groupid)
				.' AND '.$this->db->quoteName('type').'='.$this->db->Quote('group');

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function getGroupBulletin($groupid){
		$query = 'SELECT * FROM '.$this->db->quoteName('#__social_clusters_news')
				.' WHERE '.$this->db->quoteName('cluster_id').'='.$this->db->Quote($groupid);

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
		
	}
	
}