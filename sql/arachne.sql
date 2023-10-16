--
-- Estrutura da tabela `chaves`
--

DROP TABLE IF EXISTS `chaves`;
CREATE TABLE IF NOT EXISTS `chaves` (
  `numero` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nome` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `efetivo`
--

DROP TABLE IF EXISTS `efetivo`;
CREATE TABLE IF NOT EXISTS `efetivo` (
  `saram` varchar(21) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `senha` varchar(96) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `posto` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `nome` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `permissao` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`saram`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `servicos`
--

DROP TABLE IF EXISTS `servicos`;
CREATE TABLE IF NOT EXISTS `servicos` (
  `data` varchar(30) NOT NULL,
  `sargento` varchar(60) DEFAULT NULL,
  `motorista` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`data`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `svchaves`
--

DROP TABLE IF EXISTS `svchaves`;
CREATE TABLE IF NOT EXISTS `svchaves` (
  `numero` varchar(15) DEFAULT NULL,
  `chave` varchar(150) DEFAULT NULL,
  `dataRetirada` varchar(30) DEFAULT NULL,
  `horarioRetirada` varchar(25) DEFAULT NULL,
  `nomeRetirada` varchar(60) DEFAULT NULL,
  `permanenciaRetirada` varchar(60) DEFAULT NULL,
  `dataDevolucao` varchar(30) DEFAULT NULL,
  `horarioDevolucao` varchar(25) DEFAULT NULL,
  `nomeDevolucao` varchar(60) DEFAULT NULL,
  `permanenciaDevolucao` varchar(60) DEFAULT NULL,
  `editado` varchar(150) DEFAULT NULL,
  `editado2` varchar(150) DEFAULT NULL,
  `servico` varchar(30) DEFAULT NULL,
  `servico2` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `svviaturas`
--

DROP TABLE IF EXISTS `svviaturas`;
CREATE TABLE IF NOT EXISTS `svviaturas` (
  `registro` varchar(25) DEFAULT NULL,
  `modelo` varchar(25) DEFAULT NULL,
  `motoristaSaida` varchar(60) DEFAULT NULL,
  `destino` varchar(100) DEFAULT NULL,
  `dataSaida` varchar(30) DEFAULT NULL,
  `horarioSaida` varchar(25) DEFAULT NULL,
  `hodometroSaida` varchar(20) DEFAULT NULL,
  `permanenciaSaida` varchar(60) DEFAULT NULL,
  `motoristaRetorno` varchar(60) DEFAULT NULL,
  `dataRetorno` varchar(30) DEFAULT NULL,
  `horarioRetorno` varchar(25) DEFAULT NULL,
  `hodometroRetorno` varchar(20) DEFAULT NULL,
  `permanenciaRetorno` varchar(60) DEFAULT NULL,
  `editado` varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `editado2` varchar(150) DEFAULT NULL,
  `servico` varchar(30) DEFAULT NULL,
  `servico2` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `svvisitantes`
--

DROP TABLE IF EXISTS `svvisitantes`;
CREATE TABLE IF NOT EXISTS `svvisitantes` (
  `documento` varchar(35) NOT NULL DEFAULT '',
  `cracha` varchar(6) DEFAULT NULL,
  `nome` varchar(150) DEFAULT NULL,
  `empresa` varchar(100) DEFAULT NULL,
  `dataEntrada` varchar(30) DEFAULT NULL,
  `horarioEntrada` varchar(25) DEFAULT NULL,
  `responsavel` varchar(60) DEFAULT NULL,
  `permanenciaEntrada` varchar(60) DEFAULT NULL,
  `dataSaida` varchar(30) DEFAULT NULL,
  `horarioSaida` varchar(25) DEFAULT NULL,
  `permanenciaSaida` varchar(60) DEFAULT NULL,
  `editado` varchar(150) DEFAULT NULL,
  `editado2` varchar(150) DEFAULT NULL,
  `servico` varchar(30) DEFAULT NULL,
  `servico2` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `viaturas`
--

DROP TABLE IF EXISTS `viaturas`;
CREATE TABLE IF NOT EXISTS `viaturas` (
  `registro` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `modelo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`registro`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;