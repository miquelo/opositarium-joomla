<?php
/**
 * @copyright (C) 2017 jooomlart, Inc. - All rights reserved.
 * @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author joomlart.com <info@joomlart.com>
 * @url https://www.jomsocial.com/license-agreement
 * The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at https://www.jomsocial.com/license-agreement
 */
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<div class="alert alert-info"><?php echo JText::_('COM_COMMUNITY_MIGRATOR_EASYSOCIAL_TITLE')?></div>
<p>
	<strong><?php echo JText::_('COM_COMMUNITY_MIGRATOR_DATA')?></strong><br>
	<ul>
		<li><?php echo JText::_('COM_COMMUNITY_MIGRATOR_USER_AVATARS')?></li>
		<li><?php echo JText::_('COM_COMMUNITY_MIGRATOR_USER_COVERS')?></li>
		<li><?php echo JText::_('COM_COMMUNITY_MIGRATOR_PROFILE_FIELDS')?></li>
		<li><?php echo JText::_('COM_COMMUNITY_FRIENDS')?></li>
		<li><?php echo JText::_('COM_COMMUNITY_PHOTOS')?></li>
		<li><?php echo JText::_('COM_COMMUNITY_VIDEOS')?></li>
		<li><?php echo JText::_('COM_COMMUNITY_EVENTS')?></li>
		<li><?php echo JText::_('COM_COMMUNITY_GROUPS')?></li>
	</ul>
</p>

<p>
	<strong><?php echo JText::_('COM_MIGRATOR_PREPARATION')?></strong>
	<?php echo JText::_('COM_MIGRATOR_PREPARATION_DESC')?>
</p>

<?php if($this->ESExist==false){?>
<p class="alert alert-error"> 
<?php echo JText::_('COM_COMMUNITY_EASYSOCIAL_NOT_EXIST')?>
</p>
<?php }?>

<button class="btn btn-success" onclick="es_start()" <?php echo $this->ESExist==false?'disabled':''?>><?php echo JText::_('COM_COMMUNITY_MIGRATOR_EASYSOCIAL_START')?></button>

<div class="es_progress" style="display: none">
	<div class="progress es_progress_bar">
	  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 2%;background-color: #3498db; color: #FFF;" style="">
	    <span class="sr-only" style="padding-left:10px">0%</span>
	  </div>
	</div>
	<div class="es_progress_text"></div>
</div>

