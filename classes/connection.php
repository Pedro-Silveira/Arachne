<?php
	$host = '';
	$usuario = '';
	$senha = '';
	$banco = 'arachne';

	$mysqli = new mysqli($host, $usuario, $senha, $banco);
	$mysqli->set_charset('utf8');
	
	if($mysqli->error){
		die("Falha ao conectar ao banco de dados: ".$mysqli->error);
	}
?>