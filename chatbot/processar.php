<?php
header('Content-Type: application/json');

$respostas = [
    'servicos' => 'Oferecemos serviços de: 
        - Formatação (R$80,00)
        - Limpeza de Notebooks (R$70,00)
        - Limpeza de PCs (R$60,00)
        - Montagem de PCs
        - Instalação de Redes
        Digite o serviço específico para mais detalhes.',
        
    'horario' => 'Nosso horário de atendimento é:
        - Segunda a Sexta: 8h às 18h
        - Sábados: 8h às 12h',
        
    'pagamento' => 'Aceitamos as seguintes formas de pagamento:
        - PIX
        - Dinheiro
        - Cartão de débito
        - Cartão de crédito (até 3x sem juros)',
        
    'garantia' => 'Oferecemos garantia de 90 dias em todos os nossos serviços. 
        A garantia cobre:
        - Defeitos relacionados ao serviço realizado
        - Peças instaladas por nós
        - Mão de obra em caso de necessidade de reparo
        
        Em caso de problemas dentro do período de garantia, entre em contato imediatamente.',
    
    'outro' => 'Para outros assuntos, por favor digite sua dúvida ou entre em contato pelo WhatsApp (71) 99212-4952',
    
    'default' => [
        'Posso ajudar com informações sobre nossos serviços, horários, formas de pagamento ou garantia. Se precisar falar com um atendente humano, é só me avisar!',
        'Como posso ajudar? Tenho informações sobre serviços, horários e pagamentos. Se preferir falar com um atendente, me avise!'
    ],

    'positivo' => [
        'palavras' => ['otimo', 'ótimo', 'bom', 'legal', 'show', 'beleza', 'ok', 'blz', 'perfeito'],
        'respostas' => [
            'Que bom que pude ajudar! Precisa de mais alguma informação?',
            'Fico feliz em ajudar! Posso esclarecer mais alguma dúvida?',
            'Ótimo! Se precisar de mais detalhes, é só perguntar.'
        ]
    ],
    'negativo' => [
        'palavras' => ['ruim', 'péssimo', 'nao', 'não', 'nunca', 'horrível'],
        'respostas' => [
            'Sinto muito! Como posso ajudar melhor?',
            'Peço desculpas. Pode me explicar melhor sua dúvida?'
        ]
    ],
    'saudacao' => [
        'palavras' => ['oi', 'olá', 'ola', 'hey', 'ei'],
        'respostas' => [
            'Olá! Como posso ajudar?',
            'Oi! Em que posso ser útil?'
        ]
    ],

    // Respostas para interações comuns
    'interacao' => [
        'saudacao' => [
            'palavras' => ['oi', 'olá', 'ola', 'hey', 'eae', 'e ai', 'hi', 'hello'],
            'respostas' => [
                'Olá! Em que posso ajudar?',
                'Oi! Como posso te auxiliar hoje?',
                'Olá! Estou aqui para ajudar!'
            ]
        ],
        'despedida' => [
            'palavras' => ['tchau', 'adeus', 'até logo', 'ate logo', 'valeu', 'obrigado', 'obrigada'],
            'respostas' => [
                'Até mais! Se precisar de algo é só voltar!',
                'Obrigado pelo contato! Tenha um ótimo dia!',
                'Até a próxima! Estamos sempre à disposição!'
            ]
        ],
        'agradecimento' => [
            'palavras' => ['obrigado', 'obrigada', 'valeu', 'thanks', 'grato', 'grata'],
            'respostas' => [
                'Fico feliz em ajudar! Precisa de mais alguma coisa?',
                'Por nada! Se tiver mais dúvidas é só perguntar!',
                'Disponha! Posso ajudar com mais alguma informação?'
            ]
        ],
        'confirmacao' => [
            'palavras' => ['sim', 'ok', 'beleza', 'certo', 'entendi', 'blz', 'show', 'legal'],
            'respostas' => [
                'Ótimo! Posso ajudar com mais alguma coisa?',
                'Que bom! Tem mais alguma dúvida?',
                'Perfeito! Precisa de mais informações?'
            ]
        ],
        'negacao' => [
            'palavras' => ['não', 'nao', 'no', 'nunca', 'jamais'],
            'respostas' => [
                'Entendi. Se mudar de ideia estou à disposição!',
                'Ok! Se precisar de algo mais tarde é só chamar!',
                'Certo! Fico à disposição se precisar de ajuda!'
            ]
        ]
    ],

    'atendente' => [
        'palavras' => ['atendente', 'humano', 'pessoa', 'falar com alguem', 'atendimento humano', 'pessoa real'],
        'respostas' => 'Claro! Você pode falar diretamente com um de nossos atendentes através do WhatsApp (71) 99212-4952 ou aguardar alguns instantes que irei transferir seu atendimento. Qual você prefere?'
    ]
];

