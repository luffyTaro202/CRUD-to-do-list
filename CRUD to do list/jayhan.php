<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jayhan";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Add new task
  if (isset($_POST["add_task"])) {
    $task_name = $_POST["task_name"];
    $task_description = $_POST["task_description"];
    $task_due_date = $_POST["task_due_date"];
    $task_status = $_POST["task_status"];

    $sql = "INSERT INTO tasks (task_name, task_description, task_due_date, task_status)
            VALUES ('$task_name', '$task_description', '$task_due_date', '$task_status')";

    if (mysqli_query($conn, $sql)) {
      echo "Task added successfully";
    } else {
      echo "Error adding task: " . mysqli_error($conn);
    }
  }
?>
<?php
// Check if the user has clicked the "Edit" button for a task
if (isset($_POST["edit_task"])) {
  // Get the ID of the task to edit from the form data
  $task_id = $_POST["task_id"];
  
  // Retrieve the details of the task from the database
  $sql = "SELECT * FROM tasks WHERE id = $task_id";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
?>
  <!-- Display the form for editing the task -->
  <h2 style="text-align: center">Edit task</h2>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
    <label for="task_name">Task name:</label>
    <input type="text" name="task_name" value="<?php echo $row["task_name"]; ?>"><br>
    <label for="task_description">Task description:</label>
    <textarea name="task_description"><?php echo $row["task_description"]; ?></textarea><br>
    <label for="task_due_date">Due date:</label>
    <input type="date" name="task_due_date" value="<?php echo $row["task_due_date"]; ?>"><br>
    <label for="task_status">Status:</label>
    <select name="task_status">
      <option value="incomplete" <?php if ($row["task_status"] == "incomplete") echo "selected"; ?>>Incomplete</option>
      <option value="in progress" <?php if ($row["task_status"] == "in progress") echo "selected"; ?>>In progress</option>
      <option value="complete" <?php if ($row["task_status"] == "complete") echo "selected"; ?>>Complete</option>
    </select><br>
    <input type="submit" name="update_task" value="Update task">
  </form>
<?php
}
?>
<?php
// Check if the user has submitted the form to update a task
if (isset($_POST["update_task"])) {
  // Get the ID of the task to update from the form data
  $task_id = $_POST["task_id"];
  
  // Get the updated details of the task from the form data
  $task_name = $_POST["task_name"];
$task_description = $_POST["task_description"];
$task_due_date = $_POST["task_due_date"];
$task_status = $_POST["task_status"];
$task_id = $_POST["task_id"];

// Update the task in the database
$sql = "UPDATE tasks SET task_name='$task_name', task_description='$task_description', task_due_date='$task_due_date', task_status='$task_status' WHERE id=$task_id";

if (mysqli_query($conn, $sql)) {
echo '<script>alert("Task updated successfully.")</script>';
} else {
echo "<script>alert('Error updating task: " . mysqli_error($conn) . "');</script>";
}
}?>
<?php

  // Delete task
  if (isset($_POST["delete_task"])) {
    $task_id = $_POST["task_id"];

    $sql = "DELETE FROM tasks WHERE id = $task_id";

    if (mysqli_query($conn, $sql)) {
        echo '<script>alert("Task deleted successfully.")</script>';
    } else {
        echo "<script>alert('Error deleting task: " . mysqli_error($conn) . "');</script>";
    }
  }
}

// Retrieve tasks from the database
$sql = "SELECT * FROM tasks";

if (isset($_GET["status"])) {
  $status = $_GET["status"];
  $sql .= " WHERE task_status = '$status'";
}

$result = mysqli_query($conn, $sql);

?>

<!-- Display the to-do list form -->
<h2 style="text-align: center">Add a new task</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" >
  <label for="task_name">Task name:</label>
  <input type="text" name="task_name"><br>

  <label for="task_description">Task description:</label>
  <textarea name="task_description"></textarea><br>

  <label for="task_due_date">Due date:</label>
  <input type="date" name="task_due_date"><br>

  <label for="task_status">Status:</label>
  <select name="task_status">
    <option value="incomplete">Incomplete</option>
    <option value="in progress">In progress</option>
    <option value="complete">Complete</option>
  </select><br>

  <input type="submit" name="add_task" value="Add task" style="margin-bottom: 55px">
</form>

<!-- Display the to-do list table -->
<h1 style="text-align: center">ALL TASKS</h1>
<form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
<label for="status_filter">Filter by status:</label>
<select name="status" id="status_filter">
<option value="">All</option>
<option value="incomplete">Incomplete</option>
<option value="in progress">In progress</option>
<option value="complete">Complete</option>
</select>
<input type="submit" value="Filter">

</form>
<table>
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Description</th>
    <th>Due Date</th>
    <th>Status</th>
    <th>Actions</th>
  </tr>
  <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
  <td><?php echo $row["id"]; ?></td>
  <td><?php echo $row["task_name"]; ?></td>
  <td><?php echo $row["task_description"]; ?></td>
  <td><?php echo $row["task_due_date"]; ?></td>
  <td><?php echo $row["task_status"]; ?></td>
  <td>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <input type="hidden" name="task_id" value="<?php echo $row["id"]; ?>">
      <input type="submit" name="edit_task" value="Edit">
      <input type="submit" name="delete_task" value="Delete" onclick="return confirm('Are you sure you want to delete this task?')">
    </form>
  </td>
</tr>
<?php } ?>
</table>
<?php mysqli_close($conn); ?>

<style>
    /* Style the form */
form {
  display: flex;
  flex-direction: column;
  max-width: 500px;
  margin: 0 auto;
}

label {
  margin-top: 10px;
  font-weight: bold;
}

input[type="text"],
textarea,
select {
  padding: 10px;
  border-radius: 5px;
  border: 1px solid #ccc;
  margin-bottom: 15px;
  font-size: 16px;
}

input[type="date"] {
  padding: 10px;
  border-radius: 5px;
  border: 1px solid #ccc;
  margin-bottom: 15px;
  font-size: 16px;
  width: 100%;
}

input[type="submit"] {
  background-color: #4CAF50;
  color: white;
  padding: 10px 20px;
  border-radius: 5px;
  border: none;
  font-size: 16px;
  cursor: pointer;
}

input[type="submit"]:hover {
  background-color: #3e8e41;
}

textarea {
  height: 100px;
}

/* Style the table */
table {
  margin: 20px auto;
  border-collapse: collapse;
  width: 100%;
}

th, td {
  text-align: left;
  padding: 10px;
}

th {
  background-color: #4CAF50;
  color: white;
}

tr:nth-child(even) {
  background-color: #f2f2f2;
}

tr:hover {
  background-color: #ddd;
}

/* Style the filter select */
#status_filter {
  margin-bottom: 20px;
  padding: 10px;
  border-radius: 5px;
  border: 1px solid #ccc;
  font-size: 16px;
  width: 150px;
}

</style>