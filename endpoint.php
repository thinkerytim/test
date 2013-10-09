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
$team = isset($_GET['team']) ? $_GET['team'] : 1;
$soldby = isset($_GET['seller']) ? $_GET['seller'] : 1;
$boughtby = isset($_GET['buyer']) ? $_GET['buyer'] : 1;
$quantity = isset($_GET['quantity']) ? $_GET['quantity'] : 1;
$unitprice = isset($_GET['priceper']) ? $_GET['priceper'] : 1;
$id = isset($_GET['id']) ? $_GET['id'] : 0;

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
		addContract($poll, $week, $year, $team, $soldby, $boughtby, $quantity, $unitprice);
	break;
	case 'getUsers':
		getUsers();
	break;
	case 'getTeams':
		getTeams($poll, $week, $year);
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
	$query = 'SELECT a.*, b.shortname as name, c.name as buyer, d.name as seller FROM contracts a'
			.' JOIN teams b ON a.team = b.id'
			.' JOIN users c ON a.boughtby = c.id'
			.' JOIN users d ON a.soldby = d.id'
			.' WHERE a.week <= '.(int) $week.' AND a.poll = '.(int) $poll.' AND a.year = '.(int) $year.';';
	
	$STH = $DBH->query($query);  
	$STH->setFetchMode(PDO::FETCH_ASSOC);  
	
	while($row = $STH->fetch()) {  
		$query = 'SELECT price FROM ranks a'
			.' WHERE a.week = '.(int) $week.' AND a.poll = '.(int) $poll.' AND a.year = '.(int) $year
			.' AND a.team = '.$row['team'];
		
		$handle = $DBH->query($query);  
		$handle->setFetchMode(PDO::FETCH_ASSOC); 
		$price = $handle->fetch();
		
		$row['price'] = $price['price'] * $row['quantity'];
		
		$contracts[$row['id']] = $row;
	}
	
	echo json_encode($contracts); 	
}

function addOffer($poll, $week, $year, $team, $soldby, $quantity, $unitprice){
	global $DBH;
	$totalcost = (int) $quantity * (int) $unitprice;
	
	$query = 'INSERT INTO offers (team, poll, week, year, soldby, quantity, unitprice, totalcost)'
			.' VALUES ('.(int) $team.', '.(int) $poll.','.(int) $week.','.(int) $year.','.(int) $soldby.','.(int) $quantity.','.(int) $unitprice.','.(int) $totalcost.');';

	$STH = $DBH->query($query);  
	$STH->setFetchMode(PDO::FETCH_ASSOC);  
	  
	while($row = $STH->fetch()) {  
		$contracts[] = $row;
	}
	
	echo json_encode($contracts); 	
}

function addContract($poll, $week, $year, $team, $soldby, $boughtby, $quantity, $unitprice){
	global $DBH;
	$totalcost = (int) $quantity * (int) $unitprice;
	
	$query = 'INSERT INTO contracts (team, poll, week, year, soldby, boughtby, quantity, unitprice, totalcost)'
			.' VALUES ('.(int) $team.', '.(int) $poll.','.(int) $week.','.(int) $year.','.(int) $soldby.','.(int) $boughtby.','.(int) $quantity.','.(int) $unitprice.','.(int) $totalcost.');';
	
	$STH = $DBH->query($query);  
	$STH->setFetchMode(PDO::FETCH_ASSOC);  
	  
	while($row = $STH->fetch()) {  
		$contracts[] = $row;
	}
	
	// now remove the mone from the account of the person
	$query = 'UPDATE users SET balance = balance - '.$totalcost.' WHERE id = '.$boughtby;
	$STH = $DBH->query($query); 
	
	echo json_encode($contracts); 	
}

function getTeams($poll, $week, $year){
	global $DBH;
	// get teams
	$teams = array();
	$query = 'SELECT a.id, a.shortname, b.rank as rank FROM teams a'
			.' JOIN ranks b ON a.id = b.team'
			.' WHERE b.week = '.(int) $week.' AND b.poll = '.(int) $poll.' AND b.year = '.(int) $year
			.' ORDER BY b.rank ASC';

	$STH = $DBH->query($query);  
	$STH->setFetchMode(PDO::FETCH_ASSOC);  
	  
	while($row = $STH->fetch()) {  
		$teams[(string) $row['id']] = $row;
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
		$users[$row['id']] = array($row['name'], $row['balance'], $row['email']);
	}
	
	echo json_encode($users); 	
}

function deleteContract($id){
	global $DBH;
	// get teams
	$query = 'DELETE FROM contracts WHERE id = '.(int) $id;
	
	$count = $DBH->exec($query);  
	
	echo $count ? json_encode(true) : json_encode(false); 	
}

function doMail($recipient, $type = "new", $price = false){
	switch ($recipient){
		case 1: 
			$to = 'tim@thethinkery.net';
			break;
		case 2: 
			$to = 'caseyjmaz@gmail.com';
			break;
	}
	
	switch ($type){
		case 'new':
			$subject = 'New Offer';
			$message = 'A new offer is available on the bcs page';
			break;
		case 'counter':
			$subject = 'New Counter Offer';
			$message = 'A counter offer has been made on the bcs page';
			break;
	}
	mail ( $to, $subject, $message );
}

