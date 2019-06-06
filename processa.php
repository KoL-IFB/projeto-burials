<?php

require_once("conecta.php"); 

########## Pesquisa no BD ####################

if(isset($_POST['numero-camadas']) and isset($_POST['classe-camada']) and isset($_POST['tamanho-janela'])){
	
	$numero = $_POST['numero-camadas'];
	$classe = $_POST['classe-camada'];
	$tamanho= $_POST['tamanho-janela'];


	echo pesquisaDB($numero, $classe, $tamanho);
}else if(!(isset($_POST["tamanho-janela"])) and isset($_POST['numero-camadas']) and isset($_POST['classe-camada'])){

	$numero = $_POST['numero-camadas'];
	$classe = $_POST['classe-camada'];


	echo pesquisaDB2($numero, $classe);
}else if(!(isset($_POST["numero-camadas"])) and isset($_POST['tamanho-janela']) and isset($_POST['classe-camada'])){

	$tamanho= $_POST['tamanho-janela'];
	$classe = $_POST['classe-camada'];



	echo pesquisaDB3($tamanho, $classe);
}



##############################################


    



function pesquisaDB(string $numero, string $classe, string $tamanho){
	
		global $pdo;
		$query1 = $pdo->prepare('SELECT p.pdb, ch.sequence, ch.burials, p.burials AS prediction, p.accuracy
					FROM prediction AS p
					JOIN configuration AS cf ON p.configuration = cf.id
					JOIN dataset as ds ON cf.dataset = ds.id
					JOIN chain as ch ON p.pdb = ch.pdb
					WHERE ch.dataset = ds.id
					AND ds.nlayers= ?
					AND ds.size= ?
					AND cf.windowsize= ?');

		$resultado = $query1->execute(array($numero, $classe, $tamanho));

		

		$nlinhas = $query1->rowCount();
		$ncampos = $query1->columnCount();
		#####FALTA MONTAR AS TABELAS COM O RESULTADO#######
		$string_teste = "<p>A tabela retornada tem $ncampos campos e $nlinhas linhas.</p>\n";
		return $string_teste;

}

function pesquisaDB2(string $numero, string $classe){
	
		global $pdo;	
		$query2 =$pdo->prepare('SELECT cf.windowsize, AVG(p.accuracy) AS avgacc
					FROM prediction AS p
					JOIN configuration AS cf ON p.configuration = cf.id
					JOIN dataset as ds ON cf.dataset = ds.id
					JOIN chain as ch ON p.pdb = ch.pdb
					WHERE ch.dataset = ds.id
					AND ds.nlayers=?
					AND ds.size=?
					GROUP BY (cf.windowsize)') ;
		
		$resultado = $query2->execute(array($numero, $classe));

		$nlinhas = $query2->rowCount();
		$ncampos = $query2->columnCount();
		#####FALTA MONTAR AS TABELAS COM O RESULTADO#######
		$string_teste = "<p>A tabela retornada tem $ncampos campos e $nlinhas linhas.</p>\n";
		
		return $string_teste;
}


function pesquisaDB3(string $tamanho, string $classe){
		
		global $pdo;
		$query3 = $pdo->prepare('SELECT ds.nlayers, AVG(p.accuracy) AS avgacc
					FROM prediction AS p
					JOIN configuration AS cf ON p.configuration = cf.id
					JOIN dataset as ds ON cf.dataset = ds.id
					JOIN chain as ch ON p.pdb = ch.pdb
					WHERE ch.dataset = ds.id
					AND cf.windowsize = ?
					AND ds.size = ?
					GROUP BY (ds.nlayers)');


		$resultado = $query3->execute(array($tamanho, $classe));

		$nlinhas = $query3->rowCount();
		$ncampos = $query3->columnCount();
		#####FALTA MONTAR AS TABELAS COM O RESULTADO#######
		$string_teste = "<p>A tabela retornada tem $ncampos campos e $nlinhas linhas.</p>\n";
		return $string_teste;

}
?>
