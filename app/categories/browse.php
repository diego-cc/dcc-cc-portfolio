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

$title = 'Browse categories'
?>

<?php
include('../templates/nav.php'); ?>

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

    // Fetch data
    use PDO;

    include_once '../../config/Database.php';

    $recordsPerPage = 5;

    $database = new Database();
    $conn = $database->getConnection();

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
        // if the page required is greater than the number of pages, set the last one to be active
        $activePage = (int)$_GET["page"] > $numOfPages ? $numOfPages : (int)$_GET["page"];
    }

    // select only the data needed per page, sorted by latest updated
    $query = "SELECT id, code, icon, name, description, created_at, updated_at
                          FROM categories
                          ORDER BY created_at DESC, updated_at ASC
                          LIMIT :rowsToSkip, :recordsPerPage";

    $rowsToSkip = ($activePage - 1) * $recordsPerPage;

    $stmt = $conn->prepare($query);
    $stmt->bindParam(":rowsToSkip", $rowsToSkip, PDO::PARAM_INT);
    $stmt->bindParam(":recordsPerPage", $recordsPerPage, PDO::PARAM_INT);
    $stmt->execute();

    $num = $stmt->rowCount();
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
                                // Get icon extension
                                $ext = pathinfo($row->icon, PATHINFO_EXTENSION);

                                if ($ext === 'png') {
                                    // icon extension is valid, check if it was uploaded
                                    try {
                                        date_default_timezone_set('Australia/Perth');
                                        $filePath = 'uploads/'.date_format(
                                                new \DateTime($row->created_at),
                                                'd-m-Y'
                                            ).'/'.sha1($row->icon.'_'.$row->code).'.png';

                                        if (file_exists($filePath)) {
                                            // display icon
                                            echo '<img src="'.$filePath.'" alt="'.$row->icon.'" width="48" height="48" />';
                                        } else {
                                            // icon was not found, show its name anyway
                                            // a placeholder icon could be displayed here as well
                                            echo $row->icon;
                                        }
                                    } catch (\Exception $e) {
                                        echo 'Could not load icon';
                                    }
                                } else {
                                    // "Unavailable"
                                    echo $row->icon;
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars_decode($row->code, ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars_decode($row->name, ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars_decode($row->description, ENT_QUOTES) ?></td>
                            <td>
                                <a href="read.php?id=<?= $row->id ?>"
                                   class="btn btn-info mr-1">
                                    View
                                </a>
                                <a href="update.php?id=<?= $row->id ?>"
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
            } else {
                include_once '../../classes/Utils.php';
                // if no records found
                $messages[] = ['info' => 'No records found'];
                Utils::messages($messages);
            }
            ?>
        </div>
    </div>
</main> <!-- end .container -->

<?php
include('../templates/scripts.php'); ?>

