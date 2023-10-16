<?php
	include('connection.php');

	function logout(){
		unset($_SESSION["saram"]);
		unset($_SESSION["posto"]);
		unset($_SESSION["nome"]);
		unset($_SESSION["permissao"]);
		session_destroy();
		header("Location: autenticacao.php");
	}
	
	function limpar(){
		global $mysqli;
		
		if(mysqli_query($mysqli, "DELETE FROM servicos WHERE YEAR(data) <= '".(date("Y") - 5)."'")){
			if(!mysqli_query($mysqli, "DELETE FROM svChaves WHERE YEAR(servico) <= '".(date("Y") - 5)."'")){
				return(1);
			}
			
			if(!mysqli_query($mysqli, "DELETE FROM svViaturas WHERE YEAR(servico) <= '".(date("Y") - 5)."'")){
				return(1);
			}
			
			if(!mysqli_query($mysqli, "DELETE FROM svVisitantes WHERE YEAR(servico) <= '".(date("Y") - 5)."'")){
				return(1);
			}
		}else{
			return(1);
		}
	}
	
	function excluir($data){
		global $mysqli;
		
		if(mysqli_query($mysqli, "DELETE FROM servicos WHERE data = '$data';")){
			if(!mysqli_query($mysqli, "DELETE FROM svChaves WHERE servico = '$data';")){
				return(1);
			}
			
			if(!mysqli_query($mysqli, "DELETE FROM svViaturas WHERE servico = '$data';")){
				return(1);
			}
			
			if(!mysqli_query($mysqli, "DELETE FROM svVisitantes WHERE servico = '$data';")){
				return(1);
			}
		}else{
			return(1);
		}
	}
	
	function editar($data, $sargento, $motorista, $dataAntiga){
		global $mysqli;
		
		if(mysqli_query($mysqli, "UPDATE servicos SET data = '$data', sargento = '$sargento', motorista = '$motorista' WHERE data = '$dataAntiga';")){
			if(!mysqli_query($mysqli, "UPDATE svChaves SET servico = '$data' WHERE servico = '$dataAntiga';")){
				return(1);
			}

			if(!mysqli_query($mysqli, "UPDATE svViaturas SET servico = '$data' WHERE servico = '$dataAntiga';")){
				return(1);
			}
			
			if(!mysqli_query($mysqli, "UPDATE svVisitantes SET servico = '$data' WHERE servico = '$dataAntiga';")){
				return(1);
			}
		}else{
			return(1);
		}
	}
?>