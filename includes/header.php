<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - TechHelp</title>
    <!-- Corrigir paths absolutos -->
    <link rel="stylesheet" href="/sistema/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="manifest" href="/sistema/manifest.json">
    <meta name="theme-color" content="#075E54">
    <link rel="apple-touch-icon" href="/sistema/assets/images/icon-192.png">
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sistema/service-worker.js');
        }
    </script>
    <?php if(isset($pagina_css)): ?>
        <link rel="stylesheet" href="/sistema/assets/css/<?php echo $pagina_css; ?>.css">
    <?php endif; ?>
</head>
<body>
