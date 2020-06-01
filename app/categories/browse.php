<?php
/**********************************************************
 * Package: dcc_cc_portfolio
 * Project: dcc-cc-portfolio-part2
 * File: app/categories/browse.php
 * Author: Diego <20026893@tafe.wa.edu.au>
 * Date: 2020-05-26
 * Version: 1.0.0
 * Description: Browse all categories
 **********************************************************/

namespace DccCcPortfolio;

use PDO;

include_once '../../classes/Category.php';
include_once '../../classes/Utils.php';
include_once '../../config/Database.php';

$title = 'Browse categories';

$cat = '';
$num = 0;
$activePage = '';
$numOfPages = '';
$stmt = '';
$messages = [];

// Fetch data
$recordsPerPage = 5;

try {
    $database = new Database();
    $conn = $database->getConnection();

    $cat = new Category($conn);

    // get total number of records
    $queryCount = "SELECT COUNT(id) AS count FROM categories";

    $stmt = $conn->prepare($queryCount);
    $stmt->execute();

    $totalRecords = $stmt->fetch(PDO::FETCH_ASSOC)["count"];

    // Get number of pages
    $numOfPages = (int)ceil($totalRecords / $recordsPerPage);

    // Fetch data and set up pagination here
    $activePage = 1;

    if (isset($_GET["page"]) && is_numeric($_GET["page"]) && (int)$_GET["page"] > 0) {
        // if the page count requested is greater than the number of pages, set the last one to active
        $activePage = (int)$_GET["page"] > $numOfPages ? $numOfPages : (int)$_GET["page"];
    }

    // select only the data needed per page, sorted by latest updated
    $rowsToSkip = ($activePage - 1) * $recordsPerPage;

    $results = $cat->read($recordsPerPage, $rowsToSkip);

    if (!$results['error']) {
        // categories found
        $stmt = $results['stmt'];
        $num = $stmt->rowCount();
    }
    else {
        // errors, could not retrieve categories
        $messages[] = $results['message'];
    }

    if ($num < 1) {
        // if no records found
        $messages[] = ['info' => 'No records found'];
    }
} catch (\PDOException $ex) {
    $messages[] = [
        'Danger' => 'Could not connect to the database'
    ];
}
?>

<?php
include('../templates/nav.php'); ?>

<?php
Utils::messages($messages) ?>
<!-- container -->
<main role="main" class="container">

    <div class="row mb-4 text-center">
        <div class="col-sm">
            <h1>Browse categories</h1>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-sm text-center">
            <a class="btn btn-primary btn-lg" href="create.php">
                Add a new category
            </a>
        </div>
    </div>

    <div class="row mb-2 text-center">
        <div class="col-sm">
            <p>All categories</p>
        </div>
    </div>
    <?php


    ?>

    <div class="row">
        <div class="col-sm">
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item <?= $activePage <= 1 ? 'disabled' : '' ?>">
                        <a
                                class="page-link"
                                href="
                            <?= "browse.php?page=1" ?>
                                 "
                                aria-label="Previous">
                            <span aria-hidden="true">&#8606;</span>
                            <span class="sr-only">First page</span>
                        </a>
                    </li>
                    <li class="page-item <?= $activePage <= 1 ? 'disabled' : '' ?>">
                        <a
                                class="page-link"
                                href="
                            <?= "browse.php?page=".($activePage - 1 < 1 ? $activePage : $activePage - 1) ?>
                                 "
                                aria-label="Previous">
                            <span aria-hidden="true">&larr;</span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>
                    <?php
                    for ($i = 0; $i < $numOfPages; $i++) {
                        ?>
                        <li class="<?= ($activePage === $i + 1) ? "page-item active" : "page-item" ?>"><a
                                    class="page-link" href="<?= "browse.php?page=".($i + 1) ?>"><?= $i + 1 ?></a></li>
                        <?php
                    }
                    ?>
                    <li class="page-item <?= $activePage >= $numOfPages ? 'disabled' : '' ?>">
                        <a class="page-link"
                           href="<?= "browse.php?page=".($activePage + 1 > $numOfPages ? $activePage : $activePage + 1) ?>"
                           aria-label="Next">
                            <span aria-hidden="true">&rarr;</span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                    <li class="page-item <?= $activePage >= $numOfPages ? 'disabled' : '' ?>">
                        <a class="page-link"
                           href="<?= "browse.php?page=".($numOfPages) ?>"
                           aria-label="Next">
                            <span aria-hidden="true">&#8608;</span>
                            <span class="sr-only">Last page</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-sm">
            <?php
            if ($num > 0) {
                ?>
                <table class="table">
                    <thead>
                    <tr class="text-center">
                        <th>ID</th>
                        <th>Icon</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) { ?>

                        <tr class="text-center">
                            <td><?= $row->id ?></td>
                            <td>
                                <?php
                                // map properties
                                $cat = new Category(null);
                                $cat->id = $row->id;
                                $cat->code = $row->code;
                                $cat->name = $row->name;
                                $cat->icon = $row->icon;
                                $cat->description = $row->description;
                                $cat->createdAt = $row->created_at;
                                $cat->updatedAt = $row->updated_at;

                                $foundIcon = $cat->getIconImage();

                                if ($foundIcon['found']) {
                                    // image found, show it
                                    echo '<img src="'.$foundIcon['path'].'" alt="'.$cat->icon.'" width="48" height="48" />';
                                } else {
                                    // image not found
                                    echo $cat->icon;
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars_decode($cat->code, ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars_decode($cat->name, ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars_decode($cat->description, ENT_QUOTES) ?></td>
                            <td>
                                <a href="show.php/<?= $cat->id ?>"
                                   class="btn btn-info mr-1">
                                    View
                                </a>
                                <a href="edit.php/<?= $cat->id ?>"
                                   class="btn btn-warning mr-1">
                                    Edit
                                </a>
                            </td>
                        </tr>

                        <?php
                    } ?>
                    </tbody>
                </table>
                <?php
            }
            ?>
        </div>
    </div>
</main> <!-- end .container -->

<?php
include('../templates/scripts.php'); ?>

