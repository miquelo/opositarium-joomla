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


// Core file is required since we need to use CFactory
require_once( JPATH_ROOT . '/components/com_community/libraries/core.php' );

// check if FB library already available or not
if (!class_exists('Facebook')) {
    // Need to include Facebook's PHP API library so we can utilize them.
    require_once( JPATH_ROOT . '/components/com_community/libraries/facebooklib/autoload.php' );
}

/**
 * Wrapper class for Facebook's API.
 * */
class CFacebook {

    public $facebook = null;

    /**
     * 	Fields to map from Facebook and the values are the default field codes in Jomsocial.
     * */
    private $_fields = array(
        'gender' => 'FIELD_GENDER',
        'birthday' => 'FIELD_BIRTHDATE',
        'hometown_location' => array('state' => 'FIELD_STATE', 'city' => 'FIELD_CITY', 'country' => 'FIELD_COUNTRY'),
        'education' => 'FIELD_COLLEGE',
        'website' => 'FIELD_WEBSITE'
    );

    /**
     * Deprecated since 1.8.x
     * */
    public $lib = null;
    public $userId = null;
    public $response;
    public $accessToken;

    /**
     * 	Initial method
     * */
    public function __construct($requireLogin = false) {
        //include_once(JPATH_COMPONENT.'/libraries/facebook/autoload.php');
        $config = CFactory::getConfig();
        $key = $config->get('fbconnectkey');
        $secret = $config->get('fbconnectsecret');

        //$this->accessToken = 'EAAFOagPB6NcBAKRRjpvMbA8dXyMEn0ouHcjbXumZBTj31M2QhiufZAl96jbRgpIvgKO6BBzR5doYKEVHpzUB5hRVHF7TAxHqknhyeusvTIjdR6efU9wcqZBv5pDvWVGB0KZBUH8WP8eqS6XW25FuW022TK6jXiz0fDDSFWjImtIybzlbYSdaIKSIY0nE76oZD';
        $session = JFactory::getSession();
        $token = $session->get('facebookAccessToken');

        if($token){
            $this->accessToken = $token;
        }

        $this->facebook = new Facebook\Facebook([
            'app_id' => $key,
            'app_secret' => $secret,
            'default_graph_version' => 'v2.5',
        ]);
    }

    /**
     * 	Return user's data that is fetched from Facebook
     *
     * 	@params $fields	Array of fields available.
     * */
    public function getUserInfo() {
        $param = '/me?fields=first_name,last_name,birthday,location,gender,name,link,website,education,email,picture.type(large)';
        $result = $this->facebook->get($param, $this->accessToken)->getGraphUser();
        $result['pic_square'] = $result['picture']['url'];
        //we cannot get picture in different size at once, so, do 1 more time to get

        //$big_picture = $this->facebook->get('/me?fields=picture.type(large)',$this->accessToken)->getGraphUser(); //big picture
        //$result['pic_big'] = $big_picture['picture']['url'];
        $result['pic_big'] = $result['picture']['url'];
        if (isset($result['pic_square']) && empty($result['pic_square'])) {
            $result['pic_square'] = JURI::root(true) . '/' . DEFAULT_USER_THUMB;
        }

        $result = (isset($result) && count($result)) ? $result : false;

        return $result;
    }

    /**
     * get User Id
     * */
    public function getUserId() {
        $user = $this->getUser();
        return $user['id'];
    }

