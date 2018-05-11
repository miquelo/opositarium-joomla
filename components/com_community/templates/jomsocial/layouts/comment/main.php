<?php
/**
 * @copyright (C) 2016 iJoomla, Inc. - All rights reserved.
 * @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author iJoomla.com <webmaster@ijoomla.com>
 * @url https://www.jomsocial.com/license-agreement
 * The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at https://www.jomsocial.com/license-agreement
 */
defined('_JEXEC') or die('Restricted access');

?>
<style>
    .joms-foo,
    .joms-foo * {
        box-sizing: border-box;
    }

    .joms-comment {
        display: block !important;
    }
</style>
<div class="joms-foo">
    <div style="display:none"><?php echo $wallViewAll; ?></div>
    <?php echo $wallContent; ?>
    <?php if ($isLoggedIn) { ?>
    <div class="joms-comment__reply joms-js--newcomment joms-js--newcomment-<?php echo $id; ?>" data-id="<?php echo $id; ?>">
        <div class="joms-textarea__wrapper">
            <div class="joms-textarea joms-textarea__beautifier"></div>
            <textarea class="joms-textarea" name="comment" data-type="<?php echo $type; ?>" data-id="<?php echo $id; ?>" data-func="system,ajaxSaveWall"
                placeholder="<?php echo JText::_('COM_COMMUNITY_WRITE_A_COMMENT'); ?>"></textarea>
            <div class="joms-textarea__loading"><img src="<?php echo JURI::root(true); ?>/components/com_community/assets/ajax-loader.gif" alt="loader" ></div>
            <div class="joms-textarea joms-textarea__attachment">
                <button onclick="joms.view.comment.removeAttachment(this);">×</button>
                <div class="joms-textarea__attachment--loading"><img src="<?php echo JURI::root(true); ?>/components/com_community/assets/ajax-loader.gif" alt="loader" ></div>
                <div class="joms-textarea__attachment--thumbnail"><img src="" alt="attachment"></div>
            </div>
        </div>
        <svg viewBox="0 0 16 16" class="joms-icon joms-icon--add" onclick="joms.view.comment.addAttachment(this);">
            <use xlink:href="<?php echo JUri::getInstance(); ?>#joms-icon-camera"></use>
        </svg>
        <span>
            <button class="joms-button--comment joms-js--btn-send">
                <?php echo JText::_('COM_COMMUNITY_SEND'); ?>
            </button>
        </span>
    </div>
    <?php } ?>
    <script>
        (function( w ) {
            w.joms_prev_comment_load = '<?php echo $totalPreviousComments; ?>';
            w.joms_wall_remove_func = 'system,ajaxStreamRemoveComment';
            w.joms_queue || (w.joms_queue = []);
            w.joms_queue.push(function( $ ) {
                $('.joms-js--comments').prepend( $('.joms-js--more-comments').parent().html() );
            });
        })( window );
    </script>
</div>
