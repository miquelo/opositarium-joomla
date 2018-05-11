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
$jinput = JFactory::getApplication()->input;
$task = $jinput->getString('task', '');

if ($task == 'element') {
    echo $this->loadTemplate('element');
} else {
    ?>
    <script type="text/javascript" language="javascript">
        /**
         * This function needs to be here because, Joomla toolbar calls it
         **/
        Joomla.submitbutton = function(action) {
            submitbutton(action);
        }

        function submitbutton(action)
        {   
            switch (action)
            {   
                case 'approveall':
                    if (confirm("<?php echo JText::_('COM_COMMUNITY_PENDING_APPROVE_ALL_CONFIRM'); ?>")) {
                        submitform(action);
                    } else {
                        return false;
                    }
                    break;
                case 'rejectall':
                    if (confirm("<?php echo JText::_('COM_COMMUNITY_PENDING_REJECT_ALL_CONFIRM'); ?>")) {
                        submitform(action);
                    } else {
                        return false;
                    }
                    break;
                default:
                    submitform(action);
                    break;
            }

        }
    </script>
    <form action="index.php?option=com_community" method="get" name="adminForm" id="adminForm">

        <!-- page header -->
        <div class="row-fluid">
            <div class="span24">
                <input type="text" onchange="document.adminForm.submit();" class="no-margin" value="<?php echo ($this->search) ? $this->escape($this->search) : ''; ?>" id="search" name="search"/>
                <div onclick="document.adminForm.submit();" class="btn btn-small btn-primary">
                    <i class="js-icon-search"></i>
                    <?php echo JText::_('COM_COMMUNITY_SEARCH'); ?>
                </div>

                <div class="pull-right text-right">
                    <?php echo $this->_getStatusHTML();?>
                </div>
            </div>
        </div>

        <table class="table table-hover middle-content">
            <thead>
                <tr class="title">
                    <th width="10"><?php echo JText::_('COM_COMMUNITY_NUMBER'); ?></th>
                    <th width="10">
                        <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
                        <span class="lbl"></span>
                    </th>
                    <th width="150">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_COMMUNITY_NAME'), 'name', $this->lists['order_Dir'], $this->lists['order']); ?>
                    </th>
                    <th>
                        <?php echo JHTML::_('grid.sort', JText::_('COM_COMMUNITY_EMAIL'), 'email', $this->lists['order_Dir'], $this->lists['order']); ?>
                    </th>
                    <th>
                        <?php echo JHTML::_('grid.sort', JText::_('COM_COMMUNITY_REASON'), 'reason', $this->lists['order_Dir'], $this->lists['order']); ?>
                    </th>
                    <th style="text-align: center;">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_COMMUNITY_STATUS'), 'status', $this->lists['order_Dir'], $this->lists['order']); ?>
                    </th>
                    <th style="text-align: center;">
                        <?php echo JHTML::_('grid.sort', JText::_('COM_COMMUNITY_CREATED'), 'created', $this->lists['order_Dir'], $this->lists['order']); ?>
                    </th>
                    <th style="text-align: center;">
                        <?php echo JText::_('COM_COMMUNITY_ACTIONS'); ?>
                    </th>
                </tr>
            </thead>
            <?php $i = 0; ?>
            <?php
            if ($this->invites) {
                foreach ($this->invites as $row) {
                    ?>
                    <tr>
                        <td>
                            <?php echo ( $i + 1 ); ?>
                        </td>
                        <td>
                            <?php echo JHTML::_('grid.id', $i++, $row->id); ?>
                            <span class="lbl"></span>
                        </td>
                        <td align="center">
                            <?php echo $row->name; ?>
                        </td>
                        <td>
                            <a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a>
                        </td>
                        <td>
                            <?php echo nl2br($row->reason); ?>
                        </td>
                        <td>
                            <div style="text-align: center;">
                                <?php
                                    if( $row->status == 1 )
                                    {
                                        ?> <span class="label label-success"><?php echo JText::_('COM_COMMUNITY_APPROVED'); ?></span> <?php
                                    }
                                    else if( $row->status == 2 )
                                    {
                                        ?> <span class="label"><?php echo JText::_('COM_COMMUNITY_REJECTED'); ?></span> <?php
                                    }
                                    else
                                    {
                                        ?> <span class="label label-important"><?php echo JText::_('COM_COMMUNITY_PENDING'); ?></span> <?php
                                    }
                                ?>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <?php
                            $date = JDate::getInstance($row->created);
                            $mainframe = JFactory::getApplication();

                            echo $row->created == '0000-00-00 00:00:00' ? '0000-00-00 00:00:00' : $date->format('Y-m-d H:i:s');
                            ?>
                        </td>
                        <td style="text-align: center;">
                            <a class="btn btn-small btn-success" href="javascript:void(0);" onclick="azcommunity.pendingInviteAction('<?php echo $row->id;?>', 1);">
                                <?php echo JText::_('COM_COMMUNITY_APPROVE'); ?>
                            </a>
                            <a class="btn btn-small btn-danger" href="javascript:void(0);" onclick="azcommunity.pendingInviteAction('<?php echo $row->id;?>', 2);">
                                <?php echo JText::_('COM_COMMUNITY_REJECT'); ?>
                            </a>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="12" align="center"><?php echo JText::_('COM_COMMUNITY_NO_RESULT'); ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <div class="pull-left">
            <?php echo $this->pagination->getListFooter(); ?>
        </div>

        <div class="pull-right">
            <?php echo $this->pagination->getLimitBox(); ?>
        </div>
        <input type="hidden" name="view" value="pendinginvites" />
        <input type="hidden" name="option" value="com_community" />
        <input type="hidden" name="task" value="pendinginvites" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
        <?php echo JHTML::_('form.token'); ?>
    </form>
    <?php
}
?>