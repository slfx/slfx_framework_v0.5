<?php

/**
  * Zipcode Lookup Class
  *
  * php 4,5
  *
  *
  * LICENSE: Use of this library is governed by the Creative Commons License Attribution 2.5.
  * You can check it out at: http://creativecommons.org/licenses/by/2.5/legalcode
  *
  * - You may copy, distribute, and eat this code as you wish. You must give me credit for
  * writing it. You may not misrepresent yourself as the author of this code.
  * - You may not use this code in a commercial setting without prior consent from me.
  * - If you make changes to this code, you must make the changes available under a license like
  * this one.
  *
  * @category   Geo-Location
  * @package    ZIPCODE
  * @author     R. Fritz Washabaugh <general@nucleusdevelopment.com>
  * @copyright  2012 Nucleus Development - R. Fritz Washabaugh
  * @license    http://creativecommons.org/licenses/by/2.5/legalcode CC 2.5
  * @link       http://www.nucleusdevelopment.com/code/do/zipcode
  *
  **/

/**
  * Zipcode Lookup Class
  *
  * @version	Release: 1.0
  * @link		http://www.nucleusdevelopment.com/code/do/zipcode
  * @ModDate	2012-05-15
  *
  **/

// {{{ ZIPCODE
/**
  * Zipcode Lookup Class
  *
  * Used for:
  * 1) looking up the zip of a long and lat,
  * 2) looking up the disance between two zipcodes,
  * 3) finding all the zipcodes in the radius of a zipcode.
  *
  *
  * Usage example:
  * <code>
  *
  * // invoke
  * include_once($DIR_LIBS . 'ZIPCODE.php');
  * $zipcode = new ZIPCODE();
  *
  * // Lookup zipcode by long and lat
  * $zip_r = $zipcode->parseDistanceByGeoLoc(
  *					$longitude, //
  *					$latitude,
  *					0.7, // range, default in miles
  *					1 // limit result, int, (optional)
  *					'M' // Units, (Optional)
  *			  								);
  *		Units ['M'=>'miles' default, 'K'=>'Kilometers', 'N'=>'Nautical']
  *
  *
  * if(is_array($zip_r))
  * 	echo explode(',', $zip_r);
  * else echo $zip_r;
  *
  *
  * // lookup all zipcodes within range
  * $array = parseAllDistancesByZip($zip,$radius,$units=null,$limit=null);
  *
  * </code>
  *
  *
  * Associated Database:
  * http://www.nucleusdevelopment.com/downloads/zipcode.sql
  *
  *
  * Version Changes:
  * v1.0 - 2012/05/15
  * Complete overhaul, and optimization
  *
  *
  * v0.9b - 2011/04/24
  * release.
  *
  *
  *
  * @author R. Fritz Washabaugh
  * @package ZIPCODE
  * @access public
  **/

class ZIPCODE
{

	// (objects)
	var $zips_arry = array();
	var $distance;

	var $lat1;
	var $long1;
	var $lat2;
	var $long2;

	var $city;
	var $state;


	// (constructor)
	function ZIPCODE() {

	}

	function getDistance() {
		return $this->distance;
	}


	function getZipData($num = null, $type = null) {

		if($type != null && is_numeric( $num ) )
			return $this->zips_arry[$num][$type];

		if( is_numeric( $num ) )
			return $this->zips_arry[$num];

		if($num == null)
			return $this->zips_arry;

	}


	function quickDistanceLookup( $origin = null, $destination = null, $unit = null ) {

		if( $origin == '' || $destination == '' )
			return false;

		if( $origin == $destination )
			return 0;

		$this->readByZip( array((int)$origin, (int)$destination) ); // input zips

		return $this->calcDistance($unit); // return distance
	}


	function readByZip( $zips_arry ) {

		$q = "zzip='" . addslashes( (int)$zips_arry ) . "'";

		if( is_array( $zips_arry ) )
			$q = "zzip = '" . addslashes( (int)$zips_arry[0] ) . "' OR zzip = '" .
			addslashes( (int)$zips_arry[1] ) . "'";

		return $this->read( $q );
	}


	// (public)
	function calcDistance($long1=null, $lat1=null, $long2=null, $lat2=null, $unit=null) {

		if($long1 == null || $long2 == null || $lat1 == null || $lat2 == null )
			return false;

		$this->zips_arry[0]['long'] = (float)$long1;
		$this->zips_arry[1]['long'] = (float)$long2;
		$this->zips_arry[0]['lat'] = (float)$lat1;
		$this->zips_arry[1]['lat'] = (float)$lat2;

		// default unit is miles
		$theta = $this->zips_arry[0]['long'] - $this->zips_arry[1]['long'];

		$dist 	= sin( deg2rad( $this->zips_arry[0]['lat'] ) )
				* sin( deg2rad( $this->zips_arry[1]['lat'] ) )
				+ cos( deg2rad( $this->zips_arry[0]['lat'] ) )
				* cos( deg2rad( $this->zips_arry[1]['lat'] ) )
				* cos( deg2rad( $theta ) );

		$dist = acos( $dist );
		$dist = rad2deg( $dist );
		$distance = $dist * 60 * 1.1515;

		$unit = strtoupper( $unit );

		if( $unit != 'M' && $unit != null )
			$distance = ZIPCODE::convertFromUnit( 'M', $unit ) * $distance;

		$distance = round( $distance, 1 );

		return $distance;
	}


