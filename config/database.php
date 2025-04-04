<?php
class Database {
    private $host = "localhost";
    private $port = "3306";
    private $db_name = "techhelp";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        try {
            // Primeiro tenta conectar sem especificar o banco
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Verifica se o banco existe, se não, cria
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS `$this->db_name`");
            
            // Reconecta usando o banco
            $dsn .= ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->conn;
        } catch(PDOException $e) {
            // Log detalhado do erro
            $error = "Erro de conexão: " . $e->getMessage() . "\n";
            $error .= "Host: " . $this->host . "\n";
            $error .= "Port: " . $this->port . "\n";
            $error .= "Database: " . $this->db_name . "\n";
            
            error_log($error, 3, __DIR__ . '/../logs/database.log');
            throw new Exception("Erro de conexão com o banco de dados. Verifique:\n
                1. Se o MySQL está rodando\n
                2. Se as credenciais estão corretas\n
                3. Se o banco 'techhelp' existe");
        }
    }
}
