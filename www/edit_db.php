 <?php
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "alienvault";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "update dashboard_tab_config set title='تیکت' where id=2";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - title: " . $row["title"]. "<br>";
    }
} else {
    echo "0 results";
}
$conn->close();
?> 