<?php
    function connect_bdd()
	{
        $serveur = "localhost";
        $login = "root";
        $password = "";
        $base = "projet tut";
        
	return ( mysqli_connect($serveur,$login,$password,$base) );        
    }
?>