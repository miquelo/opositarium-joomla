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

require_once ( JPATH_ROOT . '/components/com_community/models/models.php');

class CommunityModelChat extends JCCModel implements CNotificationsInterface
{

    function getTotalNotifications($user)
    {
        $config = CFactory::getConfig();
        $enablepm = $config->get('enablepm');

        if (!$enablepm) {
            return;
        }

        $js = JURI::root(true) . '/components/com_community/assets/source/js/chat/dist/bundle.min.js';
        JFactory::getDocument()->addScript($js);

        $assets = CAssets::getInstance();
        $template = new CTemplate();

        $assets->addVariable('chat_enablereadstatus', $config->get('enablereadstatus'));
        $assets->addVariable('chat_pooling_time_active', $config->get('message_pooling_time_active', 10));
        $assets->addVariable('chat_pooling_time_inactive', $config->get('message_pooling_time_inactive', 30));
        $assets->addVariable('chat_show_timestamp', $config->get('message_show_timestamp'));
        $assets->addVariable('chat_base_uri', rtrim( JUri::root() ));
        $assets->addVariable('chat_uri', CRoute::_('index.php?option=com_community&view=chat', false));
        $assets->addVariable('chat_time_format', $config->get('message_time_format'));
        $assets->addVariable('chat_template_notification_item', $template->fetch('chat/notification-item'));
        $assets->addVariable('chat_text_and', JText::_('COM_COMMUNITY_AND'));
        $assets->addVariable('chat_recall', $config->get('message_recall_minutes', 0));


        $chat = $this->getMyChatList();
        $count = 0;

        foreach ($chat as $item) {
            if ($item->seen == 0 && $item->mute == 0) {
                $count++;
            }
        }
        return $count;
    }

    public function getLastChat($chatId, $offset = 0,  $limit = 20)
    {
        $this->seen($chatId);
        $db = JFactory::getDbo();

        //TODO: validate chat belong to current user
        $offset_query = $offset ? " AND id < ". $offset : '';
        $query = "SELECT * FROM #__community_chat_activity WHERE
             chat_id =" . $chatId . " AND action in ('sent', 'leave', 'add') ".$offset_query." ORDER BY id DESC LIMIT " . $limit;

        $db->setQuery($query);
        $list = $db->loadObjectList();
        $count = count($list);

        $result = new stdClass();
        $result->seen = array();
        $result->messages = array();

        if ($count) {
            $last_message = $list[0];
            $result->seen = $this->getLastSeen($last_message);
            $result->messages = $this->formatResults($list);
        }

        return $result;
    }

    public function getLastSeen($last_message)
    {
        $query = "SELECT * FROM #__community_chat_activity
            WHERE chat_id = " . $last_message->chat_id . "
            AND action = 'seen'
            AND id > " . $last_message->id;

        $db = JFactory::getDbo();
        $list = $db->setQuery($query)->loadObjectList();
        $count = count($list);

        if ($count) {
            return $list;
        } else {
            return array();
        }
    }

    public function getChatList($ids = array())
    {
        $data = new stdClass();
        $list = array();

        foreach ($ids as $id) {
            $chat = $this->getChat($id);
            if ($chat) {
                $chat->seen = 0;

                if ($chat->type === 'single') {
                    $partner = $this->getChatPartner($chat->chat_id);
                    $chat->partner = $partner[0];
                }

                $list[] = $chat;
            }
        }

        $data->list = $list;
        $data->buddies = $this->getBuddies($ids);

        return $data;
    }

    public function getChat($id)
    {
        $db = JFactory::getDbo();
        $my = CFactory::getUser();

        $query = "SELECT id AS chat_id, name, type
            FROM #__community_chat
            WHERE id = " . $id;

        $chat = $db->setQuery($query)->loadObject();

        $query = "SELECT user_id
            FROM #__community_chat_participants
            WHERE chat_id = $chat->chat_id AND user_id != $my->id AND enabled = 1";

        if ($chat->type === 'single') {
            $user_id = $db->setQuery($query)->loadResult();
            $user = CFactory::getUser($user_id);
            $chat->name = $user->getDisplayName();
            $chat->thumb = $user->getThumbAvatar();
        } else if ($chat->type === 'group') {
            $user_ids = $db->setQuery($query)->loadColumn();
            $chat->name = array();
            $chat->participants = 0;
            foreach ($user_ids as $index => $user_id) {
                $user = CFactory::getUser($user_id);
                $chat->name[] = $user->getDisplayName();
                $chat->participants++;
            }
            $chat->thumb = JUri::root() . '/components/com_community/assets/group_thumb.jpg';
        }

        return $chat;
    }

