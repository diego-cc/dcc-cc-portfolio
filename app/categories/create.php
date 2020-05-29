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

$title = 'Add category';
?>

<?php
$messages = [];

if ($_POST) {
    // Image validation
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $img = $_FILES['image'];

        $imageType = $img['type'];
        $imageSizeMB = $img['size'] / (1024 * 1024);

        $imageX = imagesx($img['tmp_name']);
        $imageY = imagesy($img['tmp_name']);

        $imageName = $img['name'];

        if ($imageType !== 'image/png') {
            $messages[] = ['Danger' => 'Invalid image format. Only PNG is allowed'];
        }

        if ($imageX > 256 || $imageY > 256) {
            $messages[] = ['Danger' => 'Only images up to 256x256 are allowed'];
        }

        if ($imageSizeMB > 2) {
            $messages[] = ['Danger' => 'Only images up to 2MB are allowed'];
        }

        if (strlen(trim($imageName)) > 255 || strlen(trim($imageName)) === '.png') {
            $messages[] = ['Warning' => 'Invalid image filename. Up to 255 characters are allowed'];
        }
    }

    // Fields validation
    if (isset($_POST['code'])) {
        $code = $_POST['code'];

        if (strlen(trim($code)) <= 0) {
            $messages[] = ['Danger' => 'Please provide a category code'];
        }

        if (strlen(trim($code)) !== 4) {
            $messages[] = ['Danger' => 'Exactly 4 characters are required for the category code'];
        }
    }

    if (empty($errors)) {
        // No errors, save data
    }
}
?>

<?php include_once '../templates/nav.php' ?>

<?php Utils::messages($messages) ?>

    <div class="container">
        <h1 class="text-center mb-5">Add a new category</h1>

        <form action="<?= Utils::sanitize($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data">
            <div class="row justify-content-center mb-3">
                <div class="col col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="code">Code<span class="text-danger">*</span> (exactly 4 characters)</label>
                        <input type="text" id="code" name="code" class="form-control" placeholder="Code"
                               maxlength="4" minlength="4"/>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mb-3">
                <div class="col col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="name">Name<span class="text-danger">*</span> (maximum 32 characters)</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Name"
                               maxlength="32"/>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mb-3">
                <div class="col col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="image">Image (.png only, up to 2 MB and 256x256)</label>
                        <input type="file" id="image" name="image" class="form-control-file" placeholder="Image..."
                               accept="image/png"/>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mb-4">
                <div class="col col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="description">Description<span class="text-danger">*</span> (maximum 255 characters)</label>
                        <textarea class="form-control" name="description" id="description" cols="30" rows="10"
                                  placeholder="Description"
                                  maxlength="255"></textarea>
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
    </div>

<?php include_once '../templates/scripts.php' ?>