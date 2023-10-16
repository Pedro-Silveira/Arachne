<?php
	include('classes/protect.php');
	include('classes/functions-admin.php');
	
	if($_SESSION["permissao"] != 'Administrador'){
		header('Location: index.php');
	}

	if(isset($_POST['saram']) || isset($_POST['senha']) || isset($_POST['posto']) || isset($_POST['nome']) || isset($_POST['permissao'])){
		if(strlen($_POST['saram']) == 0){
			$resultado = '<div class="alert alert-danger">O <b>saram</b> não foi preenchido!</div>';
		}else if(strlen($_POST['senha']) == 0){
			$resultado = '<div class="alert alert-danger">A <b>senha</b> não foi preenchido!</div>';
		}else if(strlen($_POST['posto']) == 0){
			$resultado = '<div class="alert alert-danger">O <b>posto</b> não foi selecionado!</div>';
		}else if(strlen($_POST['nome']) == 0){
			$resultado = '<div class="alert alert-danger">O <b>nome</b> não foi preenchido!</div>';
		}else if(strlen($_POST['permissao']) == 0){
			$resultado = '<div class="alert alert-danger">A <b>permissao</b> não foi selecionada!</div>';
		}else{
			$saram = $mysqli->real_escape_string($_POST['saram']);
			$senha = $mysqli->real_escape_string($_POST['senha']);
			$posto = $mysqli->real_escape_string($_POST['posto']);
			$nome = $mysqli->real_escape_string($_POST['nome']);
			$permissao = $mysqli->real_escape_string($_POST['permissao']);

			$verificarEfetivo = $mysqli->query("SELECT * FROM efetivo WHERE saram = '$saram'") or die("Falha na execução do código SQL: ".$mysqli->error);
			$quantidade = $verificarEfetivo->num_rows;

			if($quantidade != 0){
				$resultado = '<div class="alert alert-danger">O militar preenchido já foi cadastrado!</div>';
			}else{
				if(mysqli_query($mysqli, "INSERT INTO efetivo(saram, senha, posto, nome, permissao) VALUES('$saram', md5('$senha'), '$posto', '$nome', '$permissao')")){
					$resultado = '<div class="alert alert-success">Militar cadastrado com sucesso!</div>';
				}else{
					$resultado = '<div class="alert alert-danger">Houve um erro ao cadastrar o militar!</div>';
				}
			}
		}
	}else if(isset($_POST['efetivo'])){
		if(mysqli_query($mysqli, "DELETE FROM efetivo WHERE saram = '".$_POST['efetivo']."'")) {
			$resultado = '<div class="alert alert-success">Militar excluído com sucesso!</div>';
		}else{
			$resultado = '<div class="alert alert-danger">Houve um erro ao excluir o militar!</div>';
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
		<title>Arachne - Cadastrar Efetivo</title>
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
								<a class="nav-link active" aria-current="page" href="efetivo-admin.php">Efetivo</a>
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
							<span class="h6">Cadastro de Efetivo</span>
						</div>
						<form class="border mb-3 pt-0 p-3" style="background-color: #f5f5f5;" action="" method="POST">
							<div class="row">
								<div class="col">
									<label class="mt-2" for="saram">Saram:</label>
									<input id="saram" name="saram" type="text" class="form-control mb-0" placeholder="Ex: 7138105, 7318456..." required>
								</div>
								<div class="col">
									<label class="mt-2" for="senha">Senha:</label>
									<div class="input-group">
										<input id="senha" name="senha" type="password" class="form-control rounded-start mb-0" placeholder="Ex: Abacaxi01, Aranha21..." required>
										<div class="input-group-text" onclick="password_show_hide();">
											<img id="show_eye" class="m-auto" src="images/eye.png" alt="" width="16" height="16">
											<img id="hide_eye" class="d-none m-auto" src="images/invisible.png" alt="" width="16" height="16">
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="posto">Posto:</label>
									<select id="posto" name="posto" autocomplete="off" required>
										<option selected disabled value="">Selecione um posto...</option>
										<option value="Civ">Civil</option>
										<option value="T2">Taifeiro de Segunda Classe</option>
										<option value="S2">Soldado de Segunda Classe</option>
										<option value="T1">Taifeiro de Primeira Classe</option>
										<option value="S1">Soldado de Primeira Classe</option>
										<option value="TM">Taifeiro-Mor</option>
										<option value="Cb">Cabo</option>
										<option value="3S">Terceiro Sargento</option>
										<option value="2S">Segundo Sargento</option>
										<option value="1S">Primeiro Sargento</option>
										<option value="SO">Suboficial</option>
										<option value="Asp">Aspirante</option>
										<option value="2T">Segundo Tenente</option>
										<option value="1T">Primeiro Tenente</option>
										<option value="Cap">Capitão</option>
										<option value="Maj">Major</option>
										<option value="Ten-Cel">Tenente-Coronel</option>
										<option value="Cel">Coronel</option>
										<option value="Brig">Brigadeiro</option>
										<option value="Maj-Brig">Major-Brigadeiro-do-Ar</option>
										<option value="Ten-Brig-Ar">Tenente-Brigadeiro-do-Ar</option>
										<option value="Mar-Ar">Marechal-do-Ar</option>
									</select>
								</div>
								<div class="col">
									<label class="mt-2" for="nome">Nome:</label>
									<input id="nome" name="nome" type="text" class="form-control mb-0" placeholder="Ex: Balsemão, Silveira..." required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label class="mt-2" for="permissao">Permissão:</label>
									<select id="permissao" name="permissao" autocomplete="off" required>
										<option selected disabled value="">Selecione uma permissão...</option>
										<option value="Usuário">Usuário</option>
										<option value="Sentinela">Sentinela</option>
										<option value="Sargento de Dia">Sargento de Dia</option>
										<option value="Administrador">Administrador</option>
									</select>
								</div>
							</div>
							<div class="row mt-3">
								<div class="col">
									<button id="limpar" type="reset" class="w-100 btn btn-dark">Limpar</button>
								</div>
								<div class="col">
									<button id="cadastrar" type="submit" class="w-100 btn btn-primary">Cadastrar</button>
								</div>
							</div>
						</form>
						<?php if(isset($resultado)){echo "$resultado";} ?>
					</div>
					<div class="col m-0">
						<div class="bg-dark p-2 text-white">
							<span class="h6">Listagem do Efetivo</span>
							<span class="badge bg-primary m-0" style="float: right;"><?php $query = $mysqli->query("SELECT * FROM efetivo WHERE saram <> 'Administrador'"); $quantidadeEfetivo = $query->num_rows; echo "$quantidadeEfetivo"; ?></span>
						</div>
						<div class="table-responsive mb-3">
							<table class="table table-hover table-bordered text-center align-middle my-0">
								<thead>
									<tr>
										<th style="background-color: #f5f5f5;" scope="col">Saram</th>
										<th style="background-color: #f5f5f5;" scope="col">Posto</th>
										<th style="background-color: #f5f5f5;" scope="col">Nome</th>
										<th style="background-color: #f5f5f5;" scope="col">Permissão</th>
										<th class="table-secondary" colspan="2" scope="col">Ações</th>
									</tr>
								</thead>
								<tbody>
									<?php
										if($result = $mysqli->query("SELECT * FROM efetivo ORDER BY saram;")){
											while($row = $result->fetch_assoc()){													
												echo '<div class="modal fade" id="efetivo-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["saram"])).'" tabindex="-1" aria-hidden="true">
													<div class="modal-dialog">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title"><b>Atenção:</b></h5>
																<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
															</div>
															<div class="modal-body">
																<p>Tem certeza que deseja excluir o seguinte militar:</p>
																<p><b>'.$row["saram"].' - '.$row["posto"].' '.$row["nome"].'?</b></p>
															</div>
															<div class="modal-footer">
																<button type="button" class="btn btn-dark" data-bs-dismiss="modal">Não</button>
																<form action="" method="POST"><input type="hidden" id="efetivo" name="efetivo" value="'.$row["saram"].'"><button type="submit" class="btn btn-primary">Sim</button></form>
															</div>
														</div>
													</div>
												</div>
												<tr>
													<th style="background-color: #fff;" scope="row">'.$row["saram"].'</th>
													<td style="background-color: #fff;">'.$row["posto"].'</td>
													<td style="background-color: #fff;">'.$row["nome"].'</td>
													<td style="background-color: #fff;">'.$row["permissao"].'</td>
													<td class="table-light"><button onclick="location.href=\'editar-efetivo-admin.php?saram='.$row["saram"].'\'" type="submit" class="btn m-0 p-0 bg-transparent"><img src="images/edit.png" alt="" width="16" height="16"></button></td>
													<td class="table-light"><button type="button" class="btn m-0 p-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#efetivo-'.trim(preg_replace("/[^a-zA-Z0-9]+/", "", $row["saram"])).'"><img src="images/delete.png" alt="" width="16" height="16"></button></td>
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
			new TomSelect("#posto",{
				create: false,
				render:{
					no_results: function( data, escape ){
						return '<div class="no-results">Nenhum posto encontrado...</div>';
					},
				}
			});
			
			new TomSelect("#permissao",{
				create: false,
				render:{
					no_results: function( data, escape ){
						return '<div class="no-results">Nenhuma permissão encontrada...</div>';
					},
				}
			});
			
			function password_show_hide(){
				var x = document.getElementById("senha");
				var show_eye = document.getElementById("show_eye");
				var hide_eye = document.getElementById("hide_eye");
				hide_eye.classList.remove("d-none");
				
				if(x.type === "password"){
					x.type = "text";
					show_eye.style.display = "none";
					hide_eye.style.display = "block";
				}else{
					x.type = "password";
					show_eye.style.display = "block";
					hide_eye.style.display = "none";
				}
			}
		</script>
	</body>
</html>
