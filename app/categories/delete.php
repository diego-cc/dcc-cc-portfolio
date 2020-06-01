<?php
/**********************************************************
 * Package: dcc_cc_portfolio
 * Project: dcc-cc-portfolio
 * File: delete.php
 * Author: Diego <20026893@tafe.wa.edu.au>
 * Date: 2020-06-01
 * Version: 1.0.0
 * Description: Form to confirm category to be deleted
 **********************************************************/

namespace DccCcPortfolio;

use PDO;

include_once '../../classes/Utils.php';
include_once '../../config/Database.php';
include_once '../../classes/Category.php';

$title = 'Delete category';
?>

<?php
$messages = [];
$cat = '';
$result = [];
$stmt = '';
$deleted = false;

$result = Utils::tryFetchCategory($messages);

if ($result['error']) {
    $messages = $result['messages'];
} else {
    $cat = $result['category'];
}

if (isset($_POST['id'])) {
    $id = Utils::sanitize($_POST['id']);

    if (is_numeric($id) && isset($cat->id) && (int)$id === $cat->id) {
        try {
            $stmt = $cat->delete($cat->id);

            if ($stmt->rowCount() < 1) {
                $messages[] = [
                    'Warning' => 'Could not delete category: invalid ID'
                ];
            } else {
                $messages[] = [
                    'Success' =>
                        'Category successfully deleted. 
                        <p class="text-center">
                            <a class="text-info" href="/app/categories/browse.php">
                                Browse categories
                            </a>
                        </p>'
                ];
                $deleted = true;
            }
        } catch (\PDOException $e) {
            $messages[] = [
                'Danger' => 'Could not connect to the database'
            ];
        } catch (\Exception $e) {
            $messages[] = [
                'Danger' => 'Could not delete category. Please try again later or contact your server administrator.'
            ];
        }
    } else {
        if (isset($cat->id)) {
            $messages[] = [
                'Danger' => 'Stop being naughty!'
            ];
        }
    }
}

?>
<?php
include_once '../templates/nav.php' ?>

<?= Utils::messages($messages) ?>

<main class="container-fluid" role="main">
    <div class="container">
        <h1 class="text-center mb-5">Confirm category to be deleted:
            #<?= isset($cat->id) ? $cat->id : 'Invalid ID' ?></h1>

        <?php
        if (isset($cat->id)) {

        // category was successfully retrieved from database, display details here
        ?>
        <ul class="list-group list-group-flush text-center mb-5">
            <li class="list-group-item">
                Category ID: <?= '<span class="font-weight-bold">'.$cat->id.'</span>' ?>
            </li>
            <li class="list-group-item">
                Icon:
                <?php
                $foundIcon = $cat->getIconImage();

                if ($foundIcon['found']) {
                    // image found, display it
                    echo '<div class="mt-4"><img src="'.$foundIcon['path'].'" alt="'.$cat->icon.'" /></div>';
                } else {
                    echo $cat->icon;
                }
                ?>
            </li>
            <li class="list-group-item">
                Code: <?= '<span class="text-success font-weight-bold">'.$cat->code.'</span>' ?></li>
            <li class="list-group-item">Name: <?= $cat->name ?></li>
            <li class="list-group-item">Description: <?= $cat->description ?></li>
            <li class="list-group-item">Created at: <?= Utils::prettyPrintDateTime($cat->createdAt) ?></li>
            <li class="list-group-item">Updated at: <?= Utils::prettyPrintDateTime($cat->updatedAt) ?></li>
        </ul>
    </div>


    <?php
    if (!$deleted) {
        ?>
        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center"
             style="margin-bottom: 100px;">
            <a href="/app/categories/show.php/<?= $cat->id ?>" class="btn btn-dark mb-4 mb-md-0 mr-md-5">View
                category</a>
            <form action="<?= Utils::sanitize($_SERVER['PHP_SELF']) ?>" method="POST">
                <input type="hidden" name="id" value="<?= $cat->id ?>"/>
                <button type="submit" class="btn btn-danger">
                    Confirm delete
                </button>
            </form>
        </div>
        <?php
    }
    ?>

    <div class="row text-center mb-5">
        <div class="col">
            <a href="/app/categories/browse.php" class="h5 text-info">Browse categories</a>
        </div>
    </div>
    <?php
    } ?>

</main>

<?php
include_once '../templates/scripts.php' ?>
