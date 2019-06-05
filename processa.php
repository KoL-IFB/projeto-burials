<?php

require_once("conecta.php"); 

$query='';

if(isset($_POST['numero-camadas']) and isset($_POST['classe-camadas']) and isset($_POST['tamanho-janela'])){
	
	$query = 'SELECT p.pdb, ch.sequence, ch.burials, p.burials AS prediction, p.accuracy
		  FROM prediction AS p
		  JOIN configuration AS cf ON p.configuration = cf.id
		  JOIN dataset as ds ON cf.dataset = ds.id
		  JOIN chain as ch ON p.pdb = ch.pdb
		  WHERE ch.dataset = ds.id
		  AND ds.nlayers= $_POST["numero-camadas"]
		  AND ds.size= $_POST["classe-camadas"]
		  AND cf.windowsize= $_POST["tamanho-janela"]';

}else if(!(isset($_POST['tamanho-janela']))){
	
	$query = 'SELECT cf.windowsize, AVG(p.accuracy) AS avgacc
		  FROM prediction AS p
		  JOIN configuration AS cf ON p.configuration = cf.id
		  JOIN dataset as ds ON cf.dataset = ds.id
		  JOIN chain as ch ON p.pdb = ch.pdb
		  WHERE ch.dataset = ds.id
		  AND ds.nlayers=$_POST["numero-camadas"]
		  AND ds.size=$_POST["classe-camadas"]
		  GROUP BY (cf.windowsize)' ;

}else if(!(isset($_POST['numero-camadas']))){
	
	$query = 'SELECT ds.nlayers, AVG(p.accuracy) AS avgacc
		  FROM prediction AS p
		  JOIN configuration AS cf ON p.configuration = cf.id
  		  JOIN dataset as ds ON cf.dataset = ds.id
  		  JOIN chain as ch ON p.pdb = ch.pdb
		  WHERE ch.dataset = ds.id
		  AND cf.windowsize= $_POST["tamanho-janela"]
		  AND ds.size= $_POST["classe-camadas"]
		  GROUP BY (ds.nlayers)';
}



$ps = $pdo->query($query);

$nlinhas = $ps->rowCount();
$ncampos = $ps->columnCount();

echo "<p>A tabela retornada tem $ncampos
campos e $nlinhas linhas.</p>\n";








?>
