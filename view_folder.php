<?php
session_start();
require_once "./connection.php";

// Redirect if not logged in
if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}

$uploadDirectory = 'uploads/';
$folder = isset($_GET['folder']) ? $_GET['folder'] : '';
$zipFilePath = $uploadDirectory . $folder . '.zip';
$folderPath = $uploadDirectory . $folder;

// Check if the folder exists
if ($folder && is_dir($folderPath)) {
    $images = array_diff(scandir($folderPath), array('..', '.'));
} else {
    header("Location: index-images-upload.php");
    exit();
}

// Handle actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action === 'create_zip') {
        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            $files = array_diff(scandir($folderPath), array('..', '.'));
            foreach ($files as $file) {
                $filePath = $folderPath . '/' . $file;
                if (is_file($filePath)) {
                    $zip->addFile($filePath, $file);
                }
            }
            $zip->close();

            // Trigger download
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($zipFilePath) . '"');
            header('Content-Length: ' . filesize($zipFilePath));
            readfile($zipFilePath);
            exit();
        } else {
            $errorMessage = "Failed to create the ZIP file.";
        }
    } elseif ($action === 'unzip') {
        if (file_exists($zipFilePath)) {
            $zip = new ZipArchive();
            if ($zip->open($zipFilePath) === TRUE) {
                if (!is_dir($folderPath)) {
                    mkdir($folderPath, 0777, true);
                }
                // Remove existing files if any
                $existingFiles = array_diff(scandir($folderPath), array('..', '.'));
                foreach ($existingFiles as $file) {
                    unlink($folderPath . '/' . $file);
                }

                // Extract files
                $zip->extractTo($folderPath);
                $zip->close();

                // Redirect to current page with success message
                header("Location: ?folder=" . urlencode($folder) . "&unzip_success=1");
                exit();
            } else {
                $errorMessage = "Failed to unzip the file. The ZIP file might be corrupted or not readable.";
            }
        } else {
            $errorMessage = "The ZIP file does not exist or cannot be found.";
        }
    }
}
?>

<?php include "./includes/header.php"; ?>
<link rel="stylesheet" href="./css/managestyles.css">
<style>
   * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
    }

    .container-fluid {
        padding: 20px;
    }

    .container {
        width: 100%;
        margin: auto;
        overflow: hidden;
    }

    .gallery {
        background: #fff;
        padding: 20px;
        margin: 20px 0;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .gallery-images {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
    }

    .gallery img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }

    .btn-zip, .btn-unzip {
        display: inline-block;
        padding: 10px 20px;
        color: #fff;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        cursor: pointer;
        margin-top: 20px;
    }

    .btn-zip {
        background: #007bff;
    }

    .btn-unzip {
        background: #007bff; 
    }

    .popup {
        display: none;
        position: fixed;
        left: 50%;
        top: 50%;
        width: 80vw; 
        height: 80vh; 
        background: rgba(0, 0, 0, 0.8);
        transform: translate(-50%, -50%); 
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .popup-content {
        position: relative;
        width: 100%;
        height: 100%;
        background: #fff;
        border-radius: 5px;
        overflow: hidden;
    }

    .popup img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .popup .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #ff0000;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        padding: 5px 10px;
    }
</style>

<div class="container-fluid px-4">
    <h3 class="mt-4">Images in Folder: <?= htmlspecialchars($folder) ?></h3>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="./dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="./index-images-upload.php">Images</a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($folder) ?></li>
    </ol>
    <div>
        <div class="container">
            <div class="gallery">
                <?php if (count($images) > 0): ?>
                    <div class="gallery-images">
                        <?php foreach ($images as $image): ?>
                            <img src="<?= htmlspecialchars($folderPath . '/' . $image) ?>" alt="<?= htmlspecialchars($image) ?>" onclick="openPopup('<?= htmlspecialchars($folderPath . '/' . $image) ?>')">
                        <?php endforeach; ?>
                    </div>
                    <a href="?action=create_zip&folder=<?= htmlspecialchars($folder) ?>" class="btn-zip">Download ZIP</a>
                    <a href="?action=unzip&folder=<?= htmlspecialchars($folder) ?>" class="btn-unzip">Unzip File</a>
                    <?php if (isset($_GET['zip_created']) && $_GET['zip_created'] == 1): ?>
                        <p>ZIP file created successfully. <a href="<?= htmlspecialchars($uploadDirectory . $folder . '.zip') ?>">Download now</a>.</p>
                    <?php endif; ?>
                    <?php if (isset($_GET['unzip_success']) && $_GET['unzip_success'] == 1): ?>
                        <p>ZIP file unzipped successfully.</p>
                    <?php endif; ?>
                    <?php if (isset($errorMessage)): ?>
                        <p style="color: red;"><?= htmlspecialchars($errorMessage) ?></p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>No images found in this folder.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Popup Container -->
<div id="popup" class="popup">
    <div class="popup-content">
        <button class="close-btn" onclick="closePopup()">Close</button>
        <img id="popup-img" src="" alt="Image">
    </div>
</div>

<script>
    function openPopup(imageSrc) {
        document.getElementById('popup-img').src = imageSrc;
        document.getElementById('popup').style.display = 'flex';
    }

    function closePopup() {
        document.getElementById('popup').style.display = 'none';
    }
</script>

<?php include "./includes/footer.php"; ?>

