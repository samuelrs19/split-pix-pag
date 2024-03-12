<?php
// echo "SSAAA";
// die;
// $servername = "193.203.175.50";
// $database = "u391517370_bancodedados";
// $username = "u391517370_bancodedados";
// $password = "Zx]l]cs$0lS$";
// // Create connection
// $conn = mysqli_connect($servername, $username, $password, $database);
// // Check connection
// if (!$conn) {
//   die("Connection failed: " . mysqli_connect_error());
// }

// echo "Connected successfully";
// $date = date('Y-m-d H:i:s');

// $sql = "INSERT INTO logs (origin, description, date) VALUES ('Test', 'Testing', {$date})";
// if (mysqli_query($conn, $sql)) {
//   echo "New record created successfully";
// } else {
//   echo "Error: " . $sql . "<br>" . mysqli_error($conn);
// }
// mysqli_close($conn);
// die;

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://olhonopremio.com/api/v1/pix/webhook/log',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => file_get_contents("php://input"),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

require_once __DIR__ . "/baixa_pix.php";
