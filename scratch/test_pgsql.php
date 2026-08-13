<?php
try {
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=postgres', 'postgres', 'admin');
    echo "CONEXION_EXITOSA\n";

    // Check if database 'sistema_pagos' exists, if not create it
    $stmt = $pdo->query("SELECT 1 FROM pg_database WHERE datname = 'sistema_pagos'");
    if (!$stmt->fetchColumn()) {
        $pdo->exec("CREATE DATABASE sistema_pagos");
        echo "BASE_DATOS_CREADA: sistema_pagos\n";
    } else {
        echo "BASE_DATOS_EXISTE: sistema_pagos\n";
    }
} catch (Exception $e) {
    echo "ERROR_CONEXION: " . $e->getMessage() . "\n";
}
