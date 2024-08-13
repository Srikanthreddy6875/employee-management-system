<?php
session_start();
require_once "./connection.php";

if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}

$uploadMessage = ''; // upload status message
$uploadDirectory = 'uploads/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dateFolder = date('d-m-Y');
    $targetDirectory = $uploadDirectory . $dateFolder;

    if (isset($_FILES['images'])) {
        // Create the date-wise folder if it doesn't exist
        if (!file_exists($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }

        $uploadSuccess = true;

        foreach ($_FILES['images']['name'] as $key => $name) {
            $targetFilePath = $targetDirectory . '/' . basename($name);
            if (!move_uploaded_file($_FILES['images']['tmp_name'][$key], $targetFilePath)) {
                $uploadSuccess = false;
                break;
            }
        }

        if ($uploadSuccess) {
            $uploadMessage = '<p style="color: green;">Images uploaded successfully!</p>';
        } else {
            $uploadMessage = '<p style="color: red;">Failed to upload images. Please try again.</p>';
        }
    }
}
?>

<?php include "./includes/header.php"; ?>
<link rel="stylesheet" href="./css/managestyles.css">
<main>
    <div class="container-fluid px-4">
        <h3 class="mt-4">Images Gallery</h3>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="./dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Images</li>
        </ol>
        <div>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                .container {
                    width: 100%;
                    margin: auto;
                    overflow: hidden;
                }

                .upload-form,
                .gallery {
                    background: #fff;
                    padding: 20px;
                    margin: 20px 0;
                    border-radius: 5px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }

                .upload-form input[type="file"] {
                    width: 100%;
                    padding: 10px;
                }

                .upload-form button {
                    padding: 10px 20px;
                    background: #007bff;
                    color: #fff;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    margin-left: 10px;
                }

                .upload-form button:hover {
                    background: #0056b3;
                }

                .gallery {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 20px;
                }

                .gallery-section {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
                    background: #f9f9f9;
                }

                .demo_zip {
                    width: 50px;
                    height: 50px;
                    background-image: url(./zipfile_image.webp);
                    background-color: #0056b3;
                    background-size: cover;
                    display: inline-block;
                }

                .gallery a {
                    text-decoration: none;
                    color: #007bff;
                    font-weight: normal; /* Normal font weight */
                    font-size: 16px; /* Normal font size */
                }

                .gallery a:hover {
                    color: #0056b3;
                }
            </style>

            <div class="container">
                <div class="upload-form">
                    <h4>Upload Images</h4>
                    <?= $uploadMessage ?>
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="file" name="images[]" id="images" multiple>
                        <button type="submit">Upload</button>
                    </form>
                </div>

                <div class="gallery">
                    <?php
                    $folders = array_diff(scandir($uploadDirectory), array('..', '.'));

                    foreach ($folders as $folder) {
                        echo '<div class="gallery-section">';
                        echo '<div class="demo_zip"></div>'; // Display ZIP image
                        echo '<a href="view_folder.php?folder=' . urlencode($folder) . '">' . htmlspecialchars($folder) . '</a>'; // Normal font and folder name
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include "./includes/footer.php"; ?>
