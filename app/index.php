<?php
/**********************************************************
 * Package: dcc_cc_portfolio
 * Project: dcc-cc-portfolio-part2
 * File: app/index.php
 * Author: Diego <20026893@tafe.wa.edu.au>
 * Date: 2020-05-26
 * Version: 1.0.0
 * Description: Entry point of the web application
 **********************************************************/

namespace DccCcPortfolio;
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>PDO - PHP CRUD Tutorial</title>

    <link rel="stylesheet" href="/app/assets/bs/css/bootstrap.min.css">
    <link rel="stylesheet" href="/app/assets/fa/css/all.min.css">

</head>
<body>
<!-- Navigation bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="./">Demo APP</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse"
            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="./">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="./products" id="navbarDropdown"
                   role="button" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">
                    Product
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="./products/browse.php">Browse</a>
                    <a class="dropdown-item" href="./products/create.php">Add</a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="./categories" id="navbarDropdown"
                   role="button" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">
                    Category
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="./categories/browse.php">Browse</a>
                    <a class="dropdown-item" href="./categories/create.php">Add</a>
                </div>
            </li>
    </div>
</nav>
<!-- container -->
<div class="container">

    <div class="row">
        <div class="col-sm">
            <h1>Simple Web App [CRUD/BREAD]</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-sm">
            <h2>Welcome</h2>
            <p>Please use the menu at the top to navigate between products and categories.</p>
        </div>

    </div>

</div> <!-- end .container -->


<script src="/app/assets/jquery/jquery-3.5.1.min.js"></script>
<script src="/app/assets/popper/popper.min.js"></script>
<script src="/app/assets/bs/js/bootstrap.min.js"></script>

</body>
</html>
