<?php
function connex($base,$param)
{
	include($param.".inc.php");
	$idcom=mysqli_connect(MYHOST,MYUSER,MYPASS,$base);
	if(!$idcom)
	{
    echo "<script type=text/javascript>";
		echo "alert('Connexion Impossible � la base  $base')</script>";
	}
	$idcom -> set_charset("utf8");
	return $idcom;
}
function connexpdo($base,$param)
{
	include_once($param.".inc.php");
	$dsn="mysql:host=".MYHOST.";
	dbname=".$base;
	$user=MYUSER;
	$pass=MYPASS;
	try
	{
		$idcom = new PDO($dsn,$user,$pass);
		return $idcom;
	}
	catch(PDOException $except)
	{
		die('Erreur : ' . $except->getMessage());
	}
}
?>

