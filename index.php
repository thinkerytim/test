<?php
require_once('simplehtmldom/simple_html_dom.php');

$poll = isset($_GET['poll']) ? $_GET['poll'] : 'bcs';
$discount = (isset($_GET['discount']) ? $_GET['discount'] : 3) * .01; // convert to percentage
$price = isset($_GET['price']) ? $_GET['price'] : 100; 

$percent = 1;

$polls = array(
	'bcs' 		=> 'BCS', 
	'ap' 			=> 'AP', 
	'coaches' => 'Coaches', 
	'harris' 	=> 'Harris'
);

$url 	= 'http://sportsillustrated.cnn.com/football/ncaa/polls/'.$poll.'/'; 

$html = file_get_html($url);
// initialize array to hold poll data
$data = array();

// read out rows into array
foreach($html->find('tr.cnnRow2') as $row) {
	$rank 		= $row->find('td',0)->plaintext;
	$team 		= $row->find('td',1)->plaintext;
	$record 	= $row->find('td',2)->plaintext;
	$votes 		= $row->find('td',3)->plaintext;
	$previous 	= $row->find('td',4)->plaintext;
	$newprice	= '$'.round($price * $percent);
	
	// add this to the data array
	$data[] = array($rank,$team,$record,$votes,$previous,$newprice);
	
	// increment percentage
	$percent = $percent - $discount;
}

// Find all SPAN tags that have a class of "myClass"
foreach($html->find('div.cnnPosted') as $e){
	$modified = $e->innertext;
}
?>

<html>
	<head>
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
		
	</head>
	<body>
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span12 well">
				<form id="form" action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>" method="get">
					<fieldset>
					    <legend>BCS Price Tool</legend>
					    <label>Poll</label>
							<select id="poll" name="poll">
								<?php
									foreach ($polls as $key => $value){
										$selected = ($poll == $key) ? 'selected=selected' : ''; 
										echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
									}
								?>
							</select>
							<label>Start Price</label>
							<input type="text" name="price" placeholder="100.00" value="<?php echo $price; ?>" />
							<div class="input-append">
							  <input class="span2" id="dicount" name="discount" type="text" value="<?php echo $discount;?>" placeholder="3" />
							  <span class="add-on">%</span>
							</div>
					    <button type="submit" class="btn">Submit</button>
					  </fieldset>
				</form>
			</div>
			</div>
		<div class="row-fluid">
			<div class="span12">
				<h3><?php echo $polls[$poll].' Poll - '.$modified; ?></h3>
				<table class="table table-striped">
					<thead>
						<td>Rank</td>
						<td>Name</td>
						<td>Record</td>
						<td>Votes</td>
						<td>Last Rank</td>
						<td>Price</td>
					</thead>
					<tbody>
						<?php
						foreach ($data as $row){
							echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td></tr>";
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	</body>
</html>