    public function formatResults($chatList)
    {
        foreach ($chatList as $chat) {
            $chat->content = htmlspecialchars($chat->content);
            $chat->created_at = strtotime($chat->created_at);
            $params = json_decode($chat->params);
            $attachment = NULL;

            if (isset($params->attachment) && isset($params->attachment->type)) {
                if ($params->attachment->type === 'image') {
                    $attachment = $params->attachment;

                    // Fix legacy image attachment which is saved as string.
                    if (isset($attachment->thumburl)) {
                        $attachment->url = JURI::root() . $attachment->thumburl;
                    } else if (isset($attachment->id)) {
                        $photoTable = JTable::getInstance('Photo', 'CTable');
                        $photoTable->load($attachment->id);

                        $attachment->url = $photoTable->getThumbURI();
                    }

                } else if ($params->attachment->type === 'file') {
                    $attachment = $params->attachment;

                    // Fix legacy image attachment which is saved as string.
                    if (isset($attachment->path)) {
                        $attachment->url = JURI::root() . $attachment->path;
                    } else if (isset($attachment->id)) {
                        $fileTable = JTable::getInstance('File', 'CTable');
                        $fileTable->load($attachment->id);

                        $attachment->name = $fileTable->name;
                        $attachment->url = JURI::root() . $fileTable->filepath;
                    }

                } else {
                    $attachment = $params->attachment;
                    $attachment->description = CStringHelper::trim_words($attachment->description);
                }
            }

            $chat->attachment = $attachment ? json_encode($attachment) : '{}';
        }

        return $chatList;
    }

    public function getSingleChatByUser($user_id)
    {
        $data = new stdClass();
        $my = CFactory::getUser();
        $query = "SELECT chat_id
            FROM #__community_chat_participants
            WHERE user_id = " . $user_id . " AND enabled = 1 AND chat_id IN (
            SELECT cc.id
            FROM #__community_chat AS cc
            LEFT JOIN #__community_chat_participants AS ccp ON cc.id = ccp.chat_id
            WHERE ccp.user_id = " . $my->id . " AND ccp.enabled = 1 AND cc.`type` = 'single'
            GROUP BY cc.id)
            ";

        $db = JFactory::getDbo();
        $db->setQuery($query);
        $chat_id = $db->loadResult();

        if ($chat_id) {
            $data = $this->getLastChat($chat_id);
            $data->chat_id = $chat_id;
        }

        $user = CFactory::getUser($user_id);
        $ob = new stdClass();
        $ob->id = $user->id;
        $ob->name = $user->name;
        $ob->avatar = $user->getThumbAvatar();
        $data->partner = $ob;

        return $data;
    }

    public function initializeChatData()
    {
        $data = new stdClass();
        $data->list = new stdClass();
        $data->buddies = new stdClass();

        $list = $this->getMyChatList();

        if (count( (array) $list)) {
            $chatids = array();

            foreach ($list as &$item) {
                $chatids[] = $item->chat_id;
                if (!$item->enabled) {
                    unset($item);
                    continue;
                }
                unset($item->enabled);
            }

            $data->list = $list;
            $data->buddies = $this->getBuddies($chatids);
            $data->last_activity = $this->getLastActivity();
        }

        return $data;
    }