try {
    $mensagem = strtolower(trim($_POST['mensagem'] ?? ''));
    
    // Verificar pedido de atendente primeiro
    foreach ($respostas['atendente']['palavras'] as $palavra) {
        if (strpos($mensagem, $palavra) !== false) {
            echo json_encode([
                'resposta' => $respostas['atendente']['respostas'],
                'timestamp' => date('H:i'),
                'tipo' => 'atendente'
            ]);
            exit;
        }
    }

    // Verificar palavras específicas primeiro
    if (strpos($mensagem, 'garantia') !== false || 
        strpos($mensagem, 'defeito') !== false || 
        strpos($mensagem, 'prazo') !== false) {
        echo json_encode([
            'resposta' => $respostas['garantia'],
            'timestamp' => date('H:i')
        ]);
        exit;
    }

    // Checar interações comuns primeiro
    foreach ($respostas['interacao'] as $tipo => $dados) {
        foreach ($dados['palavras'] as $palavra) {
            if (strpos($mensagem, $palavra) !== false) {
                $respostasDisponiveis = $dados['respostas'];
                echo json_encode([
                    'resposta' => $respostasDisponiveis[array_rand($respostasDisponiveis)],
                    'timestamp' => date('H:i')
                ]);
                exit;
            }
        }
    }

    // Se não for uma interação comum, processa com as palavras-chave normais
    $keywords = [
        'servicos' => [
            'servico', 'serviço', 'serviços', 'servicos',
            'preço', 'preco', 'precos', 'preços',
            'valor', 'valores',
            'quanto', 'custa', 'custo'
        ],
        'horario' => [
            'horario', 'horário', 'horários', 'horarios',
            'hora', 'horas', 'atendimento', 'atende',
            'funcionamento', 'aberto', 'abre', 'fecha'
        ],
        'pagamento' => [
            'pagamento', 'pagar', 'dinheiro', 'cartao',
            'cartão', 'pix', 'parcela', 'parcelar',
            'parcelas', 'débito', 'credito', 'crédito'
        ],
        'garantia' => [
            'garantia', 'defeito', 'prazo', 'devolucao',
            'devolução', 'troca', 'trocar', 'garante'
        ]
    ];

    // Função para remover acentos
    function removeAcentos($string) {
        return preg_replace(
            array("/(á|à|ã|â|ä)/","/(é|è|ê|ë)/","/(í|ì|î|ï)/","/(ó|ò|õ|ô|ö)/","/(ú|ù|û|ü)/","/(ñ)/"),
            array("a","e","i","o","u","n"),
            $string
        );
    }

    // Remover acentos da mensagem para comparação
    $mensagemSemAcentos = removeAcentos($mensagem);

    // Procurar por palavras-chave na mensagem
    foreach ($keywords as $tipo => $palavras) {
        foreach ($palavras as $palavra) {
            if (strpos($mensagemSemAcentos, removeAcentos($palavra)) !== false) {
                echo json_encode([
                    'resposta' => $respostas[$tipo],
                    'timestamp' => date('H:i')
                ]);
                exit;
            }
        }
    }

    // Se nenhuma palavra-chave foi encontrada
    echo json_encode([
        'resposta' => $respostas['default'],
        'timestamp' => date('H:i')
    ]);

} catch (Exception $e) {
    error_log("Erro no chatbot: " . $e->getMessage());
    echo json_encode([
        'resposta' => "Desculpe, não entendi. Pode reformular sua pergunta?",
        'timestamp' => date('H:i')
    ]);
}
