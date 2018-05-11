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

$user = CFactory::getUser();
$config = CFactory::getConfig();
$enablepm = $config->get('enablepm');
?>
<?php if($enablepm): ?>
<div class="joms-page joms-js-page-chat-loading">
    <div style="text-align:center; padding:50px 0">
        <div class="joms-js-loading" style="padding:14px; text-align:center">
            <img src="<?php echo JURI::root(true) ?>/components/com_community/assets/ajax-loader.gif" alt="" />
        </div>
        <div class="alert alert-notice joms-js-loading-error" style="display:none">
            <div><?php echo JText::_('COM_COMMUNITY_CHAT_NOCONV_ERROR'); ?></div>
        </div>
        <div class="joms-js-loading-empty" style="display:none">
            <h1><?php echo JText::_('COM_COMMUNITY_CHAT_NOCONV_TITLE'); ?></h1>
            <p><?php echo JText::_('COM_COMMUNITY_CHAT_NOCONV_DESCRIPTION'); ?></p>
            <br />
            <span class="joms-button--primary joms-button--small joms-js-loading-no-conv" style="display:none">
                <?php echo JText::_('COM_COMMUNITY_CHAT_NOCONV_BTN'); ?>
            </span>
            <div class="alert alert-notice joms-js-loading-no-friend" style="display:none">
                <div><?php echo JText::_('COM_COMMUNITY_CHAT_NOCONV_NOFRIEND'); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="joms-page joms-page--chat joms-js-page-chat" style="display:none">
    <div class="joms-chat__wrapper">
        <!-- Sidebar -->
        <div class="joms-chat__conversations-wrapper">
            <div class="joms-chat__search">
                <input class="joms-input joms-chat__search_conversation" type="text" placeholder="<?php echo JText::_('COM_COMMUNITY_CHAT_SEARCH'); ?>" />
            </div>
            <div class="joms-chat__conversations">
                <div class="joms-js-list" style="display:none;max-height: 415px; overflow:auto;"></div>
            </div>
        </div>

        <!-- Wrapping one conversation -->
        <div class="joms-chat__messages-wrapper">
            <div class="joms-js--chat-header">
                <div class="joms-js--chat-header-info">
                    <div class="joms-chat__header">
                        <div class="joms-chat__recipents"></div>
                        <div class="joms-chat__actions">
                            <span class="joms-button--primary joms-button--small joms-js--chat-new-message">
                                <?php echo JText::_('COM_COMMUNITY_CHAT_NEW_MESSAGE'); ?>
                            </span>
                            <div class="joms-focus__button--options--desktop joms-js--chat-options">
                                <a href="javascript:" data-ui-object="joms-dropdown-button" class="">
                                    <svg viewBox="0 0 16 16" class="joms-icon">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-cog" class="joms-icon--svg-fixed joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified joms-icon--svg-unmodified"></use>
                                    </svg>
                                </a>
                                <ul class="joms-dropdown joms-js--chat-dropdown" style="display:none">
                                    <li class="joms-js--chat-mute" style="cursor:pointer"
                                        data-text-mute="<?php echo JText::_('COM_COMMUNITY_CHAT_MUTE') ?>"
                                        data-text-unmute="<?php echo JText::_('COM_COMMUNITY_CHAT_UNMUTE') ?>"></li>
                                    <li class="joms-js--chat-add-people" style="cursor:pointer; display:none" onclick="joms.popup.chat.addRecipient();">
                                        <?php echo JText::_('COM_COMMUNITY_CHAT_ADD_PEOPLE') ?>
                                    </li>
                                    <li class="joms-js--chat-leave" style="cursor:pointer">
                                        <?php echo JText::_('COM_COMMUNITY_CHAT_LEAVE_CHAT') ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="joms-js--chat-header-selector" style="display:none">
                    <div class="joms-chat__search">
                        <div class="joms-chat-selected"></div>
                        <input class="joms-input joms-chat__search_user" type="text" placeholder="<?php echo JText::_('COM_COMMUNITY_CHAT_TYPE_YOUR_FRIEND_NAME'); ?>" />
                        <div style="position:relative">
                            <div class="joms-js--chat-header-selector-div"
                                    data-text-no-result="<?php echo JText::_('COM_COMMUNITY_CHAT_NO_RESULT') ?>"
                                    style="display:none;background:white;border:1px solid rgba(0,0,0,.2);border-top:0 none;left:0;padding:5px;position:absolute;right:0;top:0px;z-index:1">

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="joms-chat__messages" style="position:relative;">
                <div class="joms-js--chat-conversation-loading" style="position: absolute;left: 49%;text-align:center;display:none">
                    <img src="<?php echo JURI::root(true) ?>/components/com_community/assets/ajax-loader.gif" alt="loader" />
                </div>
                <div class="joms-js--chat-conversation-messages" style="padding-top: 20px;height:350px;overflow:auto;display:none"></div>
                <div class="joms-js--chat-conversation-no-participants" style="display:none;">
                    <div class="alert alert-notice">
                        <div><?php echo JText::_('COM_COMMUNITY_CHAT_NO_PARTICIPANTS'); ?></div>
                    </div>
                </div>
            </div>

            <div class="joms-chat__messagebox joms-relative joms-js--inbox-reply joms-js--pm-message" style="position:relative;">
                <div class="joms-textarea__wrapper joms-js-wrapper">
                    <div class="joms-textarea joms-textarea__beautifier"></div>
                    <textarea rows="2" class="joms-textarea" disabled="disabled" placeholder="<?php echo JText::_('COM_COMMUNITY_CHAT_TYPE_YOUR_MESSAGE_HERE'); ?>"></textarea>
                    <div class="joms-textarea__loading">
                        <img src="<?php echo JURI::root(true); ?>/components/com_community/assets/ajax-loader.gif" alt="loader" >
                    </div>
                    <div class="joms-textarea joms-textarea__attachment">
                        <button class="removeAttachment" onclick="joms.view.comment.removeAttachment(this);">×</button>
                        <div class="joms-textarea__attachment--loading">
                            <img src="<?php echo JURI::root(true); ?>/components/com_community/assets/ajax-loader.gif" alt="loader" >
                        </div>
                        <div class="joms-textarea__attachment--thumbnail">
                            <img src="" alt="attachment">
                        </div>
                    </div>
                </div>

                <svg viewBox="0 0 16 16" class="joms-icon joms-icon--add" onclick="joms.view.comment.addAttachment(this, 'image');">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-camera"></use>
                </svg>
                <?php if ($config->get('message_file_sharing')): ?>
                <svg viewBox="0 0 16 16" class="joms-icon joms-icon--add" onclick="joms.view.comment.addAttachment(this, 'file', { type: 'chat', id: joms.chat.active.chat_id, max_file_size: '<?php echo $config->get('message_file_maxsize', 0) ?>', exts: 'bmp,csv,doc,gif,ico,jpg,jpeg,odg,odp,ods,odt,pdf,png,ppt,swf,txt,xcf,xls,BMP,CSV,DOC,GIF,ICO,JPG,JPEG,ODG,ODP,ODS,ODT,PDF,PNG,PPT,SWF,TXT,XCF,XLS' });" style="right:43px">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-file-zip"></use>
                </svg>
                <?php endif; ?>

                <div style="text-align:right; padding-top:2px">
                    <button class="joms-button--primary joms-button--small joms-js--send">
                        <?php echo JText::_('COM_COMMUNITY_SEND'); ?>
                    </button>
                </div>

                <div class="joms-textarea__wrapper joms-js-disabler"
                    style="position:absolute; top:0; left:0; right:0; bottom:0; opacity:.5; display:none"></div>
            </div>

        </div>
        <!-- //conversation -->
    </div>
