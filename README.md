# dcc-cc-portfolio
A demo application for a fictional small retail store, developed as an assignment @ North Metropolitan TAFE. Built with vanilla PHP and MariaDB.

## Instructions
Make sure you have installed PHP (7.4+) and MariaDB (10.4+) before proceeding.

### 1 - Clone the repo
`git clone https://github.com/diego-cc/dcc-cc-portfolio.git`

### 2 - Move to the project's directory
`cd dcc-cc-portfolio`

### 3 - Create database, user and seed data
You can do this in one go using the [reset.php](https://github.com/diego-cc/dcc-cc-portfolio/blob/master/resources/reset.php "reset.php") script. Make sure that your database server is running first.

```
cd resources
php reset.php
```

### 4 - Start the application
Move the project to your `www` folder. Then, if you're using Laragon, press start (or restart it) and navigate to:

`http://dcc-cc-portfolio.test/app/categories/browse.php`

Otherwise, dump the contents of `dcc-cc-portfolio` into your `www` folder and navigate to (specifying your server port):

`http://localhost/app/categories/browse.php`

(Sorry, I'm too lazy to fix the `href`s and `src`s)
