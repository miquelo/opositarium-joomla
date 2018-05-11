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
?>
<div class="joms-page">
    <h3 class="joms-page__title">
        <?php echo JText::_('COM_COMMUNITY_REQUEST_INVITE'); ?>
    </h3>
    <div class="joms-form__group">
        <p>
            <?php
                $jglobalconfig = JFactory::getApplication();
                echo JText::sprintf('COM_COMMUNITY_REQUEST_INVITE_DESC', $jglobalconfig->get('sitename')); 
            ?>
        </p>
    </div>
    <form method="POST" action="<?php echo CRoute::getURI(); ?>" onsubmit="return joms_validate_form( this );">
        <div class="joms-form__group">
            <span><?php echo JText::_('COM_COMMUNITY_NAME'); ?> <span class="joms-required">*</span></span>
            <input type="text" name="jsname" value="<?php echo $data['html_field']['jsname']; ?>" class="joms-input" data-required="true">
        </div>
        <div class="joms-form__group">
            <span><?php echo JText::_('COM_COMMUNITY_EMAIL'); ?> <span class="joms-required">*</span></span>
            <input type="text" id="jsemail" name="jsemail" value="<?php echo $data['html_field']['jsemail']; ?>" class="joms-input" data-required="true" data-validation="email">
        </div>
        <div class="joms-form__group">
            <span><?php echo JText::_('COM_COMMUNITY_REQUEST_INVITE_REASON_FIELD'); ?> <span class="joms-required">*</span></span>
            <textarea id="jsreason" name="jsreason" class="joms-textarea" data-required="true"><?php echo $data['html_field']['jsreason']; ?></textarea>
        </div>

        <?php if ( !empty($recaptchaHTML) ) { ?>
            <div class="joms-form__group">
                <span></span>
                <?php echo $recaptchaHTML; ?>
            </div>
        <?php } ?>

        <div class="joms-form__group">
            <span></span>
            <?php echo JText::_('COM_COMMUNITY_REGISTER_REQUIRED_FIELDS'); ?>
        </div>
        <div class="joms-form__group">
            <span></span>
            <input type="hidden" name="task" value="registerinvite_save">
            <?php echo JHTML::_('form.token'); ?>
            <button type="submit" name="submit" class="joms-button--primary joms-button--full-small">
                <?php echo JText::_('COM_COMMUNITY_SEND'); ?>
                <span class="joms-loading" style="display:none">&nbsp;
                    <img src="<?php echo JURI::root(true) ?>/components/com_community/assets/ajax-loader.gif" alt="loader">
                </span>
            </button>
        </div>
    </form>
</div>