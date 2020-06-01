<?php
/**********************************************************
 * Package: dcc_cc_portfolio
 * Project: dcc-cc-portfolio
 * File: edit.php
 * Author: Diego <20026893@tafe.wa.edu.au>
 * Date: 2020-05-31
 * Version: 1.0.0
 * Description: Form to edit a category
 **********************************************************/

namespace DccCcPortfolio;

include_once '../../classes/Utils.php';
include_once '../../classes/Category.php';
include_once '../../config/Database.php';

$title = 'Edit category';
?>

<?php
$code = '';
$name = '';
$icon = '';
$img = '';
$description = '';
$cat = '';
$messages = [];
$result = '';

$result = Utils::tryFetchCategory($messages);

if ($result['error']) {
    $messages = $result['messages'];
} else {
    $cat = $result['category'];
}

if ($_POST) {
    try {
        $results = $cat->handleSaveRequest('UPDATE', $code, $name, $icon, $description);
        $messages = $results['messages'];
    } catch (\PDOException $e) {
        $messages[] = [
            'Danger' => 'Could not connect to the database'
        ];
    } catch (\Exception $e) {
        $messages[] = [
            'Warning' => 'Could not send request: invalid action'
        ];
    }
}
?>

<?php
include_once '../templates/nav.php' ?>
<?= Utils::messages($messages) ?>
<main role="main" class="container">
    <h1 class="text-center mb-5">
        Editing category #<?= isset($cat->id) ? $cat->id : 'Invalid ID' ?>
    </h1>
    <?php
    if (isset($cat->id)) {

    // Category was found
    ?>
    <form action="<?= Utils::sanitize($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data">
        <div class="row justify-content-center mb-3">
            <div class="col col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="code">Code<span class="text-danger">*</span> (exactly 4 characters)</label>
                    <input type="text" id="code" name="code" class="form-control" placeholder="Code"
                           value="<?= substr($cat->code, 0, 4) ?>"
                    />
                </div>
            </div>
        </div>

        <div class="row justify-content-center mb-3">
            <div class="col col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="name">Name<span class="text-danger">*</span> (maximum 32 characters)</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Name"
                           value="<?= substr($cat->name, 0, 32) ?>"
                    />
                </div>
            </div>
        </div>

        <div class="row justify-content-center mb-3">
            <div class="col col-sm-12 col-md-6">
                <div>
                    <p>Current icon:
                        <?php
                        $foundIcon = $cat->getIconImage();

                        if ($foundIcon['found']) {
                            // image found, display it
                            echo '<div class="mt-4"><img src="'.$foundIcon['path'].'" alt="'.$cat->icon.'" /></div>';
                        } else {
                            echo $cat->icon;
                        }
                        ?>
                    </p>
                </div>
                <div class="form-group">
                    <label for="icon">New icon (.png only, up to 2 MB and 256x256)</label>
                    <input type="file" id="icon" name="icon" class="form-control-file" placeholder="Icon..."
                           accept="image/png"/>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mb-4">
            <div class="col col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="description">Description<span class="text-danger">*</span> (maximum 255
                        characters)</label>
                    <textarea class="form-control text-justify" name="description" id="description" cols="30"
                              rows="10"
                              placeholder="Description"
                    ><?= substr($cat->description, 0, 255) ?></textarea>
                </div>
            </div>
        </div>

        <div class="text-center" style="margin-bottom: 100px;">
            <button class="btn btn-lg btn-primary" type="submit">Save changes</button>
        </div>

        <div class="text-center mb-5">
            <a class="h5 text-info" href="/app/categories/browse.php">Browse categories</a>
        </div>
    </form>
</main>
<?php
} ?>
<?php
include_once '../templates/scripts.php' ?>
