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
$results = '';
$messages = [];
$img = '';
$icon = '';
$code = '';
$name = '';
$description = '';

$db = new Database();
$conn = $db->getConnection();

$cat = new Category($conn);

if ($_POST) {
    $results = $cat->handleSaveRequest('CREATE', $code, $name, $icon, $description);
    $messages = $results['messages'];

    $code = $results['fields']['code'];
    $name = $results['fields']['name'];
    $description = $results['fields']['description'];
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