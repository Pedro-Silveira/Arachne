<?php
	include('classes/protect.php');
	include('classes/functions-admin.php');

	$queryViatura = $mysqli->query("SELECT * FROM svViaturas WHERE registro = '".$_GET['registro']."' and dataSaida = '".$_GET['data']."' and horarioSaida = '".$_GET['hora']."'") or die("Falha na execução do código SQL: ".$mysqli->error);
	$editar = $queryViatura->fetch_assoc();
	$retorno = $queryViatura->num_rows;
	
	if($retorno == 0){
		header('Location: administrator.php');
	}

	if(isset($_POST['viatura']) || isset($_POST['km']) || isset($_POST['motorista']) || isset($_POST['destino']) || isset($_POST['data']) || isset($_POST['hora']) || isset($_POST['sentinela']) || isset($_POST['servico']) || isset($_POST['motorista2']) || isset($_POST['km2']) || isset($_POST['data2']) || isset($_POST['hora2']) || isset($_POST['sentinela2']) || isset($_POST['servico2'])){
		if(isset($_POST['registrar'])){
			if(strlen($_POST['viatura']) == 0){
				$resultado = '<div class="alert alert-danger">A <b>viatura</b> não foi selecionada!</div>';
			}else if(strlen($_POST['km']) == 0){
				$resultado = '<div class="alert alert-danger">O <b>hodômetro</b> não foi preenchido!</div>';
			}else if(strlen($_POST['motorista']) == 0){
				$resultado = '<div class="alert alert-danger">O <b>motorista</b> não foi selecionado!</div>';
			}else if(strlen($_POST['destino']) == 0){
				$resultado = '<div class="alert alert-danger">O <b>destino</b> não foi preenchido!</div>';
			}else if(strlen($_POST['data']) == 0){
				$resultado = '<div class="alert alert-danger">A <b>data de saída</b> não foi preenchida!</div>';
			}else if(strlen($_POST['hora']) == 0){
				$resultado = '<div class="alert alert-danger">A <b>hora de saída</b> não foi preenchida!</div>';
			}else if(strlen($_POST['sentinela']) == 0){
				$resultado = '<div class="alert alert-danger">O <b>sentinela</b> não foi selecionado!</div>';
			}else if(strlen($_POST['servico']) == 0){
				$resultado = '<div class="alert alert-danger">O <b>serviço</b> não foi preenchido!</div>';
			}else{
				$viatura = $mysqli->real_escape_string($_POST['viatura']);
				$km = $mysqli->real_escape_string($_POST['km']);
				$motorista = $mysqli->real_escape_string($_POST['motorista']);
				$destino = $mysqli->real_escape_string($_POST['destino']);
				$data = $mysqli->real_escape_string($_POST['data']);
				$hora = $mysqli->real_escape_string($_POST['hora']);
				$sentinela = $mysqli->real_escape_string($_POST['sentinela']);
				$servico = $mysqli->real_escape_string($_POST['servico']);

				$verificarViatura = $mysqli->query("SELECT * FROM svViaturas WHERE registro = '".substr($viatura, strpos($viatura, ' ') + 3)."' AND dataSaida = '$data' AND horarioSaida = '$hora'") or die("Falha na execução do código SQL: ".$mysqli->error);
				$quantidade = $verificarViatura->num_rows;

				if($quantidade != 0){
					$resultado = '<div class="alert alert-danger">A viatura já possui este registro!</div>';
				}else{
					if(mysqli_query($mysqli, "UPDATE svViaturas SET registro = '".substr($viatura, strpos($viatura, ' ') + 3)."', modelo = '".strtok($viatura,  ' ')."', hodometroSaida = '$km', motoristaSaida = '$motorista', destino = '$destino', dataSaida = '$data', horarioSaida = '$hora', permanenciaSaida = '$sentinela', servico = '$servico', editado = 'Administrador ".$_SESSION['posto']." ".$_SESSION['nome']." (".date("d/m/Y")." às ".date("H:i:s").")' WHERE registro = '".$_GET['registro']."' and dataSaida = '".$_GET['data']."' and horarioSaida = '".$_GET['hora']."'")){
						header('Location: ficha-viaturas-admin.php?registro='.substr($viatura, strpos($viatura, ' ') + 3).'&data='.$data.'&hora='.$hora.'&flag=1');
					}else{
						$resultado = '<div class="alert alert-danger">Houve um erro ao editar a saída de viatura!</div>';
					}
				}
			}
		}else if(isset($_POST['registrar2'])){
			if(strlen($_POST['motorista2']) == 0){
				$resultado2 = '<div class="alert alert-danger">O <b>motorista</b> não foi selecionado!</div>';
			}else if(strlen($_POST['km2']) == 0){
				$resultado2 = '<div class="alert alert-danger">O <b>hodômetro</b> não foi preenchido!</div>';
			}else if(strlen($_POST['data2']) == 0){
				$resultado2 = '<div class="alert alert-danger">A <b>data de saída</b> não foi preenchida!</div>';
			}else if(strlen($_POST['hora2']) == 0){
				$resultado2 = '<div class="alert alert-danger">A <b>hora de saída</b> não foi preenchida!</div>';
			}else if(strlen($_POST['sentinela2']) == 0){
				$resultado2 = '<div class="alert alert-danger">O <b>sentinela</b> não foi selecionado!</div>';
			}else if(strlen($_POST['servico2']) == 0){
				$resultado2 = '<div class="alert alert-danger">O <b>serviço</b> não foi preenchido!</div>';
			}else{
				$motorista2 = $mysqli->real_escape_string($_POST['motorista2']);
				$km2 = $mysqli->real_escape_string($_POST['km2']);
				$data2 = $mysqli->real_escape_string($_POST['data2']);
				$hora2 = $mysqli->real_escape_string($_POST['hora2']);
				$sentinela2 = $mysqli->real_escape_string($_POST['sentinela2']);
				$servico2 = $mysqli->real_escape_string($_POST['servico2']);

				if(mysqli_query($mysqli, "UPDATE svViaturas SET motoristaRetorno = '$motorista2', hodometroRetorno = '$km2', dataRetorno = '$data2', horarioRetorno = '$hora2', permanenciaRetorno = '$sentinela2', servico2 = '$servico2', editado2 = 'Administrador ".$_SESSION['posto']." ".$_SESSION['nome']." (".date("d/m/Y")." às ".date("H:i:s").")' WHERE registro = '".$_GET['registro']."' and dataSaida = '".$_GET['data']."' and horarioSaida = '".$_GET['hora']."'")){
					header('Location: ficha-viaturas-admin.php?registro='.$_GET['registro'].'&data='.$_GET['data'].'&hora='.$_GET['hora'].'&flag=2');
				}else{
					$resultado = '<div class="alert alert-danger">Houve um erro ao editar o retorno de viatura!</div>';
				}
			}
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
		<title>Arachne - Editar Registro de Viatura</title>
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
							<span class="h6">Editar Saída de Viatura</span><?php if(isset($editar['editado'])){echo '<span class="h6 fw-light m-0" style="color: #909294;"> - editado por '.$editar['editado'].'</span>';} ?>
						</div>
						<form class="border mb-3 pt-0 p-3" style="background-color: #f5f5f5;" action="" method="POST">
							<div class="row">
								<div class="col">
									<label class="mt-2" for="viatura">Viatura:</label>
									<select id="viatura" name="viatura" autocomplete="off" required>
										<?php
											if($result = $mysqli->query("SELECT * FROM viaturas ORDER BY modelo")){
												while($row = $result->fetch_assoc()){
													if($row["registro"] == $editar["registro"]){
														echo '<option selected value="'.$row["modelo"].' - '.$row["registro"].'">'.$row["modelo"].' - '.$row["registro"].'</option>';
													}else{
														echo '<option value="'.$row["modelo"].' - '.$row["registro"].'">'.$row["modelo"].' - '.$row["registro"].'</option>';
													}
												}
											}
										?>
									</select>
								</div>
								<div class="col">
									<label class="mt-2" for="km">Hodômetro:</label>
									<input id="km" name="km" type="number" class="form-control" value="<?php echo $editar['hodometroSaida']; ?>" required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="motorista">Motorista:</label>
									<select id="motorista" name="motorista" autocomplete="off" required>
										<?php
											if($result = $mysqli->query("SELECT * FROM efetivo WHERE saram <> 'Administrador' ORDER BY nome")){
												while($row = $result->fetch_assoc()){
													if($row["posto"]." ".$row["nome"] == $editar["motoristaSaida"]){
														echo '<option selected value="'.$row["posto"].' '.$row["nome"].'">'.$row["posto"].' '.$row["nome"].'</option>';
													}else{
														echo '<option value="'.$row["posto"].' '.$row["nome"].'">'.$row["posto"].' '.$row["nome"].'</option>';
													}
												}
											}
										?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="destino">Destino:</label>
									<input id="destino" name="destino" type="text" class="form-control" value="<?php echo $editar['destino']; ?>" required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="data">Data:</label>
									<input id="data" name="data" type="date" class="form-control" value="<?php echo $editar['dataSaida']; ?>" required>
								</div>
								<div class="col">
									<label class="mt-2" for="hora">Horário:</label>
									<input step="1" id="hora" name="hora" type="time" class="form-control" value="<?php echo $editar['horarioSaida']; ?>" required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="sentinela">Sentinela:</label>
									<select id="sentinela" name="sentinela" autocomplete="off" required>
										<?php
											if($result = $mysqli->query("SELECT * FROM efetivo WHERE saram <> 'Administrador' ORDER BY nome")){
												while($row = $result->fetch_assoc()){
													if($row["posto"]." ".$row["nome"] == $editar["permanenciaSaida"]){
														echo '<option selected value="'.$row["posto"].' '.$row["nome"].'">'.$row["posto"].' '.$row["nome"].'</option>';
													}else{
														echo '<option value="'.$row["posto"].' '.$row["nome"].'">'.$row["posto"].' '.$row["nome"].'</option>';
													}
												}
											}
										?>
									</select>
								</div>
								<div class="col">
									<label class="mt-2" for="servico">Serviço:</label>
									<input id="servico" name="servico" type="date" class="form-control" value="<?php echo $editar['servico']; ?>" required>
								</div>
							</div>
							<div class="row mt-3">
								<div class="col">
									<button id="limpar" name="limpar" type="reset" class="w-100 btn btn-dark">Limpar</button>
								</div>
								<div class="col">
									<button id="registrar" name="registrar" type="submit" class="w-100 btn btn-primary">Editar</button>
								</div>
							</div>
						</form>
						<?php if(isset($resultado)){echo "$resultado";}elseif(isset($_GET['flag'])){if($_GET['flag'] == 1){echo '<div class="alert alert-success">Saída de viatura editada com sucesso!</div>';}} ?>
					</div>
					<div class="col m-0">
						<div class="bg-dark p-2 text-white">
							<span class="h6">Editar Retorno de Viatura</span><?php if(isset($editar['editado2'])){echo '<span class="h6 fw-light" style="color: #909294;"> - editado por '.$editar['editado2'].'</span>';} ?>
						</div>
						<form class="border mb-3 pt-0 p-3" style="background-color: #f5f5f5;" action="" method="POST">
							<div class="row">
								<div class="col">
									<label class="mt-2" for="motorista2">Motorista:</label>
									<select id="motorista2" name="motorista2" autocomplete="off" required>
										<?php
											if($result = $mysqli->query("SELECT * FROM efetivo WHERE saram <> 'Administrador' ORDER BY nome")){
												while($row = $result->fetch_assoc()){
													if($row["posto"]." ".$row["nome"] == $editar["motoristaRetorno"]){
														echo '<option selected value="'.$row["posto"].' '.$row["nome"].'">'.$row["posto"].' '.$row["nome"].'</option>';
													}else{
														echo '<option value="'.$row["posto"].' '.$row["nome"].'">'.$row["posto"].' '.$row["nome"].'</option>';
													}
												}
											}
										?>
									</select>
								</div>
								<div class="col">
									<label class="mt-2" for="km2">Hodômetro:</label>
									<input id="km2" name="km2" type="number" class="form-control" value="<?php if($editar['hodometroRetorno'] != null){echo $editar['hodometroRetorno'];}else{echo $editar['hodometroSaida'];} ?>" required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="data2">Data:</label>
									<input id="data2" name="data2" type="date" class="form-control" value="<?php if($editar['dataRetorno'] != null){echo $editar['dataRetorno'];}else{echo date("Y-m-d");} ?>" required>
								</div>
								<div class="col">
									<label class="mt-2" for="hora2">Horário:</label>
									<input step="1" id="hora2" name="hora2" type="time" class="form-control" value="<?php if($editar['horarioRetorno'] != null){echo $editar['horarioRetorno'];}else{date_default_timezone_set('America/Sao_Paulo'); echo date("H:i:s");} ?>" required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="sentinela2">Sentinela:</label>
									<select id="sentinela2" name="sentinela2" autocomplete="off" required>
										<?php
											if($result = $mysqli->query("SELECT * FROM efetivo WHERE saram <> 'Administrador' ORDER BY nome")){
												while($row = $result->fetch_assoc()){
													if($row["posto"]." ".$row["nome"] == $editar["permanenciaRetorno"]){
														echo '<option selected value="'.$row["posto"].' '.$row["nome"].'">'.$row["posto"].' '.$row["nome"].'</option>';
													}else{
														echo '<option value="'.$row["posto"].' '.$row["nome"].'">'.$row["posto"].' '.$row["nome"].'</option>';
													}
												}
											}
										?>
									</select>
								</div>
								<div class="col">
									<label class="mt-2" for="servico2">Serviço:</label>
									<input id="servico2" name="servico2" type="date" class="form-control" value="<?php echo $editar['servico2']; ?>" required>
								</div>
							</div>
							<div class="row mt-3">
								<div class="col">
									<button id="limpar2" name="limpar2" type="reset" class="w-100 btn btn-dark">Limpar</button>
								</div>
								<div class="col">
									<button id="registrar2" name="registrar2" type="submit" class="w-100 btn btn-primary">Editar</button>
								</div>
							</div>
						</form>
						<?php if(isset($resultado2)){echo "$resultado2";}elseif(isset($_GET['flag'])){if($_GET['flag'] == 2){echo '<div class="alert alert-success">Retorno de viatura editado com sucesso!</div>';}} ?>
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
			new TomSelect("#viatura",{
				create: false,
				render:{
					no_results: function( data, escape ){
						return '<div class="no-results">Nenhuma viatura encontrada...</div>';
					},
				}
			});

			new TomSelect("#motorista",{
				create: false,
				render:{
					no_results: function( data, escape ){
						return '<div class="no-results">Nenhum motorista encontrado...</div>';
					},
				}
			});
			
			new TomSelect("#sentinela",{
				create: false,
				render:{
					no_results: function( data, escape ){
						return '<div class="no-results">Nenhum sentinela encontrado...</div>';
					},
				}
			});

			new TomSelect("#motorista2",{
				create: false,
				render:{
					no_results: function( data, escape ){
						return '<div class="no-results">Nenhum motorista encontrado...</div>';
					},
				}
			});
			
			new TomSelect("#sentinela2",{
				create: false,
				render:{
					no_results: function( data, escape ){
						return '<div class="no-results">Nenhum sentinela encontrado...</div>';
					},
				}
			});
		</script>
	</body>
</html>
