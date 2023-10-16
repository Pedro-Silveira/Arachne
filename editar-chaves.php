<?php
	include('classes/protect.php');
	include('classes/functions.php');
	
	if($_SESSION["permissao"] != 'Sentinela' && $_SESSION["permissao"] != 'Sargento de Dia' && $_SESSION["permissao"] != 'Administrador'){
		header('Location: autenticacao.php');
	}

	$queryChave = $mysqli->query("SELECT * FROM svChaves WHERE numero = '".$_GET['numero']."' and dataRetirada = '".$_GET['data']."' and horarioRetirada = '".$_GET['hora']."'") or die("Falha na execução do código SQL: ".$mysqli->error);
	$editar = $queryChave->fetch_assoc();
	$retorno = $queryChave->num_rows;
	
	if($retorno == 0){
		header('Location: index.php');
	}

	if(isset($_POST['data2']) || isset($_POST['hora2']) || isset($_POST['nome2']) || isset($_POST['assinatura2']) || isset($_POST['registrar']) || isset($_POST['registrar2'])){
		if(isset($_POST['registrar'])){
			$resultado = '<div class="alert alert-danger">O registro de retirada já foi assinado e não pode ser editado!</div>';
		}else if(isset($_POST['registrar2'])){
			if($editar['dataDevolucao'] == null){
				if(strlen($_POST['data2']) == 0){
					$resultado2 = '<div class="alert alert-danger">A <b>data de devolução</b> não foi preenchida!</div>';
				}else if(strlen($_POST['hora2']) == 0){
					$resultado2 = '<div class="alert alert-danger">A <b>hora de devolução</b> não foi preenchida!</div>';
				}else{
					$data2 = $mysqli->real_escape_string($_POST['data2']);
					$hora2 = $mysqli->real_escape_string($_POST['hora2']);

					if(strlen($_POST['nome2']) == 0){
						$resultado2 = '<div class="alert alert-danger">O <b>nome</b> não foi selecionado!</div>';
					}else if(strlen($_POST['assinatura2']) == 0){
						$resultado2 = '<div class="alert alert-danger">A <b>assinatura</b> não foi preenchida!</div>';
					}else{
						$nome = $mysqli->real_escape_string($_POST['nome2']);
						$assinatura = $mysqli->real_escape_string($_POST['assinatura2']);

						$verificarAssinatura = $mysqli->query("SELECT senha FROM efetivo WHERE posto = '".strtok($nome, ' ')."' AND nome = '".substr($nome, strpos($nome, ' ') + 1)."'") or die("Falha na execução do código SQL: ".$mysqli->error);
						$comparar = $verificarAssinatura->fetch_assoc();

						if($comparar['senha'] != md5($assinatura)){
							$resultado2 = '<div class="alert alert-danger">A <b>assinatura</b> não está correta!</div>';
						}else if($editar['dataRetirada'] > $data2 || ($editar['dataRetirada'] == $data2 && $editar['horarioRetirada'] > $hora2)){
							$resultado2 = '<div class="alert alert-danger">A devolução é anterior à retirada!</div>';
						}else{
							if(isset($editar['servico'])){
								if(mysqli_query($mysqli, "UPDATE svChaves SET dataDevolucao = '$data2', horarioDevolucao = '$hora2', nomeDevolucao = '$nome', permanenciaDevolucao = '".$_SESSION['posto'].' '.$_SESSION['nome']."', servico2 = '$data2' WHERE numero = '".$_GET['numero']."' and dataRetirada = '".$_GET['data']."' and horarioRetirada = '".$_GET['hora']."'")){
									header('Location: editar-chaves.php?numero='.$_GET['numero'].'&data='.$_GET['data'].'&hora='.$_GET['hora'].'&flag=3');
								}else{
									$resultado2 = '<div class="alert alert-danger">Houve um erro ao registrar a devolução de chave!</div>';
								}
							}else{
								if(mysqli_query($mysqli, "UPDATE svChaves SET dataDevolucao = '$data2', horarioDevolucao = '$hora2', nomeDevolucao = '$nome', permanenciaDevolucao = '".$_SESSION['posto'].' '.$_SESSION['nome']."' WHERE numero = '".$_GET['numero']."' and dataRetirada = '".$_GET['data']."' and horarioRetirada = '".$_GET['hora']."'")){
									header('Location: editar-chaves.php?numero='.$_GET['numero'].'&data='.$_GET['data'].'&hora='.$_GET['hora'].'&flag=3');
								}else{
									$resultado2 = '<div class="alert alert-danger">Houve um erro ao registrar a devolução de chave!</div>';
								}
							}
						}
					}
				}
			}else{
				$resultado2 = '<div class="alert alert-danger">O registro de devolução já foi assinado e não pode ser editado!</div>';
			}
		}
	}else if(isset($_POST['passar'])){
		if(passar($_POST['dataSV'], $_POST['motorista']) != 1){
			$resultado = '<div class="alert alert-success">O serviço foi encerrado com sucesso!</div>';
		}else{
			$resultado = '<div class="alert alert-danger">Não foi possível encerrar o serviço!</div>';
		}
	}else if(isset($_POST['pdf'])){
		if(strlen($_POST['dataPDF']) == 0){
			$resultado = '<div class="alert alert-danger">A <b>data</b> não foi preenchida!</div>';
		}else{
			$data = $mysqli->real_escape_string($_POST['dataPDF']);
			
			$queryData = $mysqli->query("SELECT * FROM servicos WHERE data = '$data'") or die("Falha na execução do código SQL: ".$mysqli->error);
			$editar = $queryData->fetch_assoc();
			$retorno = $queryData->num_rows;
			
			pdf($editar, $retorno, $data);
		}
	}else if(isset($_POST['sair'])){
		logout();
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
		<title>Arachne - Editar Registro de Chave</title>
	</head>
	<body style="background-image: url('images/background.jpg'); background-repeat: repeat; background-attachment: fixed; background-position: center top; background-size: 100% auto;" class="d-flex flex-column min-vh-100">
		<header>
			<div class="modal fade" id="servico" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title"><b>Passar Serviço:</b></h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<form id="formPassar" class="mb-0" action="" method="POST">
								<div class="row">
									<div class="col">
										<input id="dataSV" name="dataSV" type="date" class="form-control mb-3" value="<?php echo date("Y-m-d"); ?>" required>
									</div>
								</div>
								<div class="row">
									<div class="col">
										<select id="motorista" name="motorista" autocomplete="off" required>
											<option selected disabled value="">Selecione o motorista...</option>
											<?php
												if($result = $mysqli->query("SELECT * FROM efetivo WHERE saram <> 'Administrador' ORDER BY nome")){
													while($row = $result->fetch_assoc()){
														echo '<option value="'.$row["posto"].' '.$row["nome"].'">'.$row["posto"].' '.$row["nome"].'</option>';
													}
												}
											?>
										</select>
									</div>
								</div>
							</form>
						</div>
						<div class="modal-footer">
							<button id="passar" name="passar" form="formPassar" type="submit" class="btn btn-primary">Confirmar</button>
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" id="consultar" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title"><b>Consultar Serviço:</b></h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<form id="formConsulta" class="mb-0" action="" method="POST" target="_blank">
								<div class="row">
									<div class="col">
										<input id="dataPDF" name="dataPDF" type="date" class="form-control mb-0" value="<?php echo date("Y-m-d"); ?>" required>
									</div>
								</div>
							</form>
						</div>
						<div class="modal-footer">
							<button id="pdf" name="pdf" form="formConsulta" type="submit" class="btn btn-primary">Gerar PDF</button>
						</div>
					</div>
				</div>
			</div>
			<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
				<div class="container-fluid">
					<a class="navbar-brand" href="index.php"><img src="images/logo-branco.png" alt="" width="160" height="32"></a>
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
						<ul class="navbar-nav me-auto mb-2 mb-lg-0">
							<li class="nav-item">
								<a class="nav-link" href="index.php">Início</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="chaves.php">Chaves</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="viaturas.php">Viaturas</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="visitantes.php">Visitantes</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#consultar">Consultar</a>
							</li>
						</ul>
						<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $_SESSION["posto"].' '.$_SESSION['nome'] ?></a>
								<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
									<li><form class="m-0 p-0" action="" method="POST"><button id="sair" name="sair" type="submit" class="dropdown-item">Sair</button></form></li>
									<?php
										if($_SESSION['permissao'] == "Sargento de Dia"){
											echo '<li>
												<hr class="dropdown-divider">
											</li>
											<li>
												<button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#servico">Passar Serviço</button>
											</li>';
										}else if($_SESSION['permissao'] == "Administrador"){
											if($_SESSION['posto'] == "3S" || $_SESSION['posto'] == "2S" || $_SESSION['posto'] == "1S" || $_SESSION['posto'] == "SO"){
												echo '<li>
													<hr class="dropdown-divider">
												</li>
												<li>
													<button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#servico">Passar Serviço</button>
												</li>';
											}
											
											echo '<li>
												<hr class="dropdown-divider">
											</li>
											<li>
												<a class="dropdown-item" href="administrator.php">Modo Administrador</a>
											</li>';
										}
									?>
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
							<span class="h6">Editar Retirada de Chave</span><?php if(isset($editar['editado'])){echo '<span class="h6 fw-light" style="color: #909294;"> - editado por '.$editar['editado'].'</span>';} ?>
						</div>
						<form class="border mb-3 pt-0 p-3" style="background-color: #f5f5f5;" action="" method="POST">
							<div class="row">
								<div class="col">
									<label class="mt-2" for="chave">Chave:</label>
									<select id="chave" name="chave" autocomplete="off" disabled required>
										<?php
											if($result = $mysqli->query("SELECT * FROM chaves WHERE numero = '".$editar['numero']."'")){
												while($row = $result->fetch_assoc()){
													echo '<option selected value="'.$row["numero"].' - '.$row["nome"].'">'.$row["numero"].' - '.$row["nome"].'</option>';
												}
											}
										?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="data">Data:</label>
									<input id="data" name="data" type="date" class="form-control" value="<?php echo $editar['dataRetirada']; ?>" disabled required>
								</div>
								<div class="col">
									<label class="mt-2" for="hora">Horário:</label>
									<input step="1" id="hora" name="hora" type="time" class="form-control" value="<?php echo $editar['horarioRetirada']; ?>" disabled required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="nome">Nome:</label>
									<select id="nome" name="nome" autocomplete="off" disabled required>
										<?php
											if($result = $mysqli->query("SELECT * FROM efetivo WHERE posto = '".strtok($editar['nomeRetirada'],  ' ')."' AND nome = '".substr($editar['nomeRetirada'], strpos($editar['nomeRetirada'], ' ') + 1)."'")){
												while($row = $result->fetch_assoc()){
													echo '<option selected value="'.$row["posto"].' '.$row["nome"].'">'.$row["posto"].' '.$row["nome"].'</option>';
												}
											}
										?>
									</select>
								</div>
							</div>
							<div class="row mt-3">
								<div class="col">
									<button id="limpar" type="reset" class="w-100 btn btn-dark">Limpar</button>
								</div>
								<div class="col">
									<button id="registrar" name="registrar" type="submit" class="w-100 btn btn-primary">Editar</button>
								</div>
							</div>
						</form>
						<?php if(isset($resultado)){echo "$resultado";}elseif(isset($_GET['flag'])){if($_GET['flag'] == 1){echo '<div class="alert alert-success">Retirada de chave editada com sucesso!</div>';}} ?>
					</div>
					<div class="col m-0">
						<div class="bg-dark p-2 text-white">
							<span class="h6"><?php if($editar['dataDevolucao'] != null){echo 'Editar';}else{echo 'Registrar';} ?> Devolução de Chave</span><?php if(isset($editar['editado2'])){echo '<span class="h6 fw-light" style="color: #909294;"> - editado por '.$editar['editado2'].'</span>';} ?>
						</div>
						<form class="border mb-3 pt-0 p-3" style="background-color: #f5f5f5;" action="" method="POST">
							<div class="row">
								<div class="col">
									<label class="mt-2" for="data2">Data:</label>
									<input id="data2" name="data2" type="date" class="form-control" value="<?php if($editar['dataDevolucao'] != null){echo $editar['dataDevolucao'].'" disabled';}else{echo date("Y-m-d").'"';} ?> required>
								</div>
								<div class="col">
									<label class="mt-2" for="hora2">Horário:</label>
									<input step="1" id="hora2" name="hora2" type="time" class="form-control" value="<?php if($editar['horarioDevolucao'] != null){echo $editar['horarioDevolucao'].'" disabled';}else{date_default_timezone_set('America/Sao_Paulo'); echo date("H:i:s").'"';} ?> required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="nome2">Nome:</label>
									<select id="nome2" name="nome2" autocomplete="off" <?php if($editar['nomeDevolucao'] != null){echo 'disabled';} ?> required>
										<?php
											if($editar['nomeDevolucao'] != null){
												if($result = $mysqli->query("SELECT * FROM efetivo WHERE posto = '".strtok($editar['nomeDevolucao'],  ' ')."' AND nome = '".substr($editar['nomeDevolucao'], strpos($editar['nomeDevolucao'], ' ') + 1)."'")){
													while($row = $result->fetch_assoc()){
														echo '<option selected value="'.$row["posto"].' '.$row["nome"].'">'.$row["posto"].' '.$row["nome"].'</option>';
													}
												}
											}else{
												echo '<option selected disabled value="">Selecione um nome...</option>';

												if($result = $mysqli->query("SELECT * FROM efetivo ORDER BY nome")){
													while($row = $result->fetch_assoc()){
														echo '<option value="'.$row["posto"].' '.$row["nome"].'">'.$row["posto"].' '.$row["nome"].'</option>';
													}
												}
											}
										?>
									</select>
								</div>
								<?php
									if($editar['nomeDevolucao'] == null){
										echo '<div class="col">
											<label class="mt-2" for="assinatura2">Assinatura:</label>
											<input id="assinatura2" name="assinatura2" type="password" class="form-control" required>
										</div>';
									}
								?>
							</div>
							<div class="row mt-3">
								<div class="col">
									<button id="limpar2" type="reset" class="w-100 btn btn-dark">Limpar</button>
								</div>
								<div class="col">
									<button id="registrar2" name="registrar2" type="submit" class="w-100 btn btn-primary"><?php if($editar['dataDevolucao'] != null){echo 'Editar';}else{echo 'Registrar';} ?></button>
								</div>
							</div>
						</form>
						<?php if(isset($resultado2)){echo "$resultado2";}elseif(isset($_GET['flag'])){if($_GET['flag'] == 2){echo '<div class="alert alert-success">Devolução de chave editada com sucesso!</div>';}else if($_GET['flag'] == 3){echo '<div class="alert alert-success">Devolução de chave registrada com sucesso!</div>';}} ?>
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
		<script>
			new TomSelect("#motorista",{
				create: false,
				render:{
					no_results: function( data, escape ){
						return '<div class="no-results">Nenhum motorista encontrado...</div>';
					},
				}
			});
		
			new TomSelect("#chave",{
				create: false,
				render:{
					no_results: function( data, escape ){
						return '<div class="no-results">Nenhuma chave encontrada...</div>';
					},
				}
			});

			new TomSelect("#nome",{
				create: false,
				render:{
					no_results: function( data, escape ){
						return '<div class="no-results">Nenhum nome encontrado...</div>';
					},
				}
			});

			new TomSelect("#nome2",{
				create: false,
				render:{
					no_results: function( data, escape ){
						return '<div class="no-results">Nenhum nome encontrado...</div>';
					},
				}
			});
		</script>
	</body>
</html>
