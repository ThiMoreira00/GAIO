-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20250816.fc507e4f0d
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 04/12/2025 às 14:43
-- Versão do servidor: 8.4.3
-- Versão do PHP: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `projetogaio`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_alunos`
--

CREATE TABLE `gaio_alunos` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `usuario_id` int NOT NULL COMMENT 'ID do usuário (referenciado)',
  `codigo_carteirinha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Código da carteirinha do aluno'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_alunos_documentos`
--

CREATE TABLE `gaio_alunos_documentos` (
  `id` int NOT NULL COMMENT 'ID do documento (gerado automaticamente)',
  `aluno_id` int NOT NULL COMMENT 'ID do aluno (referenciado)',
  `documento_tipo_id` int NOT NULL COMMENT 'ID do tipo do documento (referenciado)',
  `metadados` json NOT NULL COMMENT 'Informações do documento'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_alunos_documentos_tipos`
--

CREATE TABLE `gaio_alunos_documentos_tipos` (
  `id` int NOT NULL COMMENT 'ID do tipo do documento (gerado automaticamente)',
  `nome` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome do documento',
  `obrigatorio` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Documento obrigatório para o cadastro do aluno?'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `gaio_alunos_documentos_tipos`
--

INSERT INTO `gaio_alunos_documentos_tipos` (`id`, `nome`, `obrigatorio`) VALUES
(1, 'Registro Geral - RG', 1),
(2, 'Cadastro de Pessoa Física - CPF', 1),
(3, 'Certidão de Nascimento', 1),
(4, 'Certidão de Casamento', 1),
(5, 'Carteira de Trabalho', 1),
(6, 'Título de Eleitor', 1),
(7, 'Certificado de Alistamento Militar', 0),
(8, '[arrumar metadados] Certificado de Reservista', 1),
(9, 'RG', 1),
(10, 'CPF', 1),
(11, 'Certidão de Nascimento', 1),
(12, 'Histórico Escolar', 1),
(13, 'Certificado de Conclusão', 1),
(14, 'Comprovante de Residência', 1),
(15, 'Foto 3x4', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_alunos_escolas`
--

CREATE TABLE `gaio_alunos_escolas` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `aluno_id` int NOT NULL COMMENT 'ID do aluno (referenciado)',
  `nome` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome da escola',
  `cidade` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Cidade da escola',
  `uf` enum('AC','AL','AM','AP','BA','CE','DF','ES','GO','MA','MG','MS','MT','PA','PB','PE','PI','PR','RJ','RN','RO','RR','RS','SC','SE','SP','TO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'UF da escola',
  `ano_conclusao` year NOT NULL COMMENT 'Ano de conclusão da escola',
  `nivel` enum('ENSINO_MEDIO','GRADUACAO','SUPLETIVO','OUTRO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nível de ensino do aluno na escola'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_alunos_ingressos_tipos`
--

CREATE TABLE `gaio_alunos_ingressos_tipos` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `nome` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome do tipo de ingresso',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Situação do tipo de ingresso (ativo/inativo)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `gaio_alunos_ingressos_tipos`
--

INSERT INTO `gaio_alunos_ingressos_tipos` (`id`, `nome`, `status`) VALUES
(1, 'Vestibular Sem Reserva', 1),
(2, 'Vestibular Negro / Indígena / Quilombola', 1),
(3, 'Vestibular Rede Pública', 1),
(4, 'Vestibular Deficiente / Filhos de policiais', 1),
(5, 'SISU Sem Reserva', 1),
(6, 'SISU Negro / Indígena / Quilombola', 1),
(7, 'SISU Rede Pública', 1),
(8, 'SISU Deficiente / Filhos de policiais', 1),
(9, 'ENEM Sem Reserva', 1),
(10, 'ENEM Negro / Indígena / Quilombola', 1),
(11, 'ENEM Rede Pública', 1),
(12, 'ENEM Deficiente / Filhos de policiais', 1),
(13, 'Transferência Interna', 1),
(14, 'Transferência Ex-ofício', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_alunos_matriculas`
--

CREATE TABLE `gaio_alunos_matriculas` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `aluno_id` int NOT NULL COMMENT 'ID do aluno (referenciado)',
  `matriz_curricular_id` int NOT NULL COMMENT 'ID da matriz curricular do curso (referenciado)',
  `periodo_ingresso_id` int NOT NULL COMMENT 'ID do período de ingresso (referenciado)',
  `ingresso_tipo_id` int NOT NULL COMMENT 'ID do tipo de ingresso (referenciado)',
  `matricula` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Matrícula do aluno',
  `turno` enum('MANHA','TARDE','NOITE','INTEGRAL') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Turno de ingresso do aluno',
  `ingresso_classificacao` int NOT NULL COMMENT 'Posição de classificação no ingresso',
  `ingresso_pontos` int NOT NULL COMMENT 'Pontuação do ingresso',
  `status` enum('CURSANDO','EVADIDO','TRANCADO','CONCLUIDO','DESISTENTE') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Situação da matrícula do aluno',
  `data_matricula` date NOT NULL COMMENT 'Data da matrícula',
  `data_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data do registro da matrícula'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_alunos_matriculas_historicos`
--

CREATE TABLE `gaio_alunos_matriculas_historicos` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `aluno_matricula_id` int NOT NULL COMMENT 'ID da matrícula do aluno (referenciado)',
  `status` enum('CURSANDO','EVADIDO','TRANCADO','CONCLUIDO','DESISTENTE','DESLIGADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Status do histórico da matrícula do aluno',
  `observacao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT 'Observações sobre a alteração de situação da matrícula',
  `data_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de registro da alteração de histórico'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_alunos_responsaveis`
--

CREATE TABLE `gaio_alunos_responsaveis` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `aluno_id` int NOT NULL COMMENT 'ID do aluno (referenciado)',
  `nome` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome do responsável do aluno',
  `rg` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'RG ou CPF do responsável',
  `tipo` enum('MAE','PAI','RESPONSAVEL_LEGAL') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Tipo de responsável do aluno'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_atividades`
--

CREATE TABLE `gaio_atividades` (
  `id` int NOT NULL COMMENT 'ID da atividade (gerado automaticamente)',
  `avaliacao_turma_id` int NOT NULL COMMENT 'ID da avaliação da turma (referenciado)',
  `codigo` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Código da atividade',
  `titulo` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Título da atividade',
  `descricao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Descrição da atividade',
  `peso` float NOT NULL DEFAULT '1' COMMENT 'Peso da atividade',
  `nota_maxima` float NOT NULL DEFAULT '10' COMMENT 'Nota máxima atribuída pela atividade'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_atividades_notas`
--

CREATE TABLE `gaio_atividades_notas` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `atividade_id` int NOT NULL COMMENT 'ID da atividade (referenciada)',
  `usuario_responsavel_id` int NOT NULL COMMENT 'ID do usuário-responsável (professor/administrador) (referenciado)',
  `aluno_matricula_id` int NOT NULL COMMENT 'ID da matrícula do aluno (referenciada)',
  `nota` float UNSIGNED NOT NULL COMMENT 'Nota da atividade',
  `data_lancamento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de lançamento da nota da atividade'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_avaliacoes_notas`
--

CREATE TABLE `gaio_avaliacoes_notas` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `avaliacao_turma_id` int NOT NULL COMMENT 'ID da avaliação da turma (referenciado)',
  `aluno_matricula_id` int NOT NULL COMMENT 'ID da matrícula do aluno (referenciado)',
  `usuario_responsavel_id` int NOT NULL COMMENT 'ID do usuário-responsável (professor/administrador) (referenciado)',
  `nota` float UNSIGNED NOT NULL COMMENT 'Nota da avaliação',
  `origem` enum('ATIVIDADE','MANUAL') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'MANUAL' COMMENT 'Origem da nota de avaliação',
  `observacao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT 'Observações adicionais sobre a nota da avaliação',
  `data_lancamento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de lançamento da nota da avaliação'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_avaliacoes_tipos`
--

CREATE TABLE `gaio_avaliacoes_tipos` (
  `id` int NOT NULL COMMENT 'ID do tipo de avaliação (gerado automaticamente)',
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome do tipo de avaliação',
  `descricao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Descrição do tipo de avaliação',
  `categoria` enum('LANCADO','CALCULADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Categoria do tipo de avaliação',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Status do tipo de avaliação'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_componentes_curriculares`
--

CREATE TABLE `gaio_componentes_curriculares` (
  `id` int NOT NULL COMMENT 'ID do componente curricular (gerado automaticamente)',
  `matriz_curricular_id` int NOT NULL COMMENT 'ID da matriz curricular (referenciada)',
  `sigla` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Sigla do componente curricular',
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome do componente curricular',
  `creditos` int UNSIGNED NOT NULL COMMENT 'Créditos do componente curricular',
  `carga_horaria` int UNSIGNED NOT NULL COMMENT 'Carga horária do componente curricular',
  `periodo` int UNSIGNED DEFAULT NULL COMMENT 'Período em que o componente curricular está posicionado',
  `tipo` enum('OPTATIVA','OBRIGATORIA','ELETIVA') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'OBRIGATORIA' COMMENT 'Tipo do componente curricular'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_componentes_equivalencias`
--

CREATE TABLE `gaio_componentes_equivalencias` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `componente_curricular_id` int NOT NULL COMMENT 'ID do componente curricular (referenciado)',
  `componente_equivalente_id` int NOT NULL COMMENT 'ID do componente equivalente (referenciado)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_componentes_prerequisitos`
--

CREATE TABLE `gaio_componentes_prerequisitos` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `componente_curricular_id` int NOT NULL COMMENT 'ID do componente curricular (referenciado)',
  `componente_requisito_id` int NOT NULL COMMENT 'ID do componente do pré-requisito (referenciado)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_cursos`
--

CREATE TABLE `gaio_cursos` (
  `id` int NOT NULL COMMENT 'ID do curso (gerado automaticamente)',
  `grau_id` int NOT NULL COMMENT 'Grau do curso (referenciado)',
  `emec_codigo` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Código do curso (e-MEC)',
  `nome` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome do curso',
  `sigla` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Sigla do curso',
  `duracao_minima` int UNSIGNED NOT NULL COMMENT 'Duração mínima de semestres do curso',
  `duracao_maxima` int UNSIGNED NOT NULL COMMENT 'Duração máxima de semestres do curso',
  `parecer_reconhecimento` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Parecer de reconhecimento do curso',
  `status` enum('ATIVO','INATIVO','ARQUIVADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'ATIVO' COMMENT 'Status do curso'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_cursos_graus`
--

CREATE TABLE `gaio_cursos_graus` (
  `id` int NOT NULL COMMENT 'ID do grau de curso (gerado automaticamente)',
  `nome` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome do grau do curso',
  `titulo` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Título dado ao grau do curso',
  `nivel` enum('GRADUACAO','POS_GRADUACAO','EXTENSAO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nível do grau do curso'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `gaio_cursos_graus`
--

INSERT INTO `gaio_cursos_graus` (`id`, `nome`, `titulo`, `nivel`) VALUES
(1, 'Tecnologia', 'Tecnólogo', 'GRADUACAO'),
(2, 'Bacharelado', 'Bacharel', 'GRADUACAO'),
(3, 'Licenciatura', 'Licenciado', 'GRADUACAO'),
(4, 'Especialização', 'Especialista', 'POS_GRADUACAO'),
(5, 'Pós-Graduação', 'Especialista', 'POS_GRADUACAO'),
(6, 'Mestrado', 'Mestre', 'POS_GRADUACAO'),
(7, 'Doutorado', 'Doutor', 'POS_GRADUACAO');

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_cursos_periodos_letivos`
--

CREATE TABLE `gaio_cursos_periodos_letivos` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `curso_id` int NOT NULL COMMENT 'ID do curso (referenciado)',
  `periodo_letivo_id` int NOT NULL COMMENT 'ID do período letivo (referenciado)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_disciplinas`
--

CREATE TABLE `gaio_disciplinas` (
  `id` int NOT NULL COMMENT 'ID da disciplina (gerado automaticamente)',
  `componente_curricular_id` int NOT NULL COMMENT 'ID do componente curricular (referenciado)',
  `ementa` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT 'Ementa da disciplina',
  `bibliografia` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT 'Bibliografia da disciplina'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_espacos`
--

CREATE TABLE `gaio_espacos` (
  `id` int NOT NULL COMMENT 'ID do espaço (gerado automaticamente)',
  `nome` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome do espaço',
  `codigo` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Código do espaço',
  `capacidade_maxima` int UNSIGNED NOT NULL COMMENT 'Quantidade máxima de pessoas no espaço',
  `tipo` enum('SALA_AULA','LABORATORIO','AUDITORIO','BIBLIOTECA','OUTRO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Tipo do espaço',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Status do espaço'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_eventos`
--

CREATE TABLE `gaio_eventos` (
  `id` int NOT NULL COMMENT 'ID do evento (gerado automaticamente)',
  `tipo_id` int NOT NULL COMMENT 'ID do tipo do evento (referenciado)',
  `nome` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome do evento',
  `descricao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Descrição do evento',
  `data_inicio` datetime NOT NULL COMMENT 'Data de início do evento',
  `data_termino` datetime DEFAULT NULL COMMENT 'Data de término do evento'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_eventos_tipos`
--

CREATE TABLE `gaio_eventos_tipos` (
  `id` int NOT NULL COMMENT 'ID do tipo de evento (gerado automaticamente)',
  `codigo` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Código do tipo do evento',
  `nome` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome do tipo do evento',
  `padrao` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Tipo do evento padrão do sistema?'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_grades_horarias`
--

CREATE TABLE `gaio_grades_horarias` (
  `id` int NOT NULL COMMENT 'ID da grade horária (gerado automaticamente)',
  `nome` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome da grade horária'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `gaio_grades_horarias`
--

INSERT INTO `gaio_grades_horarias` (`id`, `nome`) VALUES
(1, 'A');

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_grupos`
--

CREATE TABLE `gaio_grupos` (
  `id` int NOT NULL COMMENT 'ID do grupo (gerado automaticamente)',
  `nome` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome do grupo',
  `descricao` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Descrição do grupo',
  `padrao` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Grupo padrão do sistema?'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `gaio_grupos`
--

INSERT INTO `gaio_grupos` (`id`, `nome`, `descricao`, `padrao`) VALUES
(1, 'Administrador', 'Acesso total ao sistema', 1),
(2, 'Professor', 'Acesso às turmas e avaliações', 1),
(3, 'Aluno', 'Acesso básico do estudante', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_grupos_permissoes`
--

CREATE TABLE `gaio_grupos_permissoes` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `grupo_id` int NOT NULL COMMENT 'ID do grupo (referenciado)',
  `permissao_id` int NOT NULL COMMENT 'ID da permissão (referenciado)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_inscricoes`
--

CREATE TABLE `gaio_inscricoes` (
  `id` int NOT NULL COMMENT 'ID da inscrição (gerado automaticamente)',
  `aluno_matricula_id` int NOT NULL COMMENT 'ID da matrícula do aluno (referenciado)',
  `turma_id` int NOT NULL COMMENT 'ID da turma (referenciado)',
  `observacao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT 'Observações adicionais sobre a inscrição',
  `status` enum('SOLICITADA','DEFERIDA','INDEFERIDA','ISENTO','CURSANDO','APROVADO','REPROVADO_FALTA','REPROVADO_MEDIA','EXCLUIDO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'SOLICITADA' COMMENT 'Situação da inscrição',
  `data_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data do registro da inscrição'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_logins_tentativas`
--

CREATE TABLE `gaio_logins_tentativas` (
  `id` int NOT NULL COMMENT 'ID da tentativa (gerado automaticamente)',
  `login_id` int DEFAULT NULL COMMENT 'ID do login (referenciado)',
  `identificador` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Identificador do usuário',
  `tentativas` int DEFAULT '0' COMMENT 'Quantidade de tentativas do usuário',
  `data_bloqueio` datetime DEFAULT NULL COMMENT 'Data/hora do tempo-limite do bloqueio da conta',
  `data_criado` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Data/hora da criação do registro',
  `data_atualizado` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data/hora da atualização do registro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_logs`
--

CREATE TABLE `gaio_logs` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `usuario_id` int NOT NULL COMMENT 'ID do usuário (referenciado)',
  `tipo` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Tipo do registro',
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Descrição do registro',
  `data_registro` datetime NOT NULL COMMENT 'Data/hora do registro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_matrizes_curriculares`
--

CREATE TABLE `gaio_matrizes_curriculares` (
  `id` int NOT NULL COMMENT 'ID da matriz curricular (gerado automaticamente)',
  `curso_id` int NOT NULL COMMENT 'ID do curso (referenciado)',
  `quantidade_periodos` int UNSIGNED NOT NULL COMMENT 'Quantidade de períodos da matriz',
  `data_vigencia` date NOT NULL COMMENT 'Data da vigência',
  `status` enum('VIGENTE','ARQUIVADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'VIGENTE' COMMENT 'Situação da matriz'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `gaio_matrizes_curriculares`
--

INSERT INTO `gaio_matrizes_curriculares` (`id`, `curso_id`, `quantidade_periodos`, `data_vigencia`, `status`) VALUES
(31, 1, 5, '2018-01-01', 'VIGENTE');

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_notificacoes`
--

CREATE TABLE `gaio_notificacoes` (
  `id` int NOT NULL COMMENT 'ID da notificação (gerado automaticamente)',
  `modelo_id` int NOT NULL COMMENT 'ID do modelo da notificação (referenciado)',
  `autor_id` int DEFAULT NULL COMMENT 'ID do autor da notificação (referenciado).\\nNULL = Sistema',
  `titulo` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Título da notificação',
  `mensagem` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Mensagem da notificação',
  `data_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data do registro da notificação'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_notificacoes_destinos`
--

CREATE TABLE `gaio_notificacoes_destinos` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `notificacao_id` int NOT NULL COMMENT 'ID da notificação (referenciado)',
  `destinatario_id` int NOT NULL COMMENT 'ID do destinatário (polimorfismo)',
  `destinatario_tipo` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Tipo do destinatário (polimorfismo)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_notificacoes_leituras`
--

CREATE TABLE `gaio_notificacoes_leituras` (
  `id` int NOT NULL COMMENT 'ID do registro de leitura (gerado automaticamente)',
  `notificacao_id` int NOT NULL COMMENT 'ID da notificação (referenciado)',
  `usuario_id` int NOT NULL COMMENT 'ID do usuário (referenciado)',
  `data_leitura` datetime NOT NULL COMMENT 'Data de leitura da notificação'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_notificacoes_modelos`
--

CREATE TABLE `gaio_notificacoes_modelos` (
  `id` int NOT NULL COMMENT 'ID do modelo de notificação (gerado automaticamente)',
  `codigo` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Código do modelo de notificações',
  `titulo` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Título do modelo da notificação',
  `mensagem` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Mensagem do modelo da notificação',
  `icone` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Código do ícone do modelo da notificação',
  `cor` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Cor do modelo da notificação'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `gaio_notificacoes_modelos`
--

INSERT INTO `gaio_notificacoes_modelos` (`id`, `codigo`, `titulo`, `mensagem`, `icone`, `cor`) VALUES
(1, 'LOGIN_NOVO_DETECTADO', 'Novo login detectado', 'Foi detectado um novo login na sua conta usando **{navegador}** em **{sistema_operacional}**. Se não foi você, altere sua senha imediatamente.', 'login', 'gray-800');

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_periodos_letivos`
--

CREATE TABLE `gaio_periodos_letivos` (
  `id` int NOT NULL COMMENT 'ID do período letivo (gerado automaticamente)',
  `sigla` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Sigla do período letivo (XXXX.X)',
  `data_inicio` date NOT NULL COMMENT 'Data de início do período letivo',
  `data_termino` date NOT NULL COMMENT 'Data de término do período letivo',
  `status` enum('ATIVO','INATIVO','ARQUIVADO','PROGRAMADO','CONCLUIDO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'PROGRAMADO' COMMENT 'Situação do período letivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_periodos_letivos_eventos`
--

CREATE TABLE `gaio_periodos_letivos_eventos` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `periodo_letivo_id` int NOT NULL COMMENT 'ID do período letivo (referenciado)',
  `evento_id` int NOT NULL COMMENT 'ID do evento (referenciado)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_permissoes`
--

CREATE TABLE `gaio_permissoes` (
  `id` int NOT NULL COMMENT 'ID da permissão (gerado automaticamente)',
  `codigo` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Código da permissão',
  `categoria` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Categoria da permissão',
  `nome` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome da permissão',
  `descricao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Descrição da permissão',
  `padrao` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Permissão padrão do sistema?'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `gaio_permissoes`
--

INSERT INTO `gaio_permissoes` (`id`, `codigo`, `categoria`, `nome`, `descricao`, `padrao`) VALUES
(1, 'GAIO_CURSO_ARQUIVAR', 'Curso', 'Arquivar curso', 'Permite que os membros arquivem os cursos.', 1),
(2, 'GAIO_CURSO_CADASTRAR', 'Curso', 'Cadastrar novo curso', 'Permite que os membros cadastrem um novo curso.', 1),
(3, 'GAIO_CURSO_EDITAR', 'Curso', 'Editar curso', 'Permite que os membros editem os cursos.', 1),
(4, 'GAIO_CURSO_GERENCIAR', 'Curso', 'Gerenciar cursos', 'Permite que os membros gerenciem os cursos.', 1),
(5, 'GAIO_CURSO_VISUALIZAR', 'Curso', 'Visualizar curso', 'Permite que os membros visualizem os cursos.', 1),
(6, 'GAIO_DISCENTE_CADASTRAR', 'Discente', 'Cadastrar discente', 'Permite que os membros cadastrem um novo discente.', 1),
(7, 'GAIO_DISCENTE_EDITAR', 'Discente', 'Editar discente', 'Permite que os membros editem os dados dos discentes.', 1),
(8, 'GAIO_DISCENTE_GERENCIAR', 'Discente', 'Gerenciar discentes', 'Permite que os membros gerenciem os discentes.', 1),
(9, 'GAIO_DISCENTE_IMPORTAR', 'Discente', 'Importar discentes', 'Permite que os membros importem registros de discentes.', 1),
(10, 'GAIO_DISCENTE_INATIVAR', 'Discente', 'Inativar discente', 'Permite que os membros inativem os discentes.', 1),
(11, 'GAIO_DISCENTE_MATRICULA_CONCLUSAO_REGISTRAR', 'Discente', 'Registrar conclusão de curso [a] matrícula do discente', 'Permite que os membros registrem a conclusão de curso na matrícula do discente.', 1),
(12, 'GAIO_DISCENTE_MATRICULA_CURSO_VINCULAR', 'Discente', 'Vincular discente a um curso', 'Permite que os membros vinculem um discente a um curso.', 1),
(13, 'GAIO_DISCENTE_MATRICULA_DESISTENCIA_REGISTRAR', 'Discente', 'Registrar desistência [a] matrícula do discente', 'Permite que os membros registrem a desistência na matrícula do discente.', 1),
(14, 'GAIO_DISCENTE_MATRICULA_DESLIGAMENTO_REGISTRAR', 'Discente', 'Registrar desligamento [a] matrícula do discente', 'Permite que os membros registrem o desligamento na matrícula do discente.', 1),
(15, 'GAIO_DISCENTE_MATRICULA_DESTRANCAR', 'Discente', 'Destrancar matrícula do discente', 'Permite que os membros destranquem a matrícula de um discente.', 1),
(16, 'GAIO_DISCENTE_MATRICULA_GERENCIAR', 'Discente', 'Gerenciar matrículas do discente', 'Permite que os membros gerenciem as matrículas dos discentes.', 1),
(17, 'GAIO_DISCENTE_MATRICULA_REATIVAR', 'Discente', 'Reativar matrícula do discente', 'Permite que os membros reativem a matrícula de um discente.', 1),
(18, 'GAIO_DISCENTE_MATRICULA_RENOVAR', 'Discente', 'Renovar matrícula do discente', 'Permite que os membros renovem a matrícula de um discente.', 1),
(19, 'GAIO_DISCENTE_MATRICULA_TRANCAR', 'Discente', 'Trancar matrícula do discente', 'Permite que os membros tranquem a matrícula de um discente.', 1),
(20, 'GAIO_DISCENTE_REATIVAR', 'Discente', 'Reativar discente', 'Permite que os membros reativem os discentes.', 1),
(21, 'GAIO_DISCENTE_VISUALIZAR', 'Discente', 'Visualizar discente', 'Permite que os membros visualizem os dados dos discentes.', 1),
(22, 'GAIO_DISCIPLINAS_FREQUENCIAS_VISUALIZAR', 'Disciplina', 'Visualizar frequência da disciplina', 'Permite que os membros visualizem a frequência da disciplina.', 1),
(23, 'GAIO_DOCENTE_CADASTRAR', 'Docente', 'Cadastrar novo docente', 'Permite que os membros cadastrem um novo docente.', 1),
(24, 'GAIO_DOCENTE_EDITAR', 'Docente', 'Editar docente', 'Permite que os membros editem os dados dos docentes.', 1),
(25, 'GAIO_DOCENTE_GERENCIAR', 'Docente', 'Gerenciar docentes', 'Permite que os membros gerenciem os docentes.', 1),
(26, 'GAIO_DOCENTE_INATIVAR', 'Docente', 'Inativar docente', 'Permite que os membros inativem os docentes.', 1),
(27, 'GAIO_DOCENTE_MATRICULAS_GERENCIAR', 'Docente', 'Gerenciar matrículas do docente', 'Permite que os membros gerenciem as matrículas dos docentes.', 1),
(28, 'GAIO_DOCENTE_VISUALIZAR', 'Docente', 'Visualizar docente', 'Permite que os membros visualizem os dados dos docentes.', 1),
(29, 'GAIO_ESPACO_CADASTRAR', 'Espaço', 'Cadastrar novo espaço', 'Permite que os membros cadastrem um novo espaço.', 1),
(30, 'GAIO_ESPACO_EDITAR', 'Espaço', 'Editar espaço', 'Permite que os membros editem os espaços.', 1),
(31, 'GAIO_ESPACO_EXCLUIR', 'Espaço', 'Excluir espaço', 'Permite que os membros excluam os espaços.', 1),
(32, 'GAIO_ESPACO_GERENCIAR', 'Espaço', 'Gerenciar espaços', 'Permite que os membros gerenciem os espaços.', 1),
(33, 'GAIO_ESPACO_VISUALIZAR', 'Espaço', 'Visualizar espaço', 'Permite que os membros visualizem os espaços.', 1),
(34, 'GAIO_EVENTO_CADASTRAR', 'Evento', 'Cadastrar novo evento', 'Permite que os membros cadastrem novos eventos.', 1),
(35, 'GAIO_EVENTO_EDITAR', 'Evento', 'Editar evento', 'Permite que os membros editem os eventos.', 1),
(36, 'GAIO_EVENTO_EXCLUIR', 'Evento', 'Excluir evento', 'Permite que os membros excluam os eventos.', 1),
(37, 'GAIO_EVENTO_GERENCIAR', 'Evento', 'Gerenciar eventos', 'Permite que os membros gerenciem os eventos.', 1),
(38, 'GAIO_EVENTO_PERIODO_LETIVO_ATRIBUIR', 'Evento', 'Atribuir evento a um período letivo', 'Permite que os membros atribuam um evento a um período letivo.', 1),
(39, 'GAIO_EVENTO_VISUALIZAR', 'Evento', 'Visualizar evento', 'Permite que os membros visualizem os eventos.', 1),
(40, 'GAIO_GRUPO_CADASTRAR', 'Grupo', 'Cadastrar novo grupo de permissões', 'Permite que os membros cadastrem novos grupos de permissões.', 1),
(41, 'GAIO_GRUPO_EDITAR', 'Grupo', 'Editar grupo de permissões', 'Permite que os membros editem os grupos de permissões.', 1),
(42, 'GAIO_GRUPO_EXCLUIR', 'Grupo', 'Excluir grupo de permissões', 'Permite que os membros excluam um grupo de permissões.', 1),
(43, 'GAIO_GRUPO_GERENCIAR', 'Grupo', 'Gerenciar grupos de permissões', 'Permite que os membros gerenciem os grupos de permissões.', 1),
(44, 'GAIO_GRUPO_MEMBROS_ADICIONAR', 'Grupo', 'Adicionar membro a um grupo de permissões', 'Permite que os membros adicionem um membro a um grupo de permissões.', 1),
(45, 'GAIO_GRUPO_MEMBROS_REMOVER', 'Grupo', 'Remover membro a um grupo de permissões', 'Permite que os membros removam um membro de um grupo de permissões.', 1),
(46, 'GAIO_GRUPO_PERMISSOES_ADICIONAR', 'Grupo', 'Adicionar permissões a um grupo', 'Permite que os membros adicionem permissões a um grupo de permissões.', 1),
(47, 'GAIO_GRUPO_PERMISSOES_REMOVER', 'Grupo', 'Remover permissões de um grupo', 'Permite que os membros removam permissões de um grupo existente.', 1),
(48, 'GAIO_GRUPO_VISUALIZAR', 'Grupo', 'Visualizar grupo de permissões', 'Permite que os membros visualizem os grupos de permissões.', 1),
(49, 'GAIO_INSCRICOES_ALUNO_AUTOMATICAMENTE_DEFERIR', 'Inscrição', 'Deferir automaticamente as inscrições de um aluno', 'Permite que os membros defiram automaticamente as inscrições de um aluno.', 1),
(50, 'GAIO_INSCRICOES_ALUNO_AUTOMATICAMENTE_INDEFERIR', 'Inscrição', 'Indeferir automaticamente as inscrições de um aluno', 'Permite que os membros indefiram automaticamente as inscrições de um aluno.', 1),
(51, 'GAIO_INSCRICOES_REALOCAR', 'Inscrição', 'Realocar solicitação de inscrição', 'Permite que os membros realoquem uma solicitação de inscrição.', 1),
(54, 'GAIO_INSCRICOES_SOLICITACAO_DEFERIR', 'Inscrição', 'Deferir solicitação de inscrição', 'Permite que os membros defiram uma solicitação de inscrição.', 1),
(55, 'GAIO_INSCRICOES_SOLICITACAO_INDEFERIR', 'Inscrição', 'Indeferir solicitação de inscrição', 'Permite que os membros indefiram uma solicitação de inscrição.', 1),
(56, 'GAIO_INSCRICOES_SOLICITACAO_PARCIAL_VISUALIZAR', 'Inscrição', 'Visualizar parcial de solicitações de inscrição', 'Permite que os membros visualizem a parcial de solicitações de inscrição.', 1),
(57, 'GAIO_INSCRICOES_SOLICITACAO_RESULTADO_VISUALIZAR', 'Inscrição', 'Visualizar resultado de solicitações de inscrição', 'Permite que os membros visualizem o resultado de solicitações de inscrição.', 1),
(58, 'GAIO_INSCRICOES_SOLICITACAO_VISUALIZAR', 'Inscrição', 'Visualizar solicitação de inscrição', 'Permite que os membros visualizem uma solicitação de inscrição.', 1),
(59, 'GAIO_INSCRICOES_SOLICITAR', 'Inscrição', 'Solicitar inscrição em uma turma', 'Permite que os membros solicitem inscrição em uma turma.', 1),
(60, 'GAIO_INSCRICOES_TURMA_AUTOMATICAMENTE_DEFERIR', 'Inscrição', 'Deferir automaticamente as inscrições de alunos em uma turma', 'Permite que os membros defiram automaticamente as inscrições de alunos em uma turma.', 1),
(61, 'GAIO_INSCRICOES_TURMA_AUTOMATICAMENTE_INDEFERIR', 'Inscrição', 'Indeferir automaticamente as inscrições de alunos em uma turma', 'Permite que os membros indefiram automaticamente as inscrições de alunos em uma turma.', 1),
(62, 'GAIO_LOGS_VISUALIZAR', 'Logs', 'Visualizar registro de auditoria', 'Permite que os membros visualizem os registros de auditoria.', 1),
(63, 'GAIO_MATRIZ_CURRICULAR_AVALIACOES_DEFINIR', 'Matriz Curricular', 'Definir tipos de avaliações na matriz curricular', 'Permite que os membros definam os tipos de avaliações na matriz curricular.', 1),
(64, 'GAIO_MATRIZ_CURRICULAR_CADASTRAR', 'Matriz Curricular', 'Cadastrar nova matriz curricular', 'Permite que os membros cadastrem uma nova matriz curricular.', 1),
(65, 'GAIO_MATRIZ_CURRICULAR_COMPONENTE_CADASTRAR', 'Matriz Curricular', 'Cadastrar novo componente curricular', 'Permite que os membros cadastrem um novo componente curricular.', 1),
(66, 'GAIO_MATRIZ_CURRICULAR_COMPONENTE_EDITAR', 'Matriz Curricular', 'Editar componente curricular', 'Permite que os membros editem um componente curricular.', 1),
(67, 'GAIO_MATRIZ_CURRICULAR_COMPONENTE_EQUIVALENCIAS_DEFINIR', 'Matriz Curricular', 'Definir equivalências do componente curricular', 'Permite que os membros definam equivalências do componente curricular.', 1),
(68, 'GAIO_MATRIZ_CURRICULAR_COMPONENTE_EXCLUIR', 'Matriz Curricular', 'Excluir componente curricular', 'Permite que os membros excluam um componente curricular.', 1),
(69, 'GAIO_MATRIZ_CURRICULAR_COMPONENTE_PREREQUISITO_DEFINIR', 'Matriz Curricular', 'Definir pré-requisitos do componente curricular', 'Permite que os membros definam pré-requisitos do componente curricular.', 1),
(70, 'GAIO_MATRIZ_CURRICULAR_EDITAR', 'Matriz Curricular', 'Editar matriz curricular', 'Permite que os membros editem as matrizes curriculares.', 1),
(71, 'GAIO_MATRIZ_CURRICULAR_GERENCIAR', 'Matriz Curricular', 'Gerenciar matrizes curriculares', 'Permite que os membros gerenciem as matrizes curriculares.', 1),
(72, 'GAIO_MATRIZ_CURRICULAR_INATIVAR', 'Matriz Curricular', 'Inativar matriz curricular', 'Permite que os membros inativem uma matriz curricular.', 1),
(73, 'GAIO_MATRIZ_CURRICULAR_VALIDAR', 'Matriz Curricular', 'Validar matriz curricular', 'Permite que os membros validem uma matriz curricular.', 1),
(74, 'GAIO_MATRIZ_CURRICULAR_VISUALIZAR', 'Matriz Curricular', 'Visualizar matriz curricular', 'Permite que os membros visualizem as matrizes curriculares.', 1),
(75, 'GAIO_PERIODO_LETIVO_ARQUIVAR', 'Período Letivo', 'Arquivar período letivo', 'Permite que os membros arquivem os períodos letivos.', 1),
(76, 'GAIO_PERIODO_LETIVO_CADASTRAR', 'Período Letivo', 'Cadastrar novo período letivo', 'Permite que os membros cadastrem um novo período letivo.', 1),
(77, 'GAIO_PERIODO_LETIVO_CURSO_ATRIBUIR', 'Período Letivo', 'Atribuir período letivo a um curso', 'Permite que os membros atribuam um período letivo a um curso.', 1),
(78, 'GAIO_PERIODO_LETIVO_EDITAR', 'Período Letivo', 'Editar período letivo', 'Permite que os membros editem os períodos letivos.', 1),
(79, 'GAIO_PERIODO_LETIVO_GERENCIAR', 'Período Letivo', 'Gerenciar períodos letivos', 'Permite que os membros gerenciem os períodos letivos.', 1),
(80, 'GAIO_PERIODO_LETIVO_PROGRAMAR', 'Período Letivo', 'Programar período letivo', 'Permite que os membros programem os períodos letivos.', 1),
(81, 'GAIO_PERIODO_LETIVO_VISUALIZAR', 'Período Letivo', 'Visualizar período letivo', 'Permite que os membros visualizem os períodos letivos.', 1),
(82, 'GAIO_TURMAS_ALUNOS_ADICIONAR', 'Turmas', 'Adicionar alunos a turma', 'Permite que os membros adicionem alunos a uma turma.', 1),
(83, 'GAIO_TURMAS_ALUNOS_REMOVER', 'Turmas', 'Remover alunos a turma', 'Permite que os membros removam alunos de uma turma.', 1),
(84, 'GAIO_TURMAS_ARQUIVAR', 'Turmas', 'Arquivar turma', 'Permite que os membros arquivem uma turma.', 1),
(85, 'GAIO_TURMAS_AVALIACAO_CRITERIOS_GERENCIAR', 'Turmas', 'Gerenciar critérios de avaliação da turma', 'Permite que os membros gerenciem os critérios de avaliação da turma.', 1),
(86, 'GAIO_TURMAS_AVALIACAO_NOTAS_ALTERAR', 'Turmas', 'Alterar notas de avaliação da turma', 'Permite que os membros alterem notas de avaliação da turma.', 1),
(87, 'GAIO_TURMAS_AVALIACAO_NOTAS_LANCAR', 'Turmas', 'Lançar notas de avaliação da turma', 'Permite que os membros lancem notas de avaliação da turma.', 1),
(88, 'GAIO_TURMAS_AVALIACAO_NOTAS_PROPRIAS_VISUALIZAR', 'Turmas', 'Visualizar notas pessoais das avaliações da turma', 'Permite que os membros visualizem suas próprias notas nas avaliações.', 1),
(89, 'GAIO_TURMAS_CADASTRAR', 'Turmas', 'Cadastrar nova turma', 'Permite que os membros cadastrem uma nova turma.', 1),
(90, 'GAIO_TURMAS_CONFIRMAR', 'Turmas', '\"Confirmar\" turma planejada', 'Permite que os membros confirmem uma turma planejada.', 1),
(91, 'GAIO_TURMAS_CONTEUDO_VISUALIZAR', 'Turmas', 'Visualizar conteúdos lecionados da turma', 'Permite que os membros visualizem os conteúdos lecionados da turma.', 1),
(92, 'GAIO_TURMAS_EDITAR', 'Turmas', 'Editar turma', 'Permite que os membros editem as turmas.', 1),
(93, 'GAIO_TURMAS_FINALIZAR', 'Turmas', 'Finalizar turma', 'Permite que os membros finalizem uma turma.', 1),
(94, 'GAIO_TURMAS_FREQUENCIAS_ALUNO_VISUALIZAR', 'Turmas', 'Visualizar frequência de um aluno na turma', 'Permite que os membros visualizem a frequência de um aluno na turma.', 1),
(95, 'GAIO_TURMAS_FREQUENCIAS_AUSENTAR', 'Turmas', 'Ausentar dia letivo na turma', 'Permite que os membros registrem ausência em dia letivo na turma.', 1),
(96, 'GAIO_TURMAS_FREQUENCIAS_CONFIGURAR', 'Turmas', 'Configurar frequência para a disciplina', 'Permite que os membros configurem a frequência para a disciplina.', 1),
(97, 'GAIO_TURMAS_FREQUENCIAS_PROPRIAS_VISUALIZAR', 'Turmas', 'Visualizar frequências próprias da turma', 'Permite que os membros visualizem suas próprias frequências na turma.', 1),
(98, 'GAIO_TURMAS_FREQUENCIAS_REATIVAR', 'Turmas', 'Reativar dia letivo na turma', 'Permite que os membros reativem um dia letivo na turma.', 1),
(99, 'GAIO_TURMAS_FREQUENCIAS_VISUALIZAR', 'Turmas', 'Visualizar frequência da turma', 'Permite que os membros visualizem a frequência da turma.', 1),
(100, 'GAIO_TURMAS_GERENCIAR', 'Turmas', 'Gerenciar turmas', 'Permite que os membros gerenciem as turmas.', 1),
(101, 'GAIO_TURMAS_LIBERAR', 'Turmas', 'Liberar turma (para solicitação de inscrições)', 'Permite que os membros liberem a turma para solicitação de inscrições.', 1),
(102, 'GAIO_TURMAS_NOTAS_VISUALIZAR', 'Turmas', 'Visualizar notas da turma', 'Permite que os membros visualizem as notas da turma.', 1),
(103, 'GAIO_TURMAS_PAUTA_DEVOLVER', 'Turmas', 'Devolver pauta eletrônica ao professor', 'Permite que os membros devolvam a pauta eletrônica ao professor.', 1),
(104, 'GAIO_TURMAS_PAUTA_LIBERAR', 'Turmas', 'Liberar pauta eletrônica', 'Permite que os membros liberem a pauta eletrônica.', 1),
(105, 'GAIO_TURMAS_PAUTAS_ALUNO_AUSENTE_INDICAR', 'Turmas', 'Indicar ausência de aluno em pauta', 'Permite que os membros indiquem ausência de aluno na pauta.', 1),
(106, 'GAIO_TURMAS_PAUTAS_REABRIR', 'Turmas', 'Reabrir pauta eletrônica', 'Permite que os membros reabram a pauta eletrônica.', 1),
(107, 'GAIO_TURMAS_PROPRIAS_VISUALIZAR', 'Turmas', 'Visualizar turmas (as próprias)', 'Permite que os membros visualizem suas próprias turmas.', 1),
(108, 'GAIO_TURMAS_REABRIR', 'Turmas', 'Reabrir turma finalizada', 'Permite que os membros reabram uma turma finalizada.', 1),
(109, 'GAIO_TURMAS_VISUALIZAR', 'Turmas', 'Visualizar turma', 'Permite que os membros visualizem as turmas.', 1),
(110, 'GAIO_USUARIOS_ACESSO_REDEFINIR', 'Usuários', 'Redefinir acesso de usuário', 'Permite que os membros redefinam o acesso de usuários.', 1),
(111, 'GAIO_USUARIOS_CADASTRAR', 'Usuários', 'Cadastrar usuário', 'Permite que os membros cadastrem novos usuários.', 1),
(112, 'GAIO_USUARIOS_DESATIVAR', 'Usuários', 'Desativar usuário', 'Permite que os membros desativem usuários.', 1),
(113, 'GAIO_USUARIOS_EDITAR', 'Usuários', 'Editar usuário', 'Permite que os membros editem os dados de usuários.', 1),
(114, 'GAIO_USUARIOS_GERENCIAR', 'Usuários', 'Gerenciar usuários', 'Permite que os membros gerenciem os usuários do sistema.', 1),
(115, 'GAIO_USUARIOS_REATIVAR', 'Usuários', 'Reativar usuário', 'Permite que os membros reativem usuários.', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_professores`
--

CREATE TABLE `gaio_professores` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `usuario_id` int NOT NULL COMMENT 'ID do usuário (referenciado)',
  `lattes_codigo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Código do Lattes',
  `titulo` enum('MESTRE','DOUTOR','ESPECIALISTA') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Titulação do professor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_professores_matriculas`
--

CREATE TABLE `gaio_professores_matriculas` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `professor_id` int NOT NULL COMMENT 'ID do professor (referenciado)',
  `matricula` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Matrícula do professor',
  `carga_horaria` int NOT NULL COMMENT 'Carga horária (horas/aula) da matrícula',
  `data_inicio` date NOT NULL COMMENT 'Data de início do professor',
  `data_termino` date DEFAULT NULL COMMENT 'Data de término da matrícula do professor',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Situação da matrícula (ativa/inativa)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_relatorios`
--

CREATE TABLE `gaio_relatorios` (
  `id` int NOT NULL COMMENT 'ID do relatório (gerado automaticamente)',
  `relatorio_tipo_id` int NOT NULL COMMENT 'ID do tipo de relatório (referenciado)',
  `usuario_emissor_id` int NOT NULL COMMENT 'ID do usuário emissor (referenciado)',
  `codigo_validador` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Código validador do relatório',
  `data_emissao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de emissão do relatório',
  `data_vencimento` datetime DEFAULT NULL COMMENT 'Data de vencimento do relatório',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Status do relatório'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_relatorios_tipos`
--

CREATE TABLE `gaio_relatorios_tipos` (
  `id` int NOT NULL COMMENT 'ID do tipo de relatório (gerado automaticamente)',
  `nome` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome do tipo de relatório',
  `dias_vencimento` int UNSIGNED DEFAULT NULL COMMENT 'Dias para vencimento do relatório',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Status do tipo de relatório'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_tempos_aulas`
--

CREATE TABLE `gaio_tempos_aulas` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `dia_semana` enum('SEGUNDA_FEIRA','TERCA_FEIRA','QUARTA_FEIRA','QUINTA_FEIRA','SEXTA_FEIRA','SABADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Dia da semana',
  `hora_inicio` time NOT NULL COMMENT 'Hora de início do intervalo',
  `hora_termino` time NOT NULL COMMENT 'Hora de término do intervalo',
  `turno` enum('MANHA','TARDE','NOITE') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Turno referente do intervalo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `gaio_tempos_aulas`
--

INSERT INTO `gaio_tempos_aulas` (`id`, `dia_semana`, `hora_inicio`, `hora_termino`, `turno`) VALUES
(1, 'SEGUNDA_FEIRA', '07:10:00', '08:00:00', 'MANHA'),
(2, 'SEGUNDA_FEIRA', '08:00:00', '08:50:00', 'MANHA'),
(3, 'SEGUNDA_FEIRA', '08:50:00', '09:40:00', 'MANHA'),
(4, 'SEGUNDA_FEIRA', '09:50:00', '10:40:00', 'MANHA'),
(5, 'SEGUNDA_FEIRA', '10:40:00', '11:30:00', 'MANHA'),
(6, 'SEGUNDA_FEIRA', '11:30:00', '12:20:00', 'MANHA'),
(7, 'SEGUNDA_FEIRA', '13:00:00', '13:50:00', 'TARDE'),
(8, 'SEGUNDA_FEIRA', '13:50:00', '14:40:00', 'TARDE'),
(9, 'SEGUNDA_FEIRA', '14:40:00', '15:30:00', 'TARDE'),
(10, 'SEGUNDA_FEIRA', '15:30:00', '16:20:00', 'TARDE'),
(11, 'SEGUNDA_FEIRA', '16:20:00', '17:10:00', 'TARDE'),
(12, 'SEGUNDA_FEIRA', '17:10:00', '18:00:00', 'TARDE'),
(13, 'SEGUNDA_FEIRA', '18:00:00', '18:40:00', 'NOITE'),
(14, 'SEGUNDA_FEIRA', '18:40:00', '19:20:00', 'NOITE'),
(15, 'SEGUNDA_FEIRA', '19:20:00', '20:00:00', 'NOITE'),
(16, 'SEGUNDA_FEIRA', '20:00:00', '20:40:00', 'NOITE'),
(17, 'SEGUNDA_FEIRA', '20:50:00', '21:30:00', 'NOITE'),
(18, 'SEGUNDA_FEIRA', '21:30:00', '22:10:00', 'NOITE'),
(32, 'TERCA_FEIRA', '07:10:00', '08:00:00', 'MANHA'),
(33, 'TERCA_FEIRA', '08:00:00', '08:50:00', 'MANHA'),
(34, 'TERCA_FEIRA', '08:50:00', '09:40:00', 'MANHA'),
(35, 'TERCA_FEIRA', '09:50:00', '10:40:00', 'MANHA'),
(36, 'TERCA_FEIRA', '10:40:00', '11:30:00', 'MANHA'),
(37, 'TERCA_FEIRA', '11:30:00', '12:20:00', 'MANHA'),
(38, 'TERCA_FEIRA', '13:00:00', '13:50:00', 'MANHA'),
(39, 'TERCA_FEIRA', '13:50:00', '14:40:00', 'MANHA'),
(40, 'TERCA_FEIRA', '14:40:00', '15:30:00', 'MANHA'),
(41, 'TERCA_FEIRA', '15:30:00', '16:20:00', 'MANHA'),
(42, 'TERCA_FEIRA', '16:20:00', '17:10:00', 'MANHA'),
(43, 'TERCA_FEIRA', '17:10:00', '18:00:00', 'MANHA'),
(44, 'TERCA_FEIRA', '18:00:00', '18:40:00', 'MANHA'),
(45, 'TERCA_FEIRA', '18:40:00', '19:20:00', 'MANHA'),
(46, 'TERCA_FEIRA', '19:20:00', '20:00:00', 'MANHA'),
(47, 'TERCA_FEIRA', '20:00:00', '20:40:00', 'MANHA'),
(48, 'TERCA_FEIRA', '20:50:00', '21:30:00', 'MANHA'),
(63, 'QUARTA_FEIRA', '07:10:00', '08:00:00', 'MANHA'),
(64, 'QUARTA_FEIRA', '08:00:00', '08:50:00', 'MANHA'),
(65, 'QUARTA_FEIRA', '08:50:00', '09:40:00', 'MANHA'),
(66, 'QUARTA_FEIRA', '09:50:00', '10:40:00', 'MANHA'),
(67, 'QUARTA_FEIRA', '10:40:00', '11:30:00', 'MANHA'),
(68, 'QUARTA_FEIRA', '11:30:00', '12:20:00', 'MANHA'),
(69, 'QUARTA_FEIRA', '13:00:00', '13:50:00', 'MANHA'),
(70, 'QUARTA_FEIRA', '13:50:00', '14:40:00', 'MANHA'),
(71, 'QUARTA_FEIRA', '14:40:00', '15:30:00', 'MANHA'),
(72, 'QUARTA_FEIRA', '15:30:00', '16:20:00', 'MANHA'),
(73, 'QUARTA_FEIRA', '16:20:00', '17:10:00', 'MANHA'),
(74, 'QUARTA_FEIRA', '17:10:00', '18:00:00', 'MANHA'),
(75, 'QUARTA_FEIRA', '18:00:00', '18:40:00', 'MANHA'),
(76, 'QUARTA_FEIRA', '18:40:00', '19:20:00', 'MANHA'),
(77, 'QUARTA_FEIRA', '19:20:00', '20:00:00', 'MANHA'),
(78, 'QUARTA_FEIRA', '20:00:00', '20:40:00', 'MANHA'),
(79, 'QUARTA_FEIRA', '20:50:00', '21:30:00', 'MANHA'),
(94, 'QUINTA_FEIRA', '07:10:00', '08:00:00', 'MANHA'),
(95, 'QUINTA_FEIRA', '08:00:00', '08:50:00', 'MANHA'),
(96, 'QUINTA_FEIRA', '08:50:00', '09:40:00', 'MANHA'),
(97, 'QUINTA_FEIRA', '09:50:00', '10:40:00', 'MANHA'),
(98, 'QUINTA_FEIRA', '10:40:00', '11:30:00', 'MANHA'),
(99, 'QUINTA_FEIRA', '11:30:00', '12:20:00', 'MANHA'),
(100, 'QUINTA_FEIRA', '13:00:00', '13:50:00', 'MANHA'),
(101, 'QUINTA_FEIRA', '13:50:00', '14:40:00', 'MANHA'),
(102, 'QUINTA_FEIRA', '14:40:00', '15:30:00', 'MANHA'),
(103, 'QUINTA_FEIRA', '15:30:00', '16:20:00', 'MANHA'),
(104, 'QUINTA_FEIRA', '16:20:00', '17:10:00', 'MANHA'),
(105, 'QUINTA_FEIRA', '17:10:00', '18:00:00', 'MANHA'),
(106, 'QUINTA_FEIRA', '18:00:00', '18:40:00', 'MANHA'),
(107, 'QUINTA_FEIRA', '18:40:00', '19:20:00', 'MANHA'),
(108, 'QUINTA_FEIRA', '19:20:00', '20:00:00', 'MANHA'),
(109, 'QUINTA_FEIRA', '20:00:00', '20:40:00', 'MANHA'),
(110, 'QUINTA_FEIRA', '20:50:00', '21:30:00', 'MANHA'),
(125, 'SEXTA_FEIRA', '07:10:00', '08:00:00', 'MANHA'),
(126, 'SEXTA_FEIRA', '08:00:00', '08:50:00', 'MANHA'),
(127, 'SEXTA_FEIRA', '08:50:00', '09:40:00', 'MANHA'),
(128, 'SEXTA_FEIRA', '09:50:00', '10:40:00', 'MANHA'),
(129, 'SEXTA_FEIRA', '10:40:00', '11:30:00', 'MANHA'),
(130, 'SEXTA_FEIRA', '11:30:00', '12:20:00', 'MANHA'),
(131, 'SEXTA_FEIRA', '13:00:00', '13:50:00', 'MANHA'),
(132, 'SEXTA_FEIRA', '13:50:00', '14:40:00', 'MANHA'),
(133, 'SEXTA_FEIRA', '14:40:00', '15:30:00', 'MANHA'),
(134, 'SEXTA_FEIRA', '15:30:00', '16:20:00', 'MANHA'),
(135, 'SEXTA_FEIRA', '16:20:00', '17:10:00', 'MANHA'),
(136, 'SEXTA_FEIRA', '17:10:00', '18:00:00', 'MANHA'),
(137, 'SEXTA_FEIRA', '18:00:00', '18:40:00', 'MANHA'),
(138, 'SEXTA_FEIRA', '18:40:00', '19:20:00', 'MANHA'),
(139, 'SEXTA_FEIRA', '19:20:00', '20:00:00', 'MANHA'),
(140, 'SEXTA_FEIRA', '20:00:00', '20:40:00', 'MANHA'),
(141, 'SEXTA_FEIRA', '20:50:00', '21:30:00', 'MANHA');

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_turmas`
--

CREATE TABLE `gaio_turmas` (
  `id` int NOT NULL COMMENT 'ID da turma (gerado automaticamente)',
  `disciplina_id` int NOT NULL COMMENT 'ID da disciplina (referenciada)',
  `periodo_letivo_id` int NOT NULL COMMENT 'ID do período letivo (referenciado)',
  `professor_id` int NOT NULL COMMENT 'ID do professor (referenciado)',
  `grade_id` int NOT NULL COMMENT 'ID da grade (referenciada)',
  `codigo` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Código da turma',
  `turno` enum('MANHA','TARDE','NOITE','INTEGRAL') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Turno da turma',
  `capacidade_maxima` int UNSIGNED NOT NULL COMMENT 'Capacidade máxima da turma',
  `modalidade` enum('PRESENCIAL','REMOTA','HIBRIDA') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Modalidade da turma',
  `status` enum('PLANEJADA','OFERTADA','CONFIRMADA','ATIVA','CONCLUIDA','CANCELADA','ARQUIVADA') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Situação da turma'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_turmas_avaliacoes`
--

CREATE TABLE `gaio_turmas_avaliacoes` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `turma_id` int NOT NULL COMMENT 'ID da turma (referenciada)',
  `avaliacao_tipo_id` int NOT NULL COMMENT 'ID do tipo de avaliação (referenciado)',
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome da avaliação da turma',
  `formula` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Fórmula para calcular a avaliação',
  `peso` float NOT NULL DEFAULT '1' COMMENT 'Peso da avaliação',
  `condicao_aplicacao` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Condição para o lançamento da avaliação',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Status da avaliação da turma'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_turmas_dias_letivos`
--

CREATE TABLE `gaio_turmas_dias_letivos` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `turma_id` int NOT NULL COMMENT 'ID da turma (referenciado)',
  `data_letivo` date NOT NULL COMMENT 'Data em que uma aula foi aplicada',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Status do dia letivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_turmas_frequencias`
--

CREATE TABLE `gaio_turmas_frequencias` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `aluno_matricula_id` int NOT NULL COMMENT 'ID da matrícula do aluno (referenciado)',
  `turma_dia_letivo_id` int NOT NULL COMMENT 'ID do registro de dia letivo na turma (referenciado)',
  `situacao` enum('PRESENTE','FALTA','FALTA_JUSTIFICADA') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Situação da frequência do aluno'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_turmas_horarios`
--

CREATE TABLE `gaio_turmas_horarios` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `turma_id` int NOT NULL COMMENT 'ID da turma (referenciada)',
  `espaco_id` int NOT NULL COMMENT 'ID do espaço (referenciado)',
  `tempo_aula_id` int NOT NULL COMMENT 'ID do tempo de aula (referenciado)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_usuarios`
--

CREATE TABLE `gaio_usuarios` (
  `id` int NOT NULL COMMENT 'ID do usuário (gerado automaticamente)',
  `nome_civil` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome civil do usuário',
  `nome_social` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Nome social do usuário',
  `caminho_foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Caminho para a foto do usuário',
  `data_nascimento` date NOT NULL COMMENT 'Data de nascimento do usuário',
  `sexo` enum('MASCULINO','FEMININO','OUTRO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Sexo biológico do usuário',
  `pronome` enum('ELE_DELE','ELA_DELA') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Pronome do usuário',
  `cor_raca` enum('AMARELA','BRANCA','PARDA','PRETA','INDIGENA','NAO_DECLARADA') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'NAO_DECLARADA' COMMENT 'Cor/raça do usuário',
  `estado_civil` enum('SOLTEIRO','CASADO','VIUVO','SEPARADO','DIVORCIADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Estado civil do usuário',
  `cpf` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'CPF do usuário',
  `rg` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'RG do usuário',
  `nacionalidade` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nacionalidade do usuário',
  `naturalidade` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Naturalidade do usuário',
  `email_pessoal` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'E-mail pessoal do usuário',
  `email_institucional` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'E-mail institucional do usuário',
  `necessidades_especificas` set('AUDITIVA','VISUAL','MOTORA','MULTIPLA','MENTAL','OUTRA') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Necessidades específicas do usuário (Deliberação CEE nº 399 de 26 de abril de 2022)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_usuarios_contatos`
--

CREATE TABLE `gaio_usuarios_contatos` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `usuario_id` int NOT NULL COMMENT 'ID do usuário (referenciado)',
  `cep` char(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'CEP do usuário (apenas números)',
  `endereco` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Endereço do usuário',
  `numero` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Número do endereço do usuário',
  `complemento` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Complemento do endereço do usuário',
  `bairro` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Bairro do endereço do usuário',
  `cidade` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Cidade do endereço do usuário',
  `uf` enum('AC','AL','AM','AP','BA','CE','DF','ES','GO','MA','MG','MS','MT','PA','PB','PE','PI','PR','RJ','RN','RO','RR','RS','SC','SE','SP','TO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'UF do endereço do usuário',
  `telefone_fixo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Telefone fixo do usuário',
  `telefone_celular` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Telefone celular do usuário'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_usuarios_grupos`
--

CREATE TABLE `gaio_usuarios_grupos` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `usuario_id` int NOT NULL COMMENT 'ID do usuário (referenciado)',
  `grupo_id` int NOT NULL COMMENT 'ID do grupo (referenciado)',
  `data_criado` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data/hora da criação do registro',
  `data_atualizado` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data/hora da atualização do registro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_usuarios_logins`
--

CREATE TABLE `gaio_usuarios_logins` (
  `id` int NOT NULL COMMENT 'ID do login (gerado automaticamente)',
  `usuario_id` int NOT NULL COMMENT 'ID do usuário',
  `nome_acesso` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nome de acesso do usuário',
  `senha_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Senha do usuário (criptografada)',
  `status` enum('ATIVO','INATIVO','BLOQUEADO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'ATIVO' COMMENT 'Status do login do usuário'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gaio_usuarios_tokens`
--

CREATE TABLE `gaio_usuarios_tokens` (
  `id` int NOT NULL COMMENT 'ID do registro (gerado automaticamente)',
  `usuario_id` int NOT NULL COMMENT 'ID do usuário',
  `token_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Hash do token de uso único',
  `tipo` enum('REDEFINICAO_SENHA','VERIFICACAO_EMAIL') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Tipo de token',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Status do token',
  `data_expiracao` datetime NOT NULL COMMENT 'Data/hora de expiração do token',
  `data_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data/hora de criação do registro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `gaio_alunos`
--
ALTER TABLE `gaio_alunos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_alunos_usuario_id` (`usuario_id`);

--
-- Índices de tabela `gaio_alunos_documentos`
--
ALTER TABLE `gaio_alunos_documentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_alunos_documentos_aluno_id` (`aluno_id`),
  ADD KEY `FK_alunos_documentos_documento_tipo_id` (`documento_tipo_id`);

--
-- Índices de tabela `gaio_alunos_documentos_tipos`
--
ALTER TABLE `gaio_alunos_documentos_tipos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `gaio_alunos_escolas`
--
ALTER TABLE `gaio_alunos_escolas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_alunos_escolas_aluno_id` (`aluno_id`);

--
-- Índices de tabela `gaio_alunos_ingressos_tipos`
--
ALTER TABLE `gaio_alunos_ingressos_tipos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `gaio_alunos_matriculas`
--
ALTER TABLE `gaio_alunos_matriculas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_alunos_matriculas_aluno_id` (`aluno_id`),
  ADD KEY `FK_alunos_matriculas_ingresso_tipo_id` (`ingresso_tipo_id`) USING BTREE,
  ADD KEY `FK_alunos_matriculas_matriz_curricular_id` (`matriz_curricular_id`) USING BTREE,
  ADD KEY `FK_alunos_matriculas_periodo_ingresso_id` (`periodo_ingresso_id`);

--
-- Índices de tabela `gaio_alunos_matriculas_historicos`
--
ALTER TABLE `gaio_alunos_matriculas_historicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_alunos_matriculas_historicos_aluno_matricula_id` (`aluno_matricula_id`);

--
-- Índices de tabela `gaio_alunos_responsaveis`
--
ALTER TABLE `gaio_alunos_responsaveis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_alunos_responsaveis_rg` (`rg`) USING BTREE,
  ADD KEY `FK_alunos_responsaveis_aluno_id` (`aluno_id`);

--
-- Índices de tabela `gaio_atividades`
--
ALTER TABLE `gaio_atividades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_avaliacao_turma_id_codigo` (`avaliacao_turma_id`,`codigo`) USING BTREE;

--
-- Índices de tabela `gaio_atividades_notas`
--
ALTER TABLE `gaio_atividades_notas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_atividades_notas_atividade_id` (`atividade_id`),
  ADD KEY `FK_atividades_notas_usuario_responsavel_id` (`usuario_responsavel_id`),
  ADD KEY `FK_atividades_notas_aluno_matricula_id` (`aluno_matricula_id`);

--
-- Índices de tabela `gaio_avaliacoes_notas`
--
ALTER TABLE `gaio_avaliacoes_notas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_avaliacoes_notas_avaliacao_turma_id_aluno_matricula_id` (`avaliacao_turma_id`,`aluno_matricula_id`) USING BTREE,
  ADD KEY `FK_avaliacoes_notas_aluno_matricula_id` (`aluno_matricula_id`),
  ADD KEY `FK_avaliacoes_notas_usuario_responsavel_id` (`usuario_responsavel_id`);

--
-- Índices de tabela `gaio_avaliacoes_tipos`
--
ALTER TABLE `gaio_avaliacoes_tipos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `gaio_componentes_curriculares`
--
ALTER TABLE `gaio_componentes_curriculares`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_componentes_curricular_sigla_matriz_curricular_id` (`matriz_curricular_id`,`sigla`) USING BTREE,
  ADD KEY `FK_componentes_curriculares_matriz_curricular_id` (`matriz_curricular_id`);

--
-- Índices de tabela `gaio_componentes_equivalencias`
--
ALTER TABLE `gaio_componentes_equivalencias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_componentes_equivalencias_componente_curricular_id` (`componente_curricular_id`,`componente_equivalente_id`) USING BTREE,
  ADD KEY `FK_componentes_equivalencias_componente_equivalente_id` (`componente_equivalente_id`);

--
-- Índices de tabela `gaio_componentes_prerequisitos`
--
ALTER TABLE `gaio_componentes_prerequisitos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_componentes_prerequisitos_componente_curricular` (`componente_curricular_id`,`componente_requisito_id`) USING BTREE,
  ADD KEY `FK_componentes_prerequisitos_componente_requisito_id` (`componente_requisito_id`);

--
-- Índices de tabela `gaio_cursos`
--
ALTER TABLE `gaio_cursos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_cursos_emec_codigo` (`emec_codigo`) USING BTREE,
  ADD KEY `FK_cursos_grau_id` (`grau_id`);

--
-- Índices de tabela `gaio_cursos_graus`
--
ALTER TABLE `gaio_cursos_graus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_cursos_graus_nome` (`nome`) USING BTREE;

--
-- Índices de tabela `gaio_cursos_periodos_letivos`
--
ALTER TABLE `gaio_cursos_periodos_letivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_cursos_periodos_letivos_curso_id` (`curso_id`) USING BTREE,
  ADD KEY `FK_cursos_periodos_letivos_periodo_letivo_id` (`periodo_letivo_id`) USING BTREE;

--
-- Índices de tabela `gaio_disciplinas`
--
ALTER TABLE `gaio_disciplinas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_disciplinas_componente_curricular_id` (`componente_curricular_id`);

--
-- Índices de tabela `gaio_espacos`
--
ALTER TABLE `gaio_espacos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_espacos_codigo` (`codigo`) USING BTREE;

--
-- Índices de tabela `gaio_eventos`
--
ALTER TABLE `gaio_eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_eventos_tipo_id` (`tipo_id`);

--
-- Índices de tabela `gaio_eventos_tipos`
--
ALTER TABLE `gaio_eventos_tipos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_eventos_tipos_codigo` (`codigo`) USING BTREE;

--
-- Índices de tabela `gaio_grades_horarias`
--
ALTER TABLE `gaio_grades_horarias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `gaio_grupos`
--
ALTER TABLE `gaio_grupos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `gaio_grupos_permissoes`
--
ALTER TABLE `gaio_grupos_permissoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_grupos_permissoes_grupo_id_permissao_id` (`grupo_id`,`permissao_id`),
  ADD KEY `FK_grupos_permissoes_permissao_id` (`permissao_id`);

--
-- Índices de tabela `gaio_inscricoes`
--
ALTER TABLE `gaio_inscricoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_inscricoes_aluno_matricula_id` (`aluno_matricula_id`),
  ADD KEY `FK_inscricoes_turma_id` (`turma_id`);

--
-- Índices de tabela `gaio_logins_tentativas`
--
ALTER TABLE `gaio_logins_tentativas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_logins_tentativas_login_id` (`login_id`);

--
-- Índices de tabela `gaio_logs`
--
ALTER TABLE `gaio_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_logs_usuario_id` (`usuario_id`);

--
-- Índices de tabela `gaio_matrizes_curriculares`
--
ALTER TABLE `gaio_matrizes_curriculares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_matrizes_curriculares_curso_id` (`curso_id`);

--
-- Índices de tabela `gaio_notificacoes`
--
ALTER TABLE `gaio_notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_notificacoes_modelo_id` (`modelo_id`),
  ADD KEY `FK_notificacoes_autor_id` (`autor_id`);

--
-- Índices de tabela `gaio_notificacoes_destinos`
--
ALTER TABLE `gaio_notificacoes_destinos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_notificacoes_destinos_notificacao_id` (`notificacao_id`);

--
-- Índices de tabela `gaio_notificacoes_leituras`
--
ALTER TABLE `gaio_notificacoes_leituras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_notificacoes_leituras_usuario_id` (`usuario_id`),
  ADD KEY `FK_notificacoes_leituras_notificacao_id` (`notificacao_id`);

--
-- Índices de tabela `gaio_notificacoes_modelos`
--
ALTER TABLE `gaio_notificacoes_modelos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_notificacoes_modelos_codigo` (`codigo`);

--
-- Índices de tabela `gaio_periodos_letivos`
--
ALTER TABLE `gaio_periodos_letivos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sigla` (`sigla`);

--
-- Índices de tabela `gaio_periodos_letivos_eventos`
--
ALTER TABLE `gaio_periodos_letivos_eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_periodos_letivos_eventos_evento_id` (`evento_id`) USING BTREE,
  ADD KEY `FK_periodos_letivos_eventos_periodo_letivo_id` (`periodo_letivo_id`) USING BTREE;

--
-- Índices de tabela `gaio_permissoes`
--
ALTER TABLE `gaio_permissoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_permissoes_codigo` (`codigo`) USING BTREE;

--
-- Índices de tabela `gaio_professores`
--
ALTER TABLE `gaio_professores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_professores_usuario_id` (`usuario_id`) USING BTREE,
  ADD UNIQUE KEY `UK_professores_lattes_codigo` (`lattes_codigo`) USING BTREE;

--
-- Índices de tabela `gaio_professores_matriculas`
--
ALTER TABLE `gaio_professores_matriculas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_professores_matriculas_matricula` (`matricula`),
  ADD KEY `FK_professores_matriculas_professor_id` (`professor_id`);

--
-- Índices de tabela `gaio_relatorios`
--
ALTER TABLE `gaio_relatorios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_relatorios_relatorio_tipo_id` (`relatorio_tipo_id`),
  ADD KEY `FK_relatorios_usuario_emissor_id` (`usuario_emissor_id`);

--
-- Índices de tabela `gaio_relatorios_tipos`
--
ALTER TABLE `gaio_relatorios_tipos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `gaio_tempos_aulas`
--
ALTER TABLE `gaio_tempos_aulas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `gaio_turmas`
--
ALTER TABLE `gaio_turmas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_turmas_codigo` (`codigo`) USING BTREE,
  ADD KEY `FK_turmas_disciplina_id` (`disciplina_id`),
  ADD KEY `FK_turmas_grade_id` (`grade_id`),
  ADD KEY `FK_turmas_professor_id` (`professor_id`),
  ADD KEY `FK_turmas_periodo_letivo_id` (`periodo_letivo_id`) USING BTREE;

--
-- Índices de tabela `gaio_turmas_avaliacoes`
--
ALTER TABLE `gaio_turmas_avaliacoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_turmas_avaliacoes_turma_id_avaliacao_tipo_id` (`turma_id`,`avaliacao_tipo_id`) USING BTREE,
  ADD KEY `FK_turmas_avaliacoes_avaliacao_tipo_id` (`avaliacao_tipo_id`) USING BTREE;

--
-- Índices de tabela `gaio_turmas_dias_letivos`
--
ALTER TABLE `gaio_turmas_dias_letivos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_turmas_dias_letivos_turma_id_data_letivo` (`turma_id`,`data_letivo`) USING BTREE;

--
-- Índices de tabela `gaio_turmas_frequencias`
--
ALTER TABLE `gaio_turmas_frequencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_turmas_frequencias_aluno_matricula_id` (`aluno_matricula_id`),
  ADD KEY `FK_turmas_frequencias_turma_dia_letivo_id` (`turma_dia_letivo_id`);

--
-- Índices de tabela `gaio_turmas_horarios`
--
ALTER TABLE `gaio_turmas_horarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_turmas_horarios_turma_id` (`turma_id`) USING BTREE,
  ADD KEY `FK_turmas_horarios_espaco_id` (`espaco_id`) USING BTREE,
  ADD KEY `FK_turmas_horarios_tempo_aula_id` (`tempo_aula_id`) USING BTREE;

--
-- Índices de tabela `gaio_usuarios`
--
ALTER TABLE `gaio_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_usuarios_email_pessoal` (`email_pessoal`) USING BTREE,
  ADD UNIQUE KEY `UK_usuarios_cpf` (`cpf`) USING BTREE,
  ADD UNIQUE KEY `UK_usuarios_rg` (`rg`) USING BTREE,
  ADD UNIQUE KEY `UK_usuarios_email_institucional` (`email_institucional`) USING BTREE;

--
-- Índices de tabela `gaio_usuarios_contatos`
--
ALTER TABLE `gaio_usuarios_contatos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_usuarios_contatos_usuario_id` (`usuario_id`) USING BTREE,
  ADD KEY `FK_usuarios_contatos_usuario_id` (`usuario_id`) USING BTREE;

--
-- Índices de tabela `gaio_usuarios_grupos`
--
ALTER TABLE `gaio_usuarios_grupos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_usuarios_grupos_usuario_id_grupo_id` (`usuario_id`,`grupo_id`) USING BTREE,
  ADD KEY `FK_usuarios_grupos_grupo_id` (`grupo_id`);

--
-- Índices de tabela `gaio_usuarios_logins`
--
ALTER TABLE `gaio_usuarios_logins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_usuarios_logins_nome_acesso` (`nome_acesso`),
  ADD UNIQUE KEY `UK_usuarios_logins_usuario_id` (`usuario_id`) USING BTREE;

--
-- Índices de tabela `gaio_usuarios_tokens`
--
ALTER TABLE `gaio_usuarios_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UK_usuarios_tokens_token_hash` (`token_hash`) USING BTREE,
  ADD KEY `FK_usuarios_tokens_usuario_id` (`usuario_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `gaio_alunos`
--
ALTER TABLE `gaio_alunos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)', AUTO_INCREMENT=3022;

--
-- AUTO_INCREMENT de tabela `gaio_alunos_documentos`
--
ALTER TABLE `gaio_alunos_documentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do documento (gerado automaticamente)';

--
-- AUTO_INCREMENT de tabela `gaio_alunos_documentos_tipos`
--
ALTER TABLE `gaio_alunos_documentos_tipos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do tipo do documento (gerado automaticamente)', AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `gaio_alunos_escolas`
--
ALTER TABLE `gaio_alunos_escolas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)';

--
-- AUTO_INCREMENT de tabela `gaio_alunos_ingressos_tipos`
--
ALTER TABLE `gaio_alunos_ingressos_tipos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)', AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `gaio_alunos_matriculas`
--
ALTER TABLE `gaio_alunos_matriculas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)', AUTO_INCREMENT=3001;

--
-- AUTO_INCREMENT de tabela `gaio_alunos_matriculas_historicos`
--
ALTER TABLE `gaio_alunos_matriculas_historicos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)';

--
-- AUTO_INCREMENT de tabela `gaio_alunos_responsaveis`
--
ALTER TABLE `gaio_alunos_responsaveis`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)';

--
-- AUTO_INCREMENT de tabela `gaio_atividades`
--
ALTER TABLE `gaio_atividades`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID da atividade (gerado automaticamente)';

--
-- AUTO_INCREMENT de tabela `gaio_atividades_notas`
--
ALTER TABLE `gaio_atividades_notas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)';

--
-- AUTO_INCREMENT de tabela `gaio_avaliacoes_notas`
--
ALTER TABLE `gaio_avaliacoes_notas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)', AUTO_INCREMENT=15397;

--
-- AUTO_INCREMENT de tabela `gaio_avaliacoes_tipos`
--
ALTER TABLE `gaio_avaliacoes_tipos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do tipo de avaliação (gerado automaticamente)', AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `gaio_componentes_curriculares`
--
ALTER TABLE `gaio_componentes_curriculares`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do componente curricular (gerado automaticamente)', AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT de tabela `gaio_componentes_equivalencias`
--
ALTER TABLE `gaio_componentes_equivalencias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)';

--
-- AUTO_INCREMENT de tabela `gaio_componentes_prerequisitos`
--
ALTER TABLE `gaio_componentes_prerequisitos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)', AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `gaio_cursos`
--
ALTER TABLE `gaio_cursos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do curso (gerado automaticamente)', AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de tabela `gaio_cursos_graus`
--
ALTER TABLE `gaio_cursos_graus`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do grau de curso (gerado automaticamente)', AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `gaio_cursos_periodos_letivos`
--
ALTER TABLE `gaio_cursos_periodos_letivos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)';

--
-- AUTO_INCREMENT de tabela `gaio_disciplinas`
--
ALTER TABLE `gaio_disciplinas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID da disciplina (gerado automaticamente)', AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de tabela `gaio_espacos`
--
ALTER TABLE `gaio_espacos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do espaço (gerado automaticamente)', AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT de tabela `gaio_eventos`
--
ALTER TABLE `gaio_eventos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do evento (gerado automaticamente)';

--
-- AUTO_INCREMENT de tabela `gaio_eventos_tipos`
--
ALTER TABLE `gaio_eventos_tipos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do tipo de evento (gerado automaticamente)';

--
-- AUTO_INCREMENT de tabela `gaio_grades_horarias`
--
ALTER TABLE `gaio_grades_horarias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID da grade horária (gerado automaticamente)', AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `gaio_grupos`
--
ALTER TABLE `gaio_grupos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do grupo (gerado automaticamente)', AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `gaio_grupos_permissoes`
--
ALTER TABLE `gaio_grupos_permissoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)', AUTO_INCREMENT=230;

--
-- AUTO_INCREMENT de tabela `gaio_inscricoes`
--
ALTER TABLE `gaio_inscricoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID da inscrição (gerado automaticamente)', AUTO_INCREMENT=2242;

--
-- AUTO_INCREMENT de tabela `gaio_logins_tentativas`
--
ALTER TABLE `gaio_logins_tentativas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID da tentativa (gerado automaticamente)', AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `gaio_logs`
--
ALTER TABLE `gaio_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)', AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `gaio_matrizes_curriculares`
--
ALTER TABLE `gaio_matrizes_curriculares`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID da matriz curricular (gerado automaticamente)', AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de tabela `gaio_notificacoes`
--
ALTER TABLE `gaio_notificacoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID da notificação (gerado automaticamente)', AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT de tabela `gaio_notificacoes_destinos`
--
ALTER TABLE `gaio_notificacoes_destinos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)', AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT de tabela `gaio_notificacoes_leituras`
--
ALTER TABLE `gaio_notificacoes_leituras`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro de leitura (gerado automaticamente)', AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de tabela `gaio_notificacoes_modelos`
--
ALTER TABLE `gaio_notificacoes_modelos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do modelo de notificação (gerado automaticamente)', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `gaio_periodos_letivos`
--
ALTER TABLE `gaio_periodos_letivos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do período letivo (gerado automaticamente)', AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `gaio_periodos_letivos_eventos`
--
ALTER TABLE `gaio_periodos_letivos_eventos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)';

--
-- AUTO_INCREMENT de tabela `gaio_permissoes`
--
ALTER TABLE `gaio_permissoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID da permissão (gerado automaticamente)', AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT de tabela `gaio_professores`
--
ALTER TABLE `gaio_professores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)', AUTO_INCREMENT=218;

--
-- AUTO_INCREMENT de tabela `gaio_professores_matriculas`
--
ALTER TABLE `gaio_professores_matriculas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)';

--
-- AUTO_INCREMENT de tabela `gaio_relatorios`
--
ALTER TABLE `gaio_relatorios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do relatório (gerado automaticamente)';

--
-- AUTO_INCREMENT de tabela `gaio_relatorios_tipos`
--
ALTER TABLE `gaio_relatorios_tipos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do tipo de relatório (gerado automaticamente)';

--
-- AUTO_INCREMENT de tabela `gaio_tempos_aulas`
--
ALTER TABLE `gaio_tempos_aulas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)', AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT de tabela `gaio_turmas`
--
ALTER TABLE `gaio_turmas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID da turma (gerado automaticamente)', AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT de tabela `gaio_turmas_avaliacoes`
--
ALTER TABLE `gaio_turmas_avaliacoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)', AUTO_INCREMENT=4150;

--
-- AUTO_INCREMENT de tabela `gaio_turmas_dias_letivos`
--
ALTER TABLE `gaio_turmas_dias_letivos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)';

--
-- AUTO_INCREMENT de tabela `gaio_turmas_frequencias`
--
ALTER TABLE `gaio_turmas_frequencias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)';

--
-- AUTO_INCREMENT de tabela `gaio_turmas_horarios`
--
ALTER TABLE `gaio_turmas_horarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)', AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT de tabela `gaio_usuarios`
--
ALTER TABLE `gaio_usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do usuário (gerado automaticamente)', AUTO_INCREMENT=6039;

--
-- AUTO_INCREMENT de tabela `gaio_usuarios_contatos`
--
ALTER TABLE `gaio_usuarios_contatos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)', AUTO_INCREMENT=5022;

--
-- AUTO_INCREMENT de tabela `gaio_usuarios_grupos`
--
ALTER TABLE `gaio_usuarios_grupos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)', AUTO_INCREMENT=3224;

--
-- AUTO_INCREMENT de tabela `gaio_usuarios_logins`
--
ALTER TABLE `gaio_usuarios_logins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do login (gerado automaticamente)', AUTO_INCREMENT=5023;

--
-- AUTO_INCREMENT de tabela `gaio_usuarios_tokens`
--
ALTER TABLE `gaio_usuarios_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID do registro (gerado automaticamente)';

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `gaio_alunos`
--
ALTER TABLE `gaio_alunos`
  ADD CONSTRAINT `FK_alunos_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `gaio_usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_alunos_documentos`
--
ALTER TABLE `gaio_alunos_documentos`
  ADD CONSTRAINT `FK_alunos_documentos_aluno_id` FOREIGN KEY (`aluno_id`) REFERENCES `gaio_alunos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_alunos_documentos_documento_tipo_id` FOREIGN KEY (`documento_tipo_id`) REFERENCES `gaio_alunos_documentos_tipos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_alunos_escolas`
--
ALTER TABLE `gaio_alunos_escolas`
  ADD CONSTRAINT `FK_alunos_escolas_aluno_id` FOREIGN KEY (`aluno_id`) REFERENCES `gaio_alunos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_alunos_matriculas`
--
ALTER TABLE `gaio_alunos_matriculas`
  ADD CONSTRAINT `FK_alunos_matriculas_aluno_id` FOREIGN KEY (`aluno_id`) REFERENCES `gaio_alunos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_alunos_matriculas_ingresso_id` FOREIGN KEY (`ingresso_tipo_id`) REFERENCES `gaio_alunos_ingressos_tipos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_alunos_matriculas_matriz_id` FOREIGN KEY (`matriz_curricular_id`) REFERENCES `gaio_matrizes_curriculares` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_alunos_matriculas_periodo_ingresso_id` FOREIGN KEY (`periodo_ingresso_id`) REFERENCES `gaio_periodos_letivos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_alunos_matriculas_historicos`
--
ALTER TABLE `gaio_alunos_matriculas_historicos`
  ADD CONSTRAINT `FK_alunos_matriculas_historicos_aluno_matricula_id` FOREIGN KEY (`aluno_matricula_id`) REFERENCES `gaio_alunos_matriculas_historicos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_alunos_responsaveis`
--
ALTER TABLE `gaio_alunos_responsaveis`
  ADD CONSTRAINT `FK_alunos_responsaveis_aluno_id` FOREIGN KEY (`aluno_id`) REFERENCES `gaio_alunos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_atividades`
--
ALTER TABLE `gaio_atividades`
  ADD CONSTRAINT `FK_atividades_avaliacao_turma_id` FOREIGN KEY (`avaliacao_turma_id`) REFERENCES `gaio_turmas_avaliacoes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_atividades_notas`
--
ALTER TABLE `gaio_atividades_notas`
  ADD CONSTRAINT `FK_atividades_notas_aluno_matricula_id` FOREIGN KEY (`aluno_matricula_id`) REFERENCES `gaio_alunos_matriculas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_atividades_notas_atividade_id` FOREIGN KEY (`atividade_id`) REFERENCES `gaio_atividades` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_atividades_notas_usuario_responsavel_id` FOREIGN KEY (`usuario_responsavel_id`) REFERENCES `gaio_usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_avaliacoes_notas`
--
ALTER TABLE `gaio_avaliacoes_notas`
  ADD CONSTRAINT `FK_avaliacoes_notas_aluno_matricula_id` FOREIGN KEY (`aluno_matricula_id`) REFERENCES `gaio_alunos_matriculas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_avaliacoes_notas_avaliacao_turma_id` FOREIGN KEY (`avaliacao_turma_id`) REFERENCES `gaio_turmas_avaliacoes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_avaliacoes_notas_usuario_responsavel_id` FOREIGN KEY (`usuario_responsavel_id`) REFERENCES `gaio_usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_componentes_curriculares`
--
ALTER TABLE `gaio_componentes_curriculares`
  ADD CONSTRAINT `FK_componentes_curriculares_matriz_curricular_id` FOREIGN KEY (`matriz_curricular_id`) REFERENCES `gaio_matrizes_curriculares` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_componentes_equivalencias`
--
ALTER TABLE `gaio_componentes_equivalencias`
  ADD CONSTRAINT `FK_componentes_equivalencias_componente_curricular_id` FOREIGN KEY (`componente_curricular_id`) REFERENCES `gaio_componentes_curriculares` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_componentes_equivalencias_componente_equivalente_id` FOREIGN KEY (`componente_equivalente_id`) REFERENCES `gaio_componentes_curriculares` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_componentes_prerequisitos`
--
ALTER TABLE `gaio_componentes_prerequisitos`
  ADD CONSTRAINT `FK_componentes_prerequisitos_componente_curricular_id` FOREIGN KEY (`componente_curricular_id`) REFERENCES `gaio_componentes_curriculares` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_componentes_prerequisitos_componente_requisito_id` FOREIGN KEY (`componente_requisito_id`) REFERENCES `gaio_componentes_curriculares` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_cursos`
--
ALTER TABLE `gaio_cursos`
  ADD CONSTRAINT `FK_cursos_grau_id` FOREIGN KEY (`grau_id`) REFERENCES `gaio_cursos_graus` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_cursos_periodos_letivos`
--
ALTER TABLE `gaio_cursos_periodos_letivos`
  ADD CONSTRAINT `FK_cursos_periodos_curso_id` FOREIGN KEY (`curso_id`) REFERENCES `gaio_cursos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_cursos_periodos_periodo_id` FOREIGN KEY (`periodo_letivo_id`) REFERENCES `gaio_periodos_letivos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_disciplinas`
--
ALTER TABLE `gaio_disciplinas`
  ADD CONSTRAINT `FK_disciplinas_componente_curricular_id` FOREIGN KEY (`componente_curricular_id`) REFERENCES `gaio_componentes_curriculares` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_eventos`
--
ALTER TABLE `gaio_eventos`
  ADD CONSTRAINT `FK_eventos_tipo_id` FOREIGN KEY (`tipo_id`) REFERENCES `gaio_eventos_tipos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_grupos_permissoes`
--
ALTER TABLE `gaio_grupos_permissoes`
  ADD CONSTRAINT `FK_grupos_permissoes_grupo_id` FOREIGN KEY (`grupo_id`) REFERENCES `gaio_grupos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_grupos_permissoes_permissao_id` FOREIGN KEY (`permissao_id`) REFERENCES `gaio_permissoes` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `gaio_inscricoes`
--
ALTER TABLE `gaio_inscricoes`
  ADD CONSTRAINT `FK_inscricoes_aluno_matricula_id` FOREIGN KEY (`aluno_matricula_id`) REFERENCES `gaio_alunos_matriculas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_inscricoes_turma_id` FOREIGN KEY (`turma_id`) REFERENCES `gaio_turmas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_logins_tentativas`
--
ALTER TABLE `gaio_logins_tentativas`
  ADD CONSTRAINT `FK_logins_tentativas_login_id` FOREIGN KEY (`login_id`) REFERENCES `gaio_usuarios_logins` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_logs`
--
ALTER TABLE `gaio_logs`
  ADD CONSTRAINT `FK_logs_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `gaio_usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_matrizes_curriculares`
--
ALTER TABLE `gaio_matrizes_curriculares`
  ADD CONSTRAINT `FK_matrizes_curriculares_curso_id` FOREIGN KEY (`curso_id`) REFERENCES `gaio_cursos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_notificacoes`
--
ALTER TABLE `gaio_notificacoes`
  ADD CONSTRAINT `FK_notificacoes_autor_id` FOREIGN KEY (`autor_id`) REFERENCES `gaio_usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_notificacoes_modelo_id` FOREIGN KEY (`modelo_id`) REFERENCES `gaio_notificacoes_modelos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_notificacoes_destinos`
--
ALTER TABLE `gaio_notificacoes_destinos`
  ADD CONSTRAINT `FK_notificacoes_destinos_notificacao_id` FOREIGN KEY (`notificacao_id`) REFERENCES `gaio_notificacoes` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_notificacoes_leituras`
--
ALTER TABLE `gaio_notificacoes_leituras`
  ADD CONSTRAINT `FK_notificacoes_leituras_notificacao_id` FOREIGN KEY (`notificacao_id`) REFERENCES `gaio_notificacoes` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_notificacoes_leituras_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `gaio_usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_periodos_letivos_eventos`
--
ALTER TABLE `gaio_periodos_letivos_eventos`
  ADD CONSTRAINT `FK_periodos_eventos_evento_id` FOREIGN KEY (`evento_id`) REFERENCES `gaio_eventos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_periodos_eventos_periodo_id` FOREIGN KEY (`periodo_letivo_id`) REFERENCES `gaio_periodos_letivos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_professores`
--
ALTER TABLE `gaio_professores`
  ADD CONSTRAINT `FK_professores_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `gaio_usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_professores_matriculas`
--
ALTER TABLE `gaio_professores_matriculas`
  ADD CONSTRAINT `FK_professores_matriculas_professor_id` FOREIGN KEY (`professor_id`) REFERENCES `gaio_professores` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_relatorios`
--
ALTER TABLE `gaio_relatorios`
  ADD CONSTRAINT `FK_relatorios_relatorio_tipo_id` FOREIGN KEY (`relatorio_tipo_id`) REFERENCES `gaio_relatorios_tipos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_relatorios_usuario_emissor_id` FOREIGN KEY (`usuario_emissor_id`) REFERENCES `gaio_usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_turmas`
--
ALTER TABLE `gaio_turmas`
  ADD CONSTRAINT `FK_turmas_disciplina_id` FOREIGN KEY (`disciplina_id`) REFERENCES `gaio_disciplinas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_turmas_grade_id` FOREIGN KEY (`grade_id`) REFERENCES `gaio_grades_horarias` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_turmas_periodo_id` FOREIGN KEY (`periodo_letivo_id`) REFERENCES `gaio_periodos_letivos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_turmas_professor_id` FOREIGN KEY (`professor_id`) REFERENCES `gaio_professores` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_turmas_avaliacoes`
--
ALTER TABLE `gaio_turmas_avaliacoes`
  ADD CONSTRAINT `FK_avaliacoes_turmas_avaliacao_tipo_id` FOREIGN KEY (`avaliacao_tipo_id`) REFERENCES `gaio_avaliacoes_tipos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_avaliacoes_turmas_turma_id` FOREIGN KEY (`turma_id`) REFERENCES `gaio_turmas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_turmas_dias_letivos`
--
ALTER TABLE `gaio_turmas_dias_letivos`
  ADD CONSTRAINT `FK_turmas_dias_letivos_turma_id` FOREIGN KEY (`turma_id`) REFERENCES `gaio_turmas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_turmas_frequencias`
--
ALTER TABLE `gaio_turmas_frequencias`
  ADD CONSTRAINT `FK_turmas_frequencias_aluno_matricula_id` FOREIGN KEY (`aluno_matricula_id`) REFERENCES `gaio_alunos_matriculas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_turmas_frequencias_turma_dia_letivo_id` FOREIGN KEY (`turma_dia_letivo_id`) REFERENCES `gaio_turmas_dias_letivos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_turmas_horarios`
--
ALTER TABLE `gaio_turmas_horarios`
  ADD CONSTRAINT `FK_alocacao_horarios_espaco_id` FOREIGN KEY (`espaco_id`) REFERENCES `gaio_espacos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_alocacao_horarios_intervalo_id` FOREIGN KEY (`tempo_aula_id`) REFERENCES `gaio_tempos_aulas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_alocacao_horarios_turma_id` FOREIGN KEY (`turma_id`) REFERENCES `gaio_turmas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_usuarios_contatos`
--
ALTER TABLE `gaio_usuarios_contatos`
  ADD CONSTRAINT `gaio_usuarios_contatos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `gaio_usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `gaio_usuarios_grupos`
--
ALTER TABLE `gaio_usuarios_grupos`
  ADD CONSTRAINT `FK_usuarios_grupos_grupo_id` FOREIGN KEY (`grupo_id`) REFERENCES `gaio_grupos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_usuarios_grupos_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `gaio_usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_usuarios_logins`
--
ALTER TABLE `gaio_usuarios_logins`
  ADD CONSTRAINT `FK_usuarios_logins_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `gaio_usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `gaio_usuarios_tokens`
--
ALTER TABLE `gaio_usuarios_tokens`
  ADD CONSTRAINT `FK_usuarios_tokens_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `gaio_usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
