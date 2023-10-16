<?php
	include('connection.php');
	include('pdf_mc_table.php');

	function logout(){
		unset($_SESSION["saram"]);
		unset($_SESSION["posto"]);
		unset($_SESSION["nome"]);
		unset($_SESSION["permissao"]);
		session_destroy();
		header("Location: autenticacao.php");
	}

	function passar($data, $motorista){
		global $mysqli;
		
		if(mysqli_query($mysqli, "INSERT INTO servicos(data, sargento, motorista) VALUES('$data', '".$_SESSION['posto']." ".$_SESSION['nome']."', '$motorista')")){
			if($result = $mysqli->query("SELECT * FROM svChaves WHERE servico IS NULL")){
				while($row = $result->fetch_assoc()){
					if(!mysqli_query($mysqli, "UPDATE svChaves SET servico = '$data' WHERE numero = '".$row['numero']."' AND dataRetirada = '".$row['dataRetirada']."' AND horarioRetirada = '".$row['horarioRetirada']."'")){
						return(1);
					}
				}
			}else{
				return(1);
			}

			if($result = $mysqli->query("SELECT * FROM svViaturas WHERE servico IS NULL")){
				while($row = $result->fetch_assoc()){
					if(!mysqli_query($mysqli, "UPDATE svViaturas SET servico = '$data' WHERE registro = '".$row['registro']."' AND dataSaida = '".$row['dataSaida']."' AND horarioSaida = '".$row['horarioSaida']."'")){
						return(1);
					}
				}
			}else{
				return(1);
			}

			if($result = $mysqli->query("SELECT * FROM svVisitantes WHERE servico IS NULL")){
				while($row = $result->fetch_assoc()){
					if(!mysqli_query($mysqli, "UPDATE svVisitantes SET servico = '$data' WHERE documento = '".$row['documento']."' AND dataEntrada = '".$row['dataEntrada']."' AND horarioEntrada = '".$row['horarioEntrada']."'")){
						return(1);
					}
				}
			}else{
				return(1);
			}
		}else{
			return(1);
		}
	}

	function pdf($editar, $retorno, $data){
		global $mysqli;
				
		$pdf = new PDF_MC_Table();
		$pdf->isFinished = false;
		$pdf->AliasNbPages();
		$pdf->AddPage('L');
				
		if($retorno != 0){
			$pdf->SetWidths(Array(23, 27, 23, 38, 38, 28, 24, 38, 38));
			$pdf->SetAligns(Array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
			$pdf->SetLineHeight(5);
			
			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Cell(0, 0, utf8_decode('Ficha de Controle do Claviculário'), 0, 1);
			$pdf->Ln(5);
			$pdf->SetFont('Arial', 'B', 10);
			$pdf->Cell(23, 6, utf8_decode("Número"), 1, 0, 'C');
			$pdf->Cell(27, 6, utf8_decode("Retirada"), 1, 0, 'C');
			$pdf->Cell(23, 6, utf8_decode("Horário"), 1, 0, 'C');
			$pdf->Cell(38, 6, utf8_decode("Nome"), 1, 0, 'C');
			$pdf->Cell(38, 6, utf8_decode("Sentinela"), 1, 0, 'C');
			$pdf->Cell(28, 6, utf8_decode("Devolução"), 1, 0, 'C');
			$pdf->Cell(24, 6, utf8_decode("Horário"), 1, 0, 'C');
			$pdf->Cell(38, 6, utf8_decode("Nome"), 1, 0, 'C');
			$pdf->Cell(38, 6, utf8_decode("Sentinela"), 1, 0, 'C');
			$pdf->Ln();
					
			$result = $mysqli->query("SELECT * FROM svChaves WHERE servico = '".$editar['data']."' OR servico2 = '".$editar['data']."' ORDER BY dataRetirada, horarioRetirada;");
			$pdf->SetFont('Arial', '', 10);
					
			while($row = $result->fetch_assoc()){
				if($row['dataDevolucao'] != null){
					$saida = date("d/m/Y", strtotime($row["dataDevolucao"]));
				}else{
					$saida = null;
				}
						
				$pdf->Row(Array($row['numero'], date("d/m/Y", strtotime($row["dataRetirada"])), $row['horarioRetirada'], utf8_decode($row['nomeRetirada']), utf8_decode($row['permanenciaRetirada']), $saida, $row['horarioDevolucao'], utf8_decode($row['nomeDevolucao']), utf8_decode($row['permanenciaDevolucao'])));
			}
					
			$pdf->SetWidths(Array(18, 20, 20, 16, 25, 25, 21, 25, 20, 16, 25, 21, 25));
			$pdf->SetAligns(Array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
			$pdf->SetLineHeight(5);
					
			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Ln(5);
			$pdf->Cell(0, 0, utf8_decode('Ficha de Controle de Viaturas'), 0, 1);
			$pdf->Ln(5);
			$pdf->SetFont('Arial', 'B', 10);
			$pdf->Cell(18, 6, utf8_decode("Registro"), 1, 0, 'C');
			$pdf->Cell(20, 6, utf8_decode("Modelo"), 1, 0, 'C');
			$pdf->Cell(20, 6, utf8_decode("Saída"), 1, 0, 'C');
			$pdf->Cell(16, 6, utf8_decode("Horário"), 1, 0, 'C');
			$pdf->Cell(25, 6, utf8_decode("Motorista"), 1, 0, 'C');
			$pdf->Cell(25, 6, utf8_decode("Destino"), 1, 0, 'C');
			$pdf->Cell(21, 6, utf8_decode("Hodômetro"), 1, 0, 'C');
			$pdf->Cell(25, 6, utf8_decode("Sentinela"), 1, 0, 'C');
			$pdf->Cell(20, 6, utf8_decode("Retorno"), 1, 0, 'C');
			$pdf->Cell(16, 6, utf8_decode("Horário"), 1, 0, 'C');
			$pdf->Cell(25, 6, utf8_decode("Motorista"), 1, 0, 'C');
			$pdf->Cell(21, 6, utf8_decode("Hodômetro"), 1, 0, 'C');
			$pdf->Cell(25, 6, utf8_decode("Sentinela"), 1, 0, 'C');
			$pdf->Ln();
					
			$result = $mysqli->query("SELECT * FROM svViaturas WHERE servico = '".$editar['data']."' OR servico2 = '".$editar['data']."' ORDER BY dataSaida, horarioSaida;");
			$pdf->SetFont('Arial', '', 10);
					
			while($row = $result->fetch_assoc()){
				if($row['dataRetorno'] != null){
					$saida = date("d/m/Y", strtotime($row["dataRetorno"]));
				}else{
					$saida = null;
				}
						
				$pdf->Row(Array($row['registro'], utf8_decode($row['modelo']), date("d/m/Y", strtotime($row["dataSaida"])), $row['horarioSaida'], utf8_decode($row['motoristaSaida']), utf8_decode($row['destino']), $row['hodometroSaida'], utf8_decode($row['permanenciaSaida']), $saida, $row['horarioRetorno'], utf8_decode($row['motoristaRetorno']), $row['hodometroRetorno'], utf8_decode($row['permanenciaRetorno'])));
			}
					
			$pdf->SetWidths(Array(25, 50, 20, 16, 15, 25, 30, 30, 20, 16, 30));
			$pdf->SetAligns(Array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
			$pdf->SetLineHeight(5);
					
			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Ln(5);
			$pdf->Cell(0, 0, utf8_decode('Ficha de Controle de Visitantes'), 0, 1);
			$pdf->Ln(5);
			$pdf->SetFont('Arial', 'B', 10);
			$pdf->Cell(25, 6, utf8_decode("Documento"), 1, 0, 'C');
			$pdf->Cell(50, 6, utf8_decode("Nome"), 1, 0, 'C');
			$pdf->Cell(20, 6, utf8_decode("Entrada"), 1, 0, 'C');
			$pdf->Cell(16, 6, utf8_decode("Horário"), 1, 0, 'C');
			$pdf->Cell(15, 6, utf8_decode("Crachá"), 1, 0, 'C');
			$pdf->Cell(25, 6, utf8_decode("Empresa"), 1, 0, 'C');
			$pdf->Cell(30, 6, utf8_decode("Responsável"), 1, 0, 'C');
			$pdf->Cell(30, 6, utf8_decode("Sentinela"), 1, 0, 'C');
			$pdf->Cell(20, 6, utf8_decode("Saída"), 1, 0, 'C');
			$pdf->Cell(16, 6, utf8_decode("Horário"), 1, 0, 'C');
			$pdf->Cell(30, 6, utf8_decode("Sentinela"), 1, 0, 'C');
			$pdf->Ln();
					
			$result = $mysqli->query("SELECT * FROM svVisitantes WHERE servico = '".$editar['data']."' OR servico2 = '".$editar['data']."' ORDER BY dataEntrada, horarioEntrada;");
			$pdf->SetFont('Arial', '', 10);
					
			while($row = $result->fetch_assoc()){
				if($row['dataSaida'] != null){
					$saida = date("d/m/Y", strtotime($row["dataSaida"]));
				}else{
					$saida = null;
				}

				$pdf->Row(Array($row['documento'], utf8_decode($row['nome']), date("d/m/Y", strtotime($row["dataEntrada"])), $row['horarioEntrada'], $row['cracha'], utf8_decode($row['empresa']), utf8_decode($row['responsavel']), utf8_decode($row['permanenciaEntrada']), $saida, $row['horarioSaida'], utf8_decode($row['permanenciaSaida'])));
			}
			
			$pdf->SetWidths(Array(20, 18, 40, 40));
			$pdf->SetAligns(Array('C', 'C', 'C', 'C'));
			$pdf->SetLineHeight(5);
					
			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Ln(5);
			$pdf->Cell(0, 0, utf8_decode('Hodômetro de Viaturas'), 0, 1);
			$pdf->Ln(5);
			$pdf->SetFont('Arial', 'B', 10);
			$pdf->Cell(20, 6, utf8_decode("Modelo"), 1, 0, 'C');
			$pdf->Cell(18, 6, utf8_decode("Registro"), 1, 0, 'C');
			$pdf->Cell(40, 6, utf8_decode("Hodômetro Recebido"), 1, 0, 'C');
			$pdf->Cell(40, 6, utf8_decode("Hodômetro Passado"), 1, 0, 'C');
			$pdf->Ln();
			
			$viaturas = $mysqli->query("SELECT * FROM viaturas ORDER BY modelo, registro;");
			$pdf->SetFont('Arial', '', 10);
			
			while($row = $viaturas->fetch_assoc()){
				$sql1 = $mysqli->query("SELECT MAX(hodometroRetorno) as 'hodometro' FROM svViaturas WHERE registro = '".$row['registro']."' AND servico < '$data';");
				$sql2 = $mysqli->query("SELECT MAX(hodometroRetorno) as 'hodometro' FROM svViaturas WHERE registro = '".$row['registro']."' AND servico = '$data';");
				$recebido = $sql1->fetch_assoc();
				$passado = $sql2->fetch_assoc();
				
				if($passado['hodometro'] == ""){
					$hodometroPassado = $recebido['hodometro'];
				}else{
					$hodometroPassado = $passado['hodometro'];
				}

				$pdf->Row(Array(utf8_decode($row['modelo']), $row['registro'], $recebido['hodometro'], $hodometroPassado));
			}
			
			$pdf->SetWidths(Array(20, 16, 20, 16, 70));
			$pdf->SetAligns(Array('C', 'C', 'C', 'C', 'C'));
			$pdf->SetLineHeight(5);
					
			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Ln(5);
			$pdf->Cell(0, 0, utf8_decode('Saídas do Motorista'), 0, 1);
			$pdf->Ln(5);
			$pdf->SetFont('Arial', 'B', 10);
			$pdf->Cell(20, 6, utf8_decode("Saída"), 1, 0, 'C');
			$pdf->Cell(16, 6, utf8_decode("Horário"), 1, 0, 'C');
			$pdf->Cell(20, 6, utf8_decode("Retorno"), 1, 0, 'C');
			$pdf->Cell(16, 6, utf8_decode("Horário"), 1, 0, 'C');
			$pdf->Cell(70, 6, utf8_decode("Total"), 1, 0, 'C');
			$pdf->Ln();
			
			$result = $mysqli->query("SELECT * FROM svViaturas WHERE motoristaSaida = '".$editar['motorista']."' AND servico = '$data' AND dataRetorno IS NOT NULL ORDER BY dataSaida, horarioSaida;");
			$pdf->SetFont('Arial', '', 10);
			
			$geral = new DateTime('00:00:00');
			$geralClone = clone $geral;
			while($row = $result->fetch_assoc()){
				if($row['dataRetorno'] != null){
					$dtRetorno = date("d/m/Y", strtotime($row["dataRetorno"]));
				}else{
					$dtRetorno = null;
				}
				
				$saida = new DateTime($row['dataSaida'].' '.$row['horarioSaida']);
				$retorno = new DateTime($row['dataRetorno'].' '.$row['horarioRetorno']);
				$total = $saida->diff($retorno);
				$geral->add($total);
				$pdf->Row(Array(utf8_decode(date("d/m/Y", strtotime($row["dataSaida"]))), utf8_decode($row['horarioSaida']), utf8_decode($dtRetorno), utf8_decode($row['horarioRetorno']), utf8_decode($total->format('%h hora(s), %i minuto(s) e %s segundo(s)'))));
			}
			
			$pdf->SetWidths(Array(72, 70));
			$pdf->SetAligns(Array('C', 'C'));
			$pdf->Row(Array('Total Geral', utf8_decode($geralClone->diff($geral)->format('%h hora(s), %i minuto(s) e %s segundo(s)'))));
		}
		
		$pdf->isFinished = true;
		$pdf->Output('I', 'Relatório de Serviço ('.date("d-m-Y", strtotime($data)).').pdf', true);
	}
?>