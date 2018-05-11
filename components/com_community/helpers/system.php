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

    class CSystemHelper
    {
        /**
         * @param $componentName
         * @return bool
         */
        public static function isComponentExists($componentName){
            $db = JFactory::getDbo();
            $db->setQuery("SELECT extension_id FROM #__extensions WHERE element =".$db->quote($componentName)." AND type=".$db->quote('component'));

            return $db->loadResult() ? true : false ;
        }

        /**
         * Check if the two factor authentication by Joomla! is enabled
         */
        public static function tfaEnabled(){

            //tfa is not supported before 3.2
            if(version_compare(JVERSION,'3.2','<')){
                return false;
            }

            //check for two way authentication
            require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';
            $tfa = UsersHelper::getTwoFactorMethods();

            if(isset($tfa) && is_array($tfa) && count($tfa) > 1){
                return true;
            }

            return false;
        }

        /**
         * show error page
         */
        public static function showErrorPage(){
            $template = new CTemplate();
            echo $template->fetch('general/error');
            return;
        }

        /**
         * show error page
         */
        public static function showUnpublishPageGroup(){
            $template = new CTemplate();
            echo $template->fetch('general/error_group_unpublish');
            return;
        }

        /**
         * show error page
         */
        public static function showUnpublishPageEvent(){
            $template = new CTemplate();
            echo $template->fetch('general/error_event_unpublish');
            return;
        }

        /**
         * @param $message
         * @param array $link, array('link_name'=>'link_url')
         */
        public static function showNoticePage($header, $message, $links = array()){
            $template = new CTemplate();
            echo $template->set('message', $message)
                    ->set('header', $header)
                    ->set('links', $links)
                    ->fetch('general/notice');
            return;
        }

        /**
         * List of the available views
         * @return array
         */
        public static function communityViewExists($viewname){
            $view = array(
                'apps',
                'connect',
                'developer',
                'events',
                'friends',
                'frontpage',
                'groups',
                'inbox',
                'memberlist',
                'multiprofile',
                'oauth',
                'photos',
                'profile',
                'register',
                'search',
                'videos'
            );

            return in_array($viewname, $view);
        }
    }