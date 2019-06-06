<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		table {text-align: center; font:
10pt,"verdana", width:1% }
	</style>
</head>
<body>
<?php

require_once("conecta.php"); 

########## Pesquisa no BD ####################

if(isset($_POST['numero-camadas']) and isset($_POST['classe-camada']) and isset($_POST['tamanho-janela'])){
	
	$numero = $_POST['numero-camadas'];
	$classe = $_POST['classe-camada'];
	$tamanho= $_POST['tamanho-janela'];

	echo "<center>";
	echo "<table border='1' >
	<tr><td><strong><center>pdb</center></strong></td><td><strong><center>sequence</center></strong></td><td><strong><center>burials</center></strong></td><td><strong><center>prediction</center></strong></td><td><strong><center>accuracy</center></strong></td></tr>";
	echo pesquisaDB($numero, $classe, $tamanho);
	echo "</table>";
	echo "</center>";
}else if(!(isset($_POST["tamanho-janela"])) and isset($_POST['numero-camadas']) and isset($_POST['classe-camada'])){

	$numero = $_POST['numero-camadas'];
	$classe = $_POST['classe-camada'];

	echo "<center>";
	echo "<table border='1' >
	<tr><td><strong><center>windowsize</center></strong></td><td><strong><center>avgacc</center></strong></td></tr>";
	echo pesquisaDB2($numero, $classe);
	echo "</table>";
	echo "</center>";

}else if(!(isset($_POST["numero-camadas"])) and isset($_POST['tamanho-janela']) and isset($_POST['classe-camada'])){

	$tamanho= $_POST['tamanho-janela'];
	$classe = $_POST['classe-camada'];

	echo "<center>";
	echo "<table border='1' >
	<tr><td><strong><center>nlayers</center></strong></td><td><strong><center>avgacc</center></strong></td></tr>";
	echo pesquisaDB3($tamanho, $classe);
	echo "</table>";
	echo "</center>";
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
		
		echo "<p>A tabela retornada tem $ncampos campos e $nlinhas linhas.</p>\n";
		
		$tabela = '';
		while($linha=$query1->fetch()){
			$tabela = $tabela."<tr><td><center>$linha[0]</center></td><td><center>$linha[1]</center></td><td><center>$linha[2]</center></td><td><center>$linha[3]</center></td><td><center>$linha[4]</center></td></tr>";
		}

		return $tabela;

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
		
		echo "<p>A tabela retornada tem $ncampos campos e $nlinhas linhas.</p>\n";
		
		$tabela = '';
		while($linha=$query2->fetch()){
			$tabela = $tabela."<tr><td><center>$linha[0]</center></td><td><center>$linha[1]</center></td></tr>";
		}
		return $tabela;
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
		
		echo "<p>A tabela retornada tem $ncampos campos e $nlinhas linhas.</p>\n";
		
		$tabela = '';
		while($linha=$query3->fetch()){
			$tabela = $tabela."<tr><td><center>$linha[0]</center</td><td><center>$linha[1]</center></td></tr>";
		}
		
			
		return $tabela;

}
?>




</body>
</html>
