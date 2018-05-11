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

jimport('joomla.utilities.date');

class CServiceCommentHelper {

    /**
     * @param $type
     * @param $identifier [identifier can be the article id, that is unique enough to distinguish between the comments]
     * @param array $groupPermission
     * @param string $objectName - for objectname
     * @param string $notificationCmd - for notification
     * @param string $objectUrl - url for the user to link to
     * @param array $groupPermission
     * @return type
     */
    public static function renderComment($type, $identifier, $objectName = '', $notificationCmd = '', $objectUrl = '', $groupPermission = array(), $objectTitle = null)
    {   
        $config = CFactory::getConfig();
        $isLoggedIn = (CFactory::getUser()->id) ? true : false;
        $db = JFactory::getDbo();
        //we must get the information and pass it to the template

        //to identify the series of comments, we must know the type and it must follow the pattern [component].[view].[task], it is possible to leave those empty

        //pre-identifier for a thirdparty
        $type = 'service.comment.'.$type;

        $params = new CParameter();
        if($objectUrl){
            $params->set('object_url', $objectUrl);
        }

        if($objectTitle){
            $params->set('object_title', $objectTitle);
        }

        $params = $params->toString();

        //update or insert the notification and object name if needed
        $query = "INSERT INTO ".$db->quoteName('#__community_thirdparty_wall_options')." (name, notification_cmd, object_name, params)
                VALUES (".$db->quote($type).",".$db->quote($notificationCmd).",".$db->quote($objectName).",".$db->quote($params).")
                ON DUPLICATE KEY UPDATE
                params=".$db->quote($params);

        $db->setQuery($query);
        $db->execute();

        //get the config
        $prevComments = $config->get('prev_comment_load');
        $totalDisplayedComments = $config->get('stream_default_comments');

        $wallContent = CWallLibrary::getWallContents($type, $identifier, COwnerHelper::isCommunityAdmin(), $totalDisplayedComments, 0);
        $wallModel = CFactory::getModel('wall');
        $wallCount = CWallLibrary::getWallCount($type, $identifier);

        $wallViewAll = '';
        if ($wallCount > $totalDisplayedComments) {
            $wallViewAll = CWallLibrary::getViewAllLinkHTML('foo', $wallCount);
        }

        //add required css
        $css = 'templates/'.$config->get('template').'/css/style.css';
        CFactory::attach($css, 'css');

        // Access check: ACL
        $postCommentACL = CFactory::getUser()->authorise('community.postcommentcreate', 'com_community');

        //we might want to move this to model someday
        $template = new CTemplate();
        $html = $template
            ->set('type', $type)
            ->set('id', $identifier)
            ->set('isLoggedIn', $isLoggedIn)
            ->set('postCommentACL', $postCommentACL)
            ->set('groupPermission', $groupPermission)
            ->set('wallViewAll', $wallViewAll)
            ->set('objectName', $objectName)
            ->set('notificationCmd', $notificationCmd)
            ->set('wallContent', $wallContent)
            ->set('currentWallCount', $wallCount)
            ->set('totalPreviousComments', $prevComments)
            ->set('totalDisplayedComments',$totalDisplayedComments)
            ->fetch('comment/thirdparty');

        return $html;
    }
}
