<?php
// BCS data scraper

require '/home/tim/www/bcs/BCS/querypath/src/qp.php';
$url 		= 'http://espn.go.com/college-football/';
$bcsurl 	= 'bcs/_/';
$otherurl 	= 'rankings/_/poll/';

//rankings/_/poll/2/year/2011/week/10 -- week 10
//rankings/_/poll/1/year/2011/week/1/seasontype/3 -- final

//bcs/_/week/10/year/2012 -- week 10
//bcs/_/year/2012 -- final

$polls = array(
			array('BCS', 0),
			array('AP', 1),
			array('Coaches', 2),
			array('Harris', 5)
		);
			
$years = array(2008, 2009, 2010, 2011, 2012, 2013);
$weeks = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,'final');
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
			if ($thisurl) echo '<a href="'.$thisurl.'">'.$thisurl.'</a><br />';
			$doc = new DOMDocument('1.0');
			@$doc->loadHTMLFile($url);
			$html = qp($doc, NULL, array('ignore_parser_warnings' => TRUE));
			
			// BCS page is totally different than others
			if ($poll[1] == 0){
				// do BCS parsing
			} else {
				// do AP, Harris, Coaches
				
			}
		}
	}	
}

?>
</body>
</html>
