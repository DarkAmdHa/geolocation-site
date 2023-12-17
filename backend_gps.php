<?php
// Assuming you have a MySQL database
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "ipaddresses";
$servername = "localhost";
$username = "u472036914_ashti";
$password = "2|VGieBfW";
$dbname = "u472036914_ashtisnap";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get GPS data from the frontend
$data = json_decode(file_get_contents('php://input'), true);
$userDataId = $data['userDataId'];
$latitude = $data['latitude'];
$longitude = $data['longitude'];

// Store GPS data linked to the user in a database
$sql = "INSERT INTO user_data_gps (user_id, latitude, longitude) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("idd", $userDataId, $latitude, $longitude);
$stmt->execute();
$stmt->close();

// Close the database connection
$conn->close();

// Respond to the frontend
echo json_encode(['status' => 'success']);
?>
