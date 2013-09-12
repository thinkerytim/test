<?php
// BCS data scraper
require_once('simplehtmldom/simple_html_dom.php');

try {    
  $DBH = new PDO("mysql:host=localhost;dbname=bcs", 'root', 'sea1sea');  
} catch(PDOException $e) {  
    echo $e->getMessage();  
    die();
}  

// get teams
$teams = array();
$STH = $DBH->query('SELECT * FROM teams');  
$STH->setFetchMode(PDO::FETCH_ASSOC);  
  
while($row = $STH->fetch()) {  
	$teams[$row['id']] = array($row['name'], $row['shortname'], $row['school']);
} 

// get poll data
$polldata = array();
$STH = $DBH->query('SELECT * FROM polls');  
$STH->setFetchMode(PDO::FETCH_ASSOC);  
  
while($row = $STH->fetch()) {  
	$polldata[$row['id']] = array($row['name'], $row['start_price'], $row['discount']);
} 

// prepare insert statement
$insertST = $DBH->prepare("INSERT INTO ranks (team, rank, poll, week, year, price) VALUES (:team, :rank, :poll, :week, :year, :price)"); 

$url 		= 'http://espn.go.com/college-football/';
$bcsurl 	= 'bcs/_/';
$otherurl 	= 'rankings/_/poll/';

$polls = array(
			1 => array('BCS', 0),
			2 => array('Harris', 5),
			3 => array('AP', 1),
			4 => array('Coaches', 2)
		);
			
$years = array(2008, 2009, 2010, 2011, 2012, 2013);
$weeks = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,'final');

$thisweek = 3;

function getTeamID($team){
	global $teams;
	foreach ($teams as $id => $value){
		if ($key = array_search($team, $value)) return $id;
	}
	echo "TEAM NOT FOUND: ".$team;
	var_dump($team);
	return false;
}

function getPrice($rank, $poll){
	global $polldata;	
	$rank = (int) $rank;
	$thispoll = $polldata[$poll];
	
	// return the start price if rank is 1
	if ($rank == 1) return (float) $thispoll[1];
	$price = $thispoll[1] - ($thispoll[1] * (($rank - 1) * $thispoll[2]));
	
	return $price;
}

?>
<html>
<body>
<?php 	
foreach ($polls as $pkey => $poll) {
	foreach ($years as $year){
		foreach ($weeks as $week){	
			$thisurl = false;
			switch ($pkey){
				case 1: // it's BCS
					if ($week >= 8 && $year >= 2010){
						$thisurl = $url.$bcsurl;
						if ($week !== 'final') {
							$thisurl .= 'week/'.$week.'/year/'.$year;
						} else { // final 
							$thisurl .= '/year/'.$year;
						}
					}
				break;
				default:
					if ($year !== 2013){
						$thisurl = $url.$otherurl.$poll[1].'/year/'.$year.'/week/';
					} else {
						$thisurl = $url.$otherurl.$poll[1].'/week/';
					}
					if ($week !== 'final') {
						$thisurl .= $week;
					} else { // final 
						$thisurl .= '1/seasontype/3';
					}
				break;
			}
			if ($year == 2013 && ( ($week > $thisweek) || ($week == 'final') ) ) $thisurl = false;

			if ($thisurl){
				var_dump($thisurl);
				$html = file_get_html($thisurl);
				foreach($html->find('tr.oddrow, tr.evenrow') as $row) {
					// BCS page is totally different than others
					if ($pkey == 1){
						// do BCS parsing
						$rank 		= (int) $row->find('td',0)->plaintext;
						$team 		= $row->find('td',1)->plaintext;
					} else {
						// do AP, Harris, Coaches
						$rank 		= (int) $row->find('td',0)->plaintext;
						$team 		= $row->find('li.school a');
						foreach ($team as $t){
							$team = $t->plaintext;
						}
					}
					$data = array(
						':team' => getTeamId($team),
						':rank' => $rank,
						':poll' => $pkey,
						':week' => $week,
						':year' => $year,
						':price' => getPrice($rank, $pkey)
					);
					
					// insert the data
					try {
						$insertST->execute($data);
					} catch(PDOException $e) {
						var_dump($e);
						die();
					}					
				}
			}			
			
		}
	}	
}

?>
</body>
</html>
