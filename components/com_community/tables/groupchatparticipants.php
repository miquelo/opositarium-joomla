<?php
/**
 * @copyright (C) 2016 iJoomla, Inc. - All rights reserved.
 * @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author iJoomla.com <webmaster@ijoomla.com>
 * @url https://www.jomsocial.com/license-agreement
 * The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at https://www.jomsocial.com/license-agreement
 */
defined('_JEXEC') or die('Restricted access');

class CTableGroupChatParticipants extends JTable
{
    var $id	= null;
    var $group_chat_id = null;
    var $user_id = null;
    var $mute = null;
    var $mute_duration = null;
    var $muted_at	= null;

    /**
     * Constructor
     */
    public function __construct( &$db )
    {
        parent::__construct( '#__community_group_chat_participants', 'id', $db );
    }

    /**
     * Handle all sorts of load error
     */
    public function load( $id=null, $reset = true )
    {
        parent::load( $id , $reset);
        return;
    }

    /**
     * we always store a new one because edit is not possible in chat
     * @param bool $updateNulls
     * @return bool
     */
    public function store($updateNulls = false)
    {
        //only create this entry if there is no such records
        parent::store($updateNulls);
    }

    //check if user is the part of the group chat
    public function checkUserExists($chatId, $userId){
        $db = JFactory::getDbo();
        $query = "SELECT id FROM ".$db->quoteName('#__community_group_chat_participants')." WHERE "
            .$db->quoteName('user_id')."=".$db->quote($userId)." AND "
            .$db->quoteName('group_chat_id')."=".$db->quote($chatId)." AND "
            .$db->quoteName('active')."=".$db->quote(1);

        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    /**
     * Check total users in the group chat
     * @param $chatId
     * @param $removeEntries if set to true, it will remove all the entries
     */
    public function checkTotalActiveUsers($chatId, $removeEntries = false){
        $db = JFactory::getDbo();
        $query = "SELECT count(id) FROM ".$db->quoteName('#__community_group_chat_participants')." WHERE "
            .$db->quoteName('group_chat_id')."=".$db->quote($chatId)." AND "
            .$db->quoteName('active')."=".$db->quote(1);

        $db->setQuery($query);
        $result = $db->loadResult();

        if(!$result && $removeEntries){
            $query = "DELETE FROM ".$db->quoteName('#__community_group_chat_participants')." WHERE "
                .$db->quoteName('group_chat_id')."=".$db->quote($chatId);
            $db->setQuery($query);
            $db->execute();
        }

        return $result;
    }

    public function mute($duration = 0){
        //@todo to find out duration is in which unit (day, min, hour)
        $this->mute = 1;
        $this->store();
        return true;
    }

    public function unmute(){
        $this->mute = 0;
        $this->store();
        return true;
    }

    public function delete($id = null)
    {

    }
}
