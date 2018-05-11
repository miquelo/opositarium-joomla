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

jimport('joomla.application.component.view');

if (!class_exists('CommunityViewLayouts')) {

    /**
     * Configuration view for JomSocial
     */
    class CommunityViewLayouts extends JViewLegacy {

        public function display($tpl = null) {
            JToolBarHelper::title(JText::_('COM_COMMUNITY_LAYOUTS'));

            // Add the necessary buttons
            JToolBarHelper::apply();
            parent::display($tpl);
        }

        public function getGenderFieldCodes($elementName, $selected = '')
        {
            $db = JFactory::getDBO();
            $query = 'SELECT DISTINCT ' . $db->quoteName('fieldcode') . ' FROM ' . $db->quoteName('#__community_fields') . ' '
                . 'WHERE ' . $db->quoteName('type') . '=' . $db->Quote('gender');

            $db->setQuery($query);
            $fieldcodes = $db->loadObjectList();
            
            $html = '<select name="'. $elementName . '">';

            foreach ($fieldcodes as $fieldcode) {
                if (!empty($fieldcode->fieldcode)) {
                    $selectedData = '';

                    if ($fieldcode->fieldcode == $selected) {
                        $selectedData = ' selected="true"';
                    }
                    
                    $html .= '<option value="' . $fieldcode->fieldcode . '"' . $selectedData . '>' . $fieldcode->fieldcode . '</option>';
                }
            }
            
            $html .= '</select>';

            return $html;
        }
    }
}
