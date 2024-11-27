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

$name = '';
$message = '';
$isDataInserted = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nic = $_POST['nic'];
    $date = $_POST['dt'];
    $qty = $_POST['qty'];

    // Validate inputs
    if (empty($nic) || empty($date) || empty($qty)) {
        $message = "All fields are required.";
    } else {
        // Fetch farmer's name using NIC
        $sql = "SELECT fname FROM farmers WHERE nic = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('s', $nic);
            $stmt->execute();
            $stmt->bind_result($name);
            $stmt->fetch();
            $stmt->close();

            if ($name) {
                // Insert into basgs table
                $insertSql = "INSERT INTO bags (nic, dt, qty) VALUES (?, ?, ?)";
                if ($insertStmt = $conn->prepare($insertSql)) {
                    if (!$insertStmt->bind_param('ssi', $nic, $date, $qty)) {
                        die("Bind param failed: " . $insertStmt->error);
                    }

                    if ($insertStmt->execute()) {
                        $message = "Data inserted successfully.";
                        $isDataInserted = true;
                    } else {
                        $message = "Error inserting data: " . $insertStmt->error;
                    }

                    $insertStmt->close();
                } else {
                    die("Error preparing insert statement: " . $conn->error);
                }
            } else {
                $message = "No farmer found with that NIC.";
            }
        } else {
            die("Error preparing select statement: " . $conn->error);
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Farmer Data</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function fetchFarmerName() {
            const nic = document.getElementById('nic').value;
            if (nic.length > 0) {
                fetch('fetch_farmer.php?nic=' + encodeURIComponent(nic))
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('farmer-name').innerText = data || "No farmer found.";
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                document.getElementById('farmer-name').innerText = "";
            }
        }

        function validateForm(event) {
            const nic = document.getElementById('nic').value;
            const date = document.getElementById('dt').value;
            const qty = document.getElementById('qty').value;

            if (!nic || !date || !qty) {
                event.preventDefault();
                alert("All fields are required.");
            }
        }
    </script>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center">අඹ කවර දැමීම </h1>
        <form method="post" action="" onsubmit="validateForm(event)">
            <div class="form-group">
                <label for="nic">ජාතික හැදුනුම් පත් අංකය :</label>
                <input type="text" id="nic" name="nic" class="form-control" required onkeyup="fetchFarmerName()">
                <small id="farmer-name" class="form-text text-muted"></small>
            </div>
            <div class="form-group">
                <label for="dt">දිනය :</label>
                <input type="date" id="dt" name="dt" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="qty">කවර ගණන :</label>
                <input type="number" id="qty" name="qty" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <?php // if ($message) { echo "<div class='alert alert-info mt-3'>" . htmlspecialchars($message) . "</div>"; } ?>
        <?php if ($isDataInserted) { echo "<div class='alert alert-success mt-3'>Data inserted successfully.</div>"; } ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
