<?php
	include('classes/protect.php');
	include('classes/functions.php');
	
	if($_SESSION["permissao"] != 'Sentinela' && $_SESSION["permissao"] != 'Sargento de Dia' && $_SESSION["permissao"] != 'Administrador'){
		header('Location: autenticacao.php');
	}

	if(isset($_POST['chave']) || isset($_POST['data']) || isset($_POST['hora']) || isset($_POST['nome']) || isset($_POST['assinatura'])){
		if(strlen($_POST['chave']) == 0){
			$resultado = '<div class="alert alert-danger">A <b>chave</b> não foi selecionada!</div>';
		}else if(strlen($_POST['data']) == 0){
			$resultado = '<div class="alert alert-danger">A <b>data</b> não foi preenchida!</div>';
		}else if(strlen($_POST['hora']) == 0){
			$resultado = '<div class="alert alert-danger">A <b>hora</b> não foi preenchida!</div>';
		}else if(strlen($_POST['nome']) == 0){
			$resultado = '<div class="alert alert-danger">O <b>nome</b> não foi selecionado!</div>';
		}else if(strlen($_POST['assinatura']) == 0){
			$resultado = '<div class="alert alert-danger">A <b>assinatura</b> não foi preenchida!</div>';
		}else{
			$chave = $mysqli->real_escape_string($_POST['chave']);
			$data = $mysqli->real_escape_string($_POST['data']);
			$hora = $mysqli->real_escape_string($_POST['hora']);
			$nome = $mysqli->real_escape_string($_POST['nome']);
			$assinatura = $mysqli->real_escape_string($_POST['assinatura']);

			$verificarChave = $mysqli->query("SELECT * FROM svChaves WHERE numero = '".strtok($chave, ' ')."' AND dataDevolucao IS NULL") or die("Falha na execução do código SQL: ".$mysqli->error);
			$quantidade = $verificarChave->num_rows;
			$verificarChave2 = $mysqli->query("SELECT * FROM svChaves WHERE numero = '".strtok($chave, ' ')."' AND dataRetirada = '$data' AND horarioRetirada = '$hora'") or die("Falha na execução do código SQL: ".$mysqli->error);
			$quantidade2 = $verificarChave2->num_rows;
			$verificarAssinatura = $mysqli->query("SELECT senha FROM efetivo WHERE posto = '".strtok($nome, ' ')."' AND nome = '".substr($nome, strpos($nome, ' ') + 1)."'") or die("Falha na execução do código SQL: ".$mysqli->error);
			$comparar = $verificarAssinatura->fetch_assoc();

			if($quantidade != 0){
				$resultado = '<div class="alert alert-danger">A chave selecionada ainda não foi devolvida!</div>';
			}else if($quantidade2 != 0){
				$resultado = '<div class="alert alert-danger">A chave já possui este registro!</div>';
			}else if($comparar['senha'] != md5($assinatura)){
				$resultado = '<div class="alert alert-danger">A <b>assinatura</b> não está correta!</div>';
			}else{
				if(mysqli_query($mysqli, "INSERT INTO svChaves (numero, chave, dataRetirada, horarioRetirada, nomeRetirada, permanenciaRetirada) VALUES ('".strtok($chave, ' ')."', '".substr($chave, strpos($chave, '- ') + 1)."', '$data', '$hora', '$nome', '".$_SESSION['posto'].' '.$_SESSION['nome']."')")){
					$resultado = '<div class="alert alert-success">Retirada de chave registrada com sucesso!</div>';
				}else{
					$resultado = '<div class="alert alert-danger">Houve um erro ao registrar a retirada de chave!</div>';
				}
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
		<title>Arachne - Chaves</title>
	</head>
	<body style="background-image: url('images/background.jpg'); background-repeat: repeat; background-attachment: fixed; background-position: center top; background-size: 100% auto;" class="d-flex flex-column min-vh-100">
		<header>
			<div class="modal fade" id="lacrada" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title"><b>Atenção:</b></h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<p class="mb-0">A chave que você selecionou é lacrada, portanto verifique se o militar tem permissão para retirá-la!</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-primary" data-bs-dismiss="modal">Ok</button>
						</div>
					</div>
				</div>
			</div>
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
								<a class="nav-link active" aria-current="page" href="chaves.php">Chaves</a>
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
							<span class="h6">Retirada de Chaves</span>
						</div>
						<form class="border mb-3 pt-0 p-3" style="background-color: #f5f5f5;" action="" method="POST">
							<div class="row">
								<div class="col">
									<label class="mt-2" for="chave">Chave:</label>
									<select id="chave" name="chave" autocomplete="off" onchange="tipoChave()" required>
										<option selected disabled value="">Selecione uma chave...</option>
										<?php
											if($result = $mysqli->query("SELECT * FROM chaves ORDER BY numero")){
												while($row = $result->fetch_assoc()){
													if($row['tipo'] == "Lacrada"){
														echo '<option value="'.$row["numero"].' - '.$row["nome"].'">'.$row["numero"].' - '.$row["nome"].' [Lacrada]</option>';
													}else{
														echo '<option value="'.$row["numero"].' - '.$row["nome"].'">'.$row["numero"].' - '.$row["nome"].'</option>';
													}
												}
											}
										?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="data">Data:</label>
									<input id="data" name="data" type="date" class="form-control mb-0" value="<?php echo date("Y-m-d"); ?>" required>
								</div>
								<div class="col">
									<label class="mt-2" for="hora">Horário:</label>
									<input step="1" id="hora" name="hora" type="time" class="form-control mb-0" value="<?php date_default_timezone_set('America/Sao_Paulo'); echo date("H:i:s"); ?>" required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="nome">Nome:</label>
									<select id="nome" name="nome" autocomplete="off" required>
										<option selected disabled value="">Selecione um nome...</option>
										<?php
											if($result = $mysqli->query("SELECT * FROM efetivo ORDER BY nome")){
												while($row = $result->fetch_assoc()){
													echo '<option value="'.$row["posto"].' '.$row["nome"].'">'.$row["posto"].' '.$row["nome"].'</option>';
												}
											}
										?>
									</select>
								</div>
								<div class="col">
									<label class="mt-2" for="assinatura">Assinatura:</label>
									<input id="assinatura" name="assinatura" type="password" class="form-control" required>
								</div>
							</div>
							<div class="row mt-3">
								<div class="col">
									<button id="limpar" type="reset" class="w-100 btn btn-dark">Limpar</button>
								</div>
								<div class="col">
									<button id="registrar" type="submit" class="w-100 btn btn-primary">Registrar</button>
								</div>
							</div>
						</form>
						<?php if(isset($resultado)){echo "$resultado";} ?>
					</div>
					<div class="col m-0">
						<div class="bg-dark p-2 text-white">
							<span class="h6">Chaves Indisponíveis</span>
							<span class="badge bg-primary m-0" style="float: right;"><?php $query = $mysqli->query("SELECT * FROM svChaves WHERE dataDevolucao IS NULL"); $quantidadeChaves = $query->num_rows; echo "$quantidadeChaves"; ?></span>
						</div>
						<div class="table-responsive mb-3">
							<table class="table table-hover table-bordered text-center align-middle my-0">
								<thead>
									<tr style="background-color: #f5f5f5;">
										<th scope="col">Número</th>
										<th scope="col">Chave</th>
										<th scope="col">Data</th>
										<th scope="col">Horário</th>
										<th scope="col">Militar</th>
										<th scope="col">Sentinela</th>
										<th scope="col">Ações</th>
									</tr>
								</thead>
								<tbody>
									<?php
										if($result = $mysqli->query("SELECT * FROM svChaves WHERE dataDevolucao IS NULL ORDER BY numero;")){
											while($row = $result->fetch_assoc()){
												echo '<tr>
													<th style="background-color: #fff;" scope="row">'.$row["numero"].'</th>
													<td style="background-color: #fff;">'.$row["chave"].'</td>
													<td style="background-color: #fff;">'.date("d/m/Y", strtotime($row["dataRetirada"])).'</td>
													<td style="background-color: #fff;">'.$row["horarioRetirada"].'</td>
													<td style="background-color: #fff;">'.$row["nomeRetirada"].'</td>
													<td style="background-color: #fff;">'.$row["permanenciaRetirada"].'</td>
													<td style="background-color: #fff;"><button onclick="location.href=\'editar-chaves.php?numero='.$row["numero"].'&data='.$row["dataRetirada"].'&hora='.$row["horarioRetirada"].'\'" type="submit" class="btn m-0 p-0 bg-transparent"><img src="images/edit.png" alt="" width="16" height="16"></button></td>
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
			
			function tipoChave(){
				var select = document.getElementById('chave');
				var text = select.options[select.selectedIndex].text;
				
				if(text.includes('[Lacrada]')){
					$('#lacrada').modal('show');
				}
			}
		</script>
	</body>
</html>
