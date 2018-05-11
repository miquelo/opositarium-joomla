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
CommunityLicenseHelper::disabledHtml();
?>

<style>

    .container-main {
        padding-bottom: 0 !important;
    }


</style>

<div class="space-16"></div>

<form name="adminForm" id="adminForm" action="index.php?option=com_community" method="POST">

<!-- Tabs header -->
<ul id="myTab" class="nav nav-tabs">
    <!-- System requirement -->
    <li class="active">
        <a href="#global" data-toggle="tab"><?php echo JText::_('COM_COMMUNITY_GLOBAL_COLOR'); ?></a>
    </li>
    <li class="">
        <a href="#element" data-toggle="tab"><?php echo JText::_('COM_COMMUNITY_ELEMENT_COLOR'); ?></a>
    </li>
    <li class="">
        <a href="#form" data-toggle="tab"><?php echo JText::_('COM_COMMUNITY_FORM_COLORS'); ?></a>
    </li>
     <li class="">
        <a href="#pagination" data-toggle="tab"><?php echo JText::_('COM_COMMUNITY_PAGINATION_COLORS'); ?></a>
    </li>
</ul>

<div id="myTabContent" class="tab-content" style="padding-top:24px;">
    <!-- global color -->
    <div class="tab-pane fade active in" id="global">
        <div class="row-fluid">
            <div class="span12">
                <div class="control-group">
                    <label class="control-label" for="scss-color-primary"><?php echo JText::_('COM_COMMUNITY_PRIMARY');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-primary');?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="scss-color-secondary"><?php echo JText::_('COM_COMMUNITY_SECONDARY');?> </label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-secondary');?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="scss-color-neutral"><?php echo JText::_('COM_COMMUNITY_NEUTRAL');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-neutral');?>
                    </div>
                </div>
            </div>
            <div class="span12">

                <div class="control-group">
                    <label class="control-label" for="scss-color-important"><?php echo JText::_('COM_COMMUNITY_IMPORTANT_COLOR');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-important');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-info" ><?php echo JText::_('COM_COMMUNITY_INFO_COLOR');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-info');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-link" ><?php echo JText::_('COM_COMMUNITY_LINK_COLOR');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-link');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-text"><?php echo JText::_('COM_COMMUNITY_TEXT_COLOR');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-text');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-text"><?php echo JText::_('COM_COMMUNITY_HEADER_COLOR');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-header');?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- element color -->
    <div class="tab-pane fade" id="element">
                
        <div class="row-fluid">
            <div class="span12">

                <div class="control-group">
                    <label class="control-label" for="scss-color-background" ><?php echo JText::_('COM_COMMUNITY_MAIN_BACKGROUND');?></label>
                    <div class="controls">
                       <?php echo $this->renderField('scss-color-background');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-background" ><?php echo JText::_('COM_COMMUNITY_SECONDARY_BACKGROUND');?></label>
                    <div class="controls">
                       <?php echo $this->renderField('scss-color-secondary-background');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-avatar-bg" ><?php echo JText::_('COM_COMMUNITY_PRIMARY_BORDER');?></label>
                    <div class="controls">
                       <?php echo $this->renderField('scss-avatar-bg');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-border" ><?php echo JText::_('COM_COMMUNITY_SECONDARY_BORDER');?></label>
                    <div class="controls">
                       <?php echo $this->renderField('scss-color-border');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-toolbar" ><?php echo JText::_('COM_COMMUNITY_TOOLBAR_BACKGROUND');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-toolbar');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-submenubar" ><?php echo JText::_('COM_COMMUNITY_SUBMENUBAR_BACKGROUND');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-submenubar');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-focus-background" ><?php echo JText::_('COM_COMMUNITY_FOCUS_BACKGROUND');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-focus-background');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-postbox" ><?php echo JText::_('COM_COMMUNITY_POSTBOX_BACKGROUND');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-postbox');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-postbox" ><?php echo JText::_('COM_COMMUNITY_POSTBOX_TAB');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-postbox-tab');?>
                    </div>
                </div>

            </div>

            <div class="span12">

                <div class="control-group">
                    <label class="control-label" for="scss-color-postbox" ><?php echo JText::_('COM_COMMUNITY_STREAM_BACKGROUND');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-stream-background');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-postbox" ><?php echo JText::_('COM_COMMUNITY_COMMENT_BACKGROUND');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-comment-background');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-module-background" ><?php echo JText::_('COM_COMMUNITY_MODULE_BACKGROUND');?></label>
                    <div class="controls">
                       <?php echo $this->renderField('scss-color-module-background');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-moduletab-background" ><?php echo JText::_('COM_COMMUNITY_MODULE_TAB_BACKGROUND');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-moduletab-background');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-dropdown-background" ><?php echo JText::_('COM_COMMUNITY_DROPDOWN_BACKGROUND');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-dropdown-background');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-dropdown-border" ><?php echo JText::_('COM_COMMUNITY_DROPDOWN_BORDER');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-dropdown-border');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-popup-background" ><?php echo JText::_('COM_COMMUNITY_POPUP_BACKGROUND');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-popup-background');?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- form color -->
    <div class="tab-pane fade" id="form">
        <div class="row-fluid">
            <div class="span12">
                <div class="control-group">
                    <label class="control-label" for="scss-color-input-border" ><?php echo JText::_('COM_COMMUNITY_INPUT_BORDER');?></label>
                    <div class="controls">
                       <?php echo $this->renderField('scss-color-input-border');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-input-background"><?php echo JText::_('COM_COMMUNITY_INPUT_BG');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-input-background');?>
                    </div>
                </div>
            </div>

            <div class="span12">
                <div class="control-group">
                    <label class="control-label" for="scss-color-input-color"><?php echo JText::_('COM_COMMUNITY_INPUT_COLOR');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-input-color');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-color-input-hover"><?php echo JText::_('COM_COMMUNITY_INPUT_HOVER');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-color-input-hover');?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- pagination color -->
    <div class="tab-pane fade" id="pagination">
        <div class="row-fluid">
            <div class="span12">
                <div class="control-group">
                    <label class="control-label" for="scss-pagination-bg"><?php echo JText::_('COM_COMMUNITY_PAGINATION_BG');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-pagination-bg');?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="scss-pagination-color"><?php echo JText::_('COM_COMMUNITY_PAGINATION_COLOR');?> </label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-pagination-color');?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="scss-pagination-bg-hover"><?php echo JText::_('COM_COMMUNITY_PAGINATION_BG_HOVER');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-pagination-bg-hover');?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="scss-pagination-color-hover"><?php echo JText::_('COM_COMMUNITY_PAGINATION_COLOR_HOVER');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-pagination-color-hover');?>
                    </div>
                </div>
            </div>
            <div class="span12">

                <div class="control-group">
                    <label class="control-label" for="scss-pagination-bg-active"><?php echo JText::_('COM_COMMUNITY_PAGINATION_BG_ACTIVE');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-pagination-bg-active');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-pagination-color-active" ><?php echo JText::_('COM_COMMUNITY_PAGINATION_COLOR_ACTIVE');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-pagination-color-active');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-pagination-bg-disabled" ><?php echo JText::_('COM_COMMUNITY_PAGINATION_BG_DISABLED');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-pagination-bg-disabled');?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="scss-pagination-color-disabled"><?php echo JText::_('COM_COMMUNITY_PAGINATION_COLOR_DISABLED');?></label>
                    <div class="controls">
                        <?php echo $this->renderField('scss-pagination-color-disabled');?>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>


<input type="hidden" name="view" value="themecolors" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="option" value="com_community" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>

<script src="<?php echo COMMUNITY_ASSETS_URL; ?>/js/jscolor/jscolor.js"></script>
<script type="text/javascript">
$( document ).ready(function() {
    // Handle submitbutton event
    Joomla.submitbutton = function(action){
        if(action == 'reset') {
            $('a.reset').trigger('click');
            return true;
        }
        submitform(action);
    }

    $('a.reset').on( 'click', function( e ) {
        var $reset = $( this ),
            id = $reset.attr('id').replace( /^reset-/, '' ),
            $field = $( '#' + id ),
            $deflt = $( '#default-' + id ),
            color;

        e.preventDefault();
        e.stopPropagation();

        color = $deflt.val();
        if ( !$field[0].color.fromString( color || '' ) ) {
            $field.val( color ).css( 'background-color', '' );
        }

        $reset.hide();
    });

    $('input.resettable').on( 'input change', function() {
        var $field = $( this ),
            id = $field.attr('id'),
            $deflt = $( '#default-' + id ),
            $reset = $( '#reset-' + id );

        if ( $field.val() === $deflt.val() ) {
            $reset.hide();
        } else {
            $reset.show();
        }
    });

});
</script>