</div>
<?php else: ?>
<div class="joms-page joms-js-page-chat-loading">
    <div style="text-align: center;">
        <h1><?php echo JText::_('COM_COMMUNITY_CHAT_IS_DISABLED') ?></h1>
        <p><?php echo JText::sprintf('COM_COMMUNITY_CHAT_DISABLED_BY_ADMIN', JFactory::getConfig()->get('sitename')); ?></p>
        <p>&nbsp;</p>
        <svg viewBox="0 0 16 16" class="joms-icon joms-icon-support" style="width:64px; height:64px;">
            <use xlink:href="#joms-icon-support"></use>
        </svg>
        <p>&nbsp;</p>
    </div>
</div>
<?php endif ?>

<!-- Conversation message template -->
<script type="text/template" id="joms-js-template-chat-message">
    <div class="joms-chat__message-item {{= data.timestamp }} {{= data.name }}"
            data-user-id="{{= data.user_id }}"
            data-timestamp="{{= data.timestamp }}">
        <div class="joms-avatar">
            <img src="{{= data.user_avatar }}" title="{{= data.user_name }}" alt="{{= data.user_name }} avatar"
                data-author="{{= data.user_id }}" />
        </div>
        <div class="joms-chat__message-body joms-js-chat-message-item-body"></div>
    </div>
</script>

<!-- Conversation message's content template -->
<script type="text/template" id="joms-js-template-chat-message-content">
    <div data-timestamp="{{= data.timestamp }}">
        <div class="joms-js-chat-loading" style="position:absolute; top:4px; right:6px; display:none">
            <img src="<?php echo JURI::root(true) ?>/components/com_community/assets/ajax-loader.gif" alt="loader">
        </div>
        <span class="joms-chat__message-content joms-js-chat-content {{= data.timestamp }}"
            {{= data.id }} title="{{= data.time }}" data-id="{{= data.id }}">{{= data.message }}</span>
        {{= data.attachment }}
        {{ if ( data.mine ) { }}
        <div class="joms-chat__message-actions">
            <a href="javascript:">
                <svg viewBox="0 0 16 16" class="joms-icon" style="width:8px; height:8px">
                    <use xlink:href="<?php echo JUri::getInstance(); ?>#joms-icon-close"></use>
                </svg>
            </a>
        </div>
        {{ } }}
    </div>
