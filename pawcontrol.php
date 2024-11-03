<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pawcontrol";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
else {
    echo "Connected successfully";
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $contactNumber = $_GET['contactNumber'];
    $photo = $_FILES['photo']['name'];

    if ($photo != "") {
        $extension = pathinfo($photo, PATHINFO_EXTENSION);

        $unique_filename = uniqid().".$extension";

        $target_dir = "uploads/";
        $target_file = $target_dir . $unique_filename;
        
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {

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

$conn->close();

?>
