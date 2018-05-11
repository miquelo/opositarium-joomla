<?php

/**
 * @copyright (C) 2013 iJoomla, Inc. - All rights reserved.
 * @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author iJoomla.com <webmaster@ijoomla.com>
 * @url https://www.jomsocial.com/license-agreement
 * The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at https://www.jomsocial.com/license-agreement
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class CommunityRegisterinviteController extends CommunityBaseController
{   
    public function display($cacheable = false, $urlparams = false)
    {
        $this->registerinvite();
    }

    public function registerinvite()
    {
        $my = CFactory::getUser();

        if ($my->id != 0) {
            $mainframe = JFactory::getApplication();
            $mainframe->redirect(CRoute::_('index.php?option=com_community&view=frontpage', false));
        }

        //run this silently to clean up the 'left-over' temp user.
        $rModel = CFactory::getModel('register');
        $rModel->cleanTempUser();

        $view = $this->getView('registerinvite');
        echo $view->get('registerinvite');
    }

    public function registerinvite_save()
    {
        // Get required system objects
        $mainframe = JFactory::getApplication();
        $jinput = $mainframe->input;
        $config = CFactory::getConfig();
        $post = $jinput->post->getArray();
        $view = $this->getView('registerinvite');

        // If user registration is not allowed, show 403 not authorized unless invitation only is enabled
        $usersConfig = JComponentHelper::getParams('com_users');
        if ($usersConfig->get('allowUserRegistration') == '1' && !$config->get('invite_only_request')) {
            //show warning message
            $this->addWarning(JText::_('COM_COMMUNITY_REQUEST_INVITE_DISABLED'));
            echo $view->get('registerinvite');
            return;
        }

        //perform forms validation before continue further.
        $errMsg = $this->_validateRequestInvite($post);

        if (count($errMsg) > 0) {
            //validation failed. show error message.
            foreach ($errMsg as $err) {
                $mainframe->enqueueMessage($err, 'error');
            }

            $this->registerinvite();
            return false;
        }

        // @rule: check with recaptcha
        $recaptcha = new CRecaptchaHelper();

        if (!$recaptcha->verify()) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_COMMUNITY_RECAPTCHA_MISMATCH'), 'error');
            $this->registerinvite();
            return false;
        }

        $registerintiveTable = JTable::getInstance('RegisterInvite', 'CommunityTable');
        $registerintiveTable->name = $post['jsname'];
        $registerintiveTable->email = $post['jsemail'];
        $registerintiveTable->reason = $post['jsreason'];
        $registerintiveTable->status = 0;
        $registerintiveTable->actionby = 0;

        if ($registerintiveTable->store()) {
            // skip avatar upload
            $mainframe->redirect(CRoute::_('index.php?option=com_community&view=registerinvite&task=registerinvitesuccess', false));
        }
        
    }

    public function registerinvitesuccess()
    {
        $view = $this->getView('registerinvite');
        echo $view->get(__FUNCTION__);
    }

    /**
     * Validate request invitation form
     */
    private function _validateRequestInvite($post = array())
    {
        //check the user infor
        $mainframe = JFactory::getApplication();
        $config = CFactory::getConfig();
        $errMsg = array();
        $data = array();

        if (!empty($post)) {
            //manual array_walk to trim
            foreach ($post as $key => $value) {
                if (is_array($value)) {
                    $data[$key] = $value; // dun do anything here.
                } else {
                    $data[$key] = CStringHelper::trim($value);
                }
            } //end of
        }

        if (empty($data['jsname'])) {
            $errMsg[] = JText::_('COM_COMMUNITY_FIELD_ENTRY') . ' \'' . JText::_(
                    'COM_COMMUNITY_NAME'
                ) . '\' ' . JText::_('COM_COMMUNITY_IS_EMPTY');
        }

        if (empty($data['jsemail'])) {
            $errMsg[] = JText::_('COM_COMMUNITY_FIELD_ENTRY') . ' \'' . JText::_(
                    'COM_COMMUNITY_EMAIL'
                ) . '\' ' . JText::_('COM_COMMUNITY_IS_EMPTY');
        }

        if (empty($data['jsreason'])) {
            $errMsg[] = JText::_('COM_COMMUNITY_FIELD_ENTRY') . ' \'' . JText::_(
                    'COM_COMMUNITY_REQUEST_INVITE_REASON_FIELD'
                ) . '\' ' . JText::_('COM_COMMUNITY_IS_EMPTY');
        }

        return $errMsg;
    }
}
