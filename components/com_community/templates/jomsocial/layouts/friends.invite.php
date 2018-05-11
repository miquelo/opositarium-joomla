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

<!--COMMUNITY FORM-->
<div class="joms-page">
    <h3 class="joms-page__title"><?php echo JText::_('COM_COMMUNITY_INVITE_FRIENDS')?></h3>
    <div class="joms-gap"></div>

    <?php if ($isLimit && $limit <= 0) { ?>

    <div class="alert alert-error"><?php echo JText::_('COM_COMMUNITY_REGISTRATION_REACHED_MAXIMUM_INVITES') ?></div>

    <?php } else { ?>

    <form name="jsform-friends-invite" action="<?php echo CRoute::getURI(); ?>" method="post" class="js-form">

        <div class="joms-form__group">
            <p><?php echo JText::_('COM_COMMUNITY_INVITE_TEXT'); ?></p>
        </div>

        <?php if ($beforeFormDisplay) { ?>
        <div class="joms-form__group"><?php echo $beforeFormDisplay; ?></div>
        <?php } ?>

        <!--
        <div class="joms-form__group">
            <span><?php echo JText::_('COM_COMMUNITY_INVITE_FROM'); ?></span>
            <input type="text" class="joms-input" name="name" value="<?php echo $my->email; ?>" disabled>
        </div>
        -->

        <div class="joms-form__group">
            <span><?php echo JText::_('COM_COMMUNITY_INVITE_TO'); ?> <span class="joms-required">*</span></span>
            <div class="joms-textarea__wrapper">
                <input type="text" class="joms-input joms-textarea--limit" name="emails" value="<?php echo (! empty($post['emails'])) ? $post['emails'] : '' ; ?>">
                <div class="joms-textarea__limit">
                    <?php echo JText::_('COM_COMMUNITY_SEPARATE_BY_COMMA'); ?>
                    <?php if ($isLimit) { ?>
                    <span>Invitations left: <b><?php echo $limit ?></b></span>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="joms-form__group">
            <span><?php echo JText::_('COM_COMMUNITY_INVITE_MESSAGE'); ?></span>
            <textarea class="joms-textarea" name="message"><?php echo (! empty($post['message'])) ? $post['message'] : '' ; ?></textarea>
        </div>

        <?php if ($afterFormDisplay) { ?>
        <div class="joms-form__group"><?php echo $afterFormDisplay;?></div>
        <?php } ?>

        <div class="joms-form__group">
            <span></span>
            <input type="submit" class="joms-button--primary joms-button--full-small" value="<?php echo JText::_('COM_COMMUNITY_INVITE_BUTTON'); ?>">
            <input type="hidden" name="action" value="invite" />
        </div>

    </form>

    <?php } ?>
</div>
<!--end: COMMUNITY FORM-->

<?php if( !empty( $friends ) ) : ?>

<div class="joms-page">
    <h3 class="joms-page__title"><?php echo JText::_('COM_COMMUNITY_FRIENDS_SUGGESTIONS'); ?></h3>

    <ul class="joms-list--friend">
        <?php foreach( $friends as $user ) : ?>
        <li class="joms-list__item">
            <div class="joms-list__avatar <?php echo CUserHelper::onlineIndicator($user); ?>">
                <a href="<?php echo $user->profileLink; ?>" class="joms-avatar">
                    <img src="<?php echo $user->getThumbAvatar(); ?>" alt="<?php echo $user->getDisplayName(); ?>" />
                </a>
            </div>

            <div class="joms-list__body">
                <a href="<?php echo $user->profileLink; ?>"><h4 class="joms-text--username"><?php echo $user->getDisplayName(); ?></h4></a>

                <span class="joms-text--title"><?php echo JText::sprintf('COM_COMMUNITY_TOTAL_MUTUAL_FRIENDS',CFriendsHelper::getTotalMutualFriends($user->id)); ?></span>

            </div>
            <div class="joms-list__actions">
                <?php echo CFriendsHelper::getUserCog($user->id,null,null,true); ?>
                <?php echo CFriendsHelper::getUserFriendDropdown($user->id); ?>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<script>
jQuery(function( $ ) {
    var limit = +'<?php echo $limit ? $limit : 0 ?>',
        isLimit = <?php echo $isLimit ? 'true' : 'false' ?>,
        $emails = $('[name=emails]'),
        $counter = $emails.siblings('div').find('b');

    if ( isLimit ) {
        $('[name=emails]').on('input', function() {
            var val = this.value;
            if ( !$.trim( val ) ) {
                $counter.html( limit );
                return;
            }

            val = val.replace(/,\s*,/, ',');
            if ( val !== this.value ) {
                this.value = val;
            }

            var emails = val.split(',');
            for ( var i = emails.length - 1; i >= 0; i-- ) {
                if ( !$.trim( emails[i] ) ) {
                    emails.splice( i, 1 );
                }
            }

            if ( emails.length > limit ) {
                val = emails.join(',');
                this.value = val = val.replace(/(((^|,)[^,]+){<?php echo $limit ? $limit : 0 ?>}).*$/, '$1');
                emails = val.split(',');
            }

            $counter.html( limit - emails.length );

        }).trigger('input');
    }
});
</script>
