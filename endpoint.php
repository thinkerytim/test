<?php

// ajax endpoint 

try {    
  $DBH = new PDO("mysql:host=localhost;dbname=bcs", 'root', 'sea1sea');  
} catch(PDOException $e) {  
    echo $e->getMessage();  
    die();
}  


$poll = isset($_GET['poll']) ? $_GET['poll'] : 2;
$year = isset($_GET['year']) ? $_GET['year'] : 2012;
$week = isset($_GET['week']) ? $_GET['week'] : 1;
$task = isset($_GET['task']) ? $_GET['task'] : 'getRanks';

switch ($task){
	case 'getRanks':
		getRanks($poll, $week, $year);
		break;
	case 'getContracts':
		getContracts($poll, $week, $year);
	break;
	case 'deleteContract':
		deleteContract($id);
	break;
	case 'addContract':
		addContract($poll, $week, $year);
	break;
	case 'getUsers':
		getUsers();
	break;
}

function getRanks($poll, $week, $year){
	global $DBH;
	// get teams
	$ranks = array();
	$query = 'SELECT a.*, b.shortname as name FROM ranks a'
			.' JOIN teams b ON a.team = b.id'
			.' WHERE a.week = '.(int) $week.' AND a.poll = '.(int) $poll.' AND a.year = '.(int) $year.';';
	
	$STH = $DBH->query($query);  
	$STH->setFetchMode(PDO::FETCH_ASSOC);  
	  
	while($row = $STH->fetch()) {  
		$ranks[$row['rank']] = array($row['name'], $row['price']);
	}
	
	echo json_encode($ranks); 	
}

function getContracts($poll, $week, $year){
	global $DBH;
	// get teams
	$contracts = array();
	$query = 'SELECT a.*, b.shortname as name FROM trades a'
			.' JOIN teams b ON a.team = b.id'
			.' JOIN users c ON a.user = c.id'
			.' WHERE a.week <= '.(int) $week.' AND a.poll = '.(int) $poll.' AND a.year = '.(int) $year.';';
	
	//$STH = $DBH->query($query);  
	//$STH->setFetchMode(PDO::FETCH_ASSOC);  
	  
	//while($row = $STH->fetch()) {  
	//	$contracts[] = $row;
	//}
	
	echo json_encode($contracts); 	
}

function getTeams($poll, $week, $year){
	global $DBH;
	// get teams
	$teams = array();
	$query = 'SELECT a.shortname, b.rank as rank FROM teams a'
			.' JOIN ranks b ON a.id = b.team'
			.' WHERE b.week = '.(int) $week.' AND b.poll = '.(int) $poll.' AND b.year = '.(int) $year.';';
	
	$STH = $DBH->query($query);  
	$STH->setFetchMode(PDO::FETCH_ASSOC);  
	  
	while($row = $STH->fetch()) {  
		$teams[] = $row;
	}
	
	echo json_encode($teams); 	
}

function getUsers(){
	global $DBH;
	// get teams
	$users = array();
	$query = 'SELECT * FROM users';
	
	$STH = $DBH->query($query);  
	$STH->setFetchMode(PDO::FETCH_ASSOC);  
	  
	while($row = $STH->fetch()) {  
		$users[$row['id']] = $row['name'];
	}
	
	echo json_encode($users); 	
}