    public function mapAvatar($avatarUrl = '', $joomlaUserId, $addWaterMark) {
        $image = '';

        if (!empty($avatarUrl)) {
            // Make sure user is properly added into the database table first
            $user = CFactory::getUser($joomlaUserId);
            $fbUser = $this->getUser()['id'];

            // Store image on a temporary folder.
            $tmpPath = JPATH_ROOT . '/images/originalphotos/facebook_connect_' . $fbUser;

            // Need to extract the non-https version since it will cause
            // certificate issue
            $avatarUrl = str_replace('https://', 'http://', $avatarUrl);

            $source = CRemoteHelper::getContent($avatarUrl, true);
            list( $headers, $source ) = explode("\r\n\r\n", $source, 2);
            JFile::write($tmpPath, $source);

            // @todo: configurable width?
            $imageMaxWidth = 160;

            // Get a hash for the file name.
            $fileName = JApplicationHelper::getHash($fbUser . time());
            $hashFileName = CStringHelper::substr($fileName, 0, 24);

            $uri_parts = explode('?',$avatarUrl, 2);
            $extension = CStringHelper::substr($uri_parts[0], CStringHelper::strrpos($uri_parts[0], '.'));

            $type = 'image/jpg';

            if ($extension == '.png') {
                $type = 'image/png';
            }

            if ($extension == '.gif') {
                $type = 'image/gif';
            }

            //@todo: configurable path for avatar storage?
            $config = CFactory::getConfig();
            $storage = JPATH_ROOT . '/' . $config->getString('imagefolder') . '/avatar';
            $storageImage = $storage . '/' . $hashFileName . $extension;
            $storageThumbnail = $storage . '/thumb_' . $hashFileName . $extension;
            $image = $config->getString('imagefolder') . '/avatar/' . $hashFileName . $extension;
            $thumbnail = $config->getString('imagefolder') . '/avatar/' . 'thumb_' . $hashFileName . $extension;

            $userModel = CFactory::getModel('user');

            // Only resize when the width exceeds the max.
            CImageHelper::resizeProportional($tmpPath, $storageImage, $type, $imageMaxWidth);
            CImageHelper::createThumb($tmpPath, $storageThumbnail, $type);

            if ($addWaterMark) {
                // Get the width and height so we can calculate where to place the watermark.
                list( $watermarkWidth, $watermarkHeight ) = getimagesize(FACEBOOK_FAVICON);
                list( $imageWidth, $imageHeight ) = getimagesize($storageImage);
                list( $thumbWidth, $thumbHeight ) = getimagesize($storageThumbnail);

                CImageHelper::addWatermark($storageImage, $storageImage, $type, FACEBOOK_FAVICON, ( $imageWidth - $watermarkWidth), ( $imageHeight - $watermarkHeight));
                CImageHelper::addWatermark($storageThumbnail, $storageThumbnail, $type, FACEBOOK_FAVICON, ( $thumbWidth - $watermarkWidth), ( $thumbHeight - $watermarkHeight));
            }

            // Update the CUser object with the correct avatar.
            $user->set('_thumb', $thumbnail);
            $user->set('_avatar', $image);

            // @rule: once user changes their profile picture, storage method should always be file.
            $user->set('_storage', 'file');

            $userModel->setImage($joomlaUserId, $image, 'avatar');
            $userModel->setImage($joomlaUserId, $thumbnail, 'thumb');

            $user->save();
        }
    }

    /**
     * Maps a user profile with JomSocial's default custom values
     *
     * 	@param	Array	User values
     * */
    public function mapProfile($values, $userId) {
        $profileModel = CFactory::getModel('Profile');

        foreach ($this->_fields as $field => $fieldCodes) {
            // Test if value really exists and it isn't empty.
            if (isset($values[$field]) && !empty($values[$field])) {
                switch ($field) {
                    case 'birthday':
                        $date = JDate::getInstance($values[$field]->format('Y-m-d'));

                        $profileModel->updateUserData($fieldCodes, $userId, $date->toSql());

                        break;
                    case 'gender':
                        $gender = 'COM_COMMUNITY_'.strtoupper($values[$field]);
                        if (!empty($gender)) {
                            $profileModel->updateUserData($fieldCodes, $userId, $gender);
                        }
                        break;
                    case 'education':

                        $education = end($values['education']);

                        if ($education['type'] == 'College') {
                            if (isset($education['school']))
                                $name = $education['school']['name'];
                            if (isset($education['year']['name']))
                                $year = $education['year']['name'];

                            if (!empty($name)) {
                                $profileModel->updateUserData($fieldCodes, $userId, $name);
                            }
                            if (!empty($year)) {
                                $profileModel->updateUserData('FIELD_GRADUATION', $userId, $year);
                            }
                        }

                        break;
                    default:
                        if (is_array($fieldCodes)) {
                            // Facebook library returns an array of values for certain fields so we need to manipulate them differently.
                            foreach ($fieldCodes as $fieldData => $fieldCode) {
                                if (isset($values[$field][$fieldData])) {
                                    $profileModel->updateUserData($fieldCode, $userId, $values[$field][$fieldData]);
                                }
                            }
                        } else {
                            if (!empty($values[$field])) {
                                $profileModel->updateUserData($fieldCodes, $userId, $values[$field]);
                            }
                        }
                        break;
                }
            }
        }
        return false;
    }

