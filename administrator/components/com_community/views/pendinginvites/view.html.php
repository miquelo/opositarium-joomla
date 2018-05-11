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

jimport( 'joomla.application.component.view' );

require_once( JPATH_ROOT . '/components/com_community/libraries/core.php' );
require_once( JPATH_ROOT . '/components/com_community/libraries/apps.php' );
require_once( JPATH_ROOT . '/components/com_community/libraries/profile.php' );

/**
 * Configuration view for JomSocial
 */
class CommunityViewPendingInvites extends JViewLegacy
{
	/**
	 * The default method that will display the output of this view which is called by
	 * Joomla
	 *
	 * @param	string template	Template file name
	 **/
	public function display( $tpl = null )
	{
		// Trigger load default library.
		CAssets::getInstance();

		$mainframe = JFactory::getApplication();
		$jinput = $mainframe->input;

		// Set the titlebar text
		JToolBarHelper::title( JText::_('COM_COMMUNITY_PENDING_INVITATIONS'), 'pendinginvites' );

		// Add action buttons
		JToolBarHelper::custom('approveselected', 'publish', 'approve', JText::_('COM_COMMUNITY_APPROVE_SELECTED'));
		JToolBarHelper::custom('approveall', 'publish', 'approveall', JText::_('COM_COMMUNITY_APPROVE_ALL'), false);
		JToolBarHelper::custom('rejectselected', 'unpublish', 'reject', JText::_('COM_COMMUNITY_REJECT_SELECTED'));
		JToolBarHelper::custom('rejectall', 'unpublish', 'rejectall', JText::_('COM_COMMUNITY_REJECT_ALL'), false);

		$search	= $mainframe->getUserStateFromRequest('com_community.pendinginvite.search', 'search', '', 'string');
		$model	= $this->getModel('Pendinginvites');
		$invites	= $model->getAllInvites();
		$pagination	= $model->getPagination();

		$filter_order = $mainframe->getUserStateFromRequest('com_community.pendinginvite.filter_order', 'filter_order', 'created', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_community.pendinginvite.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word' );
		
		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']	= $filter_order;

        $session = JFactory::getSession();

		$this->set('search', $search);
		$this->set('invites', $invites);
		$this->set('lists', $lists);
		$this->set('pagination', $pagination);

		parent::display($tpl);
	}

	public function _getStatusHTML()
	{
        $jinput = JFactory::getApplication()->input;
		$session = JFactory::getSession();
        $status = $jinput->getInt('status', $session->get('pendinginvite_status_filter', 0));

		$select	= '<select class="no-margin" name="status" onchange="submitform();">';
		
		$statusArray = array(3 => JText::_('COM_COMMUNITY_VIEW_ALL'), 1 => JText::_('COM_COMMUNITY_APPROVED'), 0 => JText::_('COM_COMMUNITY_PENDING'), 2 => JText::_('COM_COMMUNITY_REJECTED'));

		foreach($statusArray as $key=>$array)
		{
			$selected = ($status == $key) ? 'selected="true"' : '';
			$select .='<option value="'.$key.'"'.$selected.' >'.JText::_($array).'</option>';
		}

		$select	.= '</select>';

		return $select;
	}

	/**
	 * Private method to set the toolbar for this view
	 *
	 * @access private
	 *
	 * @return null
	 **/
	public function setToolBar()
	{

	}
}