    public function getBuddies($chatids)
    {
        $db = JFactory::getDbo();

        $query = "SELECT user_id
            FROM #__community_chat_participants
            WHERE chat_id IN (". implode(',', $chatids).")
            GROUP BY user_id";

        $ids = $db->setQuery($query)->loadColumn();

        $buddies = new stdClass();

        foreach ($ids as $id) {
            $profile = CFactory::getUser($id);
            $buddy = new stdClass();
            $buddy->id = $id;
            $buddy->name = $profile->getDisplayName();
            $buddy->avatar = $profile->getThumbAvatar();
            $buddies-> { $id } = $buddy;
        }

        return $buddies;
    }

    public function getMyChatList()
    {
        $my = CFactory::getUser();
        $db = JFactory::getDbo();

        $query = "SELECT c.id as chat_id, c.type, c.name, c.last_msg, cp.enabled, cp.mute
            FROM #__community_chat c
                LEFT JOIN #__community_chat_participants cp ON c.id = cp.chat_id
            WHERE cp.user_id = $my->id AND cp.enabled = 1
            ORDER BY c.last_msg DESC";

        $list = $db->setQuery($query)->loadObjectList();

        $chat = new stdClass();

        foreach ($list as $item) {
            $isSeen = $this->isSeen($item);
            if ($isSeen) {
                $item->seen = 1;
            } else {
                $item->seen = 0;
            }
            unset($item->last_msg);

            $query = "SELECT user_id
                FROM #__community_chat_participants
                WHERE chat_id = $item->chat_id AND user_id != $my->id AND enabled = 1";

            if ($item->type === 'single') {
                $user_id = $db->setQuery($query)->loadResult();
                $user = CFactory::getUser($user_id);
                $item->name = $user->getDisplayName();
                $item->thumb = $user->getThumbAvatar();
            } else if ($item->type === 'group' ) {
                $user_ids = $db->setQuery($query)->loadColumn();
                $item->name = array();
                $item->participants = 0;
                foreach ($user_ids as $index => $user_id) {
                    $user = CFactory::getUser($user_id);
                    $item->name[] = $user->getDisplayName();
                    $item->participants++;
                }
                $item->thumb = JUri::root() . '/components/com_community/assets/group_thumb.jpg';
            }

            $chat->{ 'chat_' . $item->chat_id } = $item;
        }
        return $chat;
    }

    public function isSeen($item)
    {
        $my = CFactory::getUser();
        $db = JFactory::getDbo();

        $query = "SELECT id
            FROM #__community_chat_activity
            WHERE chat_id = " . $item->chat_id . "
            AND user_id = " . $my->id . "
            ORDER BY id DESC LIMIT 1 ";

        $action = $db->setQuery($query)->loadResult();

        if ($action >= $item->last_msg) {
            return true;
        } else {
            return false;
        }
    }

    public function getChatPartner($chatid)
    {
        $user = CFactory::getUser();
        $db = JFactory::getDbo();

        $query = "SELECT cu.userid
                FROM #__community_users as cu
                LEFT JOIN #__community_chat_participants as ccp on cu.userid = ccp.user_id
                WHERE ccp.chat_id = " . $chatid . "
                AND ccp.user_id != " . $user->id;

        $parter = $db->setQuery($query)->loadColumn();

        return $parter;
    }

    public function getActivity($last_activity = 0)
    {
        $my = CFactory::getUser();

        if (!$my->id) {
            return false;
        }

        for ($i = 0; $i < 20; $i++) {
            $db = JFactory::getDbo();

            $query = "SELECT ca.*
                FROM #__community_chat_activity ca
                LEFT JOIN #__community_chat_participants cc ON ca.chat_id = cc.chat_id
                LEFT JOIN #__community_chat c ON c.id = ca.chat_id
                WHERE cc.user_id = ".$my->id."
                AND cc.enabled = 1
                AND ca.id > " . $last_activity;

            $db->setQuery($query);
            $list = $db->loadObjectList();
            $count = count($list);

            if ($count > 0) {
                $activities = $this->formatResults($list);
                $newcomer = array();
                foreach ($activities as $a) {
                    if ($a->action === 'add') {
                        $profile = CFactory::getUser($a->user_id);
                        $user = new stdClass();
                        $user->id = $profile->id;
                        $user->name = $profile->name;
                        $user->avatar = $profile->getThumbAvatar();
                        $newcomer[] = $user;
                    }
                }

                $result = new stdClass();
                $result->activities = $activities;
                $result->newcomer = $newcomer;

                return $result;
            } else {
                usleep(500000);
            }
        }

        return new stdClass();
    }

