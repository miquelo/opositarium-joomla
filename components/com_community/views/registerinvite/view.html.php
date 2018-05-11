<?php

/**
 * @copyright (C) 2013 iJoomla, Inc. - All rights reserved.
 * @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author iJoomla.com <webmaster@ijoomla.com>
 * @url https://www.jomsocial.com/license-agreement
 * The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at https://www.jomsocial.com/license-agreement
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');
jimport('joomla.utilities.arrayhelper');
jimport('joomla.html.html');

if (!class_exists('CommunityViewRegisterinvite')) {

    class CommunityViewRegisterinvite extends CommunityView {

        public function registerinvite($data = null) {
            $jinput = JFactory::getApplication()->input;
            $mainframe = JFactory::getApplication();
            $my = CFactory::getUser();

            $config = CFactory::getConfig();
            /**
             * Opengraph
             */
            CHeadHelper::setType('website', JText::_('COM_COMMUNITY_REQUEST_INVITE_WEB_TITLE'));

            // Hide this form for logged in user
            if ($my->id) {
                $mainframe->enqueueMessage(JText::_('COM_COMMUNITY_REGISTER_ALREADY_USER'), 'warning');
                return;
            }

            // If user registration is not allowed, show 403 not authorized unless invitation only is enabled
            $usersConfig = JComponentHelper::getParams('com_users');
            if ($usersConfig->get('allowUserRegistration') == '1' || !$config->get('invite_only_request')) {
                //show warning message
                $this->addWarning(JText::_('COM_COMMUNITY_REQUEST_INVITE_DISABLED'));
                return;
            }

            $fields = array();
            $post = $jinput->post->getArray();

            $data = array();
            $data['fields'] = $fields;
            $data['html_field']['jsname'] = (empty($post['jsname'])) ? '' : $post['jsname'];
            $data['html_field']['jsemail'] = (empty($post['jsemail'])) ? '' : $post['jsemail'];
            $data['html_field']['jsreason'] = (empty($post['jsreason'])) ? '' : $post['jsreason'];

            $recaptcha = new CRecaptchaHelper();
            $recaptchaHTML = $recaptcha->html();

            $tmpl = new CTemplate();
            $content = $tmpl->set('data', $data)
                    ->set('recaptchaHTML', $recaptchaHTML)
                    ->set('config', $config)
                    ->fetch('register/registerinvite');

            echo $content;
        }

        public function registerinvitesuccess() {
            /**
             * Opengraph
             */
            CHeadHelper::setType('website', JText::_('COM_COMMUNITY_REQUEST_INVITE_WEB_TITLE'));

            $mainframe = JFactory::getApplication();
            $jinput = $mainframe->input;

            $tmpl = new CTemplate();
            echo $tmpl->fetch('register/registerinvitesuccess');
        }

    }

}