<script>
	function es_start(){
		$('.es_progress .es_progress_text').html('');
    	$('.es_progress,.es_progress_bar').show();
    	es_avatar();
	}

	function es_start_over(){
		setTimeout(function(){
    			$('.es_progress_bar .progress-bar').css('width', '2%');
            	$('.es_progress_bar .sr-only').html('0% ');
			}, 1000);
	}

    function es_avatar(status=''){
    	if(status!='continue'){
    		es_start_over();
    		
    		$('.es_progress_text').append('<?php echo JText::_('COM_COMMUNITY_MIGRATING_AVATARS')?>');
    	}
    	$.post( 'index.php?option=com_community&view=migrators&task=easysocial&step=avatars&no_html=1', function( data ) {
    		parseResult('es','avatar',data);
    		var parsed =  $.parseJSON( data );
			if(parsed.countData==parsed.countLeft){
				es_cover();
			}else{
				es_avatar('continue');
			}
    		
		});
	}

	function es_cover(status=''){
    	if(status!='continue'){
    		es_start_over();
    		$('.es_progress_text').append('<?php echo JText::_('COM_COMMUNITY_MIGRATING_COVERS')?>');
    	}
		$.post( 'index.php?option=com_community&view=migrators&task=easysocial&step=covers&no_html=1', function( data ) {
    		parseResult('es','cover',data);

    		var parsed =  $.parseJSON( data );
			if(parsed.countData==parsed.countLeft){
				es_profile_type();
			}else{
				es_cover('continue');
			}
		});
	}
    
    function es_profile_type(status=''){
    	if(status!='continue'){
    		es_start_over();
    		$('.es_progress_text').append('<?php echo JText::_('COM_COMMUNITY_MIGRATING_PROFILE_TYPE')?>');
    	}

    	$.post( 'index.php?option=com_community&view=migrators&task=easysocial&step=multiprofile&no_html=1', function( data ) {
    		parseResult('es','multiprofile',data);

    		var parsed =  $.parseJSON( data );
			if(parsed.countData==parsed.countLeft){
				es_profile_fields();
			}else{
				es_profile_type('continue');
			}
		});
	}

    function es_profile_fields(status=''){
    	if(status!='continue'){
    		es_start_over();
    		$('.es_progress_text').append('<?php echo JText::_('COM_COMMUNITY_MIGRATING_FIELDS')?>');
    	}

    	$.post( 'index.php?option=com_community&view=migrators&task=easysocial&step=fields&no_html=1', function( data ) {
    		parseResult('es','fields',data);

    		var parsed =  $.parseJSON( data );
			if(parsed.countData==parsed.countLeft){
				es_friends();
			}else{
				es_profile_fields('continue');
			}
		});
	} 

	function es_friends(status=''){
		if(status!='continue'){
    		es_start_over();
    		$('.es_progress_text').append('<?php echo JText::_('COM_COMMUNITY_MIGRATING_FRIENDS')?>');
    	}
    	$.post( 'index.php?option=com_community&view=migrators&task=easysocial&step=friends&no_html=1', function( data ) {
    		parseResult('es','friends',data);
			
			var parsed =  $.parseJSON( data );
			if(parsed.countData==parsed.countLeft){
				es_groups();
			}else{
				es_friends('continue');
			}
		});
	} 

	
	function es_groups(status=''){
		if(status!='continue'){
    		es_start_over();
    		$('.es_progress_text').append('<?php echo JText::_('COM_COMMUNITY_MIGRATING_GROUPS')?>');
    	}
    	$.post( 'index.php?option=com_community&view=migrators&task=easysocial&step=groups&no_html=1', function( data ) {
    		parseResult('es','groups',data);
    		var parsed =  $.parseJSON( data );
    		if(parsed.countData==parsed.countLeft){
				es_events();
			}else{
				es_groups('continue');
			}
		});
	} 

	function es_events(status=''){
		if(status!='continue'){
    		es_start_over();
    		$('.es_progress_text').append('<?php echo JText::_('COM_COMMUNITY_MIGRATING_EVENTS')?>');
    	}
    	$.post( 'index.php?option=com_community&view=migrators&task=easysocial&step=events&no_html=1', function( data ) {
    		parseResult('es','events',data);
    		var parsed =  $.parseJSON( data );
    		if(parsed.countData==parsed.countLeft){
				es_photos();
			}else{
				es_events('continue');
			}
		});
	} 

	function es_photos(status=''){
		if(status!='continue'){
    		es_start_over();
    		$('.es_progress_text').append('<?php echo JText::_('COM_COMMUNITY_MIGRATING_PHOTOS')?>');
    	}
		$.post( 'index.php?option=com_community&view=migrators&task=easysocial&step=photos&no_html=1', function( data ) {
    		parseResult('es','photos',data);

    		var parsed =  $.parseJSON( data );
    		if(parsed.countData==parsed.countLeft){
				es_videos();
			}else{
				es_photos('continue');
			}
		});
	} 

	function es_videos(status=''){
		if(status!='continue'){
    		es_start_over();
    		$('.es_progress_text').append('<?php echo JText::_('COM_COMMUNITY_MIGRATING_VIDEOS')?>');
    	}
    	$.post( 'index.php?option=com_community&view=migrators&task=easysocial&step=videos&no_html=1', function( data ) {
    		parseResult('es','videos',data);

    		var parsed =  $.parseJSON( data );
    		if(parsed.countData!=parsed.countLeft){
				es_videos('continue');
			}
		});
	} 
</script>