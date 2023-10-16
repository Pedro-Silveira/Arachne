<?php
	include('classes/protect.php');
	include('classes/functions-admin.php');
	
	$queryVisitante = $mysqli->query("SELECT * FROM svVisitantes WHERE documento = '".$_GET['documento']."' and dataEntrada = '".$_GET['data']."' and horarioEntrada = '".$_GET['hora']."'") or die("Falha na execução do código SQL: ".$mysqli->error);
	$editar = $queryVisitante->fetch_assoc();
	$retorno = $queryVisitante->num_rows;
	
	if($retorno == 0){
		header('Location: administrator.php');
	}

	if(isset($_POST['documento']) || isset($_POST['cracha']) || isset($_POST['nome']) || isset($_POST['empresa']) || isset($_POST['data']) || isset($_POST['hora']) || isset($_POST['responsavel']) || isset($_POST['sentinela']) || isset($_POST['servico']) || isset($_POST['data2']) || isset($_POST['hora2']) || isset($_POST['sentinela2']) || isset($_POST['servico2'])){
		if(isset($_POST['registrar'])){
			if(strlen($_POST['documento']) == 0){
				$resultado = '<div class="alert alert-danger">O <b>documento</b> não foi preenchido!</div>';
			}else if(strlen($_POST['cracha']) == 0){
				$resultado = '<div class="alert alert-danger">O <b>crachá</b> não foi preenchido!</div>';
			}else if(strlen($_POST['nome']) == 0){
				$resultado = '<div class="alert alert-danger">O <b>nome</b> não foi preenchido!</div>';
			}else if(strlen($_POST['empresa']) == 0){
				$resultado = '<div class="alert alert-danger">A <b>empresa</b> não foi preenchida!</div>';
			}else if(strlen($_POST['data']) == 0){
				$resultado = '<div class="alert alert-danger">A <b>data de entrada</b> não foi preenchida!</div>';
			}else if(strlen($_POST['hora']) == 0){
				$resultado = '<div class="alert alert-danger">A <b>hora de entrada</b> não foi preenchida!</div>';
			}else if(strlen($_POST['responsavel']) == 0){
				$resultado = '<div class="alert alert-danger">O <b>responsável</b> não foi selecionado!</div>';
			}else if(strlen($_POST['sentinela']) == 0){
				$resultado = '<div class="alert alert-danger">O <b>sentinela</b> não foi selecionado!</div>';
			}else if(strlen($_POST['servico']) == 0){
				$resultado = '<div class="alert alert-danger">O <b>serviço</b> não foi preenchido!</div>';
			}else{
				$documento = $mysqli->real_escape_string($_POST['documento']);
				$cracha = $mysqli->real_escape_string($_POST['cracha']);
				$nome = $mysqli->real_escape_string($_POST['nome']);
				$empresa = $mysqli->real_escape_string($_POST['empresa']);
				$data = $mysqli->real_escape_string($_POST['data']);
				$hora = $mysqli->real_escape_string($_POST['hora']);
				$responsavel = $mysqli->real_escape_string($_POST['responsavel']);
				$sentinela = $mysqli->real_escape_string($_POST['sentinela']);
				$servico = $mysqli->real_escape_string($_POST['servico']);

				$verificarVisitante = $mysqli->query("SELECT * FROM svVisitantes WHERE documento = '$documento' AND dataEntrada = '$data' AND horarioEntrada = '$hora'") or die("Falha na execução do código SQL: ".$mysqli->error);
				$quantidade = $verificarVisitante->num_rows;

				if($quantidade != 0){
					$resultado = '<div class="alert alert-danger">O visitante já possui este registro!</div>';
				}else{
					if(mysqli_query($mysqli, "UPDATE svVisitantes SET documento = '$documento', cracha = '$cracha', nome = '".ucwords(strtolower($nome))."', empresa = '".ucwords(strtolower($empresa))."', dataEntrada = '$data', horarioEntrada = '$hora', responsavel = '$responsavel', permanenciaEntrada = '$sentinela', servico = '$servico', editado = 'Administrador ".$_SESSION['posto']." ".$_SESSION['nome']." (".date("d/m/Y")." às ".date("H:i:s").")' WHERE documento = '".$_GET['documento']."' and dataEntrada = '".$_GET['data']."' and horarioEntrada = '".$_GET['hora']."'")){
						header('Location: ficha-visitantes-admin.php?documento='.$documento.'&data='.$data.'&hora='.$hora.'&flag=1');
					}else{
						$resultado = '<div class="alert alert-danger">Houve um erro ao editar a entrada de visitante!</div>';
					}
				}
			}
		}else if(isset($_POST['registrar2'])){
			if(strlen($_POST['data2']) == 0){
				$resultado2 = '<div class="alert alert-danger">A <b>data de saída</b> não foi preenchida!</div>';
			}else if(strlen($_POST['hora2']) == 0){
				$resultado2 = '<div class="alert alert-danger">A <b>hora de saída</b> não foi preenchida!</div>';
			}else if(strlen($_POST['sentinela2']) == 0){
				$resultado2 = '<div class="alert alert-danger">O <b>sentinela</b> não foi selecionado!</div>';
			}else if(strlen($_POST['servico2']) == 0){
				$resultado2 = '<div class="alert alert-danger">O <b>serviço</b> não foi preenchido!</div>';
			}else{
				$data2 = $mysqli->real_escape_string($_POST['data2']);
				$hora2 = $mysqli->real_escape_string($_POST['hora2']);
				$sentinela2 = $mysqli->real_escape_string($_POST['sentinela2']);
				$servico2 = $mysqli->real_escape_string($_POST['servico2']);

				if(mysqli_query($mysqli, "UPDATE svVisitantes SET dataSaida = '$data2', horarioSaida = '$hora2', permanenciaSaida = '$sentinela2', servico2 = '$servico2', editado2 = 'Administrador ".$_SESSION['posto']." ".$_SESSION['nome']." (".date("d/m/Y")." às ".date("H:i:s").")' WHERE documento = '".$_GET['documento']."' and dataEntrada = '".$_GET['data']."' and horarioEntrada = '".$_GET['hora']."'")){
					header('Location: ficha-visitantes-admin.php?documento='.$_GET['documento'].'&data='.$_GET['data'].'&hora='.$_GET['hora'].'&flag=2');
				}else{
					$resultado = '<div class="alert alert-danger">Houve um erro ao editar a saída de visitante!</div>';
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
		<title>Arachne - Editar Registro de Visitante</title>
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
							<span class="h6">Editar Entrada de Visitante</span><?php if(isset($editar['editado'])){echo '<span class="h6 fw-light" style="color: #909294;"> - editado por '.$editar['editado'].'</span>';} ?>
						</div>
						<form class="border mb-3 pt-0 p-3" style="background-color: #f5f5f5;" action="" method="POST">
							<div class="row">
								<div class="col">
									<label class="mt-2" for="documento">Documento:</label>
									<input id="documento" name="documento" type="text" class="form-control" value="<?php echo $editar['documento']; ?>" required>
								</div>
								<div class="col">
									<label class="mt-2" for="cracha">Crachá:</label>
									<input id="cracha" name="cracha" type="text" class="form-control" value="<?php echo $editar['cracha']; ?>" required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="nome">Nome:</label>
									<input id="nome" name="nome" type="text" class="form-control" value="<?php echo $editar['nome']; ?>" required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="empresa">Empresa:</label>
									<input id="empresa" name="empresa" type="text" class="form-control" value="<?php echo $editar['empresa']; ?>" required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="data">Data:</label>
									<input id="data" name="data" type="date" class="form-control" value="<?php echo $editar['dataEntrada']; ?>" required>
								</div>
								<div class="col">
									<label class="mt-2" for="hora">Horário:</label>
									<input step="1" id="hora" name="hora" type="time" class="form-control" value="<?php echo $editar['horarioEntrada']; ?>" required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="responsavel">Responsável:</label>
									<select id="responsavel" name="responsavel" autocomplete="off" required>
										<?php
											if($result = $mysqli->query("SELECT * FROM efetivo WHERE saram <> 'Administrador' ORDER BY nome")){
												while($row = $result->fetch_assoc()){
													if($row["posto"]." ".$row["nome"] == $editar["responsavel"]){
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
									<label class="mt-2" for="sentinela">Sentinela:</label>
									<select id="sentinela" name="sentinela" autocomplete="off" required>
										<?php
											if($result = $mysqli->query("SELECT * FROM efetivo WHERE saram <> 'Administrador' ORDER BY nome")){
												while($row = $result->fetch_assoc()){
													if($row["posto"]." ".$row["nome"] == $editar["permanenciaEntrada"]){
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
						<?php if(isset($resultado)){echo "$resultado";}elseif(isset($_GET['flag'])){if($_GET['flag'] == 1){echo '<div class="alert alert-success">Entrada de visitante editada com sucesso!</div>';}} ?>
					</div>
					<div class="col m-0">
						<div class="bg-dark p-2 text-white">
							<span class="h6">Editar Saída de Visitante</span><?php if(isset($editar['editado2'])){echo '<span class="h6 fw-light" style="color: #909294;"> - editado por '.$editar['editado2'].'</span>';} ?>
						</div>
						<form class="border mb-3 pt-0 p-3" style="background-color: #f5f5f5;" action="" method="POST">
							<div class="row">
								<div class="col">
									<label class="mt-2" for="data2">Data:</label>
									<input id="data2" name="data2" type="date" class="form-control" value="<?php if($editar['dataSaida'] != null){echo $editar['dataSaida'];}else{echo date("Y-m-d");} ?>" required>
								</div>
								<div class="col">
									<label class="mt-2" for="hora2">Horário:</label>
									<input step="1" id="hora2" name="hora2" type="time" class="form-control" value="<?php if($editar['horarioSaida'] != null){echo $editar['horarioSaida'];}else{date_default_timezone_set('America/Sao_Paulo'); echo date("H:i:s");} ?>" required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="sentinela2">Sentinela:</label>
									<select id="sentinela2" name="sentinela2" autocomplete="off" required>
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
						<?php if(isset($resultado2)){echo "$resultado2";}elseif(isset($_GET['flag'])){if($_GET['flag'] == 2){echo '<div class="alert alert-success">Saída de visitante editada com sucesso!</div>';}} ?>
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
			new TomSelect("#responsavel",{
				create: false,
				render:{
					no_results: function( data, escape ){
						return '<div class="no-results">Nenhum responsável encontrado...</div>';
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
