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

/**
 * Configuration view for JomSocial
 */
class CommunityViewThemeprofile extends JViewLegacy
{
    public function display( $tpl = null )
    {
        // Set the titlebar text
        JToolBarHelper::title( JText::_('COM_COMMUNITY_CONFIGURATION_THEME_PROFILE'), 'profile');
        JToolBarHelper::apply();
        JToolBarHelper::cancel();
        JToolBarHelper::custom('reset','undo-2','',JText::_('COM_COMMUNITY_THEME_GENERAL_RESET'),false);

        // Get Moods by type (preset & custom)
        $scssTable= JTable::getInstance( 'Theme' , 'CommunityTable' );
        $this->set('settings', $scssTable->getByKey('settings'));
        $profile = $this->getModel('Profiles');
        $multiProfilesModel = new CommunityModelMultiProfile();

        $fields  = $profile->getFields();
        $config = CFactory::getConfig();

        //we should cater for different profile type
        foreach($fields as $field) {
            $fieldsById[$field->id] = $field;
        }

        $multiProfiles = $multiProfilesModel->getMultiProfiles();
        $multiProfilesFields = array();

        //all the fields from each multiprofile
        foreach($multiProfiles as $profile){
            $fieldsID = $multiProfilesModel->getFieldsByProfileID($profile->id);
            foreach($fieldsID as $field){
                $multiProfilesFields[$profile->id][] = $field;
            }
        }

        $this->config = $config;
        $this->set('fields', $fields);
        $this->set('fieldsById', $fieldsById);
        $this->set('multiProfiles', $multiProfiles);
        $this->set('multiProfilesFields', $multiProfilesFields);
        parent::display( $tpl );
    }
}