    public function getLastActivity()
    {
        $my = CFactory::getUser();
        $db = JFactory::getDbo();

        $query = "SELECT ca.*
                FROM #__community_chat_activity ca
                LEFT JOIN #__community_chat_participants cc ON ca.chat_id = cc.chat_id
                LEFT JOIN #__community_chat c ON c.id = ca.chat_id
                WHERE cc.user_id = ".$my->id."
                AND cc.enabled = 1
                ORDER BY ca.id DESC LIMIT 1";

        $id = $db->setQuery($query)->loadResult();

        return $id ? $id : 0;
    }

    public function addActivity($chatid, $user_id, $action, $content = '', $params = '', $created_at = '')
    {
        $table = JTable::getInstance('ChatActivity', 'CTable');
        $data = array(
            'chat_id' => $chatid,
            'user_id' => $user_id,
            'action' => $action,
            'content' => $content,
            'params' => $params,
            'created_at' => $created_at ? $created_at : date('Y-m-d H:i:s')
        );

        $table->bind($data);
        $table->store();

        return $table;
    }

    public function recallMessage($chatReplyId)
    {
        $my = CFactory::getUser();

        // simple and straight forward validation
        $timeout = CFactory::getConfig()->get('message_recall_minutes');
        if (!$timeout) {
            return false;
        }

        $db = JFactory::getDbo();
        $query = "SELECT id FROM " . $db->quoteName('#__community_chat_activity') . " WHERE "
            . $db->quoteName('id') . "=" . $db->quote($chatReplyId) . " AND "
            . $db->quoteName('user_id') . "=" . $db->quote($my->id);
        $db->setQuery($query);

        $result = $db->loadColumn();
        if ($result) { // if there exists such record, delete it immediately
            $query = "DELETE FROM " . $db->quoteName('#__community_chat_activity') . " WHERE "
                . $db->quoteName('id') . "=" . $db->quote($chatReplyId) . " AND "
                . $db->quoteName('user_id') . "=" . $db->quote($my->id);
            $db->setQuery($query);
            return $db->execute();
        }

        return false;
    }

    public function addChat($chatid, $message, $attachment)
    {
        $my = CFactory::getUser();

        $params = new CParameter();
        $params->set('attachment', $attachment);
        $params = $params->toString();

        $activity = $this->addActivity($chatid, $my->id, 'sent', $message, $params, date('Y-m-d H:i:s'));
        $this->updateLastChat($chatid, $activity->id);

        $data = new stdClass();
        $data->chat_id = $activity->chat_id;
        $data->reply_id = $activity->id;
        $data->attachment = $attachment;

        return $data;
    }

    function updateLastChat($chatid, $activity_id)
    {
        $query = 'UPDATE #__community_chat SET last_msg = "' . $activity_id . '" WHERE id = ' . $chatid;
        $db = JFactory::getDbo();
        $db->setQuery($query)->execute();
    }

    public function createChat($message, $attachment, $partner, $name)
    {
        $chatTable = JTable::getInstance('Chat', 'CTable');
        $my = CFactory::getUser();
        $partner = json_decode($partner);
        $count = count($partner);

        $chat = new stdClass();

        if ($count === 1) {
            $chat->type = 'single';
            $chat->name = '';
            $chat->partner = $partner[0];
        } elseif ($count > 1) {
            $chat->type = 'group';
            $chat->name = $name;
        } else {
            return;
        }

        $chatTable->bind($chat);

        if (!$chatTable->store()) {
            return;
        }

        $chatid = $chatTable->id;
        $ids = $partner;
        $ids[] = $my->id;

        foreach ($ids as $id) {
            $data = new stdClass();
            $data->chat_id = $chatid;
            $data->user_id = $id;

            $db = JFactory::getDbo();
            $insert = $db->insertObject('#__community_chat_participants', $data, 'id');

            if (!$insert) {
                return;
            }
        }

        $chat->chat_id = $chatid;
        $result = $this->addChat($chatid, $message, $attachment);
        $result->chat = $chat;

        return $result;
    }

