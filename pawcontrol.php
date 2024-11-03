<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pawcontrol";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
else {
    echo "Connected successfully";
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Process form data
    $contactNumber = $_GET['contactNumber'];
    $photo = $_FILES['photo']['name'];

    // Check if file was uploaded
    if ($photo != "") {
        // Get file extension
        $extension = pathinfo($photo, PATHINFO_EXTENSION);

        // Create unique filename
        $unique_filename = uniqid().".$extension";

        // Move uploaded file to server
        $target_dir = "uploads/";
        $target_file = $target_dir . $unique_filename;
        
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            // Insert data into database
            $stmt = $conn->prepare("INSERT INTO upload (photo) VALUES (?)");
            $stmt->bind_param("s", $unique_filename);
            $result = $stmt->execute();
            
            if ($result === TRUE) {
                echo "File uploaded and data inserted successfully!";
            } else {
                echo "Error uploading file or inserting data: " . $conn->error;
            }
        } else {
            echo "Error uploading file.";
        }

        // Fetch the inserted data
        $sql = "SELECT * FROM upload ORDER BY id DESC LIMIT 1";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "Inserted data: " . json_encode($row);
            }
        } else {
            echo "No data found";
        }
    } else {
        echo "No file selected.";
    }
}

// Close connection
$conn->close();

?>
