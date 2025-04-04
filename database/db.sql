-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS techhelp;
USE techhelp;

-- Tabela de Clientes
CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefone VARCHAR(20) NOT NULL,
    endereco TEXT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Serviços
CREATE TABLE servicos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2),
    categoria ENUM('notebook', 'computador', 'rede') NOT NULL
);

-- Tabela de Orçamentos
CREATE TABLE orcamentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT,
    servico_solicitado TEXT NOT NULL,
    descricao_problema TEXT,
    status ENUM('pendente', 'aprovado', 'rejeitado', 'concluido') DEFAULT 'pendente',
    data_solicitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_conclusao TIMESTAMP NULL,
    valor_orcamento DECIMAL(10,2),
    prioridade ENUM('baixa', 'media', 'alta') DEFAULT 'media',
    tempo_estimado VARCHAR(50),
    forma_pagamento VARCHAR(50),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id)
);

-- Tabela de Portfólio
CREATE TABLE portfolio (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT,
    imagem VARCHAR(255),
    categoria VARCHAR(50),
    data_publicacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Avaliações
CREATE TABLE avaliacoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT,
    id_orcamento INT,
    nota INT NOT NULL,
    comentario TEXT,
    data_avaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id),
    FOREIGN KEY (id_orcamento) REFERENCES orcamentos(id)
);

-- Adicionar campo de avaliação no portfólio
ALTER TABLE portfolio ADD COLUMN depoimento TEXT;

-- Tabela de Pontos/Fidelidade
CREATE TABLE pontos_fidelidade (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT,
    pontos INT DEFAULT 0,
    nivel VARCHAR(20) DEFAULT 'Bronze',
    FOREIGN KEY (id_cliente) REFERENCES clientes(id)
);

-- Tabela de Histórico de Pontos
CREATE TABLE historico_pontos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT,
    pontos INT,
    tipo ENUM('ganho', 'resgate') NOT NULL,
    descricao TEXT,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id)
);

-- Tabela de Status em Tempo Real
CREATE TABLE status_tracking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_orcamento INT,
    status VARCHAR(50),
    descricao TEXT,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_orcamento) REFERENCES orcamentos(id)
);
