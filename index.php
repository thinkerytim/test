<?php

try {    
  $DBH = new PDO("mysql:host=localhost;dbname=bcs", 'root', 'sea1sea');  
} catch(PDOException $e) {  
    echo $e->getMessage();  
    die();
}  

$poll = isset($_GET['poll']) ? (int) $_GET['poll'] : 2;
$year = isset($_GET['year']) ? (int) $_GET['year'] : 2012;
$week = isset($_GET['week']) ? (int) $_GET['week'] : 1;

$polls = array(
	1 => 'BCS', 
	2 => 'Harris',
	3 => 'AP', 
	4 => 'Coaches'	
);



?>

<html>
	<head>
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
		<script>
			$(document).ready(function() {
				var year = <?php echo $year; ?>;
				var	week = <?php echo $week; ?>;
				var poll = <?php echo $poll; ?>;
				
				// get ranks
				$.ajax({
					dataType: "json",
					url: 'endpoint.php',
					data: {
						year: year,
						week: week,
						poll: poll,
						task: 'getRanks'
					},
					success: function(data){
						$.each(data, function(index, value){
							$('#ranks').append('<tr><td>'+index+'</td><td>'+value[0]+'</td><td>$'+value[1]+'</td></tr>');
						});
					}
				});
				// get users
				$.ajax({
					dataType: "json",
					url: 'endpoint.php',
					data: {
						task: 'getUsers'
					},
					success: function(data){
						$.each(data, function(index, value){
							var itemval= '<option value="'+index+'">'+value[0]+'</option>';
							$('#seller').append(itemval);
							$('#buyer').append(itemval);
						});
					}
				});
				// get teams
				$.ajax({
					dataType: "json",
					url: 'endpoint.php',
					data: {
						year: year,
						week: week,
						poll: poll,
						task: 'getTeams'
					},
					success: function(data){
						$.each(data, function(index, value){
							var itemval= '<option value="'+index+'">'+value['shortname']+' - '+value['rank']+'</option>';
							$('#team').append(itemval);
						});
					}
				});
				
				function deleteContract(id){
					$.ajax({
						dataType: "json",
						url: 'endpoint.php',
						data: {
							task: 'deleteContract',
							id: id
						},
						success: function(data){
							$('#contract'+id).fadeOut('slow', function() { $(this).remove(); });
						}
					});
				}
				function getContracts(){
					// get contracts
					$.ajax({
						dataType: "json",
						url: 'endpoint.php',
						data: {
							year: year,
							week: week,
							poll: poll,
							task: 'getContracts'
						},
						success: function(data){
							$.each(data, function(index, value){
								$('#contracts').append('<tr id="contract'+value['id']+'"><td>'+value['buyer']+'</td><td>'+value['name']+'</td><td>'+value['quantity']+'</td><td>$'+value['totalcost']+'</td><td>$'+value['price']+'</td><td>'+value['timestamp']+'</td><td><span id="'+value['id']+'" class="glyphicon glyphicon-trash"></span></td></tr>');
							});
						}
					});
				}
				
				function getOffers(){
					// get contracts
					$.ajax({
						dataType: "json",
						url: 'endpoint.php',
						data: {
							year: year,
							week: week,
							poll: poll,
							task: 'getOffers'
						},
						success: function(data){
							$.each(data, function(index, value){
								$('#offers').append('<tr id="offers'+value['id']+'"><td>'+value['name']+'</td><td>'+value['quantity']+'</td><td>$'+value['totalcost']+'</td><td>$'+value['price']+'</td><td>'+value['timestamp']+'</td><td><span id="'+value['id']+'" class="glyphicon glyphicon-trash"></span></td></tr>');
							});
						}
					});
				}
								
				function addOffer(){
					var data = {
						seller: $('#seller').val(),
						team: $('#team').val(),
						quantity: $('#quantity').val(),
						priceper: $('#priceper').val(),
						year: year,
						week: week,
						poll: poll,
						task: 'addOffer'
					}
					$.ajax({
						dataType: "json",
						url: 'endpoint.php',
						data: data,
						success: function(data){
							// first clear the contracts then repopulate
							$("#offersTable.tbody tr").remove();
							getOffers();
						}
					});
				}
				
				function buyContract(){
					var data = {
						seller: $('#seller').val(),
						team: $('#team').val(),
						quantity: $('#quantity').val(),
						priceper: $('#priceper').val(),
						year: year,
						week: week,
						poll: poll,
						task: 'addContract'
					}
					$.ajax({
						dataType: "json",
						url: 'endpoint.php',
						data: data,
						success: function(data){
							// first clear the contracts then repopulate
							$("#ContractsTable.tbody tr").remove();
							getContracts();
						}
					});
				}
				// initial call
				getContracts();
				getOffers();
				
				$('#addOffer').click(function(){
					addOffer();
				});
				
				$('tbody').on('click', '.glyphicon-trash', function(){
					console.dir(this.id);
					deleteContract(this.id);
				});
				
			});
		</script>
	</head>
	<body>
		<div class="container">
		<div class="row">
			<div class="col-md-12 well">
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
							<option value="1" <?php if ($week == 1) echo "selected=selected"; ?>>1</option>
							<option value="2" <?php if ($week == 2) echo "selected=selected"; ?>>2</option>
							<option value="3" <?php if ($week == 3) echo "selected=selected"; ?>>3</option>
							<option value="4" <?php if ($week == 4) echo "selected=selected"; ?>>4</option>
							<option value="5" <?php if ($week == 5) echo "selected=selected"; ?>>5</option>
							<option value="6" <?php if ($week == 6) echo "selected=selected"; ?>>6</option>
							<option value="7" <?php if ($week == 7) echo "selected=selected"; ?>>7</option>
							<option value="8" <?php if ($week == 8) echo "selected=selected"; ?>>8</option>
							<option value="9" <?php if ($week == 9) echo "selected=selected"; ?>>9</option>
							<option value="10" <?php if ($week == 10) echo "selected=selected"; ?>>10</option>
							<option value="11" <?php if ($week == 11) echo "selected=selected"; ?>>11</option>
							<option value="12" <?php if ($week == 12) echo "selected=selected"; ?>>12</option>
							<option value="13" <?php if ($week == 13) echo "selected=selected"; ?>>13</option>
							<option value="14" <?php if ($week == 14) echo "selected=selected"; ?>>14</option>
							<option value="15" <?php if ($week == 15) echo "selected=selected"; ?>>Final</option>
						</select>
					<button type="submit" class="btn">Submit</button>
				  </fieldset>
			</form>
			</div>
		</div>
		<div class="row">
			<div class="col-md-9">
				<h3>Create new offer</h3>
				<table class="table table-striped">
					<thead>
						<tr>
						<td>Seller</td>
						<td>Team</td>
						<td>Price Per Share</td>
						<td>Quantity</td>
						<td>Go</td>
						</tr>
					</thead>
					<tbody>
						<tr>
						<td><select id="seller"></select></td>
						<td><select id="team"></select></td>
						<td><input type="text" id="priceper" /></td>
						<td><input type="text" id="quantity" /></td>
						<td><span id="addOffer" class="glyphicon glyphicon-check"></td>
						</tr>
					</tbody>
				</table><h3>Current Offers</h3>
				<table id="offersTable" class="table table-striped">
					<thead>
						<td>Seller</td>
						<td>Team</td>
						<td>Shares</td>
						<td>Cost</td>
						<td>Value</td>
						<td><span id="purchase" class="glyphicon glyphicon-usd"></td>
						<td><span id="purchase" class="glyphicon glyphicon-thumbs-down"></td>
					</thead>
					<tfoot>
					</tfoot>
					<tbody id="contracts">
					</tbody>
				</table>
				<h3>Current Contracts</h3>
				<table id="contractsTable" class="table table-striped">
					<thead>
						<td>Owner</td>
						<td>Team</td>
						<td>Shares</td>
						<td>Cost</td>
						<td>Value</td>
						<td>Purchased</td>
						<td><span id="deleteContract" class="glyphicon glyphicon-trash"></td>
					</thead>
					<tfoot>
					</tfoot>
					<tbody id="contracts">
					</tbody>
				</table>
			</div>
			<div class="col-md-3 well well-sm">
				<h3><?php echo $polls[$poll].' Poll - Week '.$week.', '.$year; ?></h3>
				<table class="table table-striped">
					<thead>
						<td>Rank</td>
						<td>Name</td>
						<td>Price</td>
					</thead>
					<tfoot>
					</tfoot>
					<tbody id="ranks">
					</tbody>
				</table>
			</div>
		</div>

	</body>
</html>