    /**
     * Posts a status into user's facebook stream
     *
     * 	@param	$status	String	Message to be posted to Facebook
     * */
    public function postStatus($message) {
        try {
            $statusUpdate = $this->facebook->post('/me/feed', array('message' => $message, 'cb' => ''), $this->accessToken);
            
            if (!empty($statusUpdate)) {
                return true;
            }
            return false;
        } catch (FacebookApiException $e) {
            return false;
        }
    }

    /**
     * Maps a user status with JomSocial's user status
     *
     * 	@param	Array	User values
     * */
    public function mapStatus($userId) {
        $result = $this->facebook->get('/me/feed', $this->accessToken)->getGraphEdge()->asArray();
        
        $status = isset($result[0]) ? $result[0] : '';

        if (empty($status)) {
            return false;
        }

        $connectModel = CFactory::getModel('Connect');
        $status = isset($status['message']) ? $status['message'] : '';
        $rawStatus = $status;
        
        // @rule: Do not strip html tags but escape them.
        // $status = CStringHelper::escape($status);

        // @rule: Autolink hyperlinks
        //$status = CLinkGeneratorHelper::replaceURL($status);

        // @rule: Autolink to users profile when message contains @username
        //$status = CUserHelper::replaceAliasURL($status);

        // Reload $my from CUser so we can use some of the methods there.
        $my = CFactory::getUser($userId);
        $params = $my->getParams();

        // @rule: For existing statuses, do not set them.
        if ($connectModel->statusExists($status, $userId)) {
            return;
        }


        $act = new stdClass();
        $act->cmd = 'profile.status.update';
        $act->actor = $userId;
        $act->target = $userId;
        $act->title = $status;
        $act->content = '';
        $act->app = 'profile';
        $act->cid = $userId;
        $act->access = $params->get('privacyProfileView');

        $act->comment_id = CActivities::COMMENT_SELF;
        $act->comment_type = 'profile.status';
        $act->like_id = CActivities::LIKE_SELF;
        $act->like_type = 'profile.status';

        CActivityStream::add($act);

        //add user points
        CUserPoints::assignPoint('profile.status.update');

        // Update status from facebook.
        $my->setStatus($rawStatus);
    }

    public function getUser() {

        $session = JFactory::getSession();
        $token = $session->get('facebookAccessToken');

       // if(!isset($_SESSION['facebook_access_token']) || !$_SESSION['facebook_access_token']){
        if(!$token){
            $helper = $this->facebook->getJavaScriptHelper();
            $accessToken = $helper->getAccessToken();
            // OAuth 2.0 client handler
            $oAuth2Client = $this->facebook->getOAuth2Client();

            // Exchanges a short-lived access token for a long-lived one
            $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);

            $this->accessToken = (string)$longLivedAccessToken;
            $session->set('facebookAccessToken', $this->accessToken);
        }

        $this->response = $this->facebook->get('/me?fields=id,name', $this->accessToken);

        try {
            $user = $this->response->getGraphUser();
        } catch (FacebookApiException $exception) {
            return false;
        }

        return $user;
    }

    /**
     * Gets the html content of the Facebook login
     *
     * @return String the html data
     */
    public function getLoginHTML() {
        JFactory::getLanguage()->load('com_community');

        $config = CFactory::getConfig();

        $tmpl = new CTemplate();
        $tmpl->set('config', $config);

        return $tmpl->fetch('facebook.button');
    }

}