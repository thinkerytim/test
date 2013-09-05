<?php
// BCS data scraper
require_once('simplehtmldom/simple_html_dom.php');

$url 		= 'http://espn.go.com/college-football/';
$bcsurl 	= 'bcs/_/';
$otherurl 	= 'rankings/_/poll/';

$polls = array(
			array('BCS', 0),
			array('AP', 1),
			array('Coaches', 2),
			array('Harris', 5)
		);
			
$years = array(2008, 2009, 2010, 2011, 2012, 2013);
$weeks = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,'final');

function getTeamID($team){
	return $team;
}

function getPrice($rank, $poll){
	return true;
}

?>
<html>
<body>
<?php 	
foreach ($polls as $poll) {
	foreach ($years as $year){
		foreach ($weeks as $week){	
			$thisurl = false;
			switch ($poll[1]){
				case 0: // it's BCS
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
					$thisurl = $url.$otherurl.$poll.'/year/'.$year.'/week/';
					if ($week !== 'final') {
						$thisurl .= $week;
					} else { // final 
						$thisurl .= '1/seasontype/3';
					}
				break;
			}
			if ($thisurl){
				echo '<a href="'.$thisurl.'">'.$thisurl.'</a><br />';
		
				$html = file_get_html($thisurl);
				foreach($html->find('tr.oddrow, tr.evenrow') as $row) {
					// BCS page is totally different than others
					if ($poll[1] == 0){
						// do BCS parsing
						$rank 		= $row->find('td',0)->plaintext;
						$team 		= $row->find('td',1)->plaintext;
					} else {
						// do AP, Harris, Coaches
						$rank 		= $row->find('td',0)->plaintext;
						$team 		= $row->find('td',1)->find('li.school')->find('a')->plaintext;
					}
					$data = array(
						':team' => getTeamId($team),
						':rank' => $rank,
						':poll' => $poll,
						':week' => $week,
						':price' => getPrice($rank, $poll)
					);
					
					var_dump($data);
					
				}
				die();
			}
		}
	}	
}

?>
</body>
</html>
