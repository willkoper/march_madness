<?php

function db_handle (){
	
	$dsn = 'mysql:dbname=madness;host=localhost';
	$user = 'will';
	$p = 'koper';
	try{
		$c = new PDO($dsn, $user, $p);
		return $c;
	}
	catch (PDOException $c){
		echo "connection failed";
		return false;
	}
}