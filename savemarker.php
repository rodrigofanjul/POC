<?php

require("config.php");

// Gets data from URL parameters
$name = $_POST['name'];
$lat = $_POST['lat'];
$lng = $_POST['lng'];
$accuracy = $_POST['accuracy'];
$type = 'home';

// Insert new row with user data
$query = sprintf("INSERT INTO markers " .
     " (name, lat, lng, accuracy, type ) " .
     " VALUES ('%s', '%s', '%s', '%s', '%s');",
     htmlspecialchars($name),
     htmlspecialchars($lat),
     htmlspecialchars($lng),
     htmlspecialchars($accuracy),
     htmlspecialchars($type));

try {

    $db = new PDO($dsn, $username, $password);
    $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    
    $sth = $db->exec($query);

    return true;
    
} catch (Exception $e) {
echo $e->getMessage();
}

?>