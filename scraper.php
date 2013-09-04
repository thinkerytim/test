<?php
// BCS data scraper

require '/home/thinkery/www/bcs/querypath/src/QueryPath/qp.php';

$url = 'http://espn.go.com/college-football/';
_/year/2012
rankings/_/poll/1/year/2011
bcs/_/week/10/year/2012


http://espn.go.com/college-football/rankings/_/poll/2/year/2011/week/10 -- week 10
 http://espn.go.com/college-football/rankings/_/poll/1/year/2011/week/1/seasontype/3 -- final


http://espn.go.com/college-football/bcs/_/week/10/year/2012 -- week 10
http://espn.go.com/college-football/bcs/_/year/2012 -- final
$polls = array(
			1 => 'AP',
			2 => 'Coaches',
			5 => 'Harris'
		);
			
$years = array(2008, 2009, 2010, 2011, 2012, 2013);
$weeks = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,'1/seasontype/3');



