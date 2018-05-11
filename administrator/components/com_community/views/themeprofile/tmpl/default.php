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
echo CommunityLicenseHelper::disabledHtml();
?>

<style>
    .container-main {
        padding-bottom: 0 !important;
    }
</style>
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
<!-- Tabs header -->
<ul id="myTab" class="nav nav-tabs">
    <!-- System requirement -->
    <li class="active">
        <a href="#general" data-toggle="tab"><?php echo JText::_('COM_COMMUNITY_THEMEPROFILE_GENERAL_TAB'); ?></a>
    </li>
    <li class="">
        <a href="#info" data-toggle="tab"><?php echo JText::_('COM_COMMUNITY_THEMEPROFILE_USER_INFO_TAB'); ?></a>
    </li>
</ul>
<!-- Tabs content -->
<form name="adminForm" id="adminForm" action="index.php?option=com_community" method="POST" enctype="multipart/form-data">
<div id="myTabContent" class="tab-content" style="padding-top:24px;">
    <!-- general -->
    <div class="tab-pane fade active in" id="general">
        <?php include_once 'general.php'; ?>
    </div>
    <!-- user information -->
    <div class="tab-pane fade" id="info">
        <?php include_once 'info.php'; ?>
    </div>
</div>
<input type="hidden" name="view" value="themeprofile" />
<input type="hidden" name="task" value="apply" />
<input type="hidden" name="option" value="com_community" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>

<script>
$( document ).ready(function() {
    Joomla.submitbutton = function( action ) {
        if ( action === 'apply' && !checkFiles() ) {
            window.alert('<?php echo JText::_("COM_COMMUNITY_THEME_IMAGE_ERROR"); ?>')
            return false;
        }
        submitform(action);
    }

    function checkFiles() {
        var isValid = true;
        $('#adminForm input[type=file]').each(function() {
            if ( this.value && !this.value.match(/\.(jpg|jpeg|png)$/i) ) {
                isValid = false;
                return false;
            }
        });
        return isValid;
    }

    $('#default-cover-new,#default-cover-female-new,#default-cover-male-new,#default-male-avatar-new,#default-female-avatar-new,#default-general-avatar-new').ace_file_input({
        no_file:'No File ...',
        btn_choose:'Choose',
        btn_change:'Change',
        droppable:false,
        onchange:null,
        thumbnail:false
    });

});
</script>

<style type="text/css">

    .preview-cover {
        max-width: 100%;
        height: 150px;
    }

    .preview-avatar {
        max-width: 100%;
        height: 150px;
    }


</style>
