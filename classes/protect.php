<?php
	if(!isset($_SESSION)){
		session_start();
	}

	if(!isset($_SESSION['saram']) || !isset($_SESSION['posto']) || !isset($_SESSION['nome']) || !isset($_SESSION['permissao'])){
		header("Location: autenticacao.php");
	}
?>