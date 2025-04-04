<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['cliente'])) {
    header('Location: login.php');
    exit;
}

$db = (new Database())->getConnection();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("
        INSERT INTO avaliacoes (id_cliente, id_orcamento, nota, comentario)
        VALUES (?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $_SESSION['cliente']['id'],
        $_POST['orcamento_id'],
        $_POST['nota'],
        $_POST['comentario']
    ]);
    
    header('Location: dashboard.php?avaliado=1');
    exit;
}

// Buscar orçamento
$stmt = $db->prepare("SELECT * FROM orcamentos WHERE id = ? AND id_cliente = ?");
$stmt->execute([$_GET['id'], $_SESSION['cliente']['id']]);
$orcamento = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Avaliar Serviço</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/cliente.css">
</head>
<body>
    <div class="avaliacao-container">
        <h2>Avaliar Serviço</h2>
        <form method="POST" class="avaliacao-form">
            <input type="hidden" name="orcamento_id" value="<?php echo $orcamento['id']; ?>">
            
            <div class="rating">
                <?php for($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" name="nota" value="<?php echo $i; ?>" id="star<?php echo $i; ?>">
                    <label for="star<?php echo $i; ?>">★</label>
                <?php endfor; ?>
            </div>
            
            <textarea name="comentario" placeholder="Conte-nos sua experiência" required></textarea>
            
            <button type="submit">Enviar Avaliação</button>
        </form>
    </div>
</body>
</html>
