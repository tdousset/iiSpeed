<html>
<head>
<title>iiSpeed</title>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/highstock/4.2.6/highstock.js"></script>
</head>
<body>

<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>

<?php
$servername = "localhost";
$username = "iiphp";
$password = "password";
$dbname = "iiSpeed";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM data";
$result = $conn->query($sql); if ($result->num_rows > 0) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
		$varping .= "[". ((strtotime($row["Date"]) + 11*60*60) * 1000). ", ". $row["Ping"]. "], ";
		$vardlspeed .= "[". ((strtotime($row["Date"]) + 11*60*60) * 1000). ", ". $row["DownSpeed"]. "], ";
		$varulspeed .= "[". ((strtotime($row["Date"]) + 11*60*60) * 1000). ", ". $row["UpSpeed"]. "], ";
	}
} else {
	echo "0 results";
}
$conn->close();

$varping = rtrim($varping, ', ');
$vardlspeed = rtrim($vardlspeed, ', ');
$varulspeed = rtrim($varulspeed, ', ');
?>

<script>
var chart = new Highcharts.StockChart({
      chart: {
         renderTo: 'container'
      },
      title: {
         text: 'Speedtest'
      },
      xAxis: {
         type: 'datetime'
      },
      rangeSelector: {
      buttons: [{
         type: 'hour',
         count: 6,
         text: '6h'
      }, {
         type: 'day',
         count: 1,
         text: '1d'
      }, {
         type: 'day',
         count: 3,
         text: '3d'
      }, {
         type: 'day',
         count: 7,
         text: '1w'
      }, {
         type: 'month',
         count: 1,
         text: '1m'
      }, {
         type: 'all',
         text: 'All'
      }],
      selected: 2
      },
      series: [{
         name: 'Ping',
         data: [<?php echo "$varping" ?>]
      }, {
         name: 'Download Speed',
         data: [<?php echo "$vardlspeed" ?>]
      }, {
         name: 'Upload Speed',
         data: [<?php echo "$varulspeed" ?>]
      }]
});

var chartoptions = Highcharts.setOptions({
	global: {
		useUTC: false
	}
});
</script>

<small>All times are in Australian Eastern Daylight Time (UTC+11:00)</small>

</body>
</html>
