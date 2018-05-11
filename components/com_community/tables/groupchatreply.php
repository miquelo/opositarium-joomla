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

class CTableGroupChatReply extends JTable
{
    var $msg = null;
    var $created_by = null;
    var $group_chat_id = null;
    var $status = null;
    var $params = null;
    /**
     * Constructor
     */
    public function __construct( &$db )
    {
        parent::__construct( '#__community_group_chat_reply', 'id', $db );
    }

    /**
     * Handle all sorts of load error
     */
    public function load( $id = null, $reset = true )
    {
        parent::load( $id , $reset);
        return;
    }

    /**
     * we always store a new one because edit is not possible in chat
     * @param bool $updateNulls
     * @return bool
     */
    public function store($updateNulls = false)
    {
        return parent::store($updateNulls);
    }

    public function delete($id = null)
    {

    }
}
