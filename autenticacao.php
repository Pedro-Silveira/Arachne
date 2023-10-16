<?php
	include('classes/connection.php');

	if(isset($_POST['saram']) || isset($_POST['senha'])){
		if(strlen($_POST['saram']) == 0){
			$resultado = '<div class="alert alert-danger mt-5">O <b>saram</b> não foi preenchido!</div>';
		}else if(strlen($_POST['senha']) == 0){
			$resultado = '<div class="alert alert-danger mt-5">A <b>senha</b> não foi preenchida!</div>';
		}else{
			$saram = $mysqli->real_escape_string($_POST['saram']);
			$senha = $mysqli->real_escape_string($_POST['senha']);

			$query = $mysqli->query("SELECT * FROM efetivo WHERE saram = '$saram' AND senha = md5('$senha')") or die("Falha na execução do código SQL: ".$mysqli->error);
			$usuario = $query->fetch_assoc();
			$quantidade = $query->num_rows;

			if($quantidade == 1){
				if($usuario['permissao'] != "Usuário"){
					if(!isset($_SESSION)){
						session_start();
					}
				
					$_SESSION['saram'] = $usuario['saram'];
					$_SESSION['posto'] = $usuario['posto'];
					$_SESSION['nome'] = $usuario['nome'];
					$_SESSION['permissao'] = $usuario['permissao'];

					header("Location: index.php");
				}else{
					$resultado = '<div class="alert alert-danger mt-5">Você não tem permissão de acesso!</div>';
				}
			}else{
				$resultado = '<div class="alert alert-danger mt-5"><b>Saram</b> ou <b>senha</b> estão incorretos!</div>';
			}
		}
	}
?>
<html>
	<head>
		<style>
			input::-ms-reveal, input::-ms-clear{
				display: none;
			}
		</style>
		<meta charset="utf-8">
		<link rel="icon" type="image/x-icon" href="images/favicon.ico">
		<script src="js/jquery.min.js"></script>
		<link href="css/tom-select.css" rel="stylesheet"/>
		<script src="js/jquery.slim.min.js"></script>
		<script src="js/tom-select.complete.js"></script>
		<link href="css/tom-select.bootstrap5.css" rel="stylesheet"/>
		<script src="js/popper.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<link href="css/bootstrap.min.css" rel="stylesheet"/>
		<title>Arachne - Autenticação</title>
	</head>
	<body class="d-flex align-items-center justify-content-center text-center w-100 h-100" style="background-color: #f5f5f5;">
		<main class="form-signin">
			<img src="images/logo-preto.png" alt="" width="300" height="65">
			<h1 class="h6 mb-5">2°/1° Grupo de Comunicações e Controle</h1>
			<form action="" method="POST">
				<div class="form-floating">
					<input type="text" class="form-control" style="margin-bottom: -1px; border-bottom-right-radius: 0; border-bottom-left-radius: 0;" name="saram" placeholder="Saram" required>
					<label for="saram">Digite seu saram...</label>
				</div>
				<div class="form-floating">
					<input type="password" class="form-control" style="border-top-left-radius: 0; border-top-right-radius: 0;" name="senha" placeholder="Senha" required>
					<label for="senha">Digite sua senha...</label>
				</div>
				<button class="btn btn-lg btn-dark w-100 mt-3" type="submit">Entrar</button>
				<p class="text-muted mt-5">2°/1° GCC &copy; 2022</p>
			</form>
			<?php if(isset($resultado)){echo "$resultado";} ?>
		</main>
	</body>
</html>