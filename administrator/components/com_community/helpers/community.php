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

require_once JPATH_ROOT.'/components/com_community/libraries/core.php';

class CommunityHelper
{   
    public function prepareUpdate(&$update, &$table){
        $lang = JFactory::getLanguage();
        $extension = 'com_community';
        $base_dir = JPATH_ADMINISTRATOR;
        $language_tag = '';
        $lang->load($extension, $base_dir, $language_tag, true);

        $domain = $_SERVER['HTTP_HOST'];
        $domain = str_replace("https://", "", $domain);
        $domain = str_replace("http://", "", $domain);
        $component = "community";
        $valid_license = false;

        $config = CFactory::getConfig();
        $license_number = $config->get('registerlicense');

        if(trim($license_number) == ""){
            $app = JFactory::getApplication();
            $app->redirect("index.php?option=com_community&view=configuration&cfgSection=license", JText::_("COM_COMMUNITY_LICENSE_EMPTY_MESSAGE"), "error");
        } else {
            // start check license on jomsocial.com
            $check_url = "https://www.jomsocial.com/index.php?option=com_digistore&controller=digistoreAutoinstaller&task=get_license_number_details&tmpl=component&format=raw&component=".$component."&domain=".urlencode($domain)."&license=".trim($license_number);
            $extensions = get_loaded_extensions();
            $text = "";

            $license_details = file_get_contents($check_url);
            
            if (isset($license_details) && trim($license_details) != "") {
                $license_details = json_decode($license_details, true);

                if (isset($license_details["0"])) {
                    $license_details = $license_details["0"];
                    $productid = $license_details['productid'];
                } else {
                    // license not exists
                    $app = JFactory::getApplication();
                    $app->redirect("index.php?option=com_community&view=configuration&cfgSection=license", JText::sprintf('COM_COMMUNITY_GET_LICENSE_HERE', 'https://www.jomsocial.com/component/digistore/licenses?Itemid=209'), "error");
                    die();
                }
                
                if (isset($license_details["expires"]) && trim($license_details["expires"]) != "" && trim($license_details["expires"]) == "0000-00-00 00:00:00") {
                    $valid_license = true;
                } elseif (isset($license_details["expires"]) && trim($license_details["expires"]) != "" && trim($license_details["expires"]) != "0000-00-00 00:00:00") {
                    $now = strtotime(date("Y-m-d H:i:s"));
                    $license_expires = strtotime(trim($license_details["expires"]));

                    if ($license_expires >= $now) {
                        $valid_license = true;
                    } else {
                        $app = JFactory::getApplication();
                        $app->redirect("index.php?option=com_community&view=configuration&cfgSection=license", JText::sprintf('COM_COMMUNITY_EXPIRED_LICENSE_NUMBER', 'https://www.jomsocial.com/component/digistore/licenses?Itemid=209'), "error");
                        die();
                    }
                }
            }
        }

        if(!$valid_license){
            $app = JFactory::getApplication();
            $app->redirect("index.php?option=com_community&view=configuration&cfgSection=license", JText::sprintf('COM_COMMUNITY_GET_LICENSE_HERE', 'https://www.jomsocial.com/component/digistore/licenses?Itemid=209'), "error");
        } else {
            $itspro = 0;
            if (COMMUNITY_PRO_VERSION) $itspro = 1;
            // get download URL
            $url_request = "https://www.jomsocial.com/index.php?option=com_digistore&controller=digistoreAutoinstaller&task=update_extension&tmpl=component&format=raw&component=community&site=".urlencode($domain)."&license=".trim($license_number)."&itspro=".$itspro;
            $page_content = file_get_contents($url_request);

            if($page_content === FALSE || trim($page_content) == ""){
                $curl = curl_init();
                curl_setopt ($curl, CURLOPT_URL, $url_request);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $page_content = curl_exec ($curl);
                curl_close ($curl);
            }

            if(isset($page_content) && trim($page_content) != ""){
                $update->downloadurl->_data = $page_content;
            }
            
            if(!isset($update->downloadurl->_data) || trim($update->downloadurl->_data) == "" || trim($update->downloadurl->_data) == "https://www.jomsocial.com/" ){
                $app = JFactory::getApplication();
                $app->redirect("index.php?option=com_installer&view=update", JText::sprintf('COM_COMMUNITY_PACKAGE_DOWNLOAD_UPDATE', 'https://www.jomsocial.com/component/digistore/licenses?Itemid=209'), "JomSocial Update");
            }
        }
    }

