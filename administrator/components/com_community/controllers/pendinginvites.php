<?php
/**
* @copyright (C) 2013 iJoomla, Inc. - All rights reserved.
* @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
* @author iJoomla.com <webmaster@ijoomla.com>
* @url https://www.jomsocial.com/license-agreement
* The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
* More info at https://www.jomsocial.com/license-agreement
*/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

require_once( JPATH_ROOT . '/components/com_community/libraries/core.php' );

/**
 * JomSocial Component Controller
 */
class CommunityControllerPendinginvites extends CommunityController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function display( $cachable = false, $urlparams = array() )
	{
        $jinput = JFactory::getApplication()->input;
		$viewName = $jinput->get( 'view' , 'community' );

		// Set the default layout and view name
		$layout	= $jinput->get( 'layout' , 'default' );

		// Get the document object
		$document = JFactory::getDocument();

		// Get the view type
		$viewType = $document->getType();

		// Get the view
		$view = $this->getView( $viewName , $viewType );

		$model = $this->getModel( $viewName );

		if($model) {
			$view->setModel( $model , $viewName );
		}

		// Set the layout
		$view->setLayout( $layout );

		// Display the view
		$view->display();

		// Display Toolbar. View must have setToolBar method
		if( method_exists($view , 'setToolBar')) {
			$view->setToolBar();
		}
	}

	public function approveselected()
	{
		$mainframe = JFactory::getApplication();
		$jinput = $mainframe->input;
        $my = CFactory::getUser();
		$ids = $jinput->get('cid',array(),'Array');
        
		foreach($ids as $id) {
            $table = JTable::getInstance('RegisterInvite', 'CommunityTable');
			$table->load($id);
			$table->status = 1;
            $table->actionby = $my->id;

            if ($table->store()) {
                $this->_sendinvitationemail($id);
            }
		}

		$search = $jinput->get('search', '', 'String');
		$status	= $jinput->get('status', '0', 'String');

		$url = 'index.php?option=com_community&view=pendinginvites&search='.$search.'&status='.$status;
		$message = JText::_('COM_COMMUNITY_PENDING_INVITE_APPROVED');
		$mainframe->redirect($url, $message, 'message');
	}

    public function approveall()
    {   
        $db = JFactory::getDBO();
        $mainframe = JFactory::getApplication();
        $jinput = $mainframe->input;
        $my = CFactory::getUser();

        $query = 'SELECT * FROM ' . $db->quoteName('#__community_user_invites') . ' AS a '
            . 'WHERE status = ' . $db->Quote(0) . ' '
            . 'GROUP BY a.id';

        $db->setQuery($query);
        $results = $db->loadObjectList();
        
        foreach($results as $row) {
            $table = JTable::getInstance('RegisterInvite', 'CommunityTable');
            $table->load($row->id);
            $table->status = 1;
            $table->actionby = $my->id;

            if ($table->store()) {
                $this->_sendinvitationemail($row->id);
            }
        }

        $search = $jinput->get('search', '', 'String');
        $status = $jinput->get('status', '0', 'String');

        $url = 'index.php?option=com_community&view=pendinginvites&search='.$search.'&status='.$status;
        $message = JText::_('COM_COMMUNITY_PENDING_INVITE_APPROVED');
        $mainframe->redirect($url, $message, 'message');
    }

    public function rejectselected()
    {
        $mainframe = JFactory::getApplication();
        $jinput = $mainframe->input;
        $my = CFactory::getUser();
        $ids = $jinput->get('cid',array(),'Array');
        
        foreach($ids as $id) {
            $table = JTable::getInstance('RegisterInvite', 'CommunityTable');
            $table->load($id);
            $table->status = 2;
            $table->actionby = $my->id;
            $table->store();
        }

        $search = $jinput->get('search', '', 'String');
        $status = $jinput->get('status', '0', 'String');

        $url = 'index.php?option=com_community&view=pendinginvites&search='.$search.'&status='.$status;
        $message = JText::_('COM_COMMUNITY_PENDING_INVITE_REJECTED');
        $mainframe->redirect($url, $message, 'message');
    }

    public function rejectall()
    {   
        $db = JFactory::getDBO();
        $mainframe = JFactory::getApplication();
        $jinput = $mainframe->input;
        $my = CFactory::getUser();

        $query = 'SELECT * FROM ' . $db->quoteName('#__community_user_invites') . ' AS a '
            . 'WHERE status = ' . $db->Quote(0) . ' '
            . 'GROUP BY a.id';

        $db->setQuery($query);
        $results = $db->loadObjectList();
        
        foreach($results as $row) {
            $table = JTable::getInstance('RegisterInvite', 'CommunityTable');
            $table->load($row->id);
            $table->status = 2;
            $table->actionby = $my->id;
            $table->store();
        }

        $search = $jinput->get('search', '', 'String');
        $status = $jinput->get('status', '0', 'String');

        $url = 'index.php?option=com_community&view=pendinginvites&search='.$search.'&status='.$status;
        $message = JText::_('COM_COMMUNITY_PENDING_INVITE_REJECTED');
        $mainframe->redirect($url, $message, 'message');
    }

    public function ajaxPerformAction($actionId, $status = 0 )
    {
        $objResponse = new JAXResponse();
        $output = '';

        // Require Jomsocial core lib
        require_once( JPATH_ROOT . '/components/com_community/libraries/core.php' );

        $language = JFactory::getLanguage();
        $language->load('com_community', JPATH_ROOT);
        $my = CFactory::getUser();

        $table = JTable::getInstance('RegisterInvite', 'CommunityTable');
        $table->load($actionId);
        $table->status = $status;
        $table->actionby = $my->id;
        
        if ($table->store() && $status == 1) {
            $this->_sendinvitationemail($actionId);
        }

        $actions    =   '<input type="button" class="btn btn-inverse btn-mini pull-left" onclick="cWindowHide(); location.reload();" value="' . JText::_('COM_COMMUNITY_CLOSE') . '"/>';

        if($status == 1) {
            $output = JText::_('COM_COMMUNITY_PENDING_INVITE_APPROVED');    
        } else if($status == 2) {
            $output = JText::_('COM_COMMUNITY_PENDING_INVITE_REJECTED');  
        }

        $objResponse->addAssign( 'cWindowContent' , 'innerHTML' , $output);
        $objResponse->addScriptCall('cWindowActions', $actions);

        return $objResponse->sendResponse();
    }

    private function _sendinvitationemail($id = 0)
    {   
        $table = JTable::getInstance('RegisterInvite', 'CommunityTable');
        
        if ($table->load($id)) {
            $my = CFactory::getUser();
            $config = CFactory::getConfig();
            $jglobalconfig = JFactory::getApplication();

            $sitename = $jglobalconfig->get('sitename'); 
            $email = $table->email;
            $name = $table->name;

            $templateFile = 'email.request.invite';
            $templateFile .= $config->get('htmlemail') ? '.html' : '.text';

            $tmpl = new CTemplate();
            $tmpl->set('displayName', $name);
            $tmpl->set('sitename', $sitename);

            $content = $tmpl->fetch($templateFile);

            $params = new CParameter('');
            $params->set('url', 'index.php?option=com_community&view=register&inv_only_id='.$id.'&email='.$email);

            $mailq = CFactory::getModel('Mailq');
            $mailq->add(
                $email,
                JText::sprintf('COM_COMMUNITY_REQUEST_INVITE_EMAIL_SUBJECT', $sitename),
                $content,
                $templateFile, 
                $params, 
                0, 
                'etype_request_invite'
            );

            return true;
        }

        return false;
    }
}
