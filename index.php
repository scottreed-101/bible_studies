<?php

require_once("Zebra_cURL/Zebra_cURL.php");

function fetchReadingReferencesFromDay( $day = null ) {
	if ( $day === null ) $day = date( 'Y-m-d' );

	$curl = new Zebra_cURL();

	$curl->cache('cache', 86400);
	$reading_service = $curl->scrap( 'http://www.ewtn.com/se/readings/readingsservice.svc/day/' . $day . '/en', true);

	$reading_service = json_decode($reading_service, true);

	$readings = array();

	foreach ( $reading_service['ReadingGroups'] as $reading_group ) {

		foreach ( $reading_group['Readings'] as $reading ) {
			foreach ( $reading['Citations'] as $option ) {
				$readings[] = $option['Reference'];
				break;
			}
		}
	}

	return $readings;
}

function fetchReadings( $references = array() ) {
	$payload = array(
		'Language' => 'en',
		'References' => $references
	);

	$curl = new Zebra_cURL();

	$curl->cache('cache', 86400);

	$curl->post(array(
	    'http://www.ewtn.com/se/readings/readingsservice.svc/books' => '{"References":["1 Thessalonians 4:9-11","Psalms 98:1, 7-9","Matthew 25:14-30"],"Language":"en"}'
	), 'readingCallback');
}

function readingCallback ( $response ) {
	var_dump($response);
}

$references = fetchReadingReferencesFromDay();

$readings = fetchReadings($references);
