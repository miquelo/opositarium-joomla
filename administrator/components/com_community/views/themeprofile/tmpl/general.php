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
    <div class="widget-header widget-header-flat">
        <h5><?php echo JText::_('COM_COMMUNITY_CONFIGURATION_TITLE_PAGE_SETTINGS') ?></h5>
    </div>
    <div class="widget-body">
        <div class="widget-main">
            <table cellspacing="5" cellpadding="5">
                <tr>
                    <td class="key" width="120"><?php echo JText::_('COM_COMMUNITY_PREFERENCE_DEFAULT_TAB'); ?></td>
                    <td>
                        <select name="config[default_profile_tab]">
                            <option value="0"><?php echo JText::_('COM_COMMUNITY_PREFERENCE_ACTIVITY_STREAM'); ?></option>
                            <option value="1" <?php echo (CFactory::getConfig()->get('default_profile_tab')) ? 'selected' : ''?>><?php echo JText::_('COM_COMMUNITY_PREFERENCE_ACTIVITY_ABOUT_ME'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="key">
                        <span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_ACTIVITY_LIMIT_TIPS'); ?>">
                            <?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_TITLE_ACTIVITY_LIMIT' ); ?>
                        </span>
                    </td>
                    <td>
                        <input type="text" class="input-small" name="config[activityLimit]"  value="<?php echo $this->config->get('activityLimit'); ?>"  />
                    </td>
                </tr>
                <tr>
                    <td class="key">
						<span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_ENABLE_PROFILE_CARD_TIPS'); ?>">
							<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ENABLE_PROFILE_CARD' ); ?>
						</span>
                    </td>
                    <td>
                        <?php echo CHTMLInput::checkbox('config[show_profile_card]' ,'ace-switch ace-switch-5', null , CFactory::getConfig()->get('show_profile_card') ); ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="space-8"></div>
<div class="row-fluid">
    <div class="span24">
        <div class="widget-box">
            <div class="widget-header widget-header-flat">
                <h5><?php echo JText::_('COM_COMMUNITY_THEMEPROFILE_DEFAULT_IMAGES');?></h5>
            </div>
            <div class="widget-body">
                <div class="widget-main">
                    <table cellspacing="5" cellpadding="5">
                        <tr>
                            <td class="key">
                                    <span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_THEMEPROFILE_ENABLE_GRAVATAR_TIPS'); ?>">
                                        <?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_THEMEPROFILE_ENABLE_GRAVATAR' ); ?>
                                    </span>
                            </td>
                            <td>
                                <?php echo CHTMLInput::checkbox('config[use_gravatar]' ,'ace-switch ace-switch-5', null , CFactory::getConfig()->get('use_gravatar') ); ?>
                            </td>
                        </tr>
                    </table>
                    <table cellspacing="5" cellpadding="5">
                        <thead>
                        <tr>
                            <th>
                            </th>
                            <th>
                                <?php echo JText::_('COM_COMMUNITY_DEFAULT_COVER');?>
                            </th>
                            <th>
                                <?php echo JText::_('COM_COMMUNITY_DEFAULT_AVATAR');?>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="key" width="10%" valign="top">
                                <?php echo JText::_('COM_COMMUNITY_MALE');?>
                            </td>
                            <td>
                                <?php
                                $image = JURI::root() . 'components/com_community/assets/cover-male-default.png';
                                $imagePath = COMMUNITY_PATH_ASSETS;
                                if(isset($this->settings['profile']['default-cover-male']) && file_exists($imagePath.'/default-cover-male.'.$this->settings['profile']['default-cover-male']))
                                    $image = JUri::root().str_replace(JPATH_ROOT,'',COMMUNITY_PATH_ASSETS).'default-cover-male.'.$this->settings['profile']['default-cover-male'];
                                ?><img src="<?=$image;?>?ts=<?php echo time();?>" alt="" class="preview-cover">
                            </td>
                            <td width="45%" valign="top">
                                <?php
                                $image = JURI::root() . 'components/com_community/assets/user-Male.png';
                                $thumb = JURI::root() . 'components/com_community/assets/user-Male-thumb.png';

                                if(isset($this->settings['profile']['default-male-avatar']) && file_exists($imagePath.'/default-male-avatar.'.$this->settings['profile']['default-male-avatar'])) {
                                    $image = JUri::root() . str_replace(JPATH_ROOT, '', COMMUNITY_PATH_ASSETS) . 'default-male-avatar.' . $this->settings['profile']['default-male-avatar'];
                                    $thumb = JUri::root() . str_replace(JPATH_ROOT, '', COMMUNITY_PATH_ASSETS) . 'default-male-avatar-thumb.' . $this->settings['profile']['default-male-avatar'];
                                }
                                ?><img src="<?=$image;?>?ts=<?php echo time();?>" alt="" class="preview-avatar">
                                <img src="<?=$thumb;?>?ts=<?php echo time();?>" alt="" class="preview-avatar-thumb">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <div class="space-6"></div>
                                <input type="file" name="default-cover-male-new" id="default-cover-male-new">
                                <input type="hidden" name="settings[default-cover-male]" value="<?php echo isset($this->settings['profile']['default-cover-male']) ? $this->settings['profile']['default-cover-male'] : "";?>" />
                            </td>
                            <td>
                                <div class="space-6"></div>
                                <input type="file" name="default-male-avatar-new" id="default-male-avatar-new">
                                <input type="hidden" name="settings[default-male-avatar]" value="<?php echo isset($this->settings['profile']['default-male-avatar']) ? $this->settings['profile']['default-male-avatar'] : "";?>" />
                            </td>
                        </tr>
                        <tr>
                            <td class="key" valign="top">
                                <?php echo JText::_('COM_COMMUNITY_FEMALE');?>
                            </td>
                            <td>
                                <?php
                                $image = JURI::root() . 'components/com_community/assets/cover-female-default.png';
                                if(isset($this->settings['profile']['default-cover-female']) && file_exists($imagePath.'/default-cover-female.'.$this->settings['profile']['default-cover-female'])) {
                                    $image = JUri::root().str_replace(JPATH_ROOT,'',COMMUNITY_PATH_ASSETS).'default-cover-female.'.$this->settings['profile']['default-cover-female'];
                                }
                                ?>
                                <img src="<?=$image;?>?ts=<?php echo time();?>" alt=""  class="preview-cover">
                            </td>
                            <td>
                                <?php
                                $image = JURI::root() . 'components/com_community/assets/user-Female.png';
                                $thumb = JURI::root() . 'components/com_community/assets/user-Female-thumb.png';
                                if(isset($this->settings['profile']['default-female-avatar']) && file_exists($imagePath.'/default-female-avatar.'.$this->settings['profile']['default-female-avatar'])) {
                                    $image = JUri::root().str_replace(JPATH_ROOT,'',COMMUNITY_PATH_ASSETS).'default-female-avatar.'.$this->settings['profile']['default-female-avatar'];
                                    $thumb = JUri::root().str_replace(JPATH_ROOT,'',COMMUNITY_PATH_ASSETS).'default-female-avatar-thumb.'.$this->settings['profile']['default-female-avatar'];
                                }
                                ?>
                                <img src="<?=$image;?>?ts=<?php echo time();?>" alt="" class="preview-avatar">
                                <img src="<?=$thumb;?>?ts=<?php echo time();?>" alt="" class="preview-avatar-thumb">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                        <td>
                            <div class="space-6"></div>
                            <input type="file" name="default-cover-female-new" id="default-cover-female-new">
                            <input type="hidden" name="settings[default-cover-female]" value="<?php echo isset($this->settings['profile']['default-cover-female']) ? $this->settings['profile']['default-cover-female'] : "";?>" />
                        </td>
                        <td>
                            <div class="space-6"></div>
                            <input type="file" name="default-female-avatar-new" id="default-female-avatar-new">
                            <input type="hidden" name="settings[default-female-avatar]" value="<?php echo isset($this->settings['profile']['default-female-avatar']) ? $this->settings['profile']['default-female-avatar'] : "";?>" />
                        </td>
                        <tr>
                            <td class="key" valign="top">
                                <?php echo JText::_('COM_COMMUNITY_UNDEFINED');?>
                            </td>
                            <td>
                                <?php
                                $image = JURI::root() . 'components/com_community/assets/cover-undefined-default.png';
                                if(isset($this->settings['profile']['default-cover']) && file_exists($imagePath.'/default-cover.'.$this->settings['profile']['default-cover'])) {
                                    $image = JUri::root().str_replace(JPATH_ROOT,'',COMMUNITY_PATH_ASSETS).'default-cover.'.$this->settings['profile']['default-cover'];
                                }
                                ?>
                                <img src="<?=$image;?>?ts=<?php echo time();?>" alt=""  class="preview-cover">
                                <div class="space-6"></div>
                                <input type="file" name="default-cover-new" id="default-cover-new">
                                <input type="hidden" name="settings[default-cover]" value="<?php echo isset($this->settings['profile']['default-cover']) ? $this->settings['profile']['default-cover'] : "";?>" />
                            </td>
                            <td>
                                <?php
                                $image = JURI::root() . 'components/com_community/assets/user-Male.png';
                                $thumb = JURI::root() . 'components/com_community/assets/user-Male-thumb.png';
                                if(isset($this->settings['profile']['default-general-avatar']) && file_exists($imagePath.'/default-general-avatar.'.$this->settings['profile']['default-general-avatar'])) {
                                    $image = JUri::root().str_replace(JPATH_ROOT,'',COMMUNITY_PATH_ASSETS).'default-general-avatar.'.$this->settings['profile']['default-general-avatar'];
                                    $thumb = JUri::root().str_replace(JPATH_ROOT,'',COMMUNITY_PATH_ASSETS).'default-general-avatar-thumb.'.$this->settings['profile']['default-general-avatar'];
                                }
                                ?>
                                <img src="<?=$image;?>?ts=<?php echo time();?>" alt="" class="preview-avatar">
                                <img src="<?=$thumb;?>?ts=<?php echo time();?>" alt="" class="preview-avatar-thumb">
                                <div class="space-6"></div>
                                <input type="file" name="default-general-avatar-new" id="default-general-avatar-new">
                                <input type="hidden" name="settings[default-general-avatar]" value="<?php echo isset($this->settings['profile']['default-general-avatar']) ? $this->settings['profile']['default-general-avatar'] : "";?>" />
                            </td>
                        </tr>


                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="span12">

    </div>
</div>