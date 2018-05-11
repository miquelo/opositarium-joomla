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
    <!--li>
        <a href="#requirements" data-toggle="tab"><?php echo JText::_('COM_COMMUNITY_CONFIGURATION'); ?></a>
    </li-->
    <li  class="active">
        <a href="#cb" data-toggle="tab"><?php echo JText::_('COM_COMMUNITY_CB'); ?></a>
    </li>
    <li class="">
        <a href="#easysocial" data-toggle="tab"><?php echo JText::_('COM_COMMUNITY_EASYSOCIAL'); ?></a>
    </li>
</ul>

<div id="myTabContent" class="tab-content" style="padding-top:24px;">
    <!-- System requirements -->
    <div class="tab-pane fade " id="requirements">
        <?php include_once 'configuration.php'; ?>
    </div>

    <!-- Preview -->
    <div class="tab-pane fade active in" id="cb">
        <?php include_once 'cb.php'; ?>
    </div>

    <!-- Pending List -->
    <div class="tab-pane fade" id="easysocial">
        <?php include_once 'easysocial.php'; ?>
    </div>

</div>
<script type="text/javascript">

    function isJSON (something) {
        if (typeof something != 'string')
            something = JSON.stringify(something);

        try {
            JSON.parse(something);
            return true;
        } catch (e) {
            return false;
        }
    }
    
    function parseResult(prefix,step,strCode){
        if(isJSON(strCode)){
            var parsed =  $.parseJSON( strCode );
            if ( $( '.'+prefix+step ).length == 0 ){
                $('.'+prefix+'_progress_text').append(' <div class="label label-success '+prefix+step+'"> <span class="counter">'+parsed.count+'</span> <?php echo JText::_('COM_COMMUNITY_DATA_MIGRATED')?> </div> <br>');
            }else{
                currentCount = $('.'+prefix+step+ ' span.counter').html();
                counter = parseInt(parsed.count) + parseInt(currentCount);
                $('.'+prefix+step+ ' span.counter').html(counter);
            }

            // progress bar
            countData = parseInt(parsed.countData);
            countLeft = parseInt(parsed.countLeft);
            presentage = (countLeft/countData) * 100;
            presentage = parseInt(presentage);
            
            $('.'+prefix+'_progress_bar .progress-bar').css('width', presentage+'%');
            $('.'+prefix+'_progress_bar .sr-only').html(presentage+'% ');

            
        }else{
            $('.'+prefix+'_progress_text').append(' <div class="label label-warning"><?php echo JText::_('COM_COMMUNITY_INSTALLATION_FAILED')?> </div><br>');
        }
        
    }
</script>

