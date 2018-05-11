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
require_once (COMMUNITY_COM_PATH.'/libraries/fields/profilefield.php');
class CFieldsLocation extends CProfileField
{   
    public function getFieldData ($field)
    {   
        $fieldValue = json_decode(html_entity_decode($field['value']), TRUE);

        if (empty($field['value'])) return $field['value'];

        return $fieldValue['name'] . (!empty($fieldValue['desc']) ? ' ('.$fieldValue['desc'].')' : '');
    }

    public function getFieldHTML( $field , $required )
    {
        $params = new CParameter($field->params);
        $readonly = $params->get('readonly') && !COwnerHelper::isCommunityAdmin() ? ' readonly=""' : '';
        $required = ($field->required == 1) ? ' data-required="true"' : '';
        $style = $this->getStyle() ? ' style="' .$this->getStyle() . '"' : '';

        // reformat value

        $fieldName = '';
        $fieldDesc = '';
        $fieldLat = '';
        $fieldLng = '';

        try {
            $fieldValue = json_decode( html_entity_decode( $field->value ), TRUE );
            $fieldName = $fieldValue['name'];
            $fieldDesc = $fieldValue['desc'];
            $fieldLat = $fieldValue['lat'];
            $fieldLng = $fieldValue['lng'];
        } catch (Exception $e) {}

        $html  = '<div class="joms-location__wrapper">';
        $html .= '<input type="text" value="' . $fieldName . '" id="field' . $field->id . '" name="field' . $field->id . '[name]" class="joms-input joms-input--location" autocomplete="off" '. $readonly . $required . $style .' />';
        $html .= '<input type="hidden" class="js-desc" name="field' . $field->id . '[desc]" value="' . $fieldDesc . '"  />';
        $html .= '<input type="hidden" class="js-lat" name="field' . $field->id . '[lat]" value="' . $fieldLat . '" />';
        $html .= '<input type="hidden" class="js-lng" name="field' . $field->id . '[lng]" value="' . $fieldLng . '" />';
        $html .= '<div class="joms-location__description" data-tips="' . JText::_('COM_COMMUNITY_LOCATION_FIELD_DESCRIPTION', TRUE) . '">' . ( $fieldDesc ? $fieldDesc : JText::_('COM_COMMUNITY_LOCATION_FIELD_DESCRIPTION') ) . '</div>';
        $html .= '<div class="joms-location__dropdown">';
        $html .= '<div class="joms-location__loading"><img src="' . JURI::root(true) . '/components/com_community/assets/ajax-loader.gif" alt="loader"></div>';
        $html .= '<div class="joms-location__result">';
        $html .= '<div class="joms-location__header">' . JText::_('Select location') . '</div>';
        $html .= '<div class="joms-location__map"></div>';
        $html .= '<div class="joms-location__list"></div>';
        $html .= '<div class="joms-location__close">&times;</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        $config = CFactory::getConfig();
        if (!$config->get('googleapikey', '')) {
            $html = JText::_("COM_COMMUNITY_FIELD_NO_API_KEY", TRUE);
        } else {
            $document = JFactory::getDocument();
            $document->addScriptDeclaration("joms_gmap_key = '" . $config->get('googleapikey', '') . "';");
        }

        return $html;
    }

    public function isValid( $value , $required )
    {
        $config = CFactory::getConfig();
        if (!$config->get('googleapikey', '')) {
            return true;
        }

        if ( $required ) {
            if ( empty($value) ) {
                return false;
            }
            $value = json_decode( $value, TRUE );
            $name = trim( $value['name'] );
            if ( empty($name) ) {
                return false;
            }
        }

        return true;
    }

    public function formatdata( $value )
    {
        $finalvalue = array(
            'name' => $value['name'],
            'desc' => isset( $value['desc'] ) ? $value['desc'] : '',
            'lat'  => isset( $value['lat'] ) ? $value['lat'] : '',
            'lng'  => isset( $value['lng'] ) ? $value['lng'] : ''
        );

        return json_encode( $finalvalue );
    }
}
