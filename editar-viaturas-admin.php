<?php
	include('classes/protect.php');
	include('classes/functions-admin.php');
	
	if($_SESSION["permissao"] != 'Administrador'){
		header('Location: index.php');
	}

	$queryViatura = $mysqli->query("SELECT * FROM viaturas WHERE registro = '".$_GET['registro']."'") or die("Falha na execução do código SQL: ".$mysqli->error);
	$editar = $queryViatura->fetch_assoc();
	$retorno = $queryViatura->num_rows;
	
	if($retorno == 0){
		header('Location: administrator.php');
	}

	if(isset($_POST['modelo'])){
		if(strlen($_POST['modelo']) == 0){
			$resultado = '<div class="alert alert-danger">O <b>modelo</b> não foi preenchido!</div>';
		}else{
			$modelo = $mysqli->real_escape_string($_POST['modelo']);

			if(mysqli_query($mysqli, "UPDATE viaturas SET modelo = '$modelo' WHERE registro = '".$_GET['registro']."'")){
				header('Location: editar-viaturas-admin.php?registro='.$_GET['registro'].'&flag=1');
			}else{
				$resultado = '<div class="alert alert-danger">Houve um erro ao editar a viatura!</div>';
			}
		}
	}else if(isset($_POST['viatura'])){
		if(mysqli_query($mysqli, "DELETE FROM viaturas WHERE registro = '".$_POST['viatura']."'")) {
			$resultado = '<div class="alert alert-success">Viatura excluída com sucesso!</div>';
		}else{
			$resultado = '<div class="alert alert-danger">Houve um erro ao excluir a viatura!</div>';
		}
	}else if(isset($_POST['sv'])){
		if(strlen($_POST['dataSV']) == 0){
			$resultado = '<div class="alert alert-danger">A <b>data</b> não foi preenchida!</div>';
		}else{
			$data = $mysqli->real_escape_string($_POST['dataSV']);
			
			$queryServico = $mysqli->query("SELECT * FROM servicos WHERE data = '$data'") or die("Falha na execução do código SQL: ".$mysqli->error);
			$retorno = $queryServico->num_rows;
			
			if($retorno == 0){
				$resultado = '<div class="alert alert-danger">Não existe um serviço registrado nesta data!</div>';
			}else{
				header('Location: servicos-admin.php?data='.$data);
			}
		}
	}else if(isset($_POST['btnLimpar'])){
		if(limpar() != 1){
			$resultado = '<div class="alert alert-success">As fichas foram deletadas com sucesso!</div>';
		}else{
			$resultado = '<div class="alert alert-danger">Não foi possível deletar as fichas!</div>';
		}
	}else if(isset($_POST['sair'])){
		logout();
	}
