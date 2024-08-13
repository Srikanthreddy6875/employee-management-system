<?php
session_start();
require_once "./connection.php";

if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}

if (isset($_GET['folder']) && isset($_GET['zip_name'])) {
    $uploadDirectory = 'uploads/';
    $folder = $_GET['folder'];
    $zipName = $_GET['zip_name'];
    $zipPath = $uploadDirectory . $folder . '/' . $zipName;

    if (file_exists($zipPath)) {
        $zip = new ZipArchive;
        if ($zip->open($zipPath) === TRUE) {
            $zip->extractTo($uploadDirectory . $folder);
            $zip->close();

            header("Location: view_images.php?folder=" . urlencode($folder));
            exit();
        } else {
            echo "Failed to open ZIP file.";
        }
    } else {
        echo "ZIP file does not exist.";
    }
} else {
    echo "Invalid request.";
}
?>
