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
?>


<div class="widget-box">
    <div class="well">
        <strong><?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_NOTE');?></strong>:
        <p><?php echo JText::_('COM_COMMUNITY_THEMEPROFILE_PARAMETER_INFO');?></p>
    </div>

    <div class="widget-header widget-header-flat">
        <h5><?php echo JText::_('COM_COMMUNITY_USER_INFO');?></h5>
    </div>
    <div class="widget-body">
        <div class="widget-main">
            <table class="table table-bordered table-hover">
                <thead>
                <tr class="title">
                    <th><?php echo JText::_('COM_COMMUNITY_TEXT_BEFORE');?></th>
                    <th><?php echo JText::_('COM_COMMUNITY_TEXT_FIELD');?></th>
                    <th><?php echo JText::_('COM_COMMUNITY_TEXT_AFTER');?></th>
                    <th><?php echo JText::_('COM_COMMUNITY_TEXT_NEWLINE');?></th>
                </tr>
                </thead>
                <?php

                if(isset($this->settings['profile']) && isset($this->settings['profile']['tagline']) && strlen($this->settings['profile']['tagline'])) {
                    $blocks = json_decode($this->settings['profile']['tagline'], true);
                    foreach ($blocks as $key => $block) {
                        $blocks[$key] = $block;
                    }
                }
                for($i=0;$i<6;$i++) {
                    ?>
                    <tr>
                        <td>
                            <input type="hidden" name="settings[profileSpaceBefore<?php echo $i;?>]" value=""/>
                            <input type="text" name="settings[profileBefore<?php echo $i;?>]" value="<?php
                            echo (isset($blocks[$i]['before'])) ? $blocks[$i]['before'] : "";
                            ?>"/>
                        </td>
                        <td>
                            <select name="settings[profileField<?php echo $i;?>]">
                                <option value=""></option>
                                <?php
                                $group = false;
                                foreach($this->fields as $field) {

                                if($field->type == 'group') {
                                if($group) echo "</optgroup>";
                                ?>
                                <optgroup label="<?php echo $field->name;?>">
                                    <?php
                                    } else {
                                        ?>
                                        <option value="<?php echo $field->id ?>" <?php

                                        if (isset($blocks[$i]['field']) && $blocks[$i]['field'] == $field->id) echo "selected";

                                        ?>>
                                            <?php echo $field->name; ?>
                                        </option>
                                    <?php
                                    }
                                    }
                                    ?>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="settings[profileAfter<?php echo $i;?>]" value="<?php
                            echo (isset($blocks[$i]['after'])) ? $blocks[$i]['after'] : "";
                            ?>"/>
                        </td>
                        <td>
                            <?php
                            if ($i < 5) echo CHTMLInput::checkbox('settings[profileSpaceAfter'.$i.']' ,'ace-switch ace-switch-5', null , isset($blocks[$i]['spaceafter']) ?$blocks[$i]['spaceafter'] : 0, "profileSpaceAfter$i");
                            else echo '<input type="hidden" name="settings[profileSpaceAfter'.$i.']" value="0"/>';

                            ?>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </div>
</div>

<!-- This will be for multiprofile settings -->
<?php foreach($this->multiProfiles as $multiProfile){ ?>
    <div class="space-8"></div>
    <div class="widget-box">
        <div class="widget-header widget-header-flat">
            <h5><?php echo JText::_('COM_COMMUNITY_USER_INFO').' ('.$multiProfile->name.')';?></h5>
        </div>
        <div class="widget-body">
            <div class="widget-main">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr class="title">
                        <th><?php echo JText::_('COM_COMMUNITY_TEXT_BEFORE');?></th>
                        <th><?php echo JText::_('COM_COMMUNITY_TEXT_FIELD');?></th>
                        <th><?php echo JText::_('COM_COMMUNITY_TEXT_AFTER');?></th>
                        <th><?php echo JText::_('COM_COMMUNITY_TEXT_NEWLINE');?></th>
                    </tr>
                    </thead>
                    <?php

                    if(isset($this->settings['profile']) && isset($this->settings['profile'][$multiProfile->id]['tagline']) && strlen($this->settings['profile'][$multiProfile->id]['tagline'])) {
                        $blocks = json_decode($this->settings['profile'][$multiProfile->id]['tagline'], true);
                        foreach ($blocks as $key => $block) {
                            $blocks[$key] = $block;
                        }
                    }else{
                        $blocks = array();
                    }

                    for($i=0;$i<6;$i++) {
                        ?>
                        <tr>
                            <td>
                                <input type="hidden" name="settings[profileSpaceBefore<?php echo $i;?>_<?php echo $multiProfile->id ?>]" value=""/>
                                <input type="text" name="settings[profileBefore<?php echo $i;?>_<?php echo $multiProfile->id ?>]" value="<?php
                                echo (isset($blocks[$i]['before'])) ? $blocks[$i]['before'] : "";
                                ?>"/></td>
                            <td>
                                <select name="settings[profileField<?php echo $i;?>_<?php echo $multiProfile->id ?>]">
                                    <option value=""></option>
                                    <?php
                                    $group = false;
                                    foreach($this->fields as $field) {

                                    if(!in_array($field->id, $this->multiProfilesFields[$multiProfile->id])){
                                        //skip this field if its not enabled in the profile settings
                                        continue;
                                    }
                                    if($field->type == 'group') {
                                    if($group) echo "</optgroup>";
                                    ?>
                                    <optgroup label="<?php echo $field->name;?>">
                                        <?php
                                        } else {
                                            ?>
                                            <option value="<?php echo $field->id ?>" <?php

                                            if (isset($blocks[$i]['field']) && $blocks[$i]['field'] == $field->id) echo "selected";

                                            ?>>
                                                <?php echo $field->name; ?>
                                            </option>
                                        <?php
                                        }
                                        }
                                        ?>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="settings[profileAfter<?php echo $i;?>_<?php echo $multiProfile->id ?>]" value="<?php
                                echo (isset($blocks[$i]['after'])) ? $blocks[$i]['after'] : "";
                                ?>"/>
                            </td>
                            <td>
                                <?php
                                if ($i < 5) echo CHTMLInput::checkbox('settings[profileSpaceAfter'.$i.'_'.$multiProfile->id.']' ,'ace-switch ace-switch-5', null , isset($blocks[$i]['spaceafter']) ?$blocks[$i]['spaceafter'] : 0, "profileSpaceAfter$i");
                                else echo '<input type="hidden" name="settings[profileSpaceAfter'.$i.'_'.$multiProfile->id.']" value="0" />';
                                ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
<?php } ?>
