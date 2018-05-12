<?php
/**
* @copyright (C) 2013 iJoomla, Inc. - All rights reserved.
* @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
* @author iJoomla.com <webmaster@ijoomla.com>
* @url https://www.jomsocial.com/license-agreement
* The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
* More info at https://www.jomsocial.com/license-agreement
*/
defined('_JEXEC') or die();

    $config = CFactory::getConfig();
    $lang	= JFactory::getLanguage();
    $lang->load( 'com_community.country',JPATH_ROOT);

?>

<div class="joms-page <?php echo (isset($postresult)&& $postresult) ? 'joms-page--search' : ''; ?>">
    <h3 class="joms-page__title"><?php echo JText::_('COM_COMMUNITY_TITLE_CUSTOM_SEARCH'); ?></h3>
    <?php echo $submenu; ?>

    <?php if ($hasMultiprofile && count($multiprofileArr) > 0) { ?>
    <select class="joms-select" onchange="window.location=this.value;">
        <?php foreach ($multiprofileArr as $key => $value) { ?>
        <option value="<?php echo $value['url']; ?>" <?php if ($value['selected']) echo 'selected="selected"'; ?>>
        <?php echo $value['name']; ?>
        </option>
        <?php } ?>
    </select>
    <?php } ?>

<script>

    joms_tmp_pickadateOpts = {
        format   : 'yyyy-mm-dd',
        firstDay : <?php echo $config->get('event_calendar_firstday') === 'Monday' ? 1 : 0 ?>,
        today    : '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_CURRENT", true) ?>',
        'clear'  : '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_CLEAR", true) ?>'
    };

    joms_tmp_pickadateOpts.weekdaysFull = [
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_DAY_1", true) ?>',
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_DAY_2", true) ?>',
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_DAY_3", true) ?>',
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_DAY_4", true) ?>',
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_DAY_5", true) ?>',
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_DAY_6", true) ?>',
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_DAY_7", true) ?>'
    ];

    joms_tmp_pickadateOpts.weekdaysShort = [];
    for ( i = 0; i < joms_tmp_pickadateOpts.weekdaysFull.length; i++ )
        joms_tmp_pickadateOpts.weekdaysShort[i] = joms_tmp_pickadateOpts.weekdaysFull[i].substr( 0, 3 );

    joms_tmp_pickadateOpts.monthsFull = [
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_1", true) ?>',
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_2", true) ?>',
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_3", true) ?>',
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_4", true) ?>',
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_5", true) ?>',
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_6", true) ?>',
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_7", true) ?>',
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_8", true) ?>',
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_9", true) ?>',
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_10", true) ?>',
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_11", true) ?>',
        '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_12", true) ?>'
    ];

    joms_tmp_pickadateOpts.monthsShort = [];
    for ( i = 0; i < joms_tmp_pickadateOpts.monthsFull.length; i++ )
        joms_tmp_pickadateOpts.monthsShort[i] = joms_tmp_pickadateOpts.monthsFull[i].substr( 0, 3 );

</script>

<!-- advanced search form -->
<form name="jsform-search-advancesearch" class="js-form joms-form--search" action="<?php echo CRoute::getURI(); ?>" method="GET">
    <div id="optionContainer">

        <!-- Oposición -->
        <div class="joms-form__group">
            <label>Jueces y fiscales</label>
            <input type="checkbox" name="value0[]" value="Jueces y fiscales" />
        </div>
        <div class="joms-form__group">
            <label>Letrados administración de justicia</label>
            <input type="checkbox" name="value0[]" value="Letrados administración de justicia" />
        </div>
        <div class="joms-form__group">
            <label>Cuerpo de gestión</label>
            <input type="checkbox" name="value0[]" value="Cuerpo de gestión" />
        </div>
        <div class="joms-form__group">
            <label>Cuerpo de tramitación</label>
            <input type="checkbox" name="value0[]" value="Cuerpo de tramitación" />
        </div>
        <div class="joms-form__group">
            <label>Cuerpo de auxilio judicial</label>
            <input type="checkbox" name="value0[]" value="Cuerpo de auxilio judicial" />
        </div>
        <div class="joms-form__group">
            <input type="hidden" name="condition0" value="equal" />
            <input type="hidden" name="field0" value="FIELD_OPOSITION" />
            <input type="hidden" name="fieldType0" value="list" />
        </div>

        <!-- Tipo de preparación -->
        <div class="joms-form__group">
            <label>Presencial</label>
            <input type="checkbox" name="value1[]" value="Presencial" />
        </div>
        <div class="joms-form__group">
            <label>Online</label>
            <input type="checkbox" name="value1[]" value="Online" />
        </div>
        <div class="joms-form__group">
            <input type="hidden" name="condition1" value="equal" />
            <input type="hidden" name="field1" value="FIELD_TYPE_PREPARATION" />
            <input type="hidden" name="fieldType1" value="checkbox" />
        </div>

        <!-- Tipo de plan de estudio -->
        <div class="joms-form__group">
            <label>Arrastre</label>
            <input type="checkbox" name="value2[]" value="Arrastre" />
        </div>
        <div class="joms-form__group">
            <label>Vueltas</label>
            <input type="checkbox" name="value2[]" value="Vueltas" />
        </div>
        <div class="joms-form__group">
            <label>Mixto</label>
            <input type="checkbox" name="value2[]" value="Mixto" />
        </div>
        <div class="joms-form__group">
            <label>Libre</label>
            <input type="checkbox" name="value2[]" value="Libre" />
        </div>
        <div class="joms-form__group">
            <input type="hidden" name="condition2" value="equal" />
            <input type="hidden" name="field2" value="FIELD_STUDY_PLAN" />
            <input type="hidden" name="fieldType2" value="checkbox" />
        </div>

        <!-- Nombre -->
        <div class="joms-form__group">
            <input type="text" placeholder="Nombre" name="value3" />
        </div>
        <div class="joms-form__group">
            <input type="hidden" name="condition3" value="contain" />
            <input type="hidden" name="field3" value="username" />
            <input type="hidden" name="fieldType3" value="text" />
        </div>

        <div class="joms-form__group">
            <input type="hidden" name="profiletype" value="<?php echo $profileType; ?>"/>
            <input type="hidden" name="option" value="com_community" />
            <input type="hidden" name="view" value="search" />
            <input type="hidden" name="task" value="advancesearch" />
            <input type="hidden" name="Itemid" value="<?php echo CRoute::getItemId(); ?>" />

            <input type="hidden" name="operator" id="operator_all" value="or">
            <input type="hidden" id="key-list" name="key-list" value="0,1,2,3" />

            <input type="submit" class="joms-button--primary joms-right" value="<?php echo JText::_("COM_COMMUNITY_SEARCH_BUTTON_TEMP");?>">
        </div>
    </div>
    <div id="criteriaList" style="clear:both;"></div>
</form>
</div>
