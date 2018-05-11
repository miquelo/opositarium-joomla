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
require_once (COMMUNITY_COM_PATH . '/libraries/fields/profilefield.php');

class CFieldsGender extends CProfileField {

    /**
     * Method to format the specified value for text type
     * */
    public function getFieldData($field) {
        $options = $this->getFieldOptions();
        $value = strtoupper($field['value']);
        if ( !empty($value) && isset($options[$value]) ) {
            return $options[$value];
        } else {
            return '';
        }
    }

    public function getFieldHTML($field, $required) {
        $html = '';
        $selectedElement = 0;
        $required = ($field->required == 1) ? ' data-required="true"' : '';
        $style = ' style="margin: 0 5px 0 0;' . $this->getStyle() . '" ';
        $params = new CParameter($field->params);
        $disabled = '';

        if($params->get('readonly') == 1) {
            $disabled='disabled="disabled"';
        }

        $cnt = 0;

        $html .= '<select class="joms-select" name="field' . $field->id . '" '.$required.' '.$disabled.' >';
        foreach ($this->getFieldOptions() as $key => $val) {
            $selected = ( isset($field->value) && $key == $field->value ) ? ' selected="selected" ' : '';

            $html .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
        }

        $html .= '</select>';

        return $html;
    }

    public function getFieldOptions() {
        return array(
            '' => JText::_('COM_COMMUNITY_SELECT_GENDER'),
            'COM_COMMUNITY_MALE' => JText::_('COM_COMMUNITY_MALE'),
            'COM_COMMUNITY_FEMALE' => JText::_('COM_COMMUNITY_FEMALE')
        );
    }

    public function isValid($value, $required) {
        if ($required && empty($value)) {
            return false;
        }
        return true;
    }

}
