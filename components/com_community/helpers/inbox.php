<?php
/**
* @copyright (C) 2013 iJoomla, Inc. - All rights reserved.
* @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
* @author iJoomla.com <webmaster@ijoomla.com>
* @url https://www.jomsocial.com/license-agreement
* The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
* More info at https://www.jomsocial.com/license-agreement
*/

defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT .'/components/com_community/libraries/core.php' );

class CInboxHelper
{

	/**
	 * Get all the last seen by message string
	 * @param $messageId
	 */
	static public function formatReadByString( $messageId )
	{
		if(!$messageId){
			return;
		}
		$my = CFactory::getUser();

		 //latest feature in 4.3, to mark last read message by current user.
        $message =  $photo = JTable::getInstance('Message', 'CTable');
        $message->load($messageId);

        $params = new CParameter($message->body);
        $readBy = json_decode($params->get('lastreadby', '{}'));

		//check type of the message, one to one OR many perticipants
		$inbox= CFactory::getModel('inbox');
		$totalParticipants = count($inbox->getParticipantsID($messageId));
		$totalReadByUsers = count($readBy);

		$seenMsg = '';
		if($totalParticipants <= 2 &&  $totalParticipants <= $totalReadByUsers){
			//one to one, and seen, and only the sender need to know if the receiver has read it
			if($readBy[0]== $my->id) {
				$seenMsg = '<svg viewBox="0 0 16 16" class="joms-icon"><use xlink:href="' . CRoute::getURI() . '#joms-icon-eye"></use></svg>' . '<span>' . JText::_('COM_COMMUNITY_INBOX_SEEN') . '</span>';
			}
		}elseif($totalParticipants > 2 && $totalParticipants == $totalReadByUsers){
			//many participants and seen by all
			$seenMsg = '<svg viewBox="0 0 16 16" class="joms-icon"><use xlink:href="'. CRoute::getURI() .'#joms-icon-eye"></use></svg>' . '<span>'.JText::_('COM_COMMUNITY_INBOX_SEEN_BY_ALL').'</span>';
		}elseif(is_object($readBy) || ($totalReadByUsers == 1 && $readBy[0] == $my->id) ){
			//none has seen
			$seenMsg = '<span>'.JText::_('COM_COMMUNITY_INBOX_NOT_SEEN').'</span>';
		}else {
			//this must be seen by specific users only
			$names = array();
			foreach ($readBy as $id) {
				if ($id == $my->id) {
					continue;
				}
				$user = CFactory::getUser($id);
				$names[] = $user->getDisplayName();
			}

			$name = implode($names, ', ');
			$seenMsg = '<svg viewBox="0 0 16 16" class="joms-icon"><use xlink:href="'. CRoute::getURI() .'#joms-icon-eye"></use></svg><span>'.JText::_('COM_COMMUNITY_INBOX_SEEN_BY_USERS') .'<strong>'. $name .'<strong></span>';
		}

		return $seenMsg;
	}

	/**
	 * @param $msgId
	 * @param int $userid
	 * Last seen message upon new entry will reset the last seen by
	 */
	public static function resetSeenMessage($msgId, $userid = 0){
		$message =  $photo = JTable::getInstance('Message', 'CTable');
        $message->load($msgId);

		$userid = ($userid) ? $userid : CFactory::getUser()->id;
        $params = new CParameter($message->body);
        $readBy = array($userid);
        $params->set('lastreadby', json_encode($readBy));
        $message->body = $params->toString();
        $message->store();
	}
}