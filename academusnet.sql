-- phpMyAdmin SQL Dump
-- version 4.2.6deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Tempo de geração: 22/04/2015 às 15:42
-- Versão do servidor: 5.5.41-0ubuntu0.14.10.1
-- Versão do PHP: 5.5.12-2ubuntu4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de dados: `academusnet`
--
CREATE DATABASE IF NOT EXISTS `academusnet` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `academusnet`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `acesso`
--

CREATE TABLE IF NOT EXISTS `acesso` (
  `usuid` varchar(40) DEFAULT NULL,
  `disid` int(10) DEFAULT NULL,
  `timei` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `timef` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sessao` varchar(40) NOT NULL DEFAULT '',
  `navegador` varchar(150) DEFAULT NULL,
  `sisop` varchar(150) DEFAULT NULL,
  `intervalo` decimal(10,2) DEFAULT NULL,
  `clicks` int(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `agenda`
--

CREATE TABLE IF NOT EXISTS `agenda` (
`id` int(11) NOT NULL,
  `disid` int(11) NOT NULL DEFAULT '0',
  `texto` varchar(90) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `detalhe` longtext
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1380 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `alternativa`
--

CREATE TABLE IF NOT EXISTS `alternativa` (
`id` int(11) NOT NULL,
  `queid` int(11) NOT NULL DEFAULT '0',
  `texto` longtext NOT NULL,
  `resposta` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=86 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `aluno`
--

CREATE TABLE IF NOT EXISTS `aluno` (
  `id` varchar(30) NOT NULL DEFAULT '',
  `usuid` varchar(40) NOT NULL DEFAULT '0',
  `nome` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) DEFAULT NULL,
  `endid` int(11) NOT NULL DEFAULT '0',
  `ativo` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `assunto`
--

CREATE TABLE IF NOT EXISTS `assunto` (
`id` int(11) NOT NULL,
  `descricao` varchar(69) NOT NULL DEFAULT '',
  `usuid` varchar(40) NOT NULL DEFAULT ''
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `atividade`
--

CREATE TABLE IF NOT EXISTS `atividade` (
`id` int(11) NOT NULL,
  `planid` int(11) NOT NULL DEFAULT '0',
  `texto` varchar(90) NOT NULL DEFAULT '',
  `comentario` longtext NOT NULL,
  `avalid` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `avalequi`
--

CREATE TABLE IF NOT EXISTS `avalequi` (
  `avalid` int(11) NOT NULL,
  `equid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacao`
--

CREATE TABLE IF NOT EXISTS `avaliacao` (
`id` int(11) NOT NULL,
  `disid` int(11) NOT NULL DEFAULT '0',
  `texto` varchar(90) NOT NULL DEFAULT '',
  `peso` varchar(6) NOT NULL DEFAULT '0',
  `periodo` int(2) NOT NULL DEFAULT '0',
  `sigla` varchar(6) DEFAULT NULL,
  `tipoaval` int(1) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1688 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `aviso`
--

CREATE TABLE IF NOT EXISTS `aviso` (
`id` int(11) NOT NULL,
  `disid` int(11) NOT NULL DEFAULT '0',
  `titulo` varchar(90) NOT NULL,
  `texto` longtext NOT NULL,
  `datav` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=550 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `bibliografia`
--

CREATE TABLE IF NOT EXISTS `bibliografia` (
`id` int(11) NOT NULL,
  `texto` blob NOT NULL,
  `url` varchar(200) DEFAULT NULL,
  `usuid` varchar(40) DEFAULT NULL,
  `assid` int(11) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=834 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `correcao`
--

CREATE TABLE IF NOT EXISTS `correcao` (
`id` int(11) NOT NULL,
  `aluid` varchar(12) DEFAULT NULL,
  `tesid` int(11) NOT NULL DEFAULT '0',
  `queid` int(11) NOT NULL DEFAULT '0',
  `altid` int(11) NOT NULL DEFAULT '0',
  `equid` int(11) DEFAULT NULL,
  `valor` decimal(4,2) DEFAULT NULL,
  `comentario` longtext
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `curadmin`
--

CREATE TABLE IF NOT EXISTS `curadmin` (
  `usuid` varchar(40) NOT NULL,
  `curid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `curso`
--

CREATE TABLE IF NOT EXISTS `curso` (
`id` int(11) NOT NULL,
  `nome` varchar(60) NOT NULL DEFAULT '',
  `sigla` varchar(4) DEFAULT NULL,
  `endid` int(11) DEFAULT '0',
  `usuid` varchar(40) DEFAULT NULL,
  `projeto` longtext
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=101 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `descritiva`
--

CREATE TABLE IF NOT EXISTS `descritiva` (
`id` int(11) NOT NULL,
  `aluid` varchar(30) DEFAULT NULL,
  `tesid` int(11) NOT NULL DEFAULT '0',
  `queid` int(11) NOT NULL DEFAULT '0',
  `texto` longtext NOT NULL,
  `equid` int(11) DEFAULT NULL,
  `valor` decimal(4,2) DEFAULT NULL,
  `comentario` longtext
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4642 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `disalu`
--

CREATE TABLE IF NOT EXISTS `disalu` (
  `disid` int(11) NOT NULL DEFAULT '0',
  `aluid` varchar(30) NOT NULL DEFAULT '',
  `ativo` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `disbib`
--

CREATE TABLE IF NOT EXISTS `disbib` (
  `disid` int(11) NOT NULL,
  `bibid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `disciplina`
--

CREATE TABLE IF NOT EXISTS `disciplina` (
`id` int(11) NOT NULL,
  `usuid` varchar(40) NOT NULL DEFAULT '0',
  `endid` int(11) DEFAULT NULL,
  `curid` int(11) DEFAULT NULL,
  `nome` varchar(60) NOT NULL DEFAULT '',
  `cargah` varchar(6) DEFAULT NULL,
  `objetivo` longtext,
  `faltas` int(3) DEFAULT NULL,
  `sigla` varchar(4) DEFAULT NULL,
  `datai` date DEFAULT '0000-00-00',
  `dataf` date DEFAULT '0000-00-00',
  `visitante` tinyint(1) NOT NULL DEFAULT '0',
  `matricula` tinyint(1) NOT NULL DEFAULT '0',
  `datac` timestamp NULL DEFAULT NULL,
  `titular` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=490 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `disead`
--

CREATE TABLE IF NOT EXISTS `disead` (
  `disid` int(11) NOT NULL DEFAULT '0',
  `eadid` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `dismat`
--

CREATE TABLE IF NOT EXISTS `dismat` (
  `disid` int(11) NOT NULL,
  `matid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `distes`
--

CREATE TABLE IF NOT EXISTS `distes` (
  `disid` int(11) NOT NULL DEFAULT '0',
  `tesid` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `disusu`
--

CREATE TABLE IF NOT EXISTS `disusu` (
  `disid` int(11) NOT NULL DEFAULT '0',
  `usuid` varchar(40) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `disweb`
--

CREATE TABLE IF NOT EXISTS `disweb` (
  `disid` int(11) NOT NULL,
  `webid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `dual`
--

CREATE TABLE IF NOT EXISTS `dual` (
  `dummy` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='tabela de apoio';

-- --------------------------------------------------------

--
-- Estrutura para tabela `ead`
--

CREATE TABLE IF NOT EXISTS `ead` (
`id` int(11) NOT NULL,
  `texto` varchar(90) NOT NULL DEFAULT '',
  `comentario` longtext,
  `usuid` varchar(40) NOT NULL DEFAULT '',
  `eadid` int(11) DEFAULT NULL,
  `assid` int(11) DEFAULT NULL,
  `pagina` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=68 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `endadmin`
--

CREATE TABLE IF NOT EXISTS `endadmin` (
  `usuid` varchar(40) NOT NULL,
  `endid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecos`
--

CREATE TABLE IF NOT EXISTS `enderecos` (
`id` int(11) NOT NULL,
  `nome` varchar(90) NOT NULL DEFAULT '',
  `endereco` varchar(60) DEFAULT NULL,
  `bairro` varchar(30) DEFAULT NULL,
  `cidade` varchar(30) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `pais` varchar(30) DEFAULT NULL,
  `telefone` varchar(30) DEFAULT NULL,
  `email` varchar(90) DEFAULT NULL,
  `url` varchar(30) DEFAULT NULL,
  `sigla` varchar(10) DEFAULT NULL,
  `projeto` longtext
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=46 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `equialu`
--

CREATE TABLE IF NOT EXISTS `equialu` (
  `equid` int(11) NOT NULL DEFAULT '0',
  `aluid` varchar(30) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `equipes`
--

CREATE TABLE IF NOT EXISTS `equipes` (
`id` int(11) NOT NULL,
  `disid` int(11) NOT NULL DEFAULT '0',
  `nome` varchar(400) NOT NULL DEFAULT ''
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1670 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `forum`
--

CREATE TABLE IF NOT EXISTS `forum` (
`id` int(11) NOT NULL,
  `disid` int(11) NOT NULL DEFAULT '0',
  `titulo` varchar(90) NOT NULL DEFAULT '',
  `mensagem` longtext NOT NULL,
  `time` timestamp NULL DEFAULT NULL,
  `usuid` varchar(40) DEFAULT NULL,
  `forid` int(11) DEFAULT NULL,
  `planid` int(11) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `frequencia`
--

CREATE TABLE IF NOT EXISTS `frequencia` (
`id` int(11) NOT NULL,
  `aponid` int(11) NOT NULL DEFAULT '0',
  `aluid` varchar(30) NOT NULL DEFAULT '',
  `faltas` int(2) DEFAULT NULL,
  `planid` int(11) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25180 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `material`
--

CREATE TABLE IF NOT EXISTS `material` (
`id` int(11) NOT NULL,
  `texto` varchar(200) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `download` int(6) DEFAULT NULL,
  `obs` longtext,
  `tamanho` int(10) DEFAULT NULL,
  `tipo` varchar(40) DEFAULT NULL,
  `usuid` varchar(40) DEFAULT NULL,
  `assid` int(11) DEFAULT NULL,
  `link` varchar(200) DEFAULT NULL,
  `arquivo` varchar(200) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=893 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE IF NOT EXISTS `mensagens` (
`id` int(6) NOT NULL,
  `destinatario` longtext NOT NULL,
  `remetente` longtext NOT NULL,
  `assunto` varchar(90) DEFAULT NULL,
  `data` timestamp NULL DEFAULT NULL,
  `mensagem` text NOT NULL,
  `disid` int(11) NOT NULL DEFAULT '0',
  `lido` int(1) NOT NULL DEFAULT '0',
  `usuid` varchar(40) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=375 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
`id` int(11) NOT NULL,
  `disid` int(11) NOT NULL,
  `nome` varchar(40) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1300 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `notas`
--

CREATE TABLE IF NOT EXISTS `notas` (
`id` int(11) NOT NULL,
  `avalid` int(11) NOT NULL DEFAULT '0',
  `aluid` varchar(30) NOT NULL DEFAULT '0',
  `nota` decimal(6,2) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=135145 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `planbloom`
--

CREATE TABLE IF NOT EXISTS `planbloom` (
  `planid` int(11) NOT NULL,
  `conhecimento` longtext,
  `compreensao` longtext,
  `aplicacao` longtext,
  `analise` longtext,
  `avaliacao` longtext,
  `sintese` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `planead`
--

CREATE TABLE IF NOT EXISTS `planead` (
  `planid` int(11) NOT NULL,
  `eadid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `planmat`
--

CREATE TABLE IF NOT EXISTS `planmat` (
  `planid` int(11) NOT NULL,
  `matid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `plano`
--

CREATE TABLE IF NOT EXISTS `plano` (
`id` int(11) NOT NULL,
  `data` date NOT NULL DEFAULT '0000-00-00',
  `disid` int(11) NOT NULL DEFAULT '0',
  `aula` int(11) NOT NULL,
  `texto` varchar(90) NOT NULL DEFAULT '',
  `objetivos` longtext NOT NULL,
  `conteudos` longtext NOT NULL,
  `metodologia` longtext NOT NULL,
  `atividades` longtext NOT NULL,
  `leituraobr` longtext NOT NULL,
  `leiturarec` longtext NOT NULL,
  `comentario` longtext,
  `datav` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3413 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `planoensino`
--

CREATE TABLE IF NOT EXISTS `planoensino` (
  `disid` int(11) NOT NULL,
  `cargahorsem` int(11) NOT NULL,
  `cargahortot` int(11) NOT NULL,
  `ementa` longtext NOT NULL,
  `objetivos` longtext NOT NULL,
  `conteudo` longtext NOT NULL,
  `metodologia` longtext NOT NULL,
  `avaliacao` longtext NOT NULL,
  `recursos` longtext NOT NULL,
  `bibliografiab` longtext NOT NULL,
  `bibliografiac` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `plantes`
--

CREATE TABLE IF NOT EXISTS `plantes` (
  `planid` int(11) NOT NULL,
  `tesid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `planweb`
--

CREATE TABLE IF NOT EXISTS `planweb` (
  `planid` int(11) NOT NULL,
  `webid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `questoes`
--

CREATE TABLE IF NOT EXISTS `questoes` (
`id` int(11) NOT NULL,
  `assid` int(11) DEFAULT '0',
  `texto` longblob NOT NULL,
  `tipo` char(1) NOT NULL DEFAULT '',
  `resposta` longblob,
  `usuid` varchar(40) NOT NULL DEFAULT '',
  `bloom` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=420 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `reunioes`
--

CREATE TABLE IF NOT EXISTS `reunioes` (
`id` int(11) NOT NULL,
  `disid` int(11) NOT NULL DEFAULT '0',
  `aluid` varchar(30) DEFAULT NULL,
  `dataage` date NOT NULL DEFAULT '0000-00-00',
  `horaage` time NOT NULL DEFAULT '00:00:00',
  `descricao` varchar(80) DEFAULT NULL,
  `comentarios` longblob,
  `pendencias` longblob,
  `equid` int(11) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=164 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sessao`
--

CREATE TABLE IF NOT EXISTS `sessao` (
  `sessao` varchar(35) NOT NULL DEFAULT '',
  `usuid` varchar(40) NOT NULL DEFAULT '',
  `super` tinyint(1) DEFAULT NULL,
  `data` date NOT NULL DEFAULT '0000-00-00',
  `professor` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tesbloom`
--

CREATE TABLE IF NOT EXISTS `tesbloom` (
  `tesid` int(11) NOT NULL,
  `competencia` tinyint(4) NOT NULL,
  `liberado` int(11) NOT NULL,
  `maximo` int(11) NOT NULL,
  `desempenho` decimal(10,0) NOT NULL,
  `peso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tesequi`
--

CREATE TABLE IF NOT EXISTS `tesequi` (
  `tesid` int(11) NOT NULL,
  `equid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tesque`
--

CREATE TABLE IF NOT EXISTS `tesque` (
  `tesid` int(11) NOT NULL DEFAULT '0',
  `queid` int(11) DEFAULT NULL,
  `texto` longblob,
  `tipo` char(1) DEFAULT '',
  `valor` decimal(4,2) DEFAULT NULL,
  `resposta` longblob,
`id` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=402 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `teste`
--

CREATE TABLE IF NOT EXISTS `teste` (
`id` int(11) NOT NULL,
  `texto` varchar(120) NOT NULL DEFAULT '',
  `data` date NOT NULL DEFAULT '0000-00-00',
  `tipo` char(1) DEFAULT NULL,
  `desarq` longtext,
  `status` int(1) DEFAULT NULL,
  `avaliacao` char(1) DEFAULT NULL,
  `disid` int(11) DEFAULT NULL,
  `datav` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=179 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `testearq`
--

CREATE TABLE IF NOT EXISTS `testearq` (
`id` int(11) NOT NULL,
  `aluid` varchar(30) DEFAULT NULL,
  `tesid` int(11) NOT NULL DEFAULT '0',
  `arquivo` varchar(120) NOT NULL DEFAULT '',
  `equid` int(11) DEFAULT NULL,
  `queid` int(11) DEFAULT NULL,
  `valor` decimal(4,2) DEFAULT NULL,
  `comentario` longtext
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=176 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `id` varchar(40) NOT NULL DEFAULT '',
  `email` varchar(40) NOT NULL DEFAULT '',
  `nome` varchar(60) NOT NULL DEFAULT '',
  `endereco` varchar(60) DEFAULT NULL,
  `cidade` varchar(40) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `uf` char(2) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `pais` varchar(20) DEFAULT NULL,
  `profissao` varchar(60) NOT NULL DEFAULT '',
  `senha` varchar(40) NOT NULL DEFAULT '',
  `datac` date NOT NULL DEFAULT '0000-00-00',
  `aluno` int(1) DEFAULT NULL,
  `professor` int(1) DEFAULT NULL,
  `foto` varchar(30) DEFAULT NULL,
  `experiencia` longtext,
  `educacao` longtext,
  `hobby` longtext,
  `idold` varchar(40) DEFAULT NULL,
  `cota` bigint(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usucur`
--

CREATE TABLE IF NOT EXISTS `usucur` (
  `usuid` varchar(40) NOT NULL DEFAULT '',
  `curid` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuend`
--

CREATE TABLE IF NOT EXISTS `usuend` (
  `usuid` varchar(40) NOT NULL DEFAULT '',
  `endid` int(11) NOT NULL DEFAULT '0',
  `ra` varchar(30) NOT NULL DEFAULT '',
  `ativo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usulinks`
--

CREATE TABLE IF NOT EXISTS `usulinks` (
`id` int(11) NOT NULL,
  `usuid` varchar(40) NOT NULL,
  `endereco` varchar(200) NOT NULL,
  `nome` varchar(40) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `webteca`
--

CREATE TABLE IF NOT EXISTS `webteca` (
`id` int(11) NOT NULL,
  `texto` varchar(90) NOT NULL DEFAULT '',
  `endereco` varchar(200) NOT NULL DEFAULT '',
  `usuid` varchar(40) DEFAULT NULL,
  `assid` int(11) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2193 ;

--
-- Índices de tabelas apagadas
--

--
-- Índices de tabela `acesso`
--
ALTER TABLE `acesso`
 ADD KEY `usuid` (`usuid`), ADD KEY `disid` (`disid`);

--
-- Índices de tabela `agenda`
--
ALTER TABLE `agenda`
 ADD PRIMARY KEY (`id`), ADD KEY `disid` (`disid`);

--
-- Índices de tabela `alternativa`
--
ALTER TABLE `alternativa`
 ADD PRIMARY KEY (`id`), ADD KEY `queid` (`queid`);

--
-- Índices de tabela `aluno`
--
ALTER TABLE `aluno`
 ADD PRIMARY KEY (`id`,`endid`), ADD KEY `usuid` (`usuid`), ADD KEY `endid` (`endid`);

--
-- Índices de tabela `assunto`
--
ALTER TABLE `assunto`
 ADD PRIMARY KEY (`id`), ADD KEY `usuid` (`usuid`);

--
-- Índices de tabela `atividade`
--
ALTER TABLE `atividade`
 ADD PRIMARY KEY (`id`), ADD KEY `planid` (`planid`), ADD KEY `avalid` (`avalid`);

--
-- Índices de tabela `avalequi`
--
ALTER TABLE `avalequi`
 ADD KEY `equid` (`equid`), ADD KEY `avalid` (`avalid`);

--
-- Índices de tabela `avaliacao`
--
ALTER TABLE `avaliacao`
 ADD PRIMARY KEY (`id`), ADD KEY `disid` (`disid`);

--
-- Índices de tabela `aviso`
--
ALTER TABLE `aviso`
 ADD PRIMARY KEY (`id`), ADD KEY `disid` (`disid`);

--
-- Índices de tabela `bibliografia`
--
ALTER TABLE `bibliografia`
 ADD PRIMARY KEY (`id`), ADD KEY `usuid` (`usuid`), ADD KEY `assid` (`assid`);

--
-- Índices de tabela `correcao`
--
ALTER TABLE `correcao`
 ADD PRIMARY KEY (`id`), ADD KEY `aluid` (`aluid`), ADD KEY `tesid` (`tesid`), ADD KEY `queid` (`queid`), ADD KEY `altid` (`altid`), ADD KEY `equid` (`equid`);

--
-- Índices de tabela `curso`
--
ALTER TABLE `curso`
 ADD PRIMARY KEY (`id`), ADD KEY `usuid` (`usuid`), ADD KEY `endid` (`endid`);

--
-- Índices de tabela `descritiva`
--
ALTER TABLE `descritiva`
 ADD PRIMARY KEY (`id`), ADD KEY `aluid` (`aluid`), ADD KEY `tesid` (`tesid`), ADD KEY `queid` (`queid`), ADD KEY `equid` (`equid`);

--
-- Índices de tabela `disalu`
--
ALTER TABLE `disalu`
 ADD UNIQUE KEY `disid_2` (`disid`,`aluid`), ADD KEY `disid` (`disid`), ADD KEY `aluid` (`aluid`);

--
-- Índices de tabela `disbib`
--
ALTER TABLE `disbib`
 ADD UNIQUE KEY `disid_2` (`disid`,`bibid`), ADD KEY `disid` (`disid`), ADD KEY `bibid` (`bibid`);

--
-- Índices de tabela `disciplina`
--
ALTER TABLE `disciplina`
 ADD PRIMARY KEY (`id`), ADD KEY `usuid` (`usuid`), ADD KEY `endid` (`endid`), ADD KEY `curid` (`curid`);

--
-- Índices de tabela `disead`
--
ALTER TABLE `disead`
 ADD PRIMARY KEY (`disid`,`eadid`), ADD KEY `disid` (`disid`), ADD KEY `eadid` (`eadid`);

--
-- Índices de tabela `dismat`
--
ALTER TABLE `dismat`
 ADD UNIQUE KEY `disid_2` (`disid`,`matid`), ADD KEY `disid` (`disid`), ADD KEY `matid` (`matid`);

--
-- Índices de tabela `distes`
--
ALTER TABLE `distes`
 ADD KEY `disid` (`disid`), ADD KEY `tesid` (`tesid`);

--
-- Índices de tabela `disusu`
--
ALTER TABLE `disusu`
 ADD PRIMARY KEY (`disid`,`usuid`), ADD KEY `disid` (`disid`), ADD KEY `usuid` (`usuid`);

--
-- Índices de tabela `disweb`
--
ALTER TABLE `disweb`
 ADD UNIQUE KEY `disid_2` (`disid`,`webid`), ADD KEY `disid` (`disid`), ADD KEY `webid` (`webid`);

--
-- Índices de tabela `ead`
--
ALTER TABLE `ead`
 ADD PRIMARY KEY (`id`), ADD KEY `usuid` (`usuid`), ADD KEY `eadid` (`eadid`), ADD KEY `assid` (`assid`);

--
-- Índices de tabela `enderecos`
--
ALTER TABLE `enderecos`
 ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `equialu`
--
ALTER TABLE `equialu`
 ADD PRIMARY KEY (`equid`,`aluid`), ADD KEY `equid` (`equid`), ADD KEY `aluid` (`aluid`);

--
-- Índices de tabela `equipes`
--
ALTER TABLE `equipes`
 ADD PRIMARY KEY (`id`), ADD KEY `disid` (`disid`);

--
-- Índices de tabela `forum`
--
ALTER TABLE `forum`
 ADD PRIMARY KEY (`id`), ADD KEY `forid` (`forid`), ADD KEY `disid` (`disid`), ADD KEY `usuid` (`usuid`), ADD KEY `planid` (`planid`);

--
-- Índices de tabela `frequencia`
--
ALTER TABLE `frequencia`
 ADD PRIMARY KEY (`id`), ADD KEY `aluid` (`aluid`), ADD KEY `planid` (`planid`);

--
-- Índices de tabela `material`
--
ALTER TABLE `material`
 ADD PRIMARY KEY (`id`), ADD KEY `usuid` (`usuid`), ADD KEY `assid` (`assid`);

--
-- Índices de tabela `mensagens`
--
ALTER TABLE `mensagens`
 ADD PRIMARY KEY (`id`), ADD KEY `disid` (`disid`), ADD KEY `usuid` (`usuid`);

--
-- Índices de tabela `menu`
--
ALTER TABLE `menu`
 ADD PRIMARY KEY (`id`), ADD KEY `disid` (`disid`);

--
-- Índices de tabela `notas`
--
ALTER TABLE `notas`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `cncid` (`avalid`,`aluid`), ADD KEY `avalid` (`avalid`), ADD KEY `aluid` (`aluid`);

--
-- Índices de tabela `planbloom`
--
ALTER TABLE `planbloom`
 ADD PRIMARY KEY (`planid`);

--
-- Índices de tabela `planead`
--
ALTER TABLE `planead`
 ADD UNIQUE KEY `planid_2` (`planid`,`eadid`), ADD KEY `planid` (`planid`), ADD KEY `eadid` (`eadid`);

--
-- Índices de tabela `planmat`
--
ALTER TABLE `planmat`
 ADD UNIQUE KEY `planid_2` (`planid`,`matid`), ADD KEY `planid` (`planid`), ADD KEY `matid` (`matid`);

--
-- Índices de tabela `plano`
--
ALTER TABLE `plano`
 ADD PRIMARY KEY (`id`), ADD KEY `disid` (`disid`);

--
-- Índices de tabela `planoensino`
--
ALTER TABLE `planoensino`
 ADD PRIMARY KEY (`disid`) USING BTREE;

--
-- Índices de tabela `plantes`
--
ALTER TABLE `plantes`
 ADD KEY `planid` (`planid`), ADD KEY `tesid` (`tesid`);

--
-- Índices de tabela `planweb`
--
ALTER TABLE `planweb`
 ADD UNIQUE KEY `planid_2` (`planid`,`webid`), ADD KEY `webid` (`webid`), ADD KEY `planid` (`planid`);

--
-- Índices de tabela `questoes`
--
ALTER TABLE `questoes`
 ADD PRIMARY KEY (`id`), ADD KEY `assid` (`assid`), ADD KEY `usuid` (`usuid`);

--
-- Índices de tabela `reunioes`
--
ALTER TABLE `reunioes`
 ADD PRIMARY KEY (`id`), ADD KEY `disid` (`disid`), ADD KEY `aluid` (`aluid`), ADD KEY `equid` (`equid`);

--
-- Índices de tabela `sessao`
--
ALTER TABLE `sessao`
 ADD PRIMARY KEY (`sessao`), ADD KEY `usuid` (`usuid`);

--
-- Índices de tabela `tesbloom`
--
ALTER TABLE `tesbloom`
 ADD KEY `tesid` (`tesid`);

--
-- Índices de tabela `tesequi`
--
ALTER TABLE `tesequi`
 ADD UNIQUE KEY `tesid_2` (`tesid`,`equid`), ADD KEY `tesid` (`tesid`), ADD KEY `equid` (`equid`);

--
-- Índices de tabela `tesque`
--
ALTER TABLE `tesque`
 ADD PRIMARY KEY (`id`), ADD KEY `tesid` (`tesid`), ADD KEY `queid` (`queid`);

--
-- Índices de tabela `teste`
--
ALTER TABLE `teste`
 ADD PRIMARY KEY (`id`), ADD KEY `disid` (`disid`);

--
-- Índices de tabela `testearq`
--
ALTER TABLE `testearq`
 ADD PRIMARY KEY (`id`), ADD KEY `queid` (`queid`), ADD KEY `aluid` (`aluid`), ADD KEY `tesid` (`tesid`), ADD KEY `equid` (`equid`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
 ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usucur`
--
ALTER TABLE `usucur`
 ADD PRIMARY KEY (`usuid`,`curid`), ADD KEY `usuid` (`usuid`), ADD KEY `curid` (`curid`);

--
-- Índices de tabela `usuend`
--
ALTER TABLE `usuend`
 ADD PRIMARY KEY (`usuid`,`endid`,`ra`), ADD KEY `endid` (`endid`), ADD KEY `usuid` (`usuid`);

--
-- Índices de tabela `usulinks`
--
ALTER TABLE `usulinks`
 ADD PRIMARY KEY (`id`), ADD KEY `usuid` (`usuid`);

--
-- Índices de tabela `webteca`
--
ALTER TABLE `webteca`
 ADD PRIMARY KEY (`id`), ADD KEY `usuid` (`usuid`), ADD KEY `assid` (`assid`);

--
-- AUTO_INCREMENT de tabelas apagadas
--

--
-- AUTO_INCREMENT de tabela `agenda`
--
ALTER TABLE `agenda`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1380;
--
-- AUTO_INCREMENT de tabela `alternativa`
--
ALTER TABLE `alternativa`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=86;
--
-- AUTO_INCREMENT de tabela `assunto`
--
ALTER TABLE `assunto`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=36;
--
-- AUTO_INCREMENT de tabela `atividade`
--
ALTER TABLE `atividade`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `avaliacao`
--
ALTER TABLE `avaliacao`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1688;
--
-- AUTO_INCREMENT de tabela `aviso`
--
ALTER TABLE `aviso`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=550;
--
-- AUTO_INCREMENT de tabela `bibliografia`
--
ALTER TABLE `bibliografia`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=834;
--
-- AUTO_INCREMENT de tabela `correcao`
--
ALTER TABLE `correcao`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de tabela `curso`
--
ALTER TABLE `curso`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=101;
--
-- AUTO_INCREMENT de tabela `descritiva`
--
ALTER TABLE `descritiva`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4642;
--
-- AUTO_INCREMENT de tabela `disciplina`
--
ALTER TABLE `disciplina`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=490;
--
-- AUTO_INCREMENT de tabela `ead`
--
ALTER TABLE `ead`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=68;
--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=46;
--
-- AUTO_INCREMENT de tabela `equipes`
--
ALTER TABLE `equipes`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1670;
--
-- AUTO_INCREMENT de tabela `forum`
--
ALTER TABLE `forum`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT de tabela `frequencia`
--
ALTER TABLE `frequencia`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=25180;
--
-- AUTO_INCREMENT de tabela `material`
--
ALTER TABLE `material`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=893;
--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
MODIFY `id` int(6) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=375;
--
-- AUTO_INCREMENT de tabela `menu`
--
ALTER TABLE `menu`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1300;
--
-- AUTO_INCREMENT de tabela `notas`
--
ALTER TABLE `notas`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=135145;
--
-- AUTO_INCREMENT de tabela `plano`
--
ALTER TABLE `plano`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3413;
--
-- AUTO_INCREMENT de tabela `questoes`
--
ALTER TABLE `questoes`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=420;
--
-- AUTO_INCREMENT de tabela `reunioes`
--
ALTER TABLE `reunioes`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=164;
--
-- AUTO_INCREMENT de tabela `tesque`
--
ALTER TABLE `tesque`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=402;
--
-- AUTO_INCREMENT de tabela `teste`
--
ALTER TABLE `teste`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=179;
--
-- AUTO_INCREMENT de tabela `testearq`
--
ALTER TABLE `testearq`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=176;
--
-- AUTO_INCREMENT de tabela `usulinks`
--
ALTER TABLE `usulinks`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT de tabela `webteca`
--
ALTER TABLE `webteca`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2193;
--
-- Restrições para dumps de tabelas
--

--
-- Restrições para tabelas `acesso`
--
ALTER TABLE `acesso`
ADD CONSTRAINT `acesso_ibfk_1` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `acesso_ibfk_2` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `agenda`
--
ALTER TABLE `agenda`
ADD CONSTRAINT `agenda_ibfk_1` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `aluno`
--
ALTER TABLE `aluno`
ADD CONSTRAINT `aluno_ibfk_2` FOREIGN KEY (`endid`) REFERENCES `enderecos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `aluno_ibfk_3` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `assunto`
--
ALTER TABLE `assunto`
ADD CONSTRAINT `assunto_ibfk_1` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `atividade`
--
ALTER TABLE `atividade`
ADD CONSTRAINT `atividade_ibfk_1` FOREIGN KEY (`planid`) REFERENCES `plano` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `atividade_ibfk_2` FOREIGN KEY (`avalid`) REFERENCES `avaliacao` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `avalequi`
--
ALTER TABLE `avalequi`
ADD CONSTRAINT `avalequi_ibfk_1` FOREIGN KEY (`avalid`) REFERENCES `avaliacao` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `avalequi_ibfk_2` FOREIGN KEY (`equid`) REFERENCES `equipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `avaliacao`
--
ALTER TABLE `avaliacao`
ADD CONSTRAINT `avaliacao_ibfk_1` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `aviso`
--
ALTER TABLE `aviso`
ADD CONSTRAINT `aviso_ibfk_1` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `bibliografia`
--
ALTER TABLE `bibliografia`
ADD CONSTRAINT `bibliografia_ibfk_1` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `bibliografia_ibfk_2` FOREIGN KEY (`assid`) REFERENCES `assunto` (`id`);

--
-- Restrições para tabelas `correcao`
--
ALTER TABLE `correcao`
ADD CONSTRAINT `correcao_ibfk_1` FOREIGN KEY (`aluid`) REFERENCES `aluno` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `correcao_ibfk_2` FOREIGN KEY (`tesid`) REFERENCES `teste` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `correcao_ibfk_3` FOREIGN KEY (`queid`) REFERENCES `questoes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `correcao_ibfk_4` FOREIGN KEY (`altid`) REFERENCES `alternativa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `correcao_ibfk_5` FOREIGN KEY (`equid`) REFERENCES `equipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `curso`
--
ALTER TABLE `curso`
ADD CONSTRAINT `curso_ibfk_1` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `curso_ibfk_2` FOREIGN KEY (`endid`) REFERENCES `enderecos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `descritiva`
--
ALTER TABLE `descritiva`
ADD CONSTRAINT `descritiva_ibfk_1` FOREIGN KEY (`aluid`) REFERENCES `aluno` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `descritiva_ibfk_2` FOREIGN KEY (`tesid`) REFERENCES `teste` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `descritiva_ibfk_3` FOREIGN KEY (`aluid`) REFERENCES `aluno` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `descritiva_ibfk_4` FOREIGN KEY (`tesid`) REFERENCES `teste` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `descritiva_ibfk_5` FOREIGN KEY (`queid`) REFERENCES `questoes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `descritiva_ibfk_6` FOREIGN KEY (`equid`) REFERENCES `equipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `disalu`
--
ALTER TABLE `disalu`
ADD CONSTRAINT `disalu_ibfk_1` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `disalu_ibfk_2` FOREIGN KEY (`aluid`) REFERENCES `aluno` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `disbib`
--
ALTER TABLE `disbib`
ADD CONSTRAINT `disbib_ibfk_1` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `disbib_ibfk_3` FOREIGN KEY (`bibid`) REFERENCES `bibliografia` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `disciplina`
--
ALTER TABLE `disciplina`
ADD CONSTRAINT `disciplina_ibfk_1` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `disciplina_ibfk_2` FOREIGN KEY (`endid`) REFERENCES `enderecos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `disciplina_ibfk_3` FOREIGN KEY (`curid`) REFERENCES `curso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `disead`
--
ALTER TABLE `disead`
ADD CONSTRAINT `disead_ibfk_1` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `disead_ibfk_2` FOREIGN KEY (`eadid`) REFERENCES `ead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `dismat`
--
ALTER TABLE `dismat`
ADD CONSTRAINT `dismat_ibfk_1` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `dismat_ibfk_2` FOREIGN KEY (`matid`) REFERENCES `material` (`id`);

--
-- Restrições para tabelas `distes`
--
ALTER TABLE `distes`
ADD CONSTRAINT `distes_ibfk_1` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `distes_ibfk_2` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `distes_ibfk_3` FOREIGN KEY (`tesid`) REFERENCES `teste` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `disusu`
--
ALTER TABLE `disusu`
ADD CONSTRAINT `disusu_ibfk_1` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `disusu_ibfk_2` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `disweb`
--
ALTER TABLE `disweb`
ADD CONSTRAINT `disweb_ibfk_1` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `disweb_ibfk_2` FOREIGN KEY (`webid`) REFERENCES `webteca` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `ead`
--
ALTER TABLE `ead`
ADD CONSTRAINT `ead_ibfk_1` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `ead_ibfk_2` FOREIGN KEY (`eadid`) REFERENCES `ead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `ead_ibfk_3` FOREIGN KEY (`assid`) REFERENCES `assunto` (`id`);

--
-- Restrições para tabelas `equialu`
--
ALTER TABLE `equialu`
ADD CONSTRAINT `equialu_ibfk_1` FOREIGN KEY (`equid`) REFERENCES `equipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `equialu_ibfk_2` FOREIGN KEY (`aluid`) REFERENCES `aluno` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `equipes`
--
ALTER TABLE `equipes`
ADD CONSTRAINT `equipes_ibfk_1` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `forum`
--
ALTER TABLE `forum`
ADD CONSTRAINT `forum_ibfk_1` FOREIGN KEY (`forid`) REFERENCES `forum` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `forum_ibfk_2` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `forum_ibfk_3` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `forum_ibfk_4` FOREIGN KEY (`planid`) REFERENCES `plano` (`id`);

--
-- Restrições para tabelas `frequencia`
--
ALTER TABLE `frequencia`
ADD CONSTRAINT `frequencia_ibfk_1` FOREIGN KEY (`planid`) REFERENCES `plano` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `frequencia_ibfk_2` FOREIGN KEY (`aluid`) REFERENCES `aluno` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `material`
--
ALTER TABLE `material`
ADD CONSTRAINT `material_ibfk_1` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `material_ibfk_2` FOREIGN KEY (`assid`) REFERENCES `assunto` (`id`);

--
-- Restrições para tabelas `mensagens`
--
ALTER TABLE `mensagens`
ADD CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `menu`
--
ALTER TABLE `menu`
ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `notas`
--
ALTER TABLE `notas`
ADD CONSTRAINT `notas_ibfk_1` FOREIGN KEY (`avalid`) REFERENCES `avaliacao` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `notas_ibfk_2` FOREIGN KEY (`aluid`) REFERENCES `aluno` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `planbloom`
--
ALTER TABLE `planbloom`
ADD CONSTRAINT `planbloom_ibfk_1` FOREIGN KEY (`planid`) REFERENCES `plano` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `planead`
--
ALTER TABLE `planead`
ADD CONSTRAINT `planead_ibfk_1` FOREIGN KEY (`planid`) REFERENCES `plano` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `planead_ibfk_2` FOREIGN KEY (`eadid`) REFERENCES `ead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `planmat`
--
ALTER TABLE `planmat`
ADD CONSTRAINT `planmat_ibfk_1` FOREIGN KEY (`planid`) REFERENCES `plano` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `planmat_ibfk_2` FOREIGN KEY (`matid`) REFERENCES `material` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `plano`
--
ALTER TABLE `plano`
ADD CONSTRAINT `plano_ibfk_1` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `planoensino`
--
ALTER TABLE `planoensino`
ADD CONSTRAINT `planoensino_ibfk_1` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `plantes`
--
ALTER TABLE `plantes`
ADD CONSTRAINT `plantes_ibfk_1` FOREIGN KEY (`planid`) REFERENCES `plano` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `plantes_ibfk_2` FOREIGN KEY (`tesid`) REFERENCES `teste` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `planweb`
--
ALTER TABLE `planweb`
ADD CONSTRAINT `planweb_ibfk_1` FOREIGN KEY (`planid`) REFERENCES `plano` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `planweb_ibfk_2` FOREIGN KEY (`webid`) REFERENCES `webteca` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `questoes`
--
ALTER TABLE `questoes`
ADD CONSTRAINT `questoes_ibfk_1` FOREIGN KEY (`assid`) REFERENCES `assunto` (`id`),
ADD CONSTRAINT `questoes_ibfk_2` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `reunioes`
--
ALTER TABLE `reunioes`
ADD CONSTRAINT `reunioes_ibfk_1` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `reunioes_ibfk_2` FOREIGN KEY (`aluid`) REFERENCES `aluno` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `reunioes_ibfk_3` FOREIGN KEY (`equid`) REFERENCES `equipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `sessao`
--
ALTER TABLE `sessao`
ADD CONSTRAINT `sessao_ibfk_1` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `tesbloom`
--
ALTER TABLE `tesbloom`
ADD CONSTRAINT `tesbloom_ibfk_1` FOREIGN KEY (`tesid`) REFERENCES `teste` (`id`);

--
-- Restrições para tabelas `tesequi`
--
ALTER TABLE `tesequi`
ADD CONSTRAINT `tesequi_ibfk_1` FOREIGN KEY (`tesid`) REFERENCES `teste` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `tesequi_ibfk_2` FOREIGN KEY (`equid`) REFERENCES `equipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `tesque`
--
ALTER TABLE `tesque`
ADD CONSTRAINT `tesque_ibfk_1` FOREIGN KEY (`tesid`) REFERENCES `teste` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `tesque_ibfk_2` FOREIGN KEY (`queid`) REFERENCES `questoes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `teste`
--
ALTER TABLE `teste`
ADD CONSTRAINT `teste_ibfk_1` FOREIGN KEY (`disid`) REFERENCES `disciplina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `testearq`
--
ALTER TABLE `testearq`
ADD CONSTRAINT `testearq_ibfk_1` FOREIGN KEY (`queid`) REFERENCES `questoes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `testearq_ibfk_2` FOREIGN KEY (`aluid`) REFERENCES `aluno` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `testearq_ibfk_3` FOREIGN KEY (`tesid`) REFERENCES `teste` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `testearq_ibfk_4` FOREIGN KEY (`equid`) REFERENCES `equipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `usucur`
--
ALTER TABLE `usucur`
ADD CONSTRAINT `usucur_ibfk_1` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `usucur_ibfk_2` FOREIGN KEY (`curid`) REFERENCES `curso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `usuend`
--
ALTER TABLE `usuend`
ADD CONSTRAINT `usuend_ibfk_1` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `usulinks`
--
ALTER TABLE `usulinks`
ADD CONSTRAINT `usulinks_ibfk_1` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `usulinks_ibfk_2` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `webteca`
--
ALTER TABLE `webteca`
ADD CONSTRAINT `webteca_ibfk_1` FOREIGN KEY (`usuid`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `webteca_ibfk_2` FOREIGN KEY (`assid`) REFERENCES `assunto` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
