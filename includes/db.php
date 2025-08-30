<?php 
    $dsn = 'mysql:host=localhost;dbname=u174726466_Gl;charset=utf8';
	$root = 'u174726466_Gl';
	$password = 'Unisoft**11';
	
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

