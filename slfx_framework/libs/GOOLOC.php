<?php

class GOOLOC
{

	/**


	http://maps.googleapis.com/maps/api/geocode/json?latlng=40.75983069999999,-73.989203&sensor=true




	{
	   "results" : [
	      {
	         "address_components" : [
	            {
	               "long_name" : "322",
	               "short_name" : "322",
	               "types" : [ "street_number" ]
	            },
	            {
	               "long_name" : "W 46th St",
	               "short_name" : "W 46th St",
	               "types" : [ "route" ]
	            },
	            {
	               "long_name" : "Hell's Kitchen",
	               "short_name" : "Hell's Kitchen",
	               "types" : [ "neighborhood", "political" ]
	            },
	            {
	               "long_name" : "Manhattan",
	               "short_name" : "Manhattan",
	               "types" : [ "sublocality", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "New York",
	               "types" : [ "locality", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "New York",
	               "types" : [ "administrative_area_level_2", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "NY",
	               "types" : [ "administrative_area_level_1", "political" ]
	            },
	            {
	               "long_name" : "United States",
	               "short_name" : "US",
	               "types" : [ "country", "political" ]
	            },
	            {
	               "long_name" : "10036",
	               "short_name" : "10036",
	               "types" : [ "postal_code" ]
	            }
	         ],
	         "formatted_address" : "322 W 46th St, New York, NY 10036, USA",
	         "geometry" : {
	            "location" : {
	               "lat" : 40.7599940,
	               "lng" : -73.9890250
	            },
	            "location_type" : "ROOFTOP",
	            "viewport" : {
	               "northeast" : {
	                  "lat" : 40.76134298029150,
	                  "lng" : -73.98767601970850
	               },
	               "southwest" : {
	                  "lat" : 40.75864501970850,
	                  "lng" : -73.99037398029151
	               }
	            }
	         },
	         "types" : [ "street_address" ]
	      },
	      {
	         "address_components" : [
	            {
	               "long_name" : "10109",
	               "short_name" : "10109",
	               "types" : [ "postal_code" ]
	            },
	            {
	               "long_name" : "Hell's Kitchen",
	               "short_name" : "Hell's Kitchen",
	               "types" : [ "neighborhood", "political" ]
	            },
	            {
	               "long_name" : "Manhattan",
	               "short_name" : "Manhattan",
	               "types" : [ "sublocality", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "New York",
	               "types" : [ "locality", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "New York",
	               "types" : [ "administrative_area_level_2", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "NY",
	               "types" : [ "administrative_area_level_1", "political" ]
	            },
	            {
	               "long_name" : "United States",
	               "short_name" : "US",
	               "types" : [ "country", "political" ]
	            }
	         ],
	         "formatted_address" : "New York, NY 10109, USA",
	         "geometry" : {
	            "location" : {
	               "lat" : 40.760,
	               "lng" : -73.98999999999999
	            },
	            "location_type" : "APPROXIMATE",
	            "viewport" : {
	               "northeast" : {
	                  "lat" : 40.76134898029149,
	                  "lng" : -73.98865101970848
	               },
	               "southwest" : {
	                  "lat" : 40.75865101970849,
	                  "lng" : -73.99134898029149
	               }
	            }
	         },
	         "types" : [ "postal_code" ]
	      },
	      {
	         "address_components" : [
	            {
	               "long_name" : "Hell's Kitchen",
	               "short_name" : "Hell's Kitchen",
	               "types" : [ "neighborhood", "political" ]
	            },
	            {
	               "long_name" : "Manhattan",
	               "short_name" : "Manhattan",
	               "types" : [ "sublocality", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "New York",
	               "types" : [ "locality", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "New York",
	               "types" : [ "administrative_area_level_2", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "NY",
	               "types" : [ "administrative_area_level_1", "political" ]
	            },
	            {
	               "long_name" : "United States",
	               "short_name" : "US",
	               "types" : [ "country", "political" ]
	            }
	         ],
	         "formatted_address" : "Hell's Kitchen, New York, NY, USA",
	         "geometry" : {
	            "bounds" : {
	               "northeast" : {
	                  "lat" : 40.7736770,
	                  "lng" : -73.97642820
	               },
	               "southwest" : {
	                  "lat" : 40.7573250,
	                  "lng" : -74.00336170
	               }
	            },
	            "location" : {
	               "lat" : 40.76375810,
	               "lng" : -73.99181810
	            },
	            "location_type" : "APPROXIMATE",
	            "viewport" : {
	               "northeast" : {
	                  "lat" : 40.7736770,
	                  "lng" : -73.97642820
	               },
	               "southwest" : {
	                  "lat" : 40.7573250,
	                  "lng" : -74.00336170
	               }
	            }
	         },
	         "types" : [ "neighborhood", "political" ]
	      },
	      {
	         "address_components" : [
	            {
	               "long_name" : "10036",
	               "short_name" : "10036",
	               "types" : [ "postal_code" ]
	            },
	            {
	               "long_name" : "Manhattan",
	               "short_name" : "Manhattan",
	               "types" : [ "sublocality", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "New York",
	               "types" : [ "locality", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "New York",
	               "types" : [ "administrative_area_level_2", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "NY",
	               "types" : [ "administrative_area_level_1", "political" ]
	            },
	            {
	               "long_name" : "United States",
	               "short_name" : "US",
	               "types" : [ "country", "political" ]
	            }
	         ],
	         "formatted_address" : "New York, NY 10036, USA",
	         "geometry" : {
	            "bounds" : {
	               "northeast" : {
	                  "lat" : 40.7687380,
	                  "lng" : -73.97811609999999
	               },
	               "southwest" : {
	                  "lat" : 40.72362490,
	                  "lng" : -74.0047860
	               }
	            },
	            "location" : {
	               "lat" : 40.76026190,
	               "lng" : -73.99328720
	            },
	            "location_type" : "APPROXIMATE",
	            "viewport" : {
	               "northeast" : {
	                  "lat" : 40.7687380,
	                  "lng" : -73.97811609999999
	               },
	               "southwest" : {
	                  "lat" : 40.7534810,
	                  "lng" : -74.0046830
	               }
	            }
	         },
	         "types" : [ "postal_code" ]
	      },
	      {
	         "address_components" : [
	            {
	               "long_name" : "Midtown",
	               "short_name" : "Midtown",
	               "types" : [ "neighborhood", "political" ]
	            },
	            {
	               "long_name" : "Manhattan",
	               "short_name" : "Manhattan",
	               "types" : [ "sublocality", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "New York",
	               "types" : [ "locality", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "New York",
	               "types" : [ "administrative_area_level_2", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "NY",
	               "types" : [ "administrative_area_level_1", "political" ]
	            },
	            {
	               "long_name" : "United States",
	               "short_name" : "US",
	               "types" : [ "country", "political" ]
	            }
	         ],
	         "formatted_address" : "Midtown, New York, NY, USA",
	         "geometry" : {
	            "bounds" : {
	               "northeast" : {
	                  "lat" : 40.8060980,
	                  "lng" : -73.94221689999999
	               },
	               "southwest" : {
	                  "lat" : 40.72732990,
	                  "lng" : -74.00882030
	               }
	            },
	            "location" : {
	               "lat" : 40.7690810,
	               "lng" : -73.9771260
	            },
	            "location_type" : "APPROXIMATE",
	            "viewport" : {
	               "northeast" : {
	                  "lat" : 40.8060980,
	                  "lng" : -73.94221689999999
	               },
	               "southwest" : {
	                  "lat" : 40.72732990,
	                  "lng" : -74.00882030
	               }
	            }
	         },
	         "types" : [ "neighborhood", "political" ]
	      },
	      {
	         "address_components" : [
	            {
	               "long_name" : "New York",
	               "short_name" : "New York",
	               "types" : [ "administrative_area_level_2", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "NY",
	               "types" : [ "administrative_area_level_1", "political" ]
	            },
	            {
	               "long_name" : "United States",
	               "short_name" : "US",
	               "types" : [ "country", "political" ]
	            }
	         ],
	         "formatted_address" : "New York, NY, USA",
	         "geometry" : {
	            "bounds" : {
	               "northeast" : {
	                  "lat" : 40.87927790,
	                  "lng" : -73.9070
	               },
	               "southwest" : {
	                  "lat" : 40.67983780,
	                  "lng" : -74.04725999999999
	               }
	            },
	            "location" : {
	               "lat" : 40.78306030,
	               "lng" : -73.97124880
	            },
	            "location_type" : "APPROXIMATE",
	            "viewport" : {
	               "northeast" : {
	                  "lat" : 40.87927790,
	                  "lng" : -73.9070
	               },
	               "southwest" : {
	                  "lat" : 40.67983780,
	                  "lng" : -74.04725999999999
	               }
	            }
	         },
	         "types" : [ "administrative_area_level_2", "political" ]
	      },
	      {
	         "address_components" : [
	            {
	               "long_name" : "Manhattan",
	               "short_name" : "Manhattan",
	               "types" : [ "sublocality", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "New York",
	               "types" : [ "locality", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "New York",
	               "types" : [ "administrative_area_level_2", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "NY",
	               "types" : [ "administrative_area_level_1", "political" ]
	            },
	            {
	               "long_name" : "United States",
	               "short_name" : "US",
	               "types" : [ "country", "political" ]
	            }
	         ],
	         "formatted_address" : "Manhattan, New York, NY, USA",
	         "geometry" : {
	            "bounds" : {
	               "northeast" : {
	                  "lat" : 40.8822140,
	                  "lng" : -73.9070
	               },
	               "southwest" : {
	                  "lat" : 40.67954790,
	                  "lng" : -74.0472850
	               }
	            },
	            "location" : {
	               "lat" : 40.78414840,
	               "lng" : -73.96614070
	            },
	            "location_type" : "APPROXIMATE",
	            "viewport" : {
	               "northeast" : {
	                  "lat" : 40.8200450,
	                  "lng" : -73.90331300000001
	               },
	               "southwest" : {
	                  "lat" : 40.6980780,
	                  "lng" : -74.03514899999999
	               }
	            }
	         },
	         "types" : [ "sublocality", "political" ]
	      },
	      {
	         "address_components" : [
	            {
	               "long_name" : "New York",
	               "short_name" : "New York",
	               "types" : [ "locality", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "NY",
	               "types" : [ "administrative_area_level_1", "political" ]
	            },
	            {
	               "long_name" : "United States",
	               "short_name" : "US",
	               "types" : [ "country", "political" ]
	            }
	         ],
	         "formatted_address" : "New York, NY, USA",
	         "geometry" : {
	            "bounds" : {
	               "northeast" : {
	                  "lat" : 40.91524140,
	                  "lng" : -73.70027209999999
	               },
	               "southwest" : {
	                  "lat" : 40.4959080,
	                  "lng" : -74.25908790
	               }
	            },
	            "location" : {
	               "lat" : 40.71435280,
	               "lng" : -74.00597309999999
	            },
	            "location_type" : "APPROXIMATE",
	            "viewport" : {
	               "northeast" : {
	                  "lat" : 40.91524140,
	                  "lng" : -73.70027209999999
	               },
	               "southwest" : {
	                  "lat" : 40.4959080,
	                  "lng" : -74.25573489999999
	               }
	            }
	         },
	         "types" : [ "locality", "political" ]
	      },
	      {
	         "address_components" : [
	            {
	               "long_name" : "New York",
	               "short_name" : "NY",
	               "types" : [ "administrative_area_level_1", "political" ]
	            },
	            {
	               "long_name" : "United States",
	               "short_name" : "US",
	               "types" : [ "country", "political" ]
	            }
	         ],
	         "formatted_address" : "New York, USA",
	         "geometry" : {
	            "bounds" : {
	               "northeast" : {
	                  "lat" : 45.0158650,
	                  "lng" : -71.85626990
	               },
	               "southwest" : {
	                  "lat" : 40.49594540,
	                  "lng" : -79.76214379999999
	               }
	            },
	            "location" : {
	               "lat" : 43.29942850,
	               "lng" : -74.21793260000001
	            },
	            "location_type" : "APPROXIMATE",
	            "viewport" : {
	               "northeast" : {
	                  "lat" : 45.01261130,
	                  "lng" : -71.85626990
	               },
	               "southwest" : {
	                  "lat" : 40.49594540,
	                  "lng" : -79.76214379999999
	               }
	            }
	         },
	         "types" : [ "administrative_area_level_1", "political" ]
	      },
	      {
	         "address_components" : [
	            {
	               "long_name" : "United States",
	               "short_name" : "US",
	               "types" : [ "country", "political" ]
	            }
	         ],
	         "formatted_address" : "United States",
	         "geometry" : {
	            "bounds" : {
	               "northeast" : {
	                  "lat" : 71.3898880,
	                  "lng" : -66.94976079999999
	               },
	               "southwest" : {
	                  "lat" : 18.91106420,
	                  "lng" : 172.45469660
	               }
	            },
	            "location" : {
	               "lat" : 37.090240,
	               "lng" : -95.7128910
	            },
	            "location_type" : "APPROXIMATE",
	            "viewport" : {
	               "northeast" : {
	                  "lat" : 49.380,
	                  "lng" : -66.940
	               },
	               "southwest" : {
	                  "lat" : 25.820,
	                  "lng" : -124.390
	               }
	            }
	         },
	         "types" : [ "country", "political" ]
	      }
	   ],
	   "status" : "OK"
	}




	documentation:
	https://developers.google.com/maps/documentation/geocoding/#ReverseGeocoding






	 		"address_components" : [
	            {
	               "long_name" : "322",
	               "short_name" : "322",
	               "types" : [ "street_number" ]
	            },
	            {
	               "long_name" : "W 46th St",
	               "short_name" : "W 46th St",
	               "types" : [ "route" ]
	            },
	            {
	               "long_name" : "Hell's Kitchen",
	               "short_name" : "Hell's Kitchen",
	               "types" : [ "neighborhood", "political" ]
	            },
	            {
	               "long_name" : "Manhattan",
	               "short_name" : "Manhattan",
	               "types" : [ "sublocality", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "New York",
	               "types" : [ "locality", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "New York",
	               "types" : [ "administrative_area_level_2", "political" ]
	            },
	            {
	               "long_name" : "New York",
	               "short_name" : "NY",
	               "types" : [ "administrative_area_level_1", "political" ]
	            },
	            {
	               "long_name" : "United States",
	               "short_name" : "US",
	               "types" : [ "country", "political" ]
	            },
	            {
	               "long_name" : "10036",
	               "short_name" : "10036",
	               "types" : [ "postal_code" ]
	            }
	         ],
	         "formatted_address" : "322 W 46th St, New York, NY 10036, USA",
	          "geometry" : {
	            "location" : {
	               "lat" : 40.7599940,
	               "lng" : -73.9890250
	            },
	            "location_type" : "ROOFTOP",
	            "viewport" : {
	               "northeast" : {
	                  "lat" : 40.76134298029150,
	                  "lng" : -73.98767601970850
	               },
	               "southwest" : {
	                  "lat" : 40.75864501970850,
	                  "lng" : -73.99037398029151
	               }
	            }
	         },



	*/

