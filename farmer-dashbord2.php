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
    <script src="https://cdn.tailwindcss.com"></script>

    <title>Input Farmer Data</title>
 
    
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
<body >
    <div class="grid place-item-center p-2 m-3  md:h-350  item-center justify-center md:w-1/2 md:h-auto bg-green-200">
    <h1  class="rounded p-1 my-1 w-full text-1xl bg-green-300 text-center border-3 ">Anuradhapura Mango Export Cooporative Society </h1>

        <h1  class="rounded p-3 my-3 w-full text-3xl bg-yellow-300 text-center border-3 ">අඹ කවර දැමීම </h1>
        <form method="post" action="" onsubmit="validateForm(event)">
            <div class="form-group">
                <label class="text-2xl w-full" for="nic">ජාතික හැදුනුම් පත් අංකය :</label>
                <input  class="p-2 text-2xl w-full" type="text" id="nic" name="nic" class="form-control" required onkeyup="fetchFarmerName()">
                <small id="farmer-name" class="w-full"></small>
            </div>
            <div class="mt-4">
                <label  class=" text-2xl w-full" for="dt">දිනය :</label>
                <input  class="p-2 text-2xl w-full" type="date" id="dt" name="dt" class="form-control" required>
            </div>
            <div class="mt-4">
                <label  class="text-2xl w-full" for="qty">කවර ගණන :</label>
                <input  class="p-2 text-2xl w-full" type="number" id="qty" name="qty" class="form-control" required>
            </div>
            <button  class="w-full my-3 bg-green-900 text-white font-bold py-2 px-4 rounded" type="submit" >Submit</button>
        </form>

        <?php // if ($message) { echo "<div class='alert alert-info mt-3'>" . htmlspecialchars($message) . "</div>"; } ?>
        <?php if ($isDataInserted) { echo "<div class='alert alert-success mt-3'>Data inserted successfully.</div>"; } ?>
    </div>

   
</body>
</html>
