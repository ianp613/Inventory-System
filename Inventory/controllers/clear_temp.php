<?php
    $folderPath = '../assets/temp'; // Change this to your folder path

    // Check if the folder exists
    if (!is_dir($folderPath)) {
        die("Error: Folder not found.");
    }

    // Get all files inside the folder
    $files = glob($folderPath . '/*'); // Get all files

    // Loop through each file and delete it
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file); // Delete the file
        }
    }
    echo json_encode(true);

?>