	// vars
	private $json = null;

	public function __construct() {}

	private function setCity($data) {			$this->city = $data;	}
	private function setState($data) {			$this->state = $data;	}
	private function setCountry($data) {		$this->country = $data;	}

	public function getCity() {					return $this->city;		}
	public function getState() {				return $this->state;	}
	public function getCountry() {				return $this->country;	}



	public function lookupByLongLat($long, $lat){

		//$string = str_replace (" ", "+", urlencode($string));
		//$details_url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$string."&sensor=false";
		$details_url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=" . (float)$lat . "," . (float)$long . "&sensor=false";

		$result = file_get_contents($details_url);
		$json = json_decode($result);

		// If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
		if ($json->status != 'OK') {
			return false;
		}

		// commit data to class object
		$this->json = $json->results;

		return true;
	}


	public function registerLocality() {

		$json = $this->json;

		foreach( $json as $result ) {
	        foreach($result->address_components as $addressPart) {
				// find city
				if( ( in_array( 'locality', $addressPart->types ) ) && ( in_array( 'political', $addressPart->types ) ) )
				   $this->setCity( $addressPart->long_name );
				// find state
				else if( ( in_array( 'administrative_area_level_1', $addressPart->types ) ) && ( in_array( 'political', $addressPart->types ) ) )
				    $this->setState( $addressPart->short_name );
				// country
				else if( ( in_array( 'country', $addressPart->types ) ) && ( in_array( 'political', $addressPart->types ) ) )
				    $this->setCountry( $addressPart->long_name );
	        }
	    }

	    return true;
	}


}

?>