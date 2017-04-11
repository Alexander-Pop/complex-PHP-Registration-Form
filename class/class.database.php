<?php
// Prevent direct initialization of this file
if(!defined('LOCK')) { die('ACCESS DENIED!'); }

// Importing database access data
require $_SERVER['DOCUMENT_ROOT'] . '/config/config.inc.php';

class Database {
    
    // Connect to the database using PDO
    function connect() {
        $dbinfo = array(
            HOST,
            DATABASE,
            PORT,
            CHARSET,
            USER,
            PASS
        );
        
        $dsn = "mysql:host=$dbinfo[0];dbname=$dbinfo[1];port=$dbinfo[2];charset=$dbinfo[3]";
        
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => FALSE
        ];
        
        return new PDO($dsn, $dbinfo[4], $dbinfo[5], $opt);
    }
    
    // Send data to the database
    function sendquery($pdo, $query, $data) {
        $stmt = $pdo->prepare($query);
        $stmt->execute($data);
        return 0;
    }
    
    // Gather data from the database
    function getdata($pdo, $query) {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    // Gather specific data from the database
    function getspecificdata($pdo, $query, $data) {
        $stmt = $pdo->prepare($query);
        $stmt->execute($data);
        return $stmt->fetchColumn();
    }
}