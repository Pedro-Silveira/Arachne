<?php
	include('classes/protect.php');
	include('classes/functions-admin.php');
	
	$queryServico = $mysqli->query("SELECT * FROM servicos WHERE data = '".$_GET['data']."'") or die("Falha na execução do código SQL: ".$mysqli->error);
	$editar = $queryServico->fetch_assoc();
	$retorno = $queryServico->num_rows;
	
	if($retorno == 0){
		header('Location: administrator.php');
	}

	if(isset($_POST['chave'])){
		if(mysqli_query($mysqli, "DELETE FROM svChaves WHERE numero = '".$_POST['chave']."' and dataRetirada = '".$_POST['data']."' and horarioRetirada = '".$_POST['hora']."'")) {
			$resultado = '<div class="alert alert-success mt-3 mb-0">Registro de chave excluído com sucesso!</div>';
		}else{
			$resultado = '<div class="alert alert-danger mt-3 mb-0">Houve um erro ao excluir o registro de chave!</div>';
		}
	}else if(isset($_POST['registro'])){
		if(mysqli_query($mysqli, "DELETE FROM svViaturas WHERE registro = '".$_POST['registro']."' and dataSaida = '".$_POST['data']."' and horarioSaida = '".$_POST['hora']."'")){
			$resultado = '<div class="alert alert-success mt-3 mb-0">Registro de viatura excluído com sucesso!</div>';
		}else{
			$resultado = '<div class="alert alert-danger mt-3 mb-0">Houve um erro ao excluir o registro de viatura!</div>';
		}
	}else if(isset($_POST['documento'])){
		if(mysqli_query($mysqli, "DELETE FROM svVisitantes WHERE documento = '".$_POST['documento']."' and dataEntrada = '".$_POST['data']."' and horarioEntrada = '".$_POST['hora']."'")){
			$resultado = '<div class="alert alert-success mt-3 mb-0">Registro de visitante excluído com sucesso!</div>';
		}else{
			$resultado = '<div class="alert alert-danger mt-3 mb-0">Houve um erro ao excluir o registro de visitante!</div>';
		}
	}else if(isset($_POST['excluir'])){
		if(excluir($_POST['dataExcluir']) != 1){
			$resultado = '<div class="alert alert-success mt-3 mb-0">O serviço foi excluído com sucesso!</div>';
		}else{
			$resultado = '<div class="alert alert-danger mt-3 mb-0">Não foi possível excluir o serviço!</div>';
		}
	}else if(isset($_POST['editar'])){
		if(strlen($_POST['data']) == 0){
			$resultado = '<div class="alert alert-danger mt-3 mb-0">A <b>data</b> não foi preenchida!</div>';
		}else if(strlen($_POST['sargento']) == 0){
			$resultado = '<div class="alert alert-danger mt-3 mb-0">O <b>sargento</b> não foi selecionado!</div>';
		}else if(strlen($_POST['motorista']) == 0){
			$resultado = '<div class="alert alert-danger mt-3 mb-0">O <b>motorista</b> não foi selecionado!</div>';
		}else{
			$data = $mysqli->real_escape_string($_POST['data']);
			$sargento = $mysqli->real_escape_string($_POST['sargento']);
			$motorista = $mysqli->real_escape_string($_POST['motorista']);
			
			$queryData = $mysqli->query("SELECT * FROM servicos WHERE data = '$data'") or die("Falha na execução do código SQL: ".$mysqli->error);
			$retorno = $queryData->num_rows;
			
			if($retorno != 0){
				$resultado = '<div class="alert alert-danger mt-3 mb-0">Já existe um serviço registrado nessa data!</div>';
			}else{
				if(editar($data, $sargento, $motorista, $editar['data']) != 1){
					header('Location: servicos-admin.php?data='.$data.'&flag=1');
				}else{
					$resultado = '<div class="alert alert-danger mt-3 mb-0">Houve um erro ao editar o serviço!</div>';
				}
			}
		}
	}else if(isset($_POST['sv'])){
		if(strlen($_POST['dataSV']) == 0){
			$resultado = '<div class="alert alert-danger mt-3 mb-0">A <b>data</b> não foi preenchida!</div>';
		}else{
			$data = $mysqli->real_escape_string($_POST['dataSV']);
			
			$queryServico = $mysqli->query("SELECT * FROM servicos WHERE data = '$data'") or die("Falha na execução do código SQL: ".$mysqli->error);
			$retorno = $queryServico->num_rows;
			
			if($retorno == 0){
				$resultado = '<div class="alert alert-danger mt-3 mb-0">Não existe um serviço registrado nesta data!</div>';
			}else{
				header('Location: servicos-admin.php?data='.$data);
			}
		}
	}else if(isset($_POST['btnLimpar'])){
		if(limpar() != 1){
			$resultado = '<div class="alert alert-success mt-3 mb-0">As fichas foram deletadas com sucesso!</div>';
		}else{
			$resultado = '<div class="alert alert-danger mt-3 mb-0">Não foi possível deletar as fichas!</div>';
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
		<title>Arachne - Editar Serviço</title>
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
			<div class="modal fade" id="modalExcluir" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title"><b>Atenção:</b></h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<p class="m-0">Tem certeza que deseja excluir o serviço do dia <b><?php echo date("d/m/Y", strtotime($editar['data'])); ?></b>?</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-dark" data-bs-dismiss="modal">Não</button>
							<form action="" method="POST"><input type="hidden" id="dataExcluir" name="dataExcluir" value="<?php echo $editar['data']; ?>"><button id="excluir" name="excluir" type="submit" class="btn btn-primary">Sim</button></form>
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
								<a class="nav-link active" aria-current="page" href="#" data-bs-toggle="modal" data-bs-target="#servicos">Serviços</a>
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
			<div class="container-fluid p-3">
				<div class="row mb-3">
					<div class="col">
						<div class="bg-dark p-2 text-white">
							<span class="h6">Informações do Serviço</span>
							<span class="btn p-0 m-0 h6" style="float: right;" data-bs-toggle="collapse" data-bs-target="#infoSV"><img src="images/expand.png" alt="" width="18" height="18"></span>
						</div>
						<div id="infoSV" class="collapse show">
							<form id="formEditar" name="formEditar" class="border mb-0 pt-0 p-3" style="background-color: #f5f5f5;" action="" method="POST">
								<div class="row">
									<div class="col">
										<label class="mt-2" for="data">Data:</label>
										<input id="data" name="data" type="date" class="form-control" value="<?php echo $editar['data']; ?>" required>
									</div>
									<div class="col">
										<label class="mt-2" for="sargento">Sargento:</label>
										<select id="sargento" name="sargento" autocomplete="off" required>
											<option selected disabled value="">Selecione um sargento...</option>
											<?php
												if($result = $mysqli->query("SELECT * FROM efetivo WHERE saram <> 'Administrador' ORDER BY nome")){
													while($row = $result->fetch_assoc()){
														if($row["posto"]." ".$row["nome"] == $editar["sargento"]){
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
										<label class="mt-2" for="motorista">Motorista:</label>
										<select id="motorista" name="motorista" autocomplete="off" required>
											<option selected disabled value="">Selecione um motorista...</option>
											<?php
												if($result = $mysqli->query("SELECT * FROM efetivo WHERE saram <> 'Administrador' ORDER BY nome")){
													while($row = $result->fetch_assoc()){
														if($row["posto"]." ".$row["nome"] == $editar["motorista"]){
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
										<label class="mt-2" style="visibility: hidden;" for="excluir">Excluir:</label>
										<button type="button" class="w-100 btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalExcluir">Excluir</button>
									</div>
									<div class="col">
										<label class="mt-2" style="visibility: hidden;" for="editar">Editar:</label>
										<button id="editar" name="editar" form="formEditar" type="submit" class="w-100 btn btn-primary">Editar</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col">
						<div class="bg-dark p-2 text-white">
							<span class="h6">Ficha de Controle do Claviculário - <?php echo date("d/m/Y", strtotime($editar['data'])); ?></span>
							<span class="btn p-0 m-0 h6" style="float: right;" data-bs-toggle="collapse" data-bs-target="#fichaChaves"><img src="images/expand.png" alt="" width="18" height="18"></span>
						</div>
						<div id="fichaChaves" class="collapse show">
							<div class="table-responsive">
								<table class="table table-sm table-hover table-bordered text-center align-middle my-0">
									<thead>
										<tr>
											<th style="background-color: #f5f5f5;" scope="col">Número</th>
											<th style="background-color: #f5f5f5;" scope="col">Chave</th>
											<th style="background-color: #f5f5f5;" colspan="2" scope="col">Retirada</th>
											<th style="background-color: #f5f5f5;" scope="col">Militar</th>
											<th style="background-color: #f5f5f5;" scope="col">Sentinela</th>
											<th class="table-secondary" colspan="2" scope="col">Devolução</th>
											<th class="table-secondary" scope="col">Militar</th>
											<th class="table-secondary" scope="col">Sentinela</th>
											<th class="table-secondary" colspan="2" scope="col">Ações</th>
										</tr>
									</thead>
									<tbody>
										<?php
											if($result = $mysqli->query("SELECT * FROM svChaves WHERE servico = '".$editar['data']."' OR servico2 = '".$editar['data']."' ORDER BY dataRetirada, horarioRetirada;")){
												while($row = $result->fetch_assoc()){
													if($row["dataDevolucao"] != null){
														$dataAtualizada = date("d/m/Y", strtotime($row["dataDevolucao"]));
													}else{
														$dataAtualizada = null;
													}
													
													echo '<div class="modal fade" id="chave-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["numero"])).'-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["dataRetirada"])).'-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["horarioRetirada"])).'" tabindex="-1" aria-hidden="true">
														<div class="modal-dialog">
															<div class="modal-content">
																<div class="modal-header">
																	<h5 class="modal-title"><b>Atenção:</b></h5>
																	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
																</div>
																<div class="modal-body">
																	<p>Tem certeza que deseja excluir o registro da seguinte chave:</p>
																	<p><b>'.$row["numero"].' - '.date("d/m/Y", strtotime($row["dataRetirada"])).' às '.$row["horarioRetirada"].'?</b></p>
																</div>
																<div class="modal-footer">
																	<button type="button" class="btn btn-dark" data-bs-dismiss="modal">Não</button>
																	<form action="" method="POST"><input type="hidden" id="chave" name="chave" value="'.$row["numero"].'"><input type="hidden" id="data" name="data" value="'.$row["dataRetirada"].'"><input type="hidden" id="hora" name="hora" value="'.$row["horarioRetirada"].'"><button type="submit" class="btn btn-primary">Sim</button></form>
																</div>
															</div>
														</div>
													</div>
													<tr>
														<th style="background-color: #fff;" scope="row">'.$row["numero"].'</th>
														<td style="background-color: #fff;">'.$row["chave"].'</td>
														<td style="background-color: #fff;">'.date("d/m/Y", strtotime($row["dataRetirada"])).'</td>
														<td style="background-color: #fff;">'.$row["horarioRetirada"].'</td>
														<td style="background-color: #fff;">'.$row["nomeRetirada"].'</td>
														<td style="background-color: #fff;">'.$row["permanenciaRetirada"].'</td>
														<td class="table-light">'.$dataAtualizada.'</td>
														<td class="table-light">'.$row["horarioDevolucao"].'</td>
														<td class="table-light">'.$row["nomeDevolucao"].'</td>
														<td class="table-light">'.$row["permanenciaDevolucao"].'</td>
														<td class="table-light"><button onclick="location.href=\'ficha-chaves-admin.php?numero='.$row["numero"].'&data='.$row["dataRetirada"].'&hora='.$row["horarioRetirada"].'\'" type="submit" class="btn m-0 p-0 bg-transparent"><img src="images/edit.png" alt="" width="16" height="16"></button></td>
														<td class="table-light"><button type="button" class="btn m-0 p-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#chave-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["numero"])).'-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["dataRetirada"])).'-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["horarioRetirada"])).'"><img src="images/delete.png" alt="" width="16" height="16"></button></td>
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
				<div class="row mb-3">
					<div class="col">
						<div class="bg-dark p-2 text-white">
							<span class="h6">Ficha de Controle de Viaturas - <?php echo date("d/m/Y", strtotime($editar['data'])); ?></span>
							<span class="btn p-0 m-0 h6" style="float: right;" data-bs-toggle="collapse" data-bs-target="#fichaViaturas"><img src="images/expand.png" alt="" width="18" height="18"></span>
						</div>
						<div id="fichaViaturas" class="collapse show">
							<div class="table-responsive">
								<table class="table table-sm table-hover table-bordered text-center align-middle my-0">
									<thead>
										<tr>
											<th style="background-color: #f5f5f5;" scope="col">Registro</th>
											<th style="background-color: #f5f5f5;" scope="col">Modelo</th>
											<th style="background-color: #f5f5f5;" scope="col">Motorista</th>
											<th style="background-color: #f5f5f5;" scope="col">Destino</th>
											<th style="background-color: #f5f5f5;" colspan="2" scope="col">Saída</th>
											<th style="background-color: #f5f5f5;" scope="col">Hodômetro</th>
											<th style="background-color: #f5f5f5;" scope="col">Sentinela</th>
											<th class="table-secondary" colspan="2" scope="col">Retorno</th>
											<th class="table-secondary" scope="col">Motorista</th>
											<th class="table-secondary" scope="col">Hodômetro</th>
											<th class="table-secondary" scope="col">Sentinela</th>
											<th class="table-secondary" colspan="2" scope="col">Ações</th>
										</tr>
									</thead>
									<tbody>
										<?php
											if($result = $mysqli->query("SELECT * FROM svViaturas WHERE servico = '".$editar['data']."' OR servico2 = '".$editar['data']."' ORDER BY dataSaida, horarioSaida;")){
												while($row = $result->fetch_assoc()){
													if($row["dataRetorno"] != null){
														$dataAtualizada = date("d/m/Y", strtotime($row["dataRetorno"]));
													}else{
														$dataAtualizada = null;
													}
													
													echo '<div class="modal fade" id="viatura-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["registro"])).'-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["dataSaida"])).'-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["horarioSaida"])).'" tabindex="-1" aria-hidden="true">
														<div class="modal-dialog">
															<div class="modal-content">
																<div class="modal-header">
																	<h5 class="modal-title"><b>Atenção:</b></h5>
																	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
																</div>
																<div class="modal-body">
																	<p>Tem certeza que deseja excluir o registro da seguinte viatura:</p>
																	<p><b>'.$row["registro"].' - '.date("d/m/Y", strtotime($row["dataSaida"])).' às '.$row["horarioSaida"].'?</b></p>
																</div>
																<div class="modal-footer">
																	<button type="button" class="btn btn-dark" data-bs-dismiss="modal">Não</button>
																	<form action="" method="POST"><input type="hidden" id="registro" name="registro" value="'.$row["registro"].'"><input type="hidden" id="data" name="data" value="'.$row["dataSaida"].'"><input type="hidden" id="hora" name="hora" value="'.$row["horarioSaida"].'"><button type="submit" class="btn btn-primary">Sim</button></form>
																</div>
															</div>
														</div>
													</div>
													<tr>
														<th style="background-color: #fff;" scope="row">'.$row["registro"].'</th>
														<td style="background-color: #fff;">'.$row["modelo"].'</td>
														<td style="background-color: #fff;">'.$row["motoristaSaida"].'</td>
														<td style="background-color: #fff;">'.$row["destino"].'</td>
														<td style="background-color: #fff;">'.date("d/m/Y", strtotime($row["dataSaida"])).'</td>
														<td style="background-color: #fff;">'.$row["horarioSaida"].'</td>
														<td style="background-color: #fff;">'.$row["hodometroSaida"].'</td>
														<td style="background-color: #fff;">'.$row["permanenciaSaida"].'</td>
														<td class="table-light">'.$dataAtualizada.'</td>
														<td class="table-light">'.$row["horarioRetorno"].'</td>
														<td class="table-light">'.$row["motoristaRetorno"].'</td>
														<td class="table-light">'.$row["hodometroRetorno"].'</td>
														<td class="table-light">'.$row["permanenciaRetorno"].'</td>
														<td class="table-light"><button onclick="location.href=\'ficha-viaturas-admin.php?registro='.$row["registro"].'&data='.$row["dataSaida"].'&hora='.$row["horarioSaida"].'\'" type="submit" class="btn m-0 p-0 bg-transparent"><img src="images/edit.png" alt="" width="16" height="16"></button></td>
														<td class="table-light"><button type="button" class="btn m-0 p-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#viatura-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["registro"])).'-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["dataSaida"])).'-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["horarioSaida"])).'"><img src="images/delete.png" alt="" width="16" height="16"></button></td>
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
				<div class="row">
					<div class="col">
						<div class="bg-dark p-2 text-white">
							<span class="h6">Ficha de Controle de Visitantes - <?php echo date("d/m/Y", strtotime($editar['data'])); ?></span>
							<span class="btn p-0 m-0 h6" style="float: right;" data-bs-toggle="collapse" data-bs-target="#fichaVisitantes"><img src="images/expand.png" alt="" width="18" height="18"></span>
						</div>
						<div id="fichaVisitantes" class="collapse show">
							<div class="table-responsive">
								<table class="table table-sm table-hover table-bordered text-center align-middle my-0">
									<thead>
										<tr class="cinza">
											<th style="background-color: #f5f5f5;" scope="col">Documento</th>
											<th style="background-color: #f5f5f5;" scope="col">Nome</th>
											<th style="background-color: #f5f5f5;" scope="col">Empresa</th>
											<th style="background-color: #f5f5f5;" scope="col">Crachá</th>
											<th style="background-color: #f5f5f5;" colspan="2" scope="col">Entrada</th>
											<th style="background-color: #f5f5f5;" scope="col">Responsável</th>
											<th style="background-color: #f5f5f5;" scope="col">Sentinela</th>
											<th class="table-secondary" colspan="2" scope="col">Saída</th>
											<th class="table-secondary" scope="col">Sentinela</th>
											<th class="table-secondary" colspan="2" scope="col">Ações</th>
										</tr>
									</thead>
									<tbody>
										<?php
											if($result = $mysqli->query("SELECT * FROM svVisitantes WHERE servico = '".$editar['data']."' OR servico2 = '".$editar['data']."' ORDER BY dataEntrada, horarioEntrada;")){
												while($row = $result->fetch_assoc()){
													if($row["dataSaida"] != null){
														$dataAtualizada = date("d/m/Y", strtotime($row["dataSaida"]));
													}else{
														$dataAtualizada = null;
													}
													
													echo '<div class="modal fade" id="visitante-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["documento"])).'-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["dataEntrada"])).'-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["horarioEntrada"])).'" tabindex="-1" aria-hidden="true">
														<div class="modal-dialog">
															<div class="modal-content">
																<div class="modal-header">
																	<h5 class="modal-title"><b>Atenção:</b></h5>
																	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
																</div>
																<div class="modal-body">
																	<p>Tem certeza que deseja excluir o registro do seguinte visitante:</p>
																	<p><b>'.$row["documento"].' - '.date("d/m/Y", strtotime($row["dataEntrada"])).' às '.$row["horarioEntrada"].'?</b></p>
																</div>
																<div class="modal-footer">
																	<button type="button" class="btn btn-dark" data-bs-dismiss="modal">Não</button>
																	<form action="" method="POST"><input type="hidden" id="documento" name="documento" value="'.$row["documento"].'"><input type="hidden" id="data" name="data" value="'.$row["dataEntrada"].'"><input type="hidden" id="hora" name="hora" value="'.$row["horarioEntrada"].'"><button type="submit" class="btn btn-primary">Sim</button></form>
																</div>
															</div>
														</div>
													</div>
													<tr>
														<th style="background-color: #fff;" scope="row">'.$row["documento"].'</th>
														<td style="background-color: #fff;">'.$row["nome"].'</td>
														<td style="background-color: #fff;">'.$row["empresa"].'</td>
														<td style="background-color: #fff;">'.$row["cracha"].'</td>
														<td style="background-color: #fff;">'.date("d/m/Y", strtotime($row["dataEntrada"])).'</td>
														<td style="background-color: #fff;">'.$row["horarioEntrada"].'</td>
														<td style="background-color: #fff;">'.$row["responsavel"].'</td>
														<td style="background-color: #fff;">'.$row["permanenciaEntrada"].'</td>
														<td class="table-light">'.$dataAtualizada.'</td>
														<td class="table-light">'.$row["horarioSaida"].'</td>
														<td class="table-light">'.$row["permanenciaSaida"].'</td>
														<td class="table-light"><button onclick="location.href=\'ficha-visitantes-admin.php?documento='.$row["documento"].'&data='.$row["dataEntrada"].'&hora='.$row["horarioEntrada"].'\'" type="submit" class="btn m-0 p-0 bg-transparent"><img src="images/edit.png" alt="" width="16" height="16"></button></td>
														<td class="table-light"><button type="button" class="btn m-0 p-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#visitante-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["documento"])).'-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["dataEntrada"])).'-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["horarioEntrada"])).'"><img src="images/delete.png" alt="" width="16" height="16"></button></td>
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
				<?php if(isset($resultado)){echo "$resultado";}elseif(isset($_GET['flag'])){if($_GET['flag'] == 1){echo '<div class="alert alert-success mt-3 mb-0">O serviço foi editado com sucesso!</div>';}} ?>
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
			
			new TomSelect("#sargento",{
				create: false,
				render:{
					no_results: function( data, escape ){
						return '<div class="no-results">Nenhum sargento encontrado...</div>';
					},
				}
			});
		</script>
	</body>
</html>