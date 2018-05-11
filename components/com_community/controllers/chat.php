<?php
/**
 * @copyright (C) 2016 iJoomla, Inc. - All rights reserved.
 * @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author iJoomla.com <webmaster@ijoomla.com>
 * @url https://www.jomsocial.com/license-agreement
 * The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at https://www.jomsocial.com/license-agreement
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CommunityChatController extends CommunityBaseController
{

    //this should be the main page for chat
    public function display($cacheable = false, $urlparams = false)
    {
        $document = JFactory::getDocument();
        $viewType = $document->getType();
        $view = $this->getView('chat', '', $viewType);
        echo $view->get('display');
    }

    public function ajaxGetSingleChatByUser($user_id)
    {
        $model = CFactory::getModel('chat');
        die(json_encode($model->getSingleChatByUser($user_id)));
    }

    /**
     * @param $to
     * @param $message
     * @param $latestMessageId
     * @return either the chat id if successfully send through, else get a false
     */
    public function ajaxAddChat($chatid, $message, $attachment, $partner, $name)
    {
        $attachment = json_decode( $attachment );
        if (!$attachment) {
            $attachment = json_decode('{}');
        }

        // Parse link.
        $urlPattern = '/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i';
        if (preg_match($urlPattern, $message)) {
            $graphObject = CParsers::linkFetch($message);
            if ($graphObject) {
                if (!isset($attachment->type)) {
                    $attachment->type = 'url';
                }
                $attachment->url = $graphObject->get('url');
                $attachment->title = $graphObject->get('title');
                $attachment->description = $graphObject->get('description');
                $attachment->images = $graphObject->get('image');

                // Check if it is a video url (YouTube, Vimeo, etc).
                $video = JTable::getInstance('Video', 'CTable');
                if ($video->init($attachment->url)) {
                    $attachment->type = 'video';
                    $attachment->video = array(
                        'type' => $video->type,
                        'id' => $video->video_id,
                        'path' => $video->path,
                        'thumbnail' => $video->getThumbnail(),
                        'title' => $video->title,
                        'title_short' => JHTML::_('string.truncate', $video->title, 50, true, false),
                        'desc_short' => JHTML::_('string.truncate', $video->description, CFactory::getConfig()->getInt('streamcontentlength'), true, false)
                    );
                }
            }
        }

        #TODO: check has access to chat
        $model = CFactory::getModel('chat');
        if ($chatid) {
            die(json_encode($model->addChat($chatid, $message, $attachment)));
        } else {
            die(json_encode($model->createChat($message, $attachment, $partner, $name)));
        }
    }

    /**
     * Ping the server to find out if there is any new message for the current user.
     * If there is a new message, it will return the message information, same structure as getLastChat
     * OR return false if there is nothing new
     */
    public function ajaxPingChat($last_activity = 0)
    {
        $model = CFactory::getModel('chat');
        die(json_encode($model->getActivity($last_activity)));
    }

    /**
     * Retrive the last x amount of message if specified, else we will retrieve from admin settings
     * @param $chatId
     * @param int $total
     * @param int $lastID
     */
    public function ajaxGetLastChat($chat_id, $offset)
    {
        $model = CFactory::getModel('chat');
        $config = CFactory::getConfig();

        if ( $offset > 0 ) {
            $limit = $config->get('message_total_loaded_display', 10);
        } else {
            $limit = $config->get('message_total_initial_display', 10);
        }

        $data = $model->getLastChat($chat_id, $offset, $limit);
        die( json_encode($data) );
    }

    public function ajaxGetChatList($ids)
    {
        $ids = json_decode($ids);
        $model = CFactory::getModel('chat');
        die(json_encode($model->getChatList($ids)));
    }

    /**
     * Pass in the message id and that's it
     * @param $chatReplyId
     * @return true or false.
     */
    public function ajaxRecallMessage($chatReplyId){
        $model = CFactory::getModel('chat');
        die(json_encode($model->recallMessage($chatReplyId)));
    }

    /**
     * Gets all the chat windows from current user, with one message each
     * Returns all the chat windows with one latest chat info.
     * avatar = receiver avatar, chat_id = chat id
     */
    public function ajaxInitializeChatData()
    {
        $model = CFactory::getModel('chat');
        $results = $model->initializeChatData();
        die(json_encode($results));
    }

    public function ajaxSeen($chat_id)
    {
        $model = CFactory::getModel('chat');
        $model->seen( (int) $chat_id );
        die();
    }

    public function ajaxPrivateMessageSend($to, $msg, $attachment)
    {
        $attachment = json_decode( $attachment );
        if (!$attachment) {
            $attachment = json_decode('{}');
        }

        $my = CFactory::getUser();
        $model = CFactory::getModel('chat');
        $result = $model->addPrivateMessage($to, $msg, $attachment);

        // Add user points.
        CUserPoints::assignPoint('inbox.message.send');

        // Add notification.
        $chat_id = $result->chat_id;

        $params = new CParameter('');
        $params->set('url', 'index.php?option=com_community&view=chat#' . $chat_id);

        $body = htmlspecialchars($msg);
        $pattern = "/<br \/>/i";
        $replacement = "\r\n";
        $body = preg_replace($pattern, $replacement, $body);

        $params->set('message', $body);
        $params->set('title', JText::_('COM_COMMUNITY_PRIVATE_MESSAGE'));
        $params->set('msg_url', 'index.php?option=com_community&view=chat#' . $chat_id);
        $params->set('msg', JText::_('COM_COMMUNITY_PRIVATE_MESSAGE'));

        CNotificationLibrary::add('inbox_create_message', $my->id, $to, JText::sprintf('COM_COMMUNITY_SENT_YOU_MESSAGE'), '', 'inbox.sent', $params);

        die(json_encode(JText::_('COM_COMMUNITY_INBOX_MESSAGE_SENT')));
    }

    public function ajaxLeaveChat($chat_id)
    {
        $model = CFactory::getModel('chat');
        $model->leaveChat($chat_id);
        die();
    }

    public function ajaxAddPeople($chat_id, $user_ids)
    {
        $user_ids = json_decode($user_ids);
        $model = CFactory::getModel('chat');
        $result = $model->addPeople($chat_id, $user_ids);
        die(json_encode($result));
    }

    public function ajaxGetFriendListByName($keyword, $exclusion)
    {
        $model = CFactory::getModel('chat');
        $ids = $model->getFriendListByName($keyword, $exclusion);
        $result = array();
        if (count($ids)) {
            foreach ($ids as $id) {
                $profile = CFactory::getUser($id);
                $user = new stdClass;
                $user->name = $profile->getDisplayName();
                $user->id = $profile->id;
                $user->avatar = $profile->getThumbAvatar();
                $result[] = $user;
            }
        }
        die(json_encode($result));
    }

    public function ajaxMuteChat($chat_id, $mute)
    {
        $model = CFactory::getModel('chat');
        $model->muteChat($chat_id, $mute);
        die();
    }

    public function ajaxDisableChat($chat_id)
    {
        $model = CFactory::getModel('chat');
        $model->disableChat($chat_id);
        die();
    }
}

