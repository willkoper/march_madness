<?php
include ('db_config.php'); //defines db_handle() function that returns pdo
date_default_timezone_set("America/New_York");
function jsonp_decode($jsonp, $assoc = false) { // PHP 5.3 adds depth as third parameter to json_decode
    if($jsonp[0] !== '[' && $jsonp[0] !== '{') { // we have JSONP
       $jsonp = substr($jsonp, strpos($jsonp, '('));
    }
    return json_decode(trim($jsonp,'();'), $assoc);
}
function read_game_data(){
	//Get the database ready
	$insertion = prepare_to_insert();
	//get the game data
	$raw_data = file_get_contents('http://data.ncaa.com/jsonp/gametool/brackets/championships/basketball-men/d1/2012/data.json?callback=foo');
	echo("data fetched. parsing ....");
	$data = jsonp_decode($raw_data, true);
	


	foreach($data["games"] as $game){
		//reset variables
		$underdog = null;
		$gameData = null;
		//skip the game if it's over already
		if ($game["currentPeriod" === "Final"]){
			echo "Game Over";
			continue;
		}
		else if($game["away"]["names"]["full"] === ""){

			continue;
		}
		else {
			//find out who the underdog is
			if ($game["away"]["isTop"] === "F"){
				$underdog = "away";
				$fave = "home";
			}
			else if($game["home"]["isTop"] === "F"){
				$underdog = "home";
				$fave = "away";
			}
			$gameStart =$game["gameDate"].' '.$game['startTime'];
			$gameStart = str_replace('ET', 'EST', $gameStart);
			$gameDT = new DateTime($gameStart);
			$fmtGameStart = $gameDT->format('Y-m-d H:i');
			$gameData = array(
				":gameID" => $game["contestId"],
				":game_start" => $fmtGameStart,
				":under_dog" => $underdog,
				":fave" => $fave);
			var_dump($gameData);
			add_game_to_matchups($gameData, $insertion);
		}
	}

}
function prepare_to_insert(){
	$pdo = db_handle(); //defined in db_config.php just a pdo object.
	$sql = 'insert into matchups values(:gameID, :game_start, :under_dog, :fave)';
	$smt = $pdo->prepare($sql);
	return $smt;

}
function add_game_to_matchups($gameArray, $smt){
	//insert game into table
	//script should only run once a week, so don't worry about updates
	
	
	$inserted = $smt->execute($gameArray);
	if($inserted !== false){
		echo "game added!";
	}
	else{
		var_dump ($smt->errorInfo());
	}
}
read_game_data();