</script>

<!-- Conversation message's image attachment template -->
<script type="text/template" id="joms-js-template-chat-message-image">
    <div class="joms-chat__attachment-image joms-js-chat-attachment">
        <a href="javascript:" onclick="joms.api.photoZoom('{{= data.url }}');">
            <img src="{{= data.url }}" alt="photo thumbnail">
        </a>
    </div>
</script>

<!-- Conversation message's file attachment template -->
<script type="text/template" id="joms-js-template-chat-message-file">
    <div class="joms-chat__attachment-file joms-js-chat-attachment">
        <svg viewBox="0 0 16 16" class="joms-icon">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="<?php echo JUri::getInstance(); ?>#joms-icon-file-zip"></use>
        </svg>
        <a href="{{= data.url }}" target="_blank" title="{{= data.name }}"><strong>{{= data.name }}</strong></a>
    </div>
</script>

<!-- Conversation message's video attachment template -->
<script type="text/template" id="joms-js-template-chat-message-video">
    <div class="joms-chat__attachment-video joms-js-chat-attachment" style="background:white">
        <?php if ($config->get('enable_embedly')) { ?>

        <a href="{{= data.url }}" class="embedly-card"
                data-card-controls="0" data-card-recommend="0"
                data-card-theme="<?php echo $config->get('enable_embedly_card_template'); ?>"
                data-card-key="<?php echo $config->get('embedly_card_apikey'); ?>"
                data-card-align="<?php echo $config->get('enable_embedly_card_position') ?>"
            ><?php echo JText::_('COM_COMMUNITY_EMBEDLY_LOADING'); ?></a>

        <?php } else { ?>

        <div class="joms-media--video joms-js--video"
                data-type="{{= data.type }}"
                data-id="{{= data.id }}"
                data-path="<{{= data.path }}"
                style="margin-top:10px">

            <div class="joms-media__thumbnail">
                <img src="{{= data.thumbnail }}" alt="{{= data.title }}">
                <a href="javascript:" class="mejs-overlay mejs-layer mejs-overlay-play joms-js--video-play">
                    <div class="mejs-overlay-button"></div>
                </a>
            </div>
            <div class="joms-media__body">
                <h4 class="joms-media__title">{{= data.title_short }}</h4>
                <p class="joms-media__desc">{{= data.desc_short }}</p>
            </div>
        </div>

        <?php } ?>
    </div>
</script>

<!-- Conversation message's url attachment template -->
<script type="text/template" id="joms-js-template-chat-message-url">
    <div class="joms-chat__attachment-url joms-js-chat-attachment" style="background:white">
        <?php if ($config->get('enable_embedly')) { ?>

        <a href="{{= data.url }}" class="embedly-card"
                data-card-controls="0" data-card-recommend="0"
                data-card-theme="<?php echo $config->get('enable_embedly_card_template'); ?>"
                data-card-key="<?php echo $config->get('embedly_card_apikey'); ?>"
                data-card-align="<?php echo $config->get('enable_embedly_card_position') ?>"
            ><?php echo JText::_('COM_COMMUNITY_EMBEDLY_LOADING'); ?></a>

        <?php } else { ?>

        <div style="position:relative">
            <div class="row-fluid">
                {{ if ( data.images && data.images.length ) { }}
                <div class="span4">
                    <a href="{{= data.url }}" onclick="joms.api.photoZoom('{{= data.images[0] }}');">
                        <img class="joms-stream-thumb" src="{{= data.images[0] }}" alt="photo thumbnail" />
                    </a>
                </div>
                {{ } }}
                <div class="span{{= data.images && data.images.length ? 8 : 12 }}">
                    <article class="joms-stream-fetch-content" style="margin-left:0; padding-top:0">
                        <a href="{{= data.url }}"><span class="joms-stream-fetch-title">{{= data.title }}</span></a>
                        <span class="joms-stream-fetch-desc">{{= data.description }}</span>
                    </article>
                </div>
            </div>
        </div>

        <?php } ?>
    </div>
</script>

<!-- Conversation message time template  -->
<script type="text/template" id="joms-js-template-chat-message-time">
    <div class="joms-chat__message-item" style="text-align:center">
        <small><strong>{{= data.time }}</strong></small>
    </div>
</script>

