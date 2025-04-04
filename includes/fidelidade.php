<?php
function calcularPontos($valor_orcamento) {
    return floor($valor_orcamento * 0.5); // 1 ponto a cada R$ 2
}

function atualizarNivel($pontos) {
    if ($pontos >= 1000) return 'Diamante';
    if ($pontos >= 500) return 'Ouro';
    if ($pontos >= 200) return 'Prata';
    return 'Bronze';
}
