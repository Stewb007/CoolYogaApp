<?php

$error = '';
$name = '';
$email = '';
$subject = '';
$message = '';

function clean_text($string)
{
    $string = trim($string);
    $string = stripslashes($string);
    $string = htmlspecialchars($string);
    return $string;
}

if (file_exists('contact.csv') && filesize('contact.csv') > 0) {
    $csv_data = array_map('str_getcsv', file('contact.csv'));
} else {
    $csv_data = [];
}

if(isset($_POST["submit"]))
{
    if(empty($_POST["name"]) || empty($_POST["email"]) || empty($_POST["subject"]) || empty($_POST["message"]))
    {
        $error .= '<p><label class="text-danger">All fields are required</label></p>';
    }
    else
    {
        $name = clean_text($_POST["name"]);
        $email = clean_text($_POST["email"]);
        $subject = clean_text($_POST["subject"]);
        $message = clean_text($_POST["message"]);

        $file_open = fopen("contact.csv", "a");
        $form_data = array($name, $email, $subject, $message);
        fputcsv($file_open, $form_data);
        fclose($file_open);

        // Reload CSV data
        $csv_data = array_map('str_getcsv', file('contact.csv'));

        $name = '';
        $email = '';
        $subject = '';
        $message = '';
        $error = '<label class="text-success">User Added Successfully!</label>';
    }
}
if(isset($_POST["delete-submit"]))
{
    if(isset($_POST['delete']) && !empty($_POST['delete'])) {
        $delete_rows = $_POST['delete'];
        $new_csv_data = [];

        foreach($csv_data as $key => $row) {
            if (!in_array($key, $delete_rows)) {
                $new_csv_data[] = $row;
            }
        }

        $file = fopen('contact.csv', 'w');
        foreach($new_csv_data as $fields) {
            fputcsv($file, $fields);
        }
        fclose($file);

        // Reload CSV data
        $csv_data = array_map('str_getcsv', file('contact.csv'));

        header("Location: {$_SERVER['REQUEST_URI']}");
        exit;
    } else {
        $error = '<label class="text-danger">Please select at least one row to delete</label>';
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Cool Yoga app database</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
        function showForm() {
            document.getElementById('add-button').style.display = 'none';
            document.getElementById('form-container').style.display = 'block';
        }

        function toggleCheckbox(element) {
            var checkboxes = document.getElementsByName('delete[]');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = element.checked;
            }
        }

        function validateDelete() {
            var checkboxes = document.getElementsByName('delete[]');
            var isChecked = false;
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    isChecked = true;
                    break;
                }
            }
            if (!isChecked) {
                alert('Please select at least one row to delete');
                return false;
            }
            return true;
        }
    </script>
    <head>
    <title>Cool Yoga app database</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Importing fontawesome styles */
        @import url('https://kit-free.fontawesome.com/releases/latest/css/free.min.css');
        
        /* Setting font-family for all text */
        body, button, input, select, textarea {
            font-family: 'Roboto', sans-serif;
        }
        
        /* Set background color to black and text color to white */
        body {
            background-color: #141414;
            color: white;
        }
        .add-button {
            background-color: #007bff; /* Blue color */
            color: white;
            border: none;
            padding: 15px 30px; /* Larger padding for bigger button */
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 20px; /* Larger font size */
            margin: 10px 2px; /* Increased margin */
            cursor: pointer;
            border-radius: 8px;
            transition: background-color 0.3s ease; /* Smooth transition effect */
        }

        /* Hover effect for the add button */
        .add-button:hover {
            background-color: #004080; /* Darker blue color on hover */
        }
    </style>
    <!-- Font Awesome script -->
    <script src="https://kit.fontawesome.com/bbdcfd916b.js" crossorigin="anonymous"></script>
</head>
<body>
<br />
<div class="container">
    <h2 align="center">Cool Yoga App Database</h2>
    <br />
    <div class="col-md-6">
        <h3 align="center">CSV Data</h3>
        <form method="post" onsubmit="return validateDelete()">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th><input type="checkbox" onclick="toggleCheckbox(this)"></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($csv_data as $key => $row): ?>
                    <tr>
                        <td><input type="checkbox" name="delete[]" value="<?php echo $key; ?>"></td>
                        <td><?php echo $row[0]; ?></td>
                        <td><?php echo $row[1]; ?></td>
                        <td><?php echo $row[2]; ?></td>
                        <td><?php echo $row[3]; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div align="center">
                <button type="submit" name="delete-submit" class="btn btn-danger">Delete</button>
            </div>
            <?php echo $error; ?>
        </form>
    </div>
    <div id="add-button" align="center">
        <button class="btn btn-info" onclick="showForm()">Add</button>
    </div>
    <div id="form-container" class="col-md-6" style="display: none;">
        <form method="post">
            <h3 align="center">Submission Form</h3>
            <br />
            <?php echo $error; ?>
            <div class="form-group">
                <label>Enter Name</label>
                <input type="text" name="name" placeholder="Enter Name" class="form-control" value="<?php echo $name; ?>" />
            </div>
            <div class="form-group">
                <label>Enter Email</label>
                <input type="text" name="email" class="form-control" placeholder="Enter Email" value="<?php echo $email; ?>" />
            </div>
            <div class="form-group">
                <label>Enter Subject</label>
                <input type="text" name="subject" class="form-control" placeholder="Enter Subject" value="<?php echo $subject; ?>" />
            </div>
            <div class="form-group">
                <label>Enter Message</label>
                <textarea name="message" class="form-control" placeholder="Enter Message"><?php echo $message; ?></textarea>
            </div>
            <div class="form-group" align="center">
                <input type="submit" name="submit" class="btn btn-info" value="Submit" />
            </div>
        </form>
    </div>
</div>
</body>
</html>
