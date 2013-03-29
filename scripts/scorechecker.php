<?php
$script_start = time();
include ("db_config.php");

function get_games_list(){
	$pdo = db_handle();
	$smt = $pdo->query("SELECT gameID, under_dog from today_matchups");
	$results = $smt->fetchall(PDO::FETCH_ASSOC);
	$gameDict = array();
	foreach($results as $game){
		$gameDict[$game["gameID"]] = $game["under_dog"];
	}
	return $gameDict;
}
function jsonp_decode($jsonp, $assoc = false) { // PHP 5.3 adds depth as third parameter to json_decode
    if($jsonp[0] !== '[' && $jsonp[0] !== '{') { // we have JSONP
       $jsonp = substr($jsonp, strpos($jsonp, '('));
    }
    return json_decode(trim($jsonp,'();'), $assoc);
}
function read_game_data(){
	
	//get the game data
	$url = 'http://data.ncaa.com/jsonp/gametool/brackets/championships/basketball-men/d1/2012/data.json?callback=foo';
	// $url = '../data/dummydata.json';
	$raw_data = file_get_contents($url);
	$timeToFetch = time() - $fetchStart;
	echo("data fetched in $timeToFetch seconds. parsing ....");
	$data = jsonp_decode($raw_data, true);
	$gamesToWatch = get_games_list();
	$output = array();
	//build asscociative array from gameids
	foreach ($data["games"] as $game){
		$gameID = $game["contestId"];
		if (array_key_exists($gameID, $gamesToWatch)){
			
			$minsLeft = substr($game["timeclock"], 0, 1);
			if($minsLeft && $minsLeft <5){
				$fave = $gamesToWatch[$gameID]["fave"];
				$faveScore = $game[$fave]["score"];
				$dog = $gamesToWatch[$gameID]["under_dog"];
				$dogScore = $game[$dog]["score"];
				if($faveScore - $dogScore < 10){
					$output[$gameID] = $game;
				}


			}
			
		}
		

	}
	return $output;
}
$games = read_game_data();
$elapsed = time() - $script_start;
var_dump($games);
echo "data fetched and parsed in $elapsed seconds";
