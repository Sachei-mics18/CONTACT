<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
   
    <!-- PS okay lang kopyahin pero intindihin nyo naman yung code wag din ikalat para hindi lahat pare-parehas
     ng gawa kung kaya nyo baguhin yung design go lang - intindihin kung gagayahin --> 
    <!-- design css gumamit na ako ng style para hindi naman boring yung style ng activity na to-->
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #a1c4fd, #c2e9fb);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-top: 10px;
            color: #333;
        }

        input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 2.5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="text"]::placeholder, input[type="number"]::placeholder, textarea::placeholder {
            color: #888;
        }

        button {
            margin-top: 10px;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }
        /* dito naglagay na lang ako ng Display area para sa saved contacts  */
        .data-display {
            margin-top: 10px;
            padding: 5px;
            background-color: #f4f4f4;
            border-radius: 4px;
        }

        .data-display p {
            margin: 2.5px 0;
        }

        .data-display hr {
            margin: 5px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Contact Form</h2>
    <form method="POST">
        <!-- Hindi na ako naglagay ng label dito kasi meron naman placeholder na naka-input yung first name -->
        <!-- firstname -->
        <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
        <!-- lastname -->
        <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
        <!-- age -->
        <input type="number" id="age" name="age" placeholder="Enter your age" min="1" required>
        <!-- contact -->
        <input type="text" id="contact" name="contact" placeholder="Enter your contact number" required>
        <!-- address -->
        <textarea id="address" name="address" rows="4" placeholder="Enter your address" required></textarea>

        <button type="submit">Submit</button>
    </form>

    <?php
    $json_file = 'contacts.json';

    function read_json_data($json_file) {
        if (file_exists($json_file)) {
            $json_data = file_get_contents($json_file);
            // I-decode yung JSON data sa isang associative array - intindihin kung gagayahin
            return json_decode($json_data, true);
        }
        return []; // Ibabalik yung isang walang laman na array kung wala yung file - intindihin kung gagayahin

    }
        // Function na mag-save ng bagong contact data sa JSON file

    function save_to_json($data, $json_file) {
        // Babasahin yung mga existing contact mula sa JSON file
        $json_data = read_json_data($json_file);
        // Idagdag yung bagong contact sa array
        array_push($json_data, $data);
        // I-save yung na-update na array pabalik sa JSON file
        file_put_contents($json_file, json_encode($json_data, JSON_PRETTY_PRINT));
    }
        // gumamit parin ako ng delete dito if mali yung nalagay na info pwede idelete nalang
        // Function na magtanggal ng contact mula sa JSON file by index
    function delete_from_json($index, $json_file) {
        // Babasahin yung mga existing contact mula sa JSON file
        $json_data = read_json_data($json_file);
        if (isset($json_data[$index])) {
            array_splice($json_data, $index, 1);
            file_put_contents($json_file, json_encode($json_data, JSON_PRETTY_PRINT));
        }
    }
    // processing yung pagsusub ng form para sa pagdaragdag ng bagong contact
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Capture and sanitize form data
        $new_contact = [
            "first_name" => htmlspecialchars($_POST['first_name']),
            "last_name" => htmlspecialchars($_POST['last_name']),
            "age" => (int)$_POST['age'],
            "contact" => htmlspecialchars($_POST['contact']),
            "address" => htmlspecialchars($_POST['address']),
        ];

    // I-save yung bagong data ng contact sa JSON file
        save_to_json($new_contact, $json_file);
        // Mag-redirect sa parehong page para maiwasan yung muling pagsusub ng form sa pag-refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
      // Handle deletion of a contact
    if (isset($_GET['delete'])) {
        // Kunin yung index ng contact na iddelete mula sa URL
        $index_to_delete = (int)$_GET['delete']; 
        // icall yung delete function para alisin yung contact na ginawa
        delete_from_json($index_to_delete, $json_file); 
        // redirect sa parehong page para maiwasan yung muling pagsusub ng form sa pag-refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    $contacts = read_json_data($json_file);
    // Display saved contacts if any exist
    if (!empty($contacts)) {
        echo "<div class='data-display'>";
        echo "<h3>Saved Contacts</h3>";
        foreach ($contacts as $index => $contact) {
            echo "<p><strong>First Name:</strong> {$contact['first_name']}</p>";
            echo "<p><strong>Last Name:</strong> {$contact['last_name']}</p>";
            echo "<p><strong>Age:</strong> {$contact['age']}</p>";
            echo "<p><strong>Contact:</strong> {$contact['contact']}</p>";
            echo "<p><strong>Address:</strong> {$contact['address']}</p>";
            echo "<form method='GET' style='display:inline;'>";
            echo "<button class='delete-btn' type='submit' name='delete' value='$index'>Delete</button>";
            echo "</form>";
            echo "<hr>"; // Separator between contacts
        }
        echo "</div>";
    }
    ?>
</div>

</body>
</html>
