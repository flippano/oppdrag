<?php
$servername = "localhost";
$username = "root";
$password = "Root";
$dbname = "oppdrag";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $description = $_POST["description"];
    $status = "unsolved";

    $caseNumber = time();

    $sql = "INSERT INTO inquiries (name, email, description, status, case_number) VALUES ('$name', '$email', '$description', '$status', $caseNumber)";
    if ($conn->query($sql) === TRUE) {        
        echo "Your case number is: " . $caseNumber;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["case_number"])) {
    $caseNumber = $_GET["case_number"];

    $sql = "SELECT name, email, description, status FROM inquiries WHERE case_number = $caseNumber";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row["name"];
        $email = $row["email"];
        $description = $row["description"];
        $status = $row["status"];
        echo "Name: $name, Email: $email, Description: $description, Status: $status";
    } else {
        echo "Invalid case number";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["case_number"]) && isset($_POST["status"])) {
        $case_number = $_POST["case_number"];
        $status = $_POST["status"];
        $sql = "UPDATE inquiries SET status='$status' WHERE case_number='$case_number'";
        if ($conn->query($sql) === TRUE) {
            echo "Inquiry status updated successfully";
        } else {
            echo "Error updating inquiry status: " . $conn->error;
        }
    }
    // Rest of your code...
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["delete_all"])) {
        $sql = "DELETE FROM inquiries";
        if ($conn->query($sql) === TRUE) {
            echo "All inquiries have been deleted";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $name = isset($_POST["name"]) ? $_POST["name"] : '';
        $email = isset($_POST["email"]) ? $_POST["email"] : '';
        $description = isset($_POST["description"]) ? $_POST["description"] : '';
        $sql = "SELECT case_number, status FROM inquiries";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "Case number: " . $row["case_number"]. " - Status: " . $row["status"]. "<br>";
            }
        } else {
            echo "No cases found";
        }
        
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inquiry System</title>
</head>
<body>
    <h1>Inquiry System</h1>

    <h2>Submit an Inquiry</h2>
    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label for="name">Name:</label>
        <input type="text" name="name" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="description">Description:</label>
        <textarea name="description" required></textarea><br>

        <input type="submit" value="Submit">
    </form>

    <h2>Check Inquiry Status</h2>
    <form method="GET" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label for="case_number">Case Number:</label>
        <input type="text" name="case_number" required><br>

        <input type="submit" value="Check Status">
        <br>
    </form>
<form id="adminForm">
    <label for="password">Password:</label>
    <input type="password" id="password" name="password">
    <input type="submit" value="Sign In">
    <button id="signOutButton" style="display: none;">Sign Out</button>
</form>

<div id="adminPowers" style="display: none;">
    <!-- Add your admin powers here -->
    <button id="deleteButton">Delete All Inquiries</button>
    <h2>Change Inquiry Status</h2>
    <form id="statusForm">
        <label for="case_number">Case Number:</label>
        <input type="text" id="case_number" name="case_number" required><br>
        <input type="submit" value="Mark as Solved">
    </form>
</div>
</body>
<link rel="stylesheet" href="style.css">
</html>

<script>
document.getElementById('adminForm').addEventListener('submit', function(event) {
    event.preventDefault();
    var password = document.getElementById('password').value;
    if (password === 'password') {
        document.getElementById('adminPowers').style.display = 'block';
        document.getElementById('signOutButton').style.display = 'inline'; // Add this line
    } else {
        alert('Incorrect password');
    }
});

document.getElementById('deleteButton').addEventListener('click', function() {
    fetch('index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'delete_all=true',
    })
    .then(response => response.text())
    .then(data => console.log(data))
    .catch(error => console.error('Error:', error));
});

document.getElementById('signOutButton').addEventListener('click', function(event) {
    event.preventDefault();
    document.getElementById('password').value = '';
    document.getElementById('adminPowers').style.display = 'none';
    this.style.display = 'none';
});

document.getElementById('statusForm').addEventListener('submit', function(event) {
    event.preventDefault();
    var case_number = document.getElementById('case_number').value;
    fetch('index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'case_number=' + case_number + '&status=solved',
    })
    .then(response => response.text())
    .then(data => console.log(data))
    .catch(error => console.error('Error:', error));
});
</script>