?>
<html>
	<head>
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
		<title>Arachne - Editar Viatura</title>
	</head>
	<body style="background-image: url('images/background.jpg'); background-repeat: repeat; background-attachment: fixed; background-position: center top; background-size: 100% auto;" class="d-flex flex-column min-vh-100">
		<header>
			<div class="modal fade" id="limpar" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title"><b>Atenção:</b></h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<p class="m-0">Tem certeza que deseja limpar as fichas a partir de <b><?php echo (date("Y") - 5); ?></b>?</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-dark" data-bs-dismiss="modal">Não</button>
							<form class="m-0 p-0" action="" method="POST"><button id="btnLimpar" name="btnLimpar" type="submit" class="btn btn-primary">Sim</button></form>
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" id="servicos" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title"><b>Editar Serviço:</b></h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<form id="formSV" class="mb-0" action="" method="POST">
								<div class="row">
									<div class="col">
										<input id="dataSV" name="dataSV" type="date" class="form-control mb-0" value="<?php echo date("Y-m-d"); ?>" required>
									</div>
								</div>
							</form>
						</div>
						<div class="modal-footer">
							<button id="sv" name="sv" form="formSV" type="submit" class="btn btn-primary">Editar</button>
						</div>
					</div>
				</div>
			</div>
			<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
				<div class="container-fluid">
					<a class="navbar-brand" href="administrator.php"><img src="images/logo-branco.png" alt="" width="160" height="32"></a>
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
						<ul class="navbar-nav me-auto mb-2 mb-lg-0">
							<li class="nav-item">
								<a class="nav-link" href="administrator.php">Início</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="chaves-admin.php">Chaves</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="efetivo-admin.php">Efetivo</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="viaturas-admin.php">Viaturas</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#servicos">Serviços</a>
							</li>
						</ul>
						<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $_SESSION["posto"].' '.$_SESSION['nome'] ?> (Administrador)</a>
								<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
									<li><form class="m-0 p-0" action="" method="POST"><button id="sair" name="sair" type="submit" class="dropdown-item">Sair</button></form></li>
									<li><hr class="dropdown-divider"></li>
									<li><button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#limpar">Limpar Fichas</button></li>
									<li><hr class="dropdown-divider"></li>
									<li><a class="dropdown-item" href="index.php">Modo Usuário</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</nav>
		</header>
		<main>
			<div class="container-fluid p-3 pb-0">
				<div class="row p-0">
					<div class="col m-0">
						<div class="bg-dark p-2 text-white">
							<span class="h6">Editar Viatura</span>
						</div>
						<form class="border mb-3 pt-0 p-3" style="background-color: #f5f5f5;" action="" method="POST">
							<div class="row">
								<div class="col">
									<label class="mt-2" for="registro">Registro:</label>
									<input id="registro" name="registro" type="text" class="form-control" value="<?php echo $editar['registro']; ?>" disabled required>
								</div>
								<div class="col">
									<label class="mt-2" for="modelo">Modelo:</label>
									<input id="modelo" name="modelo" type="text" class="form-control" value="<?php echo $editar['modelo']; ?>" required>
								</div>
							</div>
							<div class="row mt-3">
								<div class="col">
									<button id="limpar" type="reset" class="w-100 btn btn-dark">Limpar</button>
								</div>
								<div class="col">
									<button id="editar" name="editar" type="submit" class="w-100 btn btn-primary">Editar</button>
								</div>
							</div>
						</form>
						<?php if(isset($resultado)){echo "$resultado";}elseif(isset($_GET['flag'])){echo '<div class="alert alert-success">Viatura editada com sucesso!</div>';} ?>
					</div>
					<div class="col m-0">
						<div class="bg-dark p-2 text-white">
							<span class="h6">Listagem das Viaturas</span>
							<span class="badge bg-primary m-0" style="float: right;"><?php $query = $mysqli->query("SELECT * FROM viaturas"); $quantidadeViaturas = $query->num_rows; echo "$quantidadeViaturas"; ?></span>
						</div>
						<div id="fichaVisitantes" class="collapse show">
							<div class="table-responsive mb-3">
								<table class="table table-hover table-bordered text-center align-middle my-0">
									<thead>
										<tr class="cinza">
											<th style="background-color: #f5f5f5;" scope="col">Registro</th>
											<th style="background-color: #f5f5f5;" scope="col">Modelo</th>
											<th class="table-secondary" scope="col">Ações</th>
										</tr>
									</thead>
									<tbody>
										<?php
											if($result = $mysqli->query("SELECT * FROM viaturas ORDER BY registro;")){
												while($row = $result->fetch_assoc()){													
													echo '<div class="modal fade" id="viatura-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["registro"])).'" tabindex="-1" aria-hidden="true">
														<div class="modal-dialog">
															<div class="modal-content">
																<div class="modal-header">
																	<h5 class="modal-title"><b>Atenção:</b></h5>
																	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
																</div>
																<div class="modal-body">
																	<p>Tem certeza que deseja excluir a seguinte viatura:</p>
																	<p><b>'.$row["registro"].' - '.$row["modelo"].'?</b></p>
																</div>
																<div class="modal-footer">
																	<button type="button" class="btn btn-dark" data-bs-dismiss="modal">Não</button>
																	<form action="" method="POST"><input type="hidden" id="viatura" name="viatura" value="'.$row["registro"].'"><button type="submit" class="btn btn-primary">Sim</button></form>
																</div>
															</div>
														</div>
													</div>
													<tr>
														<th style="background-color: #fff;" scope="row">'.$row["registro"].'</th>
														<td style="background-color: #fff;">'.$row["modelo"].'</td>
														<td class="table-light"><button type="button" class="btn m-0 p-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#viatura-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["registro"])).'"><img src="images/delete.png" alt="" width="16" height="16"></button></td>
													</tr>';
												}
											}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<footer class="navbar navbar-dark mt-auto py-0 px-3 bg-dark text-white-50">
			<ul class="navbar-nav me-auto">
				<span>2°/1° GCC &copy; 2022</span>
			</ul>
			<ul class="navbar-nav ms-auto">
				<a class="nav-link" href="https://github.com/pedro-silveira" target="_blank">Desenvolvido por S2 Silveira</a>
			</ul>
		</footer>
	</body>
</html>
