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
<div class="alert alert-info"><?php echo JText::_('COM_COMMUNITY_MIGRATOR_CB_TITLE')?></div>
<p>
	<strong><?php echo JText::_('COM_COMMUNITY_MIGRATOR_DATA')?></strong><br>
	<ul>
		<li><?php echo JText::_('COM_COMMUNITY_MIGRATOR_USER_AVATARS')?></li>
		<li><?php echo JText::_('COM_COMMUNITY_MIGRATOR_USER_COVERS')?></li>
		<li><?php echo JText::_('COM_COMMUNITY_MIGRATOR_PROFILE_FIELDS')?></li>
		<li><?php echo JText::_('COM_COMMUNITY_MIGRATOR_CONNECTIONS')?></li>
	</ul>
</p>

<p>
	<strong><?php echo JText::_('COM_MIGRATOR_PREPARATION')?></strong>
	<?php echo JText::_('COM_MIGRATOR_PREPARATION_DESC')?>
</p>

<?php if($this->CBExist==false){?>
<p class="alert alert-error"> 
<?php echo JText::_('COM_COMMUNITY_CB_NOT_EXIST')?>
</p>
<?php }?>

<button class="btn btn-success" onclick="cb_start()" <?php echo $this->CBExist==false?'disabled':''?>><?php echo JText::_('COM_COMMUNITY_MIGRATOR_CB_START')?></button>
<div class="cb_progress" style="display: none">
	<div class="progress cb_progress_bar">
	  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 2%;background-color: #3498db; color: #FFF;" style="">
	    <span class="sr-only" style="padding-left:10px">0%</span>
	  </div>
	</div>
	<div class="cb_progress_text"></div>
</div>

<script>
	function cb_start(){
		$('.cb_progress .cb_progress_text').html('');
    	$('.cb_progress,.cb_progress_bar').show();
    	cb_avatar();
	}

	function cb_start_over(){
		setTimeout(function(){
    			$('.es_progress_bar .progress-bar').css('width', '2%');
            	$('.es_progress_bar .sr-only').html('0% ');
			}, 1000);
	}

	function cb_avatar(status){
		if(status!='continue'){
    		cb_start_over();
    		$('.cb_progress_text').append('<?php echo JText::_('COM_COMMUNITY_MIGRATING_AVATARS')?>');
    	}
    	$.post( 'index.php?option=com_community&view=migrators&task=cb&step=avatars&no_html=1', function( data ) {
    		parseResult('cb','avatar',data);
    		var parsed =  $.parseJSON( data );
			if(parsed.countData==parsed.countLeft){
				cb_cover();
			}else{
				cb_avatar('continue');
			}
    		
		});
	}

	function cb_cover(status){
		if(status!='continue'){
    		cb_start_over();
    		$('.cb_progress_text').append('<?php echo JText::_('COM_COMMUNITY_MIGRATING_COVERS')?>');
    	}
    	$.post( 'index.php?option=com_community&view=migrators&task=cb&step=covers&no_html=1', function( data ) {
    		parseResult('cb','cover',data);
    		var parsed =  $.parseJSON( data );
			if(parsed.countData==parsed.countLeft){
				cb_profile_fields();
			}else{
				cb_cover('continue');
			}
    		
		});
	}
    
    function cb_profile_fields(status){
    	if(status!='continue'){
    		cb_start_over();
    		$('.cb_progress_text').append('<?php echo JText::_('COM_COMMUNITY_MIGRATING_FIELDS')?>');
    	}
    	$.post( 'index.php?option=com_community&view=migrators&task=cb&step=fields&no_html=1', function( data ) {
    		parseResult('cb','fields',data);
    		var parsed =  $.parseJSON( data );
			if(parsed.countData==parsed.countLeft){
				cb_profile_friends();
			}else{
				cb_profile_fields('continue');
			}
    		
		});
	} 

	function cb_profile_friends(status){
		if(status!='continue'){
    		cb_start_over();
    		$('.cb_progress_text').append('<?php echo JText::_('COM_COMMUNITY_MIGRATING_FRIENDS')?>');
    	}
    	$.post( 'index.php?option=com_community&view=migrators&task=cb&step=friends&no_html=1', function( data ) {
    		parseResult('cb','friends',data);
    		var parsed =  $.parseJSON( data );
			if(parsed.countData==parsed.countLeft){
				//cb_profile_friends();
			}else{
				cb_profile_friends('continue');
			}
    		
		});

		
	} 
</script>