<!-- End of conversation notice template  -->
<script type="text/template" id="joms-js-template-chat-message-end">
    <div class="joms-chat__message-item joms-js--chat-conversation-end" style="text-align:center">
        <small><?php echo JText::_('COM_COMMUNITY_CHAT_MSG_NO_MORE'); ?></small>
    </div>
</script>

<!-- Leave conversation template  -->
<script type="text/template" id="joms-js-template-chat-leave">
    <div class="joms-chat__message-item" data-id="{{= data.id }}" style="text-align:center">
        <small>
            <strong>{{= data.time }}</strong><br />
            {{ if ( data.mine ) { }}
            <span><?php echo JText::sprintf('COM_COMMUNITY_CHAT_MSG_YOU_LEAVE'); ?></span>
            {{ } else { }}
            <span><?php echo JText::sprintf('COM_COMMUNITY_CHAT_MSG_LEAVE', '{{= data.name }}'); ?></span>
            {{ } }}
        </small>
    </div>
</script>

<!-- Added to conversation template -->
<script type="text/template" id="joms-js-template-chat-added">
    <div class="joms-chat__message-item" data-id="{{= data.id }}" style="text-align:center">
        <small>
            <strong>{{= data.time }}</strong><br />
            {{ if ( data.mine ) { }}
            <span><?php echo JText::sprintf('COM_COMMUNITY_CHAT_MSG_YOU_ADDED', '{{= data.by }}'); ?></span>
            {{ } else { }}
            <span><?php echo JText::sprintf('COM_COMMUNITY_CHAT_MSG_ADDED', '{{= data.name }}', '{{= data.by }}'); ?></span>
            {{ } }}
        </small>
    </div>
</script>

<!-- Seen by template -->
<script type="text/template" id="joms-js-template-chat-seen-by">
    <div class="joms-chat__seen clearfix" title="<?php echo JText::sprintf('COM_COMMUNITY_CHAT_SEEN_BY', '{{= data.names }}'); ?>">
        {{ for ( var i in data.seen ) { }}
        <img src="{{= data.seen[ i ].avatar }}" />
        {{ } }}
    </div>
</script>

<!-- Sidebar item template -->
<script type="text/template" id="joms-js-template-chat-sidebar-item">
    <div class="joms-chat__item joms-js--chat-item-{{= data.id }} {{= +data.unread ? 'unread' : '' }} {{= +data.active ? 'active' : '' }}"
            data-chat-type="{{= data.type }}" data-chat-id="{{= data.id }}">
        <div class="joms-avatar">
            <img src="{{= data.avatar }}" />
        </div>
        <div class="joms-chat__item-body">
            <a href="#">{{= data.name }}</a>
            <span class="joms-js--chat-item-msg"></span>
        </div>
    </div>
</script>

<!-- Sidebar draft item template -->
<script type="text/template" id="joms-js-template-chat-sidebar-draft">
    <div class="joms-chat__item joms-js--chat-item-0" data-chat-type="new" data-chat-id="0">
        <div class="joms-avatar">
            <img src="<?php echo JURI::root(true) ?>/components/com_community/assets/mood_21.png" />
        </div>
        <div class="joms-chat__item-body">
            <a href="#"><?php echo JText::_('COM_COMMUNITY_CHAT_NEW_CHAT'); ?></a>
            <span class="joms-js--chat-item-msg"></span>
        </div>
    </div>
</script>

<!--
**** WINDOW CHAT
**** This is a popup chat which can be used on multiple pages, fixed to bottom.
****
-->
<div class="joms-chat__wrapper joms-chat--window">
  <div class="joms-chat__windows clearfix">
    <!-- Message window wrapper -->
    <div class="joms-chat__window" style="display:none">
      <div class="joms-chat__window-title">
        <span class="joms-chat__status"></span>
        Username
        <a href="#" class="joms-chat__window-close">
          <svg viewBox="0 0 16 16" class="joms-icon">
            <use xlink:href="<?php echo JUri::getInstance(); ?>#joms-icon-close"></use>
          </svg>
        </a>
      </div>

      <div class="joms-chat__window-body">
        <!-- Message wrapper -->
        <div class="joms-chat__message">
          <div class="joms-chat__message-avatar">
            <img src="" alt="">
          </div>

          <div class="joms-chat__message-bubble">
            <p>Message</p>
          </div>

          <div class="joms-chat__message-media">
            <img src="" alt="">
          </div>
        </div>
        <!-- //message -->
      </div>

      <div class="joms-chat__input-wrapper">
        <input type="text">

        <div class="joms-chat__input-actions">
          <a href="#">
            <svg viewBox="0 0 16 16" class="joms-icon">
              <use xlink:href="<?php echo JUri::getInstance(); ?>#joms-icon-camera"></use>
            </svg>
          </a>
        </div>
      </div>
    </div>
    <!-- //window -->
  </div>

  <div class="joms-chat__sidebar"></div>
</div>
<!-- //popup chat -->
