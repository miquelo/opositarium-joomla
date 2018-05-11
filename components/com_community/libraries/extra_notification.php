<?php


defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . '/components/com_community/libraries/core.php' );

class CExtraNotification{

    static $instance = null;
    private $notificationTypes = array();
    private $notificationSettings = array();

    public function __construct($populatedTypes = array())
    {
        if($this::$instance != null){
            return $this;
        }
        $this::$instance = $this;

        //when first loaded, lets check if there is any value from
        $this->loadExtraNotifications($populatedTypes);

    }


    private function loadExtraNotifications($populatedTypes = array()){
        $appsLib	= CAppPlugins::getInstance();
		$appsLib->loadApplications();
        $notifications = array($populatedTypes);
        $notifications = $appsLib->triggerEvent('onLoadingExtraNotifications', $notifications);
        if(isset($notifications[0]) && count($notifications[0]) > 0){
            $this->notificationTypes = $notifications[0];
        }else{
            $this->notificationTypes = $populatedTypes;
        }
    }

    public function getNotificationTypes(){
        return $this->notificationTypes;
    }

    public function getNotificationSettings(){
        return $this->notificationSettings;
    }

}