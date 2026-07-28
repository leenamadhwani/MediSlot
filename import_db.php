<?php
require_once 'config.php';

$sqlFile = 'aiven_import.sql';
if (!file_exists($sqlFile)) {
    die("Error: database.sql file not found in the project directory.");
}

$sql = file_get_contents($sqlFile);

// Execute multi-query to create all tables and insert default data
if ($conn->multi_query($sql)) {
    do {
        // Store and free results from multi-query execution
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    
    echo "SUCCESS: Database tables created and imported into Aiven successfully!";
} else {
    echo "ERROR during import: " . $conn->error;
}
?>