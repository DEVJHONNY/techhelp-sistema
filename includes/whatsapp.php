<?php
function enviarMensagemWhatsApp($telefone, $mensagem) {
    $telefone = preg_replace('/\D/', '', $telefone);
    $mensagem = urlencode($mensagem);
    return "https://wa.me/55{$telefone}?text={$mensagem}";
}
