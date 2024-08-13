<?php
session_start();
require_once "./connection.php";

if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}

$uploadDirectory = 'uploads/';
$folder = isset($_GET['folder']) ? $_GET['folder'] : '';
$zipFileName = isset($_GET['zip_file']) ? $_GET['zip_file'] : '';

if ($zipFileName) {
    // Unzip File
    $zipFilePath = $uploadDirectory . $zipFileName;
    $extractToPath = $uploadDirectory . pathinfo($zipFileName, PATHINFO_FILENAME);

    if (file_exists($zipFilePath)) {
        if (!file_exists($extractToPath)) {
            mkdir($extractToPath, 0777, true); // Create the directory if it doesn't exist
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath) === TRUE) {
            $zip->extractTo($extractToPath);
            $zip->close();
            header("Location: manage_images.php?folder=" . urlencode(pathinfo($zipFileName, PATHINFO_FILENAME)) . "&unzipped=1");
            exit();
        } else {
            echo "Failed to open the ZIP file.";
        }
    } else {
        echo "ZIP file does not exist.";
    }
} elseif ($folder && is_dir($uploadDirectory . $folder)) {
    // Create and Download ZIP
    $folderPath = $uploadDirectory . $folder;
    $images = array_diff(scandir($folderPath), array('..', '.'));

    $zip = new ZipArchive();
    $zipFileName = $folder . '.zip';
    $zipFilePath = tempnam(sys_get_temp_dir(), 'zip_') . '.zip'; // Temporary ZIP file

    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        foreach ($images as $image) {
            $filePath = $folderPath . '/' . $image;
            if (file_exists($filePath) && !is_dir($filePath)) {
                // Add file to ZIP without modifying
                $zip->addFile($filePath, $image);
            }
        }
        $zip->close();

        // Output the ZIP file for download
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
        header('Content-Length: ' . filesize($zipFilePath));
        flush(); // Flush system output buffer

        // Read and output the file
        readfile($zipFilePath);
        unlink($zipFilePath); // Delete the temporary file after download
        exit();
    } else {
        echo 'Failed to create ZIP file';
    }
} else {
    header("Location: index-images-upload.php");
    exit();
}
?>
