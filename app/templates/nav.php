<?php
/**********************************************************
 * Package: dcc_cc_portfolio
 * Project: dcc-cc-portfolio
 * File: app/templates/nav.php
 * Author: Diego <20026893@tafe.wa.edu.au>
 * Date: 2020-05-30
 * Version: 1.0.0
 * Description: Default navbar. Provide a $title before including it.
 **********************************************************/
namespace DccCcPortfolio;
?>

<!DOCTYPE HTML>
<html lang="en-AU">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title><?=$title?></title>

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
            <li class="nav-item dropdown">
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
                    <a class="dropdown-item" href="../categories/browse.php">Browse</a>
                    <a class="dropdown-item" href="../categories/create.php">Add</a>
                </div>
            </li>
    </div>
</nav>

