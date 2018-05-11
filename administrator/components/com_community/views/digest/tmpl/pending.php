<?php
/**
 * @copyright (C) 2013 iJoomla, Inc. - All rights reserved.
 * @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author iJoomla.com <webmaster@ijoomla.com>
 * @url https://www.jomsocial.com/license-agreement
 * The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at https://www.jomsocial.com/license-agreement
 */
defined('_JEXEC') or die('Restricted access');
?>
<div class="alert alert-info">
    <?php echo JText::_('COM_COMMUNITY_DIGEST_PENDING_LIST_INFO');?>
</div>

<?php if(count($this->pendingList) > 0){ ?>
<table class="table table-hover middle-content joms-js--digestpending">
    <thead>
        <tr class="title">
            <th><?php echo JText::_('COM_COMMUNITY_AVATAR');?></th>
            <th><?php echo JText::_('COM_COMMUNITY_USERNAME');?></th>
            <th><?php echo JText::_('COM_COMMUNITY_EMAIL');?></th>
            <th><?php echo JText::_('COM_COMMUNITY_PREVIEW');?></th>
        </tr>
    </thead>

    <tbody>
        <?php foreach($this->pendingList as $item){?>
        <tr>
            <td>
                <div class="avatar-wrapper thumbnail">
                    <a href="<?php echo JRoute::_('index.php?option=com_community&view=users&layout=edit&id=' . $item->id); ?>"><img src="<?php echo CFactory::getUser($item->id)->getThumbAvatar(); ?>" alt=""></a>
                </div>
            </td>
            <td><a href="<?php echo JRoute::_('index.php?option=com_community&view=users&layout=edit&id=' . $item->id); ?>"><?php echo $item->username; ?></a></td>
            <td><?php echo $item->email; ?></td>
            <td><button class="btn btn-small btn-primary" data-id="<?php echo $item->id ?>"><?php echo JText::_('COM_COMMUNITY_DIGEST_PREVIEW') ?></button></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>

<script>
    (function() {
        function init() {
            jQuery('.joms-js--digestpending').on('click', 'button', function() {
                var $btn = jQuery( this ),
                    id = $btn.data('id');

                cWindowShow('jax.call("community", "admin,digest,ajaxGetPreview", ' + id + ', "");', '<?php echo JText::_("COM_COMMUNITY_DIGEST_PREVIEW", TRUE) ?>' , 800 , 450 );
                jQuery('#js-cpanel .modal').css({
                    width: 800,
                    marginLeft: 0,
                    left: '50%',
                    transform: 'translate(-50%, 0)'
                });

                var timer = setInterval(function() {
                    var $ct = jQuery('#cWindowContent');
                    if ( $ct.is(':visible') ) {
                        clearInterval( timer );
                        if ( !$ct.text().length ) {
                            $ct.html('<?php echo JText::_("COM_COMMUNITY_DIGEST_PREVIEW_RELOGIN_MESSAGE", TRUE); ?>');
                        }
                    }
                }, 500 );
            });
        }

        var timer = setInterval(function() {
            if ( window.jQuery ) {
                clearInterval( timer );
                init();
            }
        }, 1000 );
    })();
</script>
