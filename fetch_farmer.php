<?php
// Database connection
$host = 'localhost'; // Change if needed
$user = 'u982154661_amco'; // Your MySQL username
$pass = '$Ny0DT#0c'; // Your MySQL password
$dbname = 'u982154661_amco'; // Your database name

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['nic'])) {
    $nic = $_GET['nic'];
    
    // Fetch farmer's name using NIC
    $sql = "SELECT fname FROM farmers WHERE nic = ?";




    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('s', $nic);
        $stmt->execute();
        $stmt->bind_result($name);
        $stmt->fetch();
        $stmt->close();
        
        echo htmlspecialchars($name);
    } else {
        echo "Error preparing statement.";
    }
}

$conn->close();
?>
