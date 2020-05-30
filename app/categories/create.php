<?php
/**********************************************************
 * Package: dcc_cc_portfolio
 * Project: dcc-cc-portfolio
 * File: app/categories/create.php
 * Author: Diego <20026893@tafe.wa.edu.au>
 * Date: 2020-05-30
 * Version: 1.0.0
 * Description: Form to add a category
 **********************************************************/

namespace DccCcPortfolio;

include_once '../../classes/Utils.php';
include_once '../../config/Database.php';
include_once '../../classes/Category.php';

$title = 'Add category';
?>

    <?php
$messages = [];
$img = '';
$icon = '';
$code = '';
$name = '';
$description = '';

date_default_timezone_set('Australia/Perth');
$uploadDir = 'uploads/'.date('d-m-Y').'/';
$uploadedFile = '';

// Form validation
if ($_POST) {
    // Icon validation
    if ($_FILES['icon']['error'] === UPLOAD_ERR_OK) {
        $img = $_FILES['icon'];

        $imageType = $img['type'];
        $imageSizeMB = $img['size'] / (1024 * 1024);

        $imgSize = getimagesize($img['tmp_name']);
        $imageX = $imgSize[0];
        $imageY = $imgSize[1];

        $icon = Utils::sanitize($img['name']);

        if ($imageType !== 'image/png') {
            $messages[] = ['Danger' => 'Invalid image format. Only PNG is allowed'];
        }

        if ($imageX > 256 || $imageY > 256) {
            $messages[] = ['Danger' => 'Only icons up to 256x256 are allowed'];
        }

        if ($imageSizeMB > 2) {
            $messages[] = ['Danger' => 'Only icons up to 2 MB are allowed'];
        }

        if (strlen($icon) > 255 || strlen(trim($icon)) === '.png') {
            $messages[] = ['Warning' => 'Invalid icon filename. Up to 255 characters are allowed'];
        }
    }

    // Code validation
    if (isset($_POST['code'])) {
        $code = Utils::sanitize($_POST['code']);

        if (strlen(trim($code)) <= 0) {
            $messages[] = ['Danger' => 'Please provide a category code'];
        }

        if (strlen(trim($code)) !== 4) {
            $messages[] = ['Danger' => 'Exactly 4 characters are required for the category code'];
        }
    }

    // Name validation
    if (isset($_POST['name'])) {
        $name = Utils::sanitize($_POST['name']);

        if (strlen(trim($name)) <= 0) {
            $messages[] = ['Danger' => 'Please provide a category name'];
        }

        if (strlen($name) > 32) {
            $messages[] = ['Danger' => 'Only category names up to 32 characters are allowed'];
        }
    }

    // Description validation
    if (isset($_POST['description'])) {
        $description = Utils::sanitize($_POST['description']);

        if (strlen(trim($description)) <= 0) {
            $messages[] = ['Danger' => 'Please provide a description'];
        }

        if (strlen($description) > 255) {
            $messages[] = ['Danger' => 'Only descriptions up to 255 characters are allowed'];
        }
    }

    // No errors, save data
    if (empty($messages)) {
        $db = new Database();
        $conn = $db->getConnection();

        $cat = new Category($conn);

        $cat->code = $code;
        $cat->name = $name;
        $cat->icon = $icon;
        $cat->description = $description;

        $result = $cat->create();

        if (!$result['error']) {
            // category was added to database, save icon
            // if upload directory doesn't exist, create it recursively
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // hash icon with corresponding category code
            // not perfect, but since $code is unique should be good enough for now
            $uploadedFile = $uploadDir.sha1($icon.'_'.$code).'.png';

            // save icon / show warning message if it could not be saved
            if (isset($img['tmp_name'])) {
                if (!move_uploaded_file($img['tmp_name'], $uploadedFile)) {
                    $messages[] = ['Warning' => 'Could not save icon. Please try again or contact your server administrator.'];
                }
            }
            $messages[] = ['Success' => 'Category successfully added'];
        } else {
            // category was not added, show error
            $messages[] = ['Warning' => 'Could not add category: '.$result['message']];
        }
    }
}
?>

    <?php
include_once '../templates/nav.php' ?>

    <?php
Utils::messages($messages) ?>

    <main role="main" class="container">
        <h1 class="text-center mb-5">Add a new category</h1>

        <form action="<?= Utils::sanitize($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data">
            <div class="row justify-content-center mb-3">
                <div class="col col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="code">Code<span class="text-danger">*</span> (exactly 4 characters)</label>
                        <input type="text" id="code" name="code" class="form-control" placeholder="Code"
                               value="<?= substr($code, 0, 4) ?>"
                        />
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mb-3">
                <div class="col col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="name">Name<span class="text-danger">*</span> (maximum 32 characters)</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Name"
                               value="<?= substr($name, 0, 32) ?>"
                        />
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mb-3">
                <div class="col col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="icon">Icon (.png only, up to 2 MB and 256x256)</label>
                        <input type="file" id="icon" name="icon" class="form-control-file" placeholder="Icon..."
                               accept="image/png"/>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mb-4">
                <div class="col col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="description">Description<span class="text-danger">*</span> (maximum 255 characters)</label>
                        <textarea class="form-control text-justify" name="description" id="description" cols="30"
                                  rows="10"
                                  placeholder="Description"
                        ><?= substr($description, 0, 255) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="text-center" style="margin-bottom: 100px;">
                <button class="btn btn-lg btn-primary" type="submit">Save changes</button>
            </div>

            <div class="text-center mb-5">
                <a class="h5 text-info" href="browse.php">Browse categories</a>
            </div>
        </form>
    </main>

    <?php
include_once '../templates/scripts.php' ?>