	// New very fast query function
	function parseAllDistancesByZip( $zip, $radius, $units=null, $limit=null ) {

		if(!empty($units) && $units != 'M')
			$radius = $this->convertFromUnit('M',$units * (int)$radius);

		if(!empty($limit))
			$limit = "LIMIT " . (int)$limit;

        $query ='SELECT * FROM '.sql_table('zipcode'). ' WHERE zzip="'.(int)$zip.'"';
        $result = sql_query($query,'get zipcode '.(int)$zip.' info');

        if(mysql_num_rows($result) > 0) {

			$obj = mysql_fetch_object($result);

			$query = 'SELECT zzip FROM '. sql_table('zipcode') .
				' WHERE (POW((69.1*(zlongitude-"' .
				$obj->zlongitude . '")*cos(' . $obj->zlatitude .
				'/57.3)),"2")+POW((69.1*(zlatitude-"' .
				$obj->zlatitude . '")),"2"))<(' . (int)$radius .
				'*' . (int)$radius . ') ' .  $limit;

            $result = sql_query( $query, 'get zipcodes within radius of ' . $zip );

			if(mysql_num_rows($result) > 0) {
                while( $obj = mysql_fetch_object( $result ) ) {
                    $zip_array[] = $obj->zzip;
                }
            }
        }

		return $zip_array;
    }


	function parseDistanceByGeoLoc($long, $lat, $radius, $limit=null, $units=null) {

		if( !empty( $units ) && $units != 'M' )
			$radius = $this->convertFromUnit( 'M', $units * (int)$radius );

		if( !empty( $limit ) )
			$limit = "LIMIT " . (int)$limit;

		$query = 'SELECT zzip FROM ' . sql_table( 'zipcode' ) .
			' WHERE (POW((69.1*(zlongitude-"' .
			addslashes( $long ) . '")*cos(' . addslashes( $long )  .
			'/57.3)),"2")+POW((69.1*(zlatitude-"' .
			$lat . '")),"2"))<(' . (float)$radius .
			'*' . (float)$radius . ') ' . $limit;

      	$result = sql_query( $query, 'get zips with radius of long lat' );

		if( mysql_num_rows( $result ) > 1 ) {
     		while( $obj = mysql_fetch_object( $result ) ) {
   		       $zipArray[] = str_pad( $obj->zzip, 5,  "0", STR_PAD_LEFT);
     		}

			return $zipArray;
      	}

      	$obj = mysql_fetch_object($result);

     	return str_pad( $obj->zzip, 5,  "0", STR_PAD_LEFT);
    }


	// (private)
	function read($where) {
		// read info
		$query = 'SELECT * FROM ' . sql_table('zipcode') . ' WHERE ' . $where;

		$count = 0;

		$res = sql_query($query, 'read origin and dest zipcode');
		while( false != ($obj = mysql_fetch_object($res) ) ) {

			$this->zips_arry[$count] = array();
			$this->zips_arry[$count]['lat'] = $obj->zlatitude;
			$this->zips_arry[$count]['long'] = $obj->zlongitude;
			$this->zips_arry[$count]['city'] = ucfirst( strtolower( $obj->zcity ));
			$this->zips_arry[$count]['state'] = $obj->zstate;
			$count++;

		}

		return mysql_num_rows($res);
	}


	function getUnit($unit = 'M') {
		if(empty($unit))
			$unit = 'M';

		switch ($unit) {
			case 'M':
				$r = _MILES;
				break;
			case 'N':
				$r =  _NAUTICAL_MILES;
				break;
			case 'K':
				$r = _KILOMETERS;
				break;
		}
		return $r;
	}


	function setUnit($name) {
		$name = strtolower($name);
		switch ($name) {
			case 'miles':
				$r = 'M';
				break;
			case 'nautical miles':
				$r = 'N';
				break;
			case 'kilometers':
				$r = 'K';
				break;
		}
		return $r;
	}


	function convertFromUnit($oldUnit,$newUnit) {

		$newUnit = strtoupper($newUnit);
		$oldUnit = strtoupper($oldUnit);

		switch($oldUnit) {
			case 'M':
				switch($newUnit) {
					case 'M':
						$r = 1;
						break;
					case 'N':
						$r = 0.8690;
						break;
					case 'K':
						$r = 1.609;
						break;
				}
				break;
			case 'N':
				switch($newUnit) {
					case 'M':
						$r = 1.151;
						break;
					case 'N':
						$r = 1;
						break;
					case 'K':
						$r = 1.852;
						break;
				}
				break;
			case 'K':
				switch($newUnit) {
					case 'M':
						$r = 0.6214;
						break;
					case 'N':
						$r = .5400;
						break;
					case 'K':
						$r = 1;
						break;
				}
				break;
		}
		return $r;

	}



}

?>
