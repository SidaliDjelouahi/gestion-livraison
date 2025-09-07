<?php 
    $dsn = 'mysql:host=localhost;dbname=gestion-livraison;charset=utf8';
	$root = 'root';
	$password = '';
	
	try
	{
		$conn = new PDO($dsn, $root , $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION);
		//echo 'you are connected';
		
	}catch(PDOException $e)
	{
		print "Erreur :" . $e->getMessage() . '<br>';
	}

?>

