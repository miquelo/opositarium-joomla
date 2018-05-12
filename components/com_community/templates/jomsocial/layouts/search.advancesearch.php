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
<form id="jsform-search-advancesearch" name="jsform-search-advancesearch" class="js-form joms-form--search" action="<?php echo CRoute::getURI(); ?>" method="GET" onsubmit="advanceSearchUpdateKeyList()">
    <div id="optionContainer">

        <!-- Nombre -->
        <div class="joms-form__group">
            <label>Nombre</label>
            <input type="text" placeholder="Juan" name="value3" />

            <input type="hidden" name="condition3" value="contain" />
            <input type="hidden" name="field3" value="username" />
            <input type="hidden" name="fieldType3" value="text" />
        </div>

        <!-- Oposición -->
        <div class="joms-form__group">
            <label>Oposición</label>
            <div>
                <select name="value0[]">
                    <option value="Jueces y fiscales">Jueces y fiscales</option>
                    <option value="Letrados administración de justicia">Letrados administración de justicia</option>
                    <option value="Cuerpo de gestión">Cuerpo de gestión</option>
                    <option value="Cuerpo de tramitación">Cuerpo de tramitación</option>
                    <option value="Cuerpo de auxilio judicial">Cuerpo de auxilio judicial</option>
                </select>
            </div>
            <input type="hidden" name="condition0" value="equal" />
            <input type="hidden" name="field0" value="FIELD_OPOSITION" />
            <input type="hidden" name="fieldType0" value="list" />
        </div>

        <!-- Tipo de preparación -->
        <div class="joms-form__group">
            <label>Tipo de preparación</label>
            <div>
                <input type="checkbox" name="value1[]" value="Presencial" />
                <span>Presencial</span>

                <input type="checkbox" name="value1[]" value="Online" />
                <span>Online</span>

                <span></span>

                <input type="hidden" name="condition1" value="equal" />
                <input type="hidden" name="field1" value="FIELD_TYPE_PREPARATION" />
                <input type="hidden" name="fieldType1" value="checkbox" />
            </div>
        </div>

        <!-- Tipo de plan de estudio -->
        <div class="joms-form__group">
            <label>Tipo de plan de estudio</label>
            <div>
                <input type="checkbox" name="value2[]" value="Arrastre" />
                <span>Arrastre</span>

                <input type="checkbox" name="value2[]" value="Vueltas" />
                <span>Vueltas</span>

                <input type="checkbox" name="value2[]" value="Mixto" />
                <span>Mixto</span>

                <input type="checkbox" name="value2[]" value="Libre" />
                <span>Libre</span>

                <span></span>
            </div>
            <input type="hidden" name="condition2" value="equal" />
            <input type="hidden" name="field2" value="FIELD_STUDY_PLAN" />
            <input type="hidden" name="fieldType2" value="checkbox" />
        </div>

        <div class="joms-form__group">
            <input type="hidden" name="profiletype" value="<?php echo $profileType; ?>"/>
            <input type="hidden" name="option" value="com_community" />
            <input type="hidden" name="view" value="search" />
            <input type="hidden" name="task" value="advancesearch" />
            <input type="hidden" name="Itemid" value="<?php echo CRoute::getItemId(); ?>" />

            <input type="hidden" name="operator" id="operator_all" value="and">
            <input type="hidden" id="key-list" name="key-list" />

            <input type="submit" class="joms-button--primary joms-right" value="<?php echo JText::_("COM_COMMUNITY_SEARCH_BUTTON_TEMP");?>">
        </div>

        <script>
            function advanceSearchUpdateKeyList() {
                var form = document.getElementById("jsform-search-advancesearch");
                var checkFieldIndexList = [ 1, 2 ];
                var selectFieldIndexList = [ 0 ];
                var textFieldIndexList = [];
                var keyList = [ 3 ];

                for (var i = 0; i < checkFieldIndexList.length; i++) {
                    var checked = false;
                    var index = checkFieldIndexList[i];
                    var name = "value" + index + "[]";
                    for (var j = 0; j < form[name].length; j++) {
                        checked = checked || form[name][j].checked;
                    }
                    if (checked) {
                        keyList.push(index);
                    }
                    else {
                        form["condition" + index].disabled = true;
                        form["field" + index].disabled = true;
                        form["fieldType" + index].disabled = true;
                        form[name].disabled = true;
                    }
                }

                for (var i = 0; i < selectFieldIndexList.length; i++) {
                    var some = false;
                    var index = selectFieldIndexList[i];
                    var select = form["value" + index + "[]"];
                    for (var j = 0; j < select.options.length; j++) {
                        some = some || select.options.item(j).selected;
                    }
                    if (some) {
                        keyList.push(index);
                    }
                    else {
                        form["condition" + index].disabled = true;
                        form["field" + index].disabled = true;
                        form["fieldType" + index].disabled = true;
                        form[name].disabled = true;
                    }
                }

                for (var i = 0; i < textFieldIndexList.length; i++) {
                    var index = textFieldIndexList[i];
                    var name = "value" + index;
                    if (form[name].value.trim().length > 0) {
                        keyList.push(index);
                    }
                    else {
                        form["condition" + index].disabled = true;
                        form["field" + index].disabled = true;
                        form["fieldType" + index].disabled = true;
                        form[name].disabled = true;
                    }
                }

                var keyListValue = "";
                var keyListSeparator = "";
                for (var i = 0; i < keyList.length; i++) {
                    var index = keyList[i];
                    keyListValue = keyListValue + keyListSeparator + index;
                    keyListSeparator = ",";
                }
                form["key-list"].value = keyListValue;
            };
        </script>
    </div>
    <div id="criteriaList" style="clear:both;"></div>
</form>
</div>
