<?php
/**********************************************************
 * Package: dcc_cc_portfolio
 * Project: dcc_cc_portfolio
 * File: show.php
 * Author: Diego <20026893@tafe.wa.edu.au>
 * Date: 2020-05-31
 * Version: 1.0.0
 * Description: Show details of a single category
 **********************************************************/

namespace DccCcPortfolio;

include_once '../../classes/Utils.php';
include_once '../../config/Database.php';
include_once '../../classes/Category.php';
include_once '../../classes/Product.php';

use PDO;

$title = 'Show category'
?>

    <?php
$id = '';
$messages = [];
$cat = '';
$prods = [];

$id = basename(Utils::sanitize($_SERVER['PHP_SELF']));

if (is_numeric($id) && floor($id) == $id && (int)$id > 0) {
    $id = (int)$id;

    // try to retrieve category from database
    $db = new Database();
    $conn = $db->getConnection();

    $cat = new Category($conn);

    $result = $cat->readOne($id);

    if (!$result['error']) {
        // category was found, map values
        $cat->id = $result['category']->id;
        $cat->code = $result['category']->code;
        $cat->name = $result['category']->name;
        $cat->icon = $result['category']->icon;
        $cat->description = $result['category']->description;
        $cat->createdAt = $result['category']->created_at;
        $cat->updatedAt = $result['category']->updated_at;
    } else {
        // category was not found, show errors
        $messages[] = ['Danger' => $result['message']];
    }
} else {
    // invalid category ID provided in the URL
    $messages[] = ['Danger' => 'Invalid category ID'];
}
?>

    <?php
include_once '../templates/nav.php' ?>

<?= Utils::messages($messages) ?>

    <main class="container-fluid" role="main">
        <div class="container">
            <h1 class="text-center mb-5">Viewing details of category #<?= $id ?></h1>

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
        // get all associated products
        if (isset($cat->id)) {
            $results = $cat->readAssociatedProducts(-1);

            if (!$results['error']) {
                // all good, products were retrieved
                $stmt = $results['stmt'];

                ?>
                <h2 class="text-center mb-5">Products</h2>
                <?php
                if ($stmt->rowCount() > 0) {
                    // If there's any product that belong to this category, create table
                    ?>
                    <div class="row mb-5">
                        <div class="col">
                            <table class="table table-responsive-sm table-hover">
                                <thead>
                                <tr class="text-center">
                                    <th scope="col">ID</th>
                                    <th scope="col">Image</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Created at</th>
                                    <th scope="col">Updated at</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                while ($prod = $stmt->fetch(PDO::FETCH_OBJ)) {
                                    ?>
                                    <tr class="text-center">
                                        <th scope="row"><?= $prod->id ?></th>
                                        <td><?= empty(trim($prod->image)) ? 'Unavailable' : $prod->image ?></td>
                                        <td><?= $prod->name ?></td>
                                        <td><?= $prod->description ?></td>
                                        <td><span class="text-success">$ <?= $prod->price ?></span></td>
                                        <td><?= Utils::prettyPrintDateTime($prod->created_at) ?></td>
                                        <td><?= Utils::prettyPrintDateTime($prod->updated_at) ?></td>
                                    </tr>
                                    <?php
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php
                } else {
                    // rowCount was 0
                    ?>
                    <p class="font-italic text-center mb-5">No products found.</p>
                    <?php
                } ?>
                <?php
            } else {
                // something went wrong
                $messages[] = ['Warning' => $results['message']];
            }
        }
        ?>
        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center mb-5">
            <a href="/app/categories/create.php" class="btn btn-primary mb-4 mb-md-0 mr-md-5">Add new category</a>
            <a href="/app/categories/update.php/<?= $cat->id ?>"
               class="btn btn-warning mb-4 mb-md-0 mr-md-5">
                Edit category
            </a>
            <a href="/app/categories/delete.php/<?= $cat->id ?>" class="btn btn-danger">Delete category</a>
        </div>
        <?php
        } ?>

    </main>

    <?php
include_once '../templates/scripts.php' ?>