    public static function preinstallExtensionCheck(){
        $db = JFactory::getDBO();

        //module to be removed
        $modules = array(
            'mod_activegroups',
            'mod_activitystream',
            'mod_community_quicksearch',
            'mod_community_search_nearbyevents',
            'mod_community_whosonline',
            'mod_datingsearch',
            'mod_hellome',
            'mod_jomsocialconnect',
            'mod_latestdiscussion',
            'mod_latestgrouppost',
            'mod_notify',
            'mod_photocomments',
            'mod_statistics',
            'mod_topmembers',
            'mod_videocomments'
        );

        //plugins to be removed
        $plugins = array(
            'invite', //its not plg_invite because its stored in db as invite as the element
            'input',
            'friendslocation',
			'kunena',
            'events',
            'feeds',
			'jomsocialconnect',
            'latestphoto'
        );

        $installedModules = array();
        $installedPlugins = array();

        //JInstaller
        foreach($modules as $module){
            //check if the module is installed
            $query = "SELECT id FROM ".$db->quoteName('#__modules')." WHERE ".$db->quoteName('module')."=".$db->quote($module);
            $db->setQuery($query);
            $installed = $db->loadResult();

            if($installed){
                $installedModules[] = $module;
            }
        }

        foreach($plugins as $plugin){
            //check if the plugin is installed
            $query = "SELECT extension_id FROM ".$db->quoteName('#__extensions')
                ." WHERE ("
					.$db->quoteName('folder')." = ".$db->quote('community')
							." OR (" // we have to be very strict here, which mean we only search for jomsocialconnect plugin in system to avoid conflict such as kunena plg that is suppose to be removed from community, not system
							. $db->quoteName('folder')." = ".$db->quote('system')." AND "
							. $db->quoteName('element')."=".$db->quote('jomsocialconnect')
							.")) AND "
                .$db->quoteName('element')." = ".$db->quote($plugin)." AND "
                .$db->quoteName('type')." = ".$db->quote('plugin');
            $db->setQuery($query);

            $installed = $db->loadResult();
            if($installed){
                $installedPlugins[] = $plugin;
            }
        }

        return array($installedPlugins, $installedModules);
    }

	public static function addSubmenu($view)
	{
		$views = array(
			'community'        => 'community',
			'users'            => 'users',
			'multiprofile'     => 'users',
			'configuration'    => 'community',
			'profiles'         => 'users',
			'groups'           => 'groups',
			'groupcategories'  => 'groups',
			'events'           => 'events',
			'eventcategories'  => 'events',
			'videoscategories' => 'community',
			'reports'          => 'community',
			'userpoints'       => 'users',
			'about'            => 'community'
		);

		$subViews = array(
			'community' => array(
				'community'        => JText::_('COM_COMMUNITY_TOOLBAR_HOME'),
				'configuration'    => JText::_('COM_COMMUNITY_TOOLBAR_CONFIGURATION'),
				'users'            => JText::_('COM_COMMUNITY_TOOLBAR_USERS'),
				'groups'           => JText::_('COM_COMMUNITY_TOOLBAR_GROUPS'),
				'events'           => JText::_('COM_COMMUNITY_TOOLBAR_EVENTS'),
				'videoscategories' => JText::_('COM_COMMUNITY_TOOLBAR_VIDEO_CATEGORIES'),
				'reports'          => JText::_('COM_COMMUNITY_TOOLBAR_REPORTINGS'),
				'about'            => JText::_('COM_COMMUNITY_TOOLBAR_ABOUT'),
			),
			'users' => array(
				'community'    => JText::_('COM_COMMUNITY_TOOLBAR_HOME'),
				'users'        => JText::_('COM_COMMUNITY_TOOLBAR_USERS'),
				'multiprofile' => JText::_('COM_COMMUNITY_TOOLBAR_MULTIPROFILES'),
				'profiles'     => JText::_('COM_COMMUNITY_TOOLBAR_CUSTOMPROFILES'),
				'userpoints'   => JText::_('COM_COMMUNITY_TOOLBAR_USERPOINTS'),
			),
			'groups' => array(
				'community'       => JText::_('COM_COMMUNITY_TOOLBAR_HOME'),
				'groups'          => JText::_('COM_COMMUNITY_TOOLBAR_GROUPS'),
				'groupcategories' => JText::_('COM_COMMUNITY_TOOLBAR_GROUP_CATEGORIES'),
			),
			'events' => array(
				'community'       => JText::_('COM_COMMUNITY_TOOLBAR_HOME'),
				'events'          => JText::_('COM_COMMUNITY_TOOLBAR_EVENTS'),
				'eventcategories' => JText::_('COM_COMMUNITY_TOOLBAR_EVENT_CATEGORIES')
			),
		);

		$currentView = '';

		if (array_key_exists($view, $views))
		{
			$currentView = $views[$view];
		}

		if ( ! array_key_exists($currentView, $subViews))
		{
			$currentView = 'community';
		}

		foreach ($subViews[$currentView] as $key => $val)
		{
			$isActive = ($view == $key);

			JHtmlSidebar::addEntry($val, 'index.php?option=com_community&view='.$key , $isActive);
		}
	}
}