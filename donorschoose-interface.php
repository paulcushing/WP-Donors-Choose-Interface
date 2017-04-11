<?php
/*
Plugin Name: WP Donors Choose Interface
Plugin URI: https://www.rektproductions.com
Description: This plugin will provide the functions to interact with the DonorsChoose.org API.
Version: 0.1
Author: Paul Cushing
License:GPL2
Copyright 2015 Paul Cushing  (email : pcushing@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/



/**
 * Creates a connection to DonorsChoose.org via their API    
 *
 * @param associative array $query - Configures the get parameters 
 * 
 * @param string $api_key - Set API as assigned by DonorsChoose.org (defaults to testing key)
 *
 * @use $DC = new DCIterface($api_key);  
 *
 * DonorsChoose API documentation at https://data.donorschoose.org/docs/overview/
 */

class DCInterface
{
	/*  Retrieve the list of projects from DonorsChoose.org    */
	/*  $q_parts should be an associative array of parameters  */
	var $key;
	
	public function __construct($api_key) 
	{
		if(isset($api_key)) {
			$this->key = $api_key;
		} else {
			$this->key = 'DONORSCHOOSE';
		}
	}
	
	public function getList($q_parts) 
	{   
		$ch = curl_init();
		/* Configure the URL request */
		$url = 'http://api.donorschoose.org/common/json_feed.html?';
		if (isset($q_parts['state'])) {
			$url .= '&state=' . $q_parts['state'];
		} else {
		$url .= '&state=ID';
		}
		if (isset($q_parts['max'])) {
		$url .= '&max=' . $q_parts['max'];
		} else {
		$url .= '&max=50';
		}
		$url .= '&index=' . $q_parts['index'];
		if (isset($q_parts['search'])) {
			$url .= '&keywords=' . $q_parts['search'];
		}
		if (isset($q_parts['subject'])) {
			$url .= $q_parts['subject'];
		}
		if (isset($q_parts['grade'])) {
			$url .= $q_parts['grade'];
		}
		if (isset($q_parts['sortby'])) {
			$url .= '&sortBy=' . $q_parts['sortby'];
		}
		if (isset($q_parts['resource'])) {
			$url .= '&proposalType=' . $q_parts['resource'];
		}
		if (isset($q_parts['historical'])) {
			$url .= '&historical=' . $q_parts['historical'];
		}
		$url .= '&APIKey=' . $this->key;  /* use DONORSCHOOSE for testing */

		/* Use cURL to retrieve the list from DonorsChoose.org */
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if( ! $data = curl_exec($ch)) {
			trigger_error(curl_error($ch));
		}
		curl_close($ch);

		/* Turn the data we got back into a hash of hashes */
		$dcInfo = json_decode($data, true);

		# Pull out the list of proposals
		$proposals = $dcInfo['proposals'];

		# Find out how many we got back
		$totals['projects'] = $dcInfo['totalProposals'];

		$dc_output = array( "proposals" => $proposals, "totals" => $totals );
		
		return $dc_output;
	}


	/* Retrieve a specific project for display */
	public function getSingle($id) {
		$ch = curl_init();
		/* Retrieve individual teacher proposals */
		$url = 'http://api.donorschoose.org/common/json_feed.html?';
		$url .= 'id=' . $id;
		$url .= '&APIKey=' . $this->key;  /* use DONORSCHOOSE FOR TESTING */
		$url .= '&showSynopsis=true';

		/* Use cURL to retrieve the list of projects from DonorsChoose.org */
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if( ! $data = curl_exec($ch)) {
			trigger_error(curl_error($ch));
		}
		curl_close($ch);

		/* Turn the data we got back into a hash of hashes */
		$dcInfo = json_decode($data, true);

		/* Pull out the proposal */
		$proposal = $dcInfo['proposals'];

		/* Uncomment to see all the data available about the proposals retrieved */
		#print_r($proposals);

		return $proposal;
	}


	/* Get the link to the specific project with provided id */
	public function projectLink($id) {
		return "/projects/" . $id . "/";
	}

	/* Get the link to fund the project */
	public function fundLink($id,$amount) {
		return "https://secure.donorschoose.org/donors/givingCart.html?proposalid=$id&donationAmount=$amount&utm_source=api&utm_medium=feed&utm_content=fundlink&utm_campaign=DONORSCHOOSE";
	}

	/* Create the block of text from the synopsis */
	public function prettySynopsis($synopsis) {
		$text = explode("&lt;br/&gt;&lt;br/&gt;", $synopsis); 
		$pretty_text = "<h3>My Students</h3><p class=\"p1\">" . $text[0] . "</p><h3>My Project</h3><p class=\"p2\">" . $text[1] . "</p>";
		if(isset($text[2])) $pretty_text .= "<p>" . $text[2] . "</p>";
		if(isset($text[3])) $pretty_text .= "<p>" . $text[3] . "</p>";
		if(isset($text[4])) $pretty_text .= "<p>" . $text[4] . "</p>";
		
		return $pretty_text;
	}

	public function getSubject($ref) {
		switch ($ref) {
		case 1:
			$subjectArg['query'] = "&subject1=-1";
		$subjectArg['name'] = "Music and the Arts";
			break;
		case 2:
			$subjectArg['query'] = "&subject2=-2";
		$subjectArg['name'] = "Health and Sports";
			break;
		case 6:
			$subjectArg['query'] = "&subject6=-6";
		$subjectArg['name'] = "Literacy and Language";
			break;
		case 3:
			$subjectArg['query'] = "&subject3=-3";
		$subjectArg['name'] = "History and Civics";
			break;
		case 4:
			$subjectArg['query'] = "&subject4=-4";
		$subjectArg['name'] = "Math and Science";
			break;
		case 7:
			$subjectArg['query'] = "&subject7=-7";
		$subjectArg['name'] = "Special Needs";
			break;
		case 5:
		$subjectArg['query'] = "&subject5=-5";
		$subjectArg['name'] = "Applied Learning";
		}
		return $subjectArg;
	}

	public function getGrade($ref) {
		switch ($ref) {
		case 1:
			$gradeArg['query'] = "&gradeType=1";
		$gradeArg['name'] = "PreK-2";
			break;
		case 2:
			$gradeArg['query'] = "&gradeType=2";
		$gradeArg['name'] = "3-5";
			break;
		case 3:
			$gradeArg['query'] = "&gradeType=3";
		$gradeArg['name'] = "6-8";
			break;
		case 4:
			$gradeArg['query'] = "&gradeType=4";
		$gradeArg['name'] = "High School";
			break;
		}
		return $gradeArg;
	}

}



?>
