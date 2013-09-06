<?php

$poll = isset($_GET['poll']) ? $_GET['poll'] : 2;
$year = isset($_GET['year']) ? $_GET['year'] : 2012;
$week = isset($_GET['week']) ? $_GET['week'] : 1;

$polls = array(
	1 => 'BCS', 
	2 => 'Harris'
	3 => 'AP', 
	4 => 'Coaches'	
);

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
							<label>Year</label>
							<select name="year">
								<option value="2008" <?php if ($year == 2008) echo "selected=selected"; ?>>2008</option>
								<option value="2009" <?php if ($year == 2009) echo "selected=selected"; ?>>2009</option>
								<option value="2010" <?php if ($year == 2010) echo "selected=selected"; ?>>2010</option>
								<option value="2011" <?php if ($year == 2011) echo "selected=selected"; ?>>2011</option>
								<option value="2012" <?php if ($year == 2012) echo "selected=selected"; ?>>2012</option>
								<option value="2013" <?php if ($year == 2013) echo "selected=selected"; ?>>2013</option>
							</select>
							<label>Week</label>
							<select name="week">
								<option value="1" <?php if ($year == 1) echo "selected=selected"; ?>>1</option>
								<option value="2" <?php if ($year == 2) echo "selected=selected"; ?>>2</option>
								<option value="3" <?php if ($year == 3) echo "selected=selected"; ?>>3</option>
								<option value="4" <?php if ($year == 4) echo "selected=selected"; ?>>4</option>
								<option value="5" <?php if ($year == 5) echo "selected=selected"; ?>>5</option>
								<option value="6" <?php if ($year == 6) echo "selected=selected"; ?>>6</option>
								<option value="7" <?php if ($year == 7) echo "selected=selected"; ?>>7</option>
								<option value="8" <?php if ($year == 8) echo "selected=selected"; ?>>8</option>
								<option value="9" <?php if ($year == 9) echo "selected=selected"; ?>>9</option>
								<option value="10" <?php if ($year == 10) echo "selected=selected"; ?>>10</option>
								<option value="11" <?php if ($year == 11) echo "selected=selected"; ?>>11</option>
								<option value="12" <?php if ($year == 12) echo "selected=selected"; ?>>12</option>
								<option value="13" <?php if ($year == 13) echo "selected=selected"; ?>>13</option>
								<option value="14" <?php if ($year == 14) echo "selected=selected"; ?>>14</option>
								<option value="15" <?php if ($year == 15) echo "selected=selected"; ?>>Final</option>
							</select>
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
