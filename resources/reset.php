<?php
/**********************************************************
 * Project:     api-practice
 * File:        reset.php
 * Author:      Adrian <Adrian@tafe.wa.edu.au>
 * Date:        2020-05-20
 * Version:     1.1.0
 * Description: add short description of file's purpose
 **********************************************************/

/**
 * Start performance timers, configure database and configure files to import
 */
$dumpTime = microtime();
$timeStart = microtime(true);
$rustart = getrusage();
echo "\nReset started... \n";

$dsn = "mysql:host=localhost;port=3306;charset=utf8";
$pdo = new PDO($dsn, 'root', '');

$filePaths = [
    'cc-portfolio-1-create-db.sql',
    'cc-portfolio-2-create-tables.sql',
    'cc-portfolio-3-seed-data.sql',
    'cc-portfolio-4-updates.sql'
];


/**
 * Process execution time (system level)
 *
 * @param $ru
 * @param $rus
 * @param $index
 * @return float|int
 */
function rutime($ru, $rus, $index)
{
    return ($ru["ru_$index.tv_sec"] * 1000 + (int) ($ru["ru_$index.tv_usec"] / 1000))
        - ($rus["ru_$index.tv_sec"] * 1000 + (int) ($rus["ru_$index.tv_usec"] / 1000));
}

/**
 * Import SQL File
 *
 * @param $pdo
 * @param $sqlFile
 * @param  null  $tablePrefix
 * @param  null  $InFilePath
 * @return bool
 */
function importSqlFile($pdo, $sqlFile, $tablePrefix = null, $InFilePath = null)
{
    try {
        // Enable LOAD LOCAL INFILE
        $pdo->setAttribute(\PDO::MYSQL_ATTR_LOCAL_INFILE, true);

        $errorDetect = false;

        // Temporary variable, used to store current query
        $tmpLine = '';

        // Read in entire file
        $lines = file($sqlFile);

        // Loop through each line
        foreach ($lines as $line) {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || trim($line) == '') {
                continue;
            }

            // Read & replace prefix
            $line = str_replace(['<<prefix>>', '<<InFilePath>>'], [$tablePrefix, $InFilePath],
                $line);

            // Add this line to the current segment
            $tmpLine .= $line;

            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';') {
                try {
                    // Perform the Query
                    $pdo->exec($tmpLine);
                } catch (\PDOException $e) {
                    echo "<br><pre>Error performing Query: '<strong>".$tmpLine."</strong>': ".$e->getMessage()."</pre>\n";
                    $errorDetect = true;
                }

                // Reset temp variable to empty
                $tmpLine = '';
            }
        }

        // Check if error is detected
        if ($errorDetect) {
            return false;
        }
    } catch (\Exception $e) {
        echo "<br><pre>Exception => ".$e->getMessage()."</pre>\n";
        return false;
    }

    return true;
}

// Import the SQL file
foreach ($filePaths as $filePath) {
    $res = importSqlFile($pdo, $filePath);
    if ($res === false) {
        die('ERROR');
    }
}

$timeEnd = microtime(true);
echo "Reset ended... \n";

//dividing with 60 will give the execution time in minutes otherwise seconds
$timeExecuted = ($timeEnd - $timeStart);

$ru = getrusage();
//execution time of the script
echo "Completed in ".number_format((float) $timeExecuted, 2)." seconds\n";
echo "This process used ".rutime($ru, $rustart, "utime")." ms for its computations\n";
echo "It spent ".rutime($ru, $rustart, "stime")." ms in system calls\n";
