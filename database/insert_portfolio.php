<?php
require_once '../config/database.php';
$db = (new Database())->getConnection();

$portfolioItems = [
    [
        'titulo' => 'Montagem PC Gamer',
        'descricao' => 'Montagem completa de PC Gamer com RTX 3060, Ryzen 5',
        'imagem' => 'images/portfolio/pc_gamer1.jpg',
        'categoria' => 'computador',
        'depoimento' => 'Excelente trabalho, PC rodando todos os jogos!'
    ],
    [
        'titulo' => 'Reparo Notebook Dell',
        'descricao' => 'Troca de tela e teclado, limpeza completa',
        'imagem' => 'images/portfolio/notebook1.jpg',
        'categoria' => 'notebook',
        'depoimento' => 'Notebook voltou a funcionar como novo!'
    ],
    [
        'titulo' => 'Instalação de Rede',
        'descricao' => 'Configuração de rede empresarial com 12 pontos',
        'imagem' => 'images/portfolio/rede1.jpg',
        'categoria' => 'rede',
        'depoimento' => 'Serviço profissional e bem executado'
    ]
];

try {
    $stmt = $db->prepare("
        INSERT INTO portfolio (titulo, descricao, imagem, categoria, depoimento)
        VALUES (:titulo, :descricao, :imagem, :categoria, :depoimento)
    ");

    foreach ($portfolioItems as $item) {
        $stmt->execute($item);
    }
    echo "Dados inseridos com sucesso!";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
