<?php

defined('_JEXEC') or die('Restricted access');


class CChatAccess implements CAccessInterface
{
    static public function authorise()
    {
        $args      = func_get_args();
        $assetName = array_shift ( $args );

        if (method_exists(__CLASS__,$assetName)) {
            return call_user_func_array(array(__CLASS__, $assetName), $args);
        } else {
            return null;
        }
    }

    /**
     * check if the user can send a messsage to the respective user
     * @param $userId
     * @param $toUserId
     */
    static public function chatMessageSend($userId, $toUserId){
        $toUser = CFactory::getUser($toUserId);
        $my = CFactory::getUser($userId);

        if(!$userId || !$toUserId){
            return false;
        }

        // @rule: Global admin can view all
        if( COwnerHelper::isCommunityAdmin() || $my->id == $toUser->id ){
            return true;
        }

        // @rule: if the user is blocked, you can't see it either
        if( $toUser->isBlocked() ){
            return false;
        }

        $param = $toUser->getParams();
        $access = $param->get('privacyProfileView');

        // @rule, User with public access, show
        // In old profile, 0 also means public
        if( $access == PRIVACY_PUBLIC || $access == 0){
            return true;
        }

        // @$my: at this stage, non registered member can't view it anyway
        if( $my->id == 0){
            return false;
        }

        // @rule: User that limit to friend only, check for friend
        if( $access == PRIVACY_FRIENDS ){
            $friends = explode( ',', $my->_friends );
            if(in_array( $toUser->id, $friends )){
                return true;
            }
        }

        if( $access == PRIVACY_MEMBERS && $my->id !==0){
            return true;
        }

        return false;

    }

    /*
     * This function will get the private message permission
     *
     * @return : bool
     */
    static public function chatPrivateMessage($userId, $targetId)
    {   
        $config = CFactory::getConfig();
        $targetUser = CFactory::getUser($targetId);
        $params = $targetUser->getParams();

        if (COwnerHelper::isCommunityAdmin()) {
            return true;
        }

        if ($userId == $targetId || !$config->get('enablepm')) {
            return false;
        }

        $privacyPrivateMessage = $params->get('privacyPrivateMessage', $config->get('privacyprivatemessage')); 
        
        if ($privacyPrivateMessage == PRIVACY_PUBLIC) {
            return true;
        } else if ($privacyPrivateMessage == PRIVACY_MEMBERS) {
            // friend
            if (CFriendsHelper::isConnected($userId, $targetId)) {
                return true;
            }
        } else if ($privacyPrivateMessage == PRIVACY_FRIENDS) {
            // friend of friend
            $friendsModel = CFactory::getModel('Friends');
            $friends = $friendsModel->getFriendIds($targetId);

            if (!$friends) {
                return false;
            } else {
                foreach ($friends as $friendId) {
                    if (CFriendsHelper::isConnected($userId, $friendId)) {
                        return true;
                    }
                }
            }
        } else if ($privacyPrivateMessage == PRIVACY_PRIVATE) {
            return false;
        }

        return false;
    }
}