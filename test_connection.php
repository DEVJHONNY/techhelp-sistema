<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    echo "<div style='font-family: Arial; padding: 20px;'>";
    echo "<h2>✅ Conexão estabelecida com sucesso!</h2>";
    
    // Verificar tabelas existentes
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<h3>Tabelas encontradas:</h3>";
    echo "<ul>";
    foreach($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
    // Teste simples de SELECT
    $stmt = $db->query("SELECT COUNT(*) FROM clientes");
    $totalClientes = $stmt->fetchColumn();
    echo "<p>Total de clientes cadastrados: $totalClientes</p>";
    
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color: red; font-family: Arial; padding: 20px;'>";
    echo "<h2>❌ Erro:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
