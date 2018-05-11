<?php
/**
 * @copyright (C) 2017 JoomlaArt, Inc. - All rights reserved.
 * @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author iJoomla.com <webmaster@ijoomla.com>
 * @url https://www.jomsocial.com/license-agreement
 * The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at https://www.jomsocial.com/license-agreement
 */
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
require_once (JPATH_ROOT.'/administrator/components/com_community/models/migratorEasySocial.php');
require_once (JPATH_ROOT.'/administrator/components/com_community/models/migratorCB.php');

/**
 * Migrators view for JomSocial
 */
class CommunityViewMigrators extends JViewLegacy
{
    /**
     * The default method that will display the output of this view which is called by
     * Joomla
     *
     * @param    string template    Template file name
     **/
    public function display($tpl = null)
    {

        $lang = JFactory::getLanguage();
        $lang->load('com_community.country', JPATH_ROOT);

        JToolBarHelper::title(JText::_('COM_COMMUNITY_MIGRATORS'), 'configuration');
        
        $modelES      = new CommunityModelMigratorEasySocial();
        $ESExist = $modelES->checkTableExistEasySocial();
        $this->set( 'ESExist' , $ESExist );

        $modelCB      = new CommunityModelMigratorCB();
        $CBExist = $modelCB->checkTableExistCB();
        $this->set( 'CBExist' , $CBExist );
       
        parent::display($tpl);
    }

    public function loadLayout()
    {
        parent::display('layout');
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
        // Set the titlebar text
        JToolBarHelper::title(JText::_('COM_COMMUNITY_MIGRATORS'), 'community');
    }
}
