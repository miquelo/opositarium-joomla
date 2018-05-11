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

require_once( JPATH_ROOT .'/components/com_community/libraries/core.php' );

try {
	JFactory::getLanguage()->load('com_community.country');
} catch (Exception $e) {
	// do nothing
}

class CMapsHelper
{
	/**
	 *	Returns an object of data containing user's address information
	 *
	 *	@access	static
	 *	@params	int	$userId
	 *	@return stdClass Object
	 **/
	static public function getAddress( $userId )
	{
		$user		= CFactory::getUser( $userId );
		$config		= CFactory::getConfig();

		// try using fieldcodelocation as detault address
		$obj		= new stdClass();

		$obj->location 	= $user->getInfo($config->get('fieldcodelocation', ''), false);
		$obj->street	= $user->getInfo($config->get('fieldcodestreet', ''), false);
		$obj->city		= $user->getInfo($config->get('fieldcodecity', ''), false);
		$obj->state		= $user->getInfo($config->get('fieldcodestate', ''), false);
		$obj->country	= JText::_($user->getInfo($config->get('fieldcodecountry', ''), false));
		$obj->zip 		= $user->getInfo($config->get('fieldcodepostcode', ''), false);
		
		// make sure location field value took from google (in JSON)
		$location = json_decode($obj->location);
		if (json_last_error() === 0 && $obj->location) {
			// return location object only
		    unset($obj->street);
		    unset($obj->city);
		    unset($obj->state);
		    unset($obj->country);
		    unset($obj->zip);
		} else {
			// return street, city, state, country, zip
			unset($obj->location);
		}
		
		return $obj;
	}

	static public function getRadiusDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $unit = 'miles', $userId = null){
		// check if google maps integration properly setup
		if (!CMapsHelper::googleMapSetup()) {
			return false;
		}

        $config = CFactory::getConfig();

		// check the user location
		if ($userId) {
			$location = implode((array)(CMapsHelper::getAddress($userId)), '');
			
			if (empty($location)) {
				return false;
			} else {
				$location = json_decode($location);
				if (json_last_error() === 0) {
				    if (isset($location->name) && empty($location->name)) {
				    	return false;
				    }
				}
			}
		}
		
        $unit = $config->get('advanced_search_units');
		switch($unit){
            case 'metric' : //km
                $earthRadius = 6371;
                break;
            default:
                $earthRadius = 6371/1.609344;

        }

		if($latitudeFrom == 255 || $longitudeFrom == 255 || $latitudeTo == 255 || $longitudeTo == 255){
			return false;
		}

		$latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);

		$latTo = deg2rad($latitudeTo);
		$lonTo = deg2rad($longitudeTo);

		$lonDelta = $lonTo - $lonFrom;
		$a = pow(cos($latTo) * sin($lonDelta), 2) +
		pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
		$b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

		$angle = atan2(sqrt($a), $b);
		$distance = $angle * $earthRadius;

		return (int)round($distance);
	}

	static public function getLocationData($location) {
		$location = json_decode($location);
		if (json_last_error() === 0) {
		    if (isset($location->name)) {
		    	return $location;
		    }
		} else {
			return $location = (object)array('name' => $location, 'desc' => '', 'lat' => '', 'lng' => '');
		}
	}

	/**
	 *	Google Maps integration setup check
	 *
	 *	@access	static
	 *	@params	int	$userId
	 *	@return boolean
	 **/
	static public function googleMapSetup() {
		$config	= CFactory::getConfig();

		if ($config->get('googleapikey', '')) {
			if ($config->get('fieldcodelocation', '')) {
				return true;
			} else if ($config->get('fieldcodestreet') || $config->get('fieldcodecity') || $config->get('fieldcodestate') || $config->get('fieldcodecountry') || $config->get('fieldcodepostcode')) {
				return true;
			}
		}
		
		return false;
	}
}