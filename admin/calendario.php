<?php
session_start();
if(!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$db = (new Database())->getConnection();

// Buscar agendamentos do mês
$mes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$ano = isset($_GET['ano']) ? $_GET['ano'] : date('Y');

$stmt = $db->prepare("
    SELECT o.*, c.nome as cliente_nome 
    FROM orcamentos o 
    JOIN clientes c ON o.id_cliente = c.id 
    WHERE MONTH(o.data_agendamento) = ? 
    AND YEAR(o.data_agendamento) = ?
");
$stmt->execute([$mes, $ano]);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Calendário - TechHelp</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
</head>
<body class="admin">
    <!-- ... nav ... -->
    <div class="admin-container">
        <div id="calendario"></div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendario');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            events: <?php echo json_encode($agendamentos); ?>,
            eventClick: function(info) {
                // Mostrar detalhes do agendamento
            }
        });
        calendar.render();
    });
    </script>
</body>
</html>
