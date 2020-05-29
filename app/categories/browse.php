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
?>

<!DOCTYPE HTML>
<html lang="en-AU">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Browse products</title>

    <!-- CSS required -->
    <!-- Bootstrap 4.x -->
    <link rel="stylesheet" href="/app/assets/bs/css/bootstrap.min.css">
    <!-- FontAwesome 5.x -->
    <link rel="stylesheet" href="/app/assets/fa/css/all.min.css">

</head>
<body>
<!-- Navigation bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="../">Demo APP</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse"
            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="../">Home</a>
            </li>
            <li class="nav-item dropdown active">
                <a class="nav-link dropdown-toggle" href="../products" id="navbarDropdown"
                   role="button" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">
                    Product <span class="sr-only">(current)</span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="../products/browse.php">Browse</a>
                    <a class="dropdown-item" href="../products/create.php">Add</a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="../categories" id="navbarDropdown"
                   role="button" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">
                    Category
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="#">Browse</a>
                    <a class="dropdown-item" href="create.php">Add</a>
                </div>
            </li>
    </div>
</nav>

<!-- container -->
<main role="main" class="container">

    <div class="row mb-4 text-center">
        <div class="col-sm">
            <h1>Browse categories</h1>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-sm text-center">
            <button class="btn btn-primary btn-lg">
                Add a new category
            </button>
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

    // select only the data needed per page
    $query = "SELECT id, code, name, description
                          FROM categories
                          ORDER BY id
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
                    <li class="page-item <?=$activePage <= 1 ? 'disabled' : ''?>">
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
                    <li class="page-item <?=$activePage <= 1 ? 'disabled' : ''?>">
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
                    <li class="page-item <?=$activePage >= $numOfPages ? 'disabled' : ''?>">
                        <a class="page-link"
                           href="<?= "browse.php?page=".($activePage + 1 > $numOfPages ? $activePage : $activePage + 1) ?>"
                           aria-label="Next">
                            <span aria-hidden="true">&rarr;</span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                    <li class="page-item <?=$activePage >= $numOfPages ? 'disabled' : ''?>">
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
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                        // creating new table row per record
                        ?>
                        <tr>
                            <td><?= $row->id ?></td>
                            <td><?= $row->code ?></td>
                            <td><?= $row->name ?></td>
                            <td><?= $row->description ?></td>
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

<!-- JavaScript that is required -->
<script src="/app/assets/jquery/jquery-3.5.1.min.js"></script>
<script src="/app/assets/popper/popper.min.js"></script>
<script src="/app/assets/bs/js/bootstrap.min.js"></script>

</body>
</html>