    public function seen($chat_id)
    {
        $my = CFactory::getUser();
        $last_user_activity = $this->getLastUserActivity($chat_id);
        $last_chat = $this->getLastChatTime($chat_id);

        if ($last_user_activity < $last_chat) {
            $this->deleteOldSeen($chat_id);
            $this->addActivity($chat_id, $my->id, 'seen');
        }
    }

    public function isLastActive($chat_id, $user_id)
    {
        $query = 'SELECT user_id FROM #__community_chat_activity WHERE chat_id ='.$chat_id.' ORDER BY id DESC LIMIT 1';

        $db = JFactory::getDbo();
        $result = $db->setQuery($query)->loadResult();

        return $result == $user_id ? true : false;
    }

    public function deleteOldSeen($chat_id)
    {
        $my = CFactory::getUser();

        $query = 'DELETE FROM #__community_chat_activity '
            . 'WHERE action="seen" '
            . 'AND user_id = ' . $my->id . ' '
            . 'AND chat_id = ' . $chat_id;

        $db = JFactory::getDbo();
        $db->setQuery($query)->execute();
    }

    public function getLastChatTime($chat_id)
    {
        $query = 'SELECT id FROM #__community_chat_activity '
            . 'WHERE chat_id = ' . $chat_id . ' '
            . 'AND action in ("sent", "leave", "add") '
            . 'ORDER BY id '
            . 'DESC LIMIT 1';

        $db = JFactory::getDbo();

        return $db->setQuery($query)->loadResult();
    }

    public function getLastUserActivity($chat_id)
    {
        $my = CFactory::getUser();

        $query = 'SELECT id FROM #__community_chat_activity '
            . 'WHERE user_id = ' . $my->id . ' '
            . 'AND chat_id = ' . $chat_id . ' '
            . 'ORDER BY id '
            . 'DESC LIMIT 1';

        $db = JFactory::getDbo();

        return $db->setQuery($query)->loadResult();
    }

    public function addPrivateMessage($to, $msg, $attachment)
    {
       $chat_id = $this->getPrivateChatByUser($to);

       if ($chat_id) {
           return $this->addChat($chat_id, $msg, $attachment);
       } else {
           return $this->createChat($msg, $attachment, json_encode(array($to)), '');
       }
    }

    public function getPrivateChatByUser($id)
    {
        $my = CFactory::getUser();
        $db = JFactory::getDbo();

        $query = "SELECT chat_id
            FROM #__community_chat_participants
            WHERE chat_id IN
            ( SELECT c.id
            FROM #__community_chat c
            LEFT JOIN #__community_chat_participants cc ON c.id = cc.chat_id
            WHERE cc.user_id = ".$my->id." AND cc.enabled = 1 AND c.`type` = 'single')
            AND user_id = " . $id . " AND enabled = 1";
        
        return $db->setQuery($query)->loadResult();
    }

    public function leaveChat($chat_id)
    {
        $my = CFactory::getUser();

        $db = JFactory::getDbo();
        $query = "UPDATE #__community_chat_participants SET enabled = 0 WHERE chat_id = ".$chat_id . " AND user_id =".$my->id;
        $db->setQuery($query)->execute();

        $this->addActivity($chat_id, $my->id, 'leave');
    }

    public function leaveGroupChat($chat_id)
    {
        $my = CFactory::getUser();
        $isGroupChat = $this->isGroupChat($chat_id);

        if ($isGroupChat) {
            $db = JFactory::getDbo();
            $query = "UPDATE #__community_chat_participants SET enabled = 0 WHERE chat_id = ".$chat_id . " AND user_id =".$my->id;
            $db->setQuery($query)->execute();

            $this->addActivity($chat_id, $my->id, 'leave');
        }
    }

    public function isGroupChat($chat_id)
    {
        $db = JFactory::getDbo();
        $query = "SELECT `type` FROM #__community_chat WHERE id = " . $chat_id;
        $result = $db->setQuery($query)->loadResult();

        if ($result && $result == 'group') {
            return true;
        }

        return false;
    }

