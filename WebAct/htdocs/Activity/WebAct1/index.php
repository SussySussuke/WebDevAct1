<?php
// Include the database connection file
include("dbconnect.php");

// Handle form submission for adding or updating a record
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $grade = $_POST['grade'];
    $age = $_POST['age'];
    
    if ($_POST['submit'] == "Add") {
        // Add new record
        $sql = "INSERT INTO students (name, grade, age) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $grade, $age);
        $stmt->execute();
        $stmt->close();
    } elseif ($_POST['submit'] == "Update") {
        // Update existing record
        $idToUpdate = $_POST['id'];
        $sql = "UPDATE students SET name = ?, grade = ?, age = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $name, $grade, $age, $idToUpdate);
        $stmt->execute();
        $stmt->close();
    }
}

// Handle row deletion
if (isset($_POST['delete'])) {
    $idToDelete = $_POST['delete_id']; // Get the ID of the row to delete

    // Prepare SQL delete statement
    $deleteSql = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $idToDelete); // "i" indicates the ID is an integer

    // Execute the delete statement
    if ($stmt->execute()) {
        echo "<script>alert('Record deleted successfully!'); window.location.href='index.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $stmt->close();
}

// Handle edit (populate the form with row data)
if (isset($_POST['edit'])) {
    $idToEdit = $_POST['edit_id']; // Get the ID of the row to edit

    $sql = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idToEdit); // "i" indicates the ID is an integer
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc(); // Fetch the row data to populate the form
    $stmt->close();

    // Populate form with the fetched data
    $editMode = true; // Set the form to edit mode
    $editId = $row['id'];
    $editName = $row['name'];
    $editGrade = $row['grade'];
    $editAge = $row['age'];
} else {
    $editMode = false; // Set to add mode by default
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student CRUD Table</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Student CRUD Table</h1>

    <!-- Form to Add/Edit Student -->
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $editMode ? $editId : ''; ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $editMode ? $editName : ''; ?>" required>
        
        <label for="grade">Grade:</label>
        <input type="text" id="grade" name="grade" value="<?php echo $editMode ? $editGrade : ''; ?>" required>
        
        <label for="age">Age:</label>
        <input type="number" id="age" name="age" value="<?php echo $editMode ? $editAge : ''; ?>" required>
        
        <input type="submit" name="submit" value="<?php echo $editMode ? 'Update' : 'Add'; ?>">
    </form>

    <!-- Table Displaying Students -->
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Grade</th>
            <th>Age</th>
            <th>Options</th>
        </tr>
        <?php
        // Fetch records from the database
        $sql = "SELECT * FROM students";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["name"] . "</td>";
                echo "<td>" . $row["grade"] . "</td>";
                echo "<td>" . $row["age"] . "</td>";
                echo "<td>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='edit_id' value='" . $row['id'] . "'>
                            <input type='submit' name='edit' value='Edit'>
                        </form>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='delete_id' value='" . $row['id'] . "'>
                            <input type='submit' name='delete' value='Delete' onclick='return confirm(\"Are you sure you want to delete this record?\")'>
                        </form>
                    </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No records found</td></tr>";
        }
        ?>
    </table>
</body>
</html>
