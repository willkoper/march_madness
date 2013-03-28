<?php

function db_handle (){
	
	$dsn = 'mysql:dbname=madness;host=localhost';
	$user = 'xxxx';
	$p = 'xxxxx';
	try{
		$c = new PDO($dsn, $user, $p);
		return $c;
	}
	catch (PDOException $c){
		echo "connection failed";
		return false;
	}
}
