# iiSpeed

### Prerequisite
Step 1: Install Raspbian to your Raspberry Pi  
Step 2: `sudo apt-get update && sudo apt-get upgrade -y`  
Step 3: `sudo apt-get install apache2 php5 libapache2-mod-php5 mysql-client mysql-server php5-mysql python-mysqldb -y`  
Step 4: `sudo pip install speedtest-cli`  

### MySQL
Step 4: `mysql -h localhost -u root -p` 

    CREATE DATABASE iiSpeed;  
    CREATE USER ‘iipython’@’localhost’ IDENTIFIED BY ‘password’;  
    GRANT ALL PRIVILEGES ON iiSpeed.* TO ‘iipython’@’localhost’;  
    CREATE USER ‘iiphp’@’localhost’ IDENTIFIED BY ‘password’;  
    GRANT SELECT ON iiSpeed.* TO ‘iiphp’@’localhost’;  
    FLUSH PRIVILEGES;  
    USE iiSpeed;  
    CREATE TABLE data ( Date varchar(255), Ping varchar(255), DownSpeed varchar(255), UpSpeed varchar(255) );  
    QUIT;  

### speedtest-cli
Step 5: `sudo vi /usr/local/lib/python2.7/dist-packages/speedtest_cli.py`  

    import datetime
    import MySQLdb
    class Database:
        host = 'localhost'
        user = 'iipython'
        password = 'password'
        db = 'iiSpeed'
    
        def __init__(self):
            self.connection = MySQLdb.connect(self.host, self.user, self.password, self.db)
            self.cursor = self.connection.cursor()
    
        def insert(self, query):
            try:
                self.cursor.execute(query)
                self.connection.commit()
            except:
                self.connection.rollback()
    
        def __del__(self):
            self.connection.close()

    dlspeed = (dlspeed/1000/1000)*8
    ulspeed = (ulspeed/1000/1000)*8
    db = Database()

    query = """
        INSERT INTO data
        ('Date', 'Ping', 'DownSpeed', 'UpSpeed')
        VALUES
        (NOW(), %f, %f, %f)
        """ % (best['latency'], dlspeed, ulspeed)

    db.insert(query)

Step 6: `sudo chmod 775 /usr/local/lib/python2.7/dist-packages/speedtest_cli.py`  
Step 7: `crontab -e`  

    */20 * * * * /usr/local/lib/python2.7/dist-packages/speedtest_cli.py

### Verify (Wait 1 hour)
Step 8: `crontab -l`  
Step 9: `mysql -h localhost -u iiphp -p`  

    USE iiSpeed;  
    SELECT * FROM data;  
    QUIT;  

### Webpage
Step 10: `sudo mv /var/www/html/index.html /var/www/html/index.html.old`  
Step 11: `sudo vi /var/www/html/index.php`  

    <html>
    <head>
    <title>iiSpeed</title>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.1/jquery.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/highstock/4.2.3/highstock.js"></script>
    </head>

    <body>

    <div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>

    <?php
    $servername = "localhost";
    $username = "piread";
    $password = "raspread";
    $dbname = "iiNet";

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
		    $varping .= "[". (strtotime($row["Date"]) * 1000). ", ". $row["Ping"]. "], ";
		    $vardlspeed .= "[". (strtotime($row["Date"]) * 1000). ", ". $row["DownSpeed"]. "], ";
		    $varulspeed .= "[". (strtotime($row["Date"]) * 1000). ", ". $row["UpSpeed"]. "], ";
	    }
    } else {
	    echo "0 results";
    }
    $conn->close();
    $varping = rtrim($varping, ', ');
    $vardlspeed = rtrim($vardlspeed, ', ');
    $varulspeed = rtrim($varulspeed, ', ');
    ?>

    <script> var chart = new Highcharts.StockChart({
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
          selected: 6
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
    </script>

    </body>
    </html>
