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

jimport('joomla.application.component.view');
jimport('joomla.utilities.arrayhelper');

class CommunityViewChat extends CommunityView
{
    public function display($tpl = null){
        $app = JFactory::getApplication();
        $user = JFactory::getUser();
        $url = JUri::getInstance();
        $encoded_url = base64_encode($url);
        if ($user->guest) {
            $app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.$encoded_url));
        }
        $tmpl = new CTemplate();
        $assets = CAssets::getInstance();
        $assets->addVariable('chat_page_list', $tmpl->fetch('chat/page-list', true));
        $assets->addVariable('chat_window', $tmpl->fetch('chat/window', true));
        $assets->addVariable('is_chat_view', true);
        echo $tmpl->fetch('chat/default');
    }
}