    public function addPeople($chat_id, $ids)
    {
        $db = JFactory::getDbo();
        $query = "SELECT type FROM #__community_chat WHERE id =" .$chat_id;
        $type = $db->setQuery($query)->loadResult();

        if (!$type) {
            return;
        }

        $query = "SELECT user_id FROM #__community_chat_participants "
            . "WHERE chat_id = ".$chat_id." "
            . "AND user_id in (". implode(',', $ids) .") "
            . "AND enabled = 0";

        $exist_user = $db->setQuery($query)->loadColumn();

        if (count($exist_user)) {
            foreach ($exist_user as $uid) {
                $q = "UPDATE #__community_chat_participants SET enabled = 1 WHERE chat_id = ".$chat_id ." AND user_id = ". $uid;

                $db->setQuery($q)->execute();

                $this->addActivity($chat_id, $uid, 'add');
            }
        }

        $query = "SELECT user_id FROM #__community_chat_participants WHERE chat_id =".$chat_id;
        $user_ids = $db->setQuery($query)->loadColumn();
        $id_diff = array_diff($ids, $user_ids);

        if (count($id_diff)) {
            foreach ($id_diff as $uid) {
                $data = new stdClass();
                $data->chat_id = $chat_id;
                $data->user_id = $uid;

                $db->insertObject('#__community_chat_participants', $data, 'id');

                $this->addActivity($chat_id, $uid, 'add');
            }
        }
    }

    public function getFriendListByName($keyword, $exclusion)
    {
        $my = CFactory::getUser();
        $db	= $this->getDBO();

        $andName = '';
        $exclude = '';

        if ($exclusion) {
            $exclude = ' AND b.'.$db->quoteName('id').' not in ('.$exclusion.')';
        }

        $config = CFactory::getConfig();
        $nameField = $config->getString('displayname');

        if(!empty($keyword)){
            $andName	= ' AND b.' . $db->quoteName( $nameField ) . ' LIKE ' . $db->Quote( '%'.$keyword.'%' ) ;
        }

        $query = 'SELECT DISTINCT(a.'.$db->quoteName('connect_to').') AS id  FROM ' . $db->quoteName('#__community_connection') . ' AS a '
            . ' INNER JOIN ' . $db->quoteName( '#__users' ) . ' AS b '
            . ' ON a.'.$db->quoteName('connect_from').'=' . $db->Quote( $my->id )
            . ' AND a.'.$db->quoteName('connect_to').'=b.'.$db->quoteName('id')
            . ' AND a.'.$db->quoteName('status').'=' . $db->Quote( '1' )
            . ' AND b.'.$db->quoteName('block').'=' .$db->Quote('0')
            . $exclude
            . ' WHERE NOT EXISTS ( SELECT d.'.$db->quoteName('blocked_userid') . ' as id'
            . ' FROM '.$db->quoteName('#__community_blocklist') . ' AS d  '
            . ' WHERE d.'.$db->quoteName('userid').' = '.$db->Quote($my->id)
            . ' AND d.'.$db->quoteName('blocked_userid').' = a.'.$db->quoteName('connect_to').')'
            . $andName
            . ' ORDER BY b.' . $db->quoteName($nameField)
            . ' LIMIT 200';

        $db->setQuery( $query );
        $friends = $db->loadColumn();

        return $friends;
    }

    public function muteChat($chat_id, $mute)
    {
        $my = CFactory::getUser();

        $query = "UPDATE #__community_chat_participants "
            . "SET mute = " . $mute . " "
            . "WHERE chat_id = ". $chat_id ." "
            . "AND user_id = " . $my->id;

        $db = JFactory::getDbo();
        $db->setQuery($query)->execute();
    }

    public function disableChat($chat_id) {
        $my = CFactory::getUser();

        $query = "UPDATE #__community_chat_participants "
            . "SET enabled = 0 "
            . "WHERE chat_id = ". $chat_id ." "
            . "AND user_id = " . $my->id;

        $db = JFactory::getDbo();
        $db->setQuery($query)->execute();
    }

}
