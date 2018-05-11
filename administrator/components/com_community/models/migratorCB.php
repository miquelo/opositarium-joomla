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
require_once 'migrators.php';

class CommunityModelMigratorCB extends CommunityModelMigrators
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

	public function checkTableExistCB(){
		try{
			$this->getFields();
			return true;
		}catch(exception $e){
			return false;
		}
	}


	/**
	* get avatar from CB
	*
	*/
	public function getAvatar($lastid=0,$limit=0){
		
		$query		= 'SELECT '.$this->db->quoteName( 'user_id' ).', '.$this->db->quoteName( 'avatar' ).' FROM ' . $this->db->quoteName( '#__comprofiler' ) .' as a ';
		$query .= ' WHERE '.$this->db->quoteName('user_id').'>'.$lastid;
		$query .= ' ORDER BY user_id ASC ';
		$query .= ' LIMIT '.$limit;

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function getCountAvatar($lastid=0){
		
		$query		= 'SELECT count(*) FROM ' . $this->db->quoteName( '#__comprofiler' ) .' as a ';
		if($lastid){
			$query .= ' WHERE '.$this->db->quoteName('user_id').'<='.$lastid;
		}

		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	/**
	* get cover from CB
	*
	*/
	public function getCover($lastid=0,$limit=0){
		
		$query		= 'SELECT '.$this->db->quoteName( 'user_id' ).', '.$this->db->quoteName( 'canvas' ).' FROM ' . $this->db->quoteName( '#__comprofiler' ) .' as a ';

		$query .= ' WHERE '.$this->db->quoteName('user_id').'>'.$lastid;
		$query .= ' ORDER BY user_id ASC ';
		$query .= ' LIMIT '.$limit;

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function getCountCover($lastid=0){
		return $this->getCountAvatar($lastid);
	}

	/**
	* get pofile from CB
	*
	*/
	public function getProfile($fields,$lastid=0,$limit=0){
		
		$query		= 'SELECT user_id,'.$fields.' FROM '.  $this->db->quoteName( '#__comprofiler' ) .' as a ';

		$query .= ' WHERE '.$this->db->quoteName('user_id').'>'.$lastid;
		$query .= ' ORDER BY user_id ASC ';
		$query .= ' LIMIT '.$limit;

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function getCountProfile($lastid=''){
		return $this->getCountAvatar($lastid);
	}
	/**
	* get fields community builder
	*
	*/
	public function getFields(){
		
		$query		= 'SELECT a.* FROM ' . $this->db->quoteName( '#__comprofiler_fields' ) .' as a WHERE tablecolumns LIKE '.$this->db->Quote('cb%');
		
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	/**
	* get fields value based on field of CB
	*
	*/ 
	public function getFieldsValueCB($field_id){

		
		$query		= 'SELECT '.$this->db->quoteName( 'fieldtitle' ).' FROM ' . $this->db->quoteName( '#__comprofiler_field_values' ) .' as a WHERE '.$this->db->quoteName( 'fieldid' ).'='.$this->db->Quote( $field_id );
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	/**
	* get friend connections  of CB
	*
	*/ 
	public function getFriends($lastid=0,$limit=0){
		
		$query		= 'SELECT * FROM ' . $this->db->quoteName( '#__comprofiler_members' );

		$query .= ' WHERE '.$this->db->quoteName('referenceid').'>'.$lastid;
		$query .= ' ORDER BY referenceid ASC ';
		$query .= ' LIMIT '.$limit;

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function getCountFriends($lastid=0){
		
		$query		= 'SELECT count(*) FROM ' . $this->db->quoteName( '#__comprofiler_members' );

		if($lastid){
			$query .= ' WHERE '.$this->db->quoteName('referenceid').'<='.$lastid;
		}
		
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}
	
}