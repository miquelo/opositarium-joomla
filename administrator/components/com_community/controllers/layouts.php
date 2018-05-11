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

jimport('joomla.application.component.controller');

if (!class_exists('CommunityControllerLayouts')) {

    /**
     * JomSocial Component Controller
     */
    class CommunityControllerLayouts extends CommunityController
    {

        /**
         *  Save an existing or a new mood form POST
         */
        public function apply($default = false)
        {
            CommunityLicenseHelper::_();

            JSession::checkToken() or jexit( JText::_( 'COM_COMMUNITY_INVALID_TOKEN' ) );

            $mainframe = JFactory::getApplication();
            $jinput = $mainframe->input;

            $config = CFactory::getConfig();

            $configs = $jinput->post->get('config', null, 'array');

            // save the config first
            $model	= $this->getModel( 'Configuration' );

            if(!empty($configs)){
                $model->save($configs);
            }

            $message = JText::_( 'COM_COMMUNITY_LAYOUTS_UPDATED' );
            $mainframe->redirect( 'index.php?option=com_community&view=layouts' , $message, 'message' );


            // Get the view type
            $document	= JFactory::getDocument();
            $viewType	= $document->getType();

            // Get the view
            $viewName	= $jinput->get( 'view' , 'community' );

            $model		= $this->getModel( $viewName ,'CommunityAdminModel' );

            if( $model )
            {
                $view->setModel( $model , $viewName );
            }

            $view->display();
        }
    }
}
