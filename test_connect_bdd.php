<?php
        echo "test<br>";

        function extraire($nom)
        {
           $fic=fopen($nom,"r");
           while(!feof($fic))
           {
               $mots=fgets($fic,4096);
               $tab=explode(" ",$mots);
               $nb=count($tab)-1;
               for($i=0;$i<$nb;$i++)
               {
                    if (strcmp($tab[$i],"-->") !== 0)
                    {
                        if (!ctype_digit("$tab[$i]"))
                        {
                            if (strlen($tab[$i]) > 1)
                            {
                                if ( substr_count($tab[$i],"00:0") == 0 )
                                {
                                    $chaine=$tab[$i];
                                    $newch=sans_accent($chaine);//supprime les accents ou caractere spécial
                                    $ch=strtolower($newch);//met la chaine en minuscule
                                    $connexion=connect_bdd();
                                    if (!$connexion)
                                    {
                                        echo ("connexion a la base impossible");
                                    }
                                    $req="select * from mot_bannis";
                                    if( $resultat = mysqli_query($lien,"$req") )
                                            
                                    echo $ch;
                                    echo "</br>";
                                }
                            }
                        }
                    }
               }
           }
           fclose($fic);
        }
    
        function list_dir($name, $level=0) //parcours d'arborescence
        {
            if ($dir=opendir($name)) 
            {
                while($fichier = readdir($dir)) 
                {
                    for($i=1;$i<=(4*$level);$i++) 
                    {
                        echo "&nbsp;";
                    }
                    $chem="$name"."/"."$fichier";
                    if(is_dir($name."/".$fichier) && !in_array($fichier, array(".","..")))
                    {
                        list_dir($name."/".$fichier,$level+1);
                    }
                    else
                    {  
                        if($fichier != ".")
                        {
                            if($fichier != "..")
                            {
                                echo "<br>fichier=$fichier<br>";
                                $path=pathinfo($fichier);
                                if($path['extension'] == 'srt' )
                                {
                                    echo "extraction<br>";
                                    extraire("$chem");
                                }
                                
                                if($path['extension'] == 'zip' )
                                {
                                    echo ".zip a traiter (extration du zip et rappel fonction listdir)<br>";
                                    
                                }
                                
                                if($path['extension'] == 'rar' )
                                {
                                    echo ".rar a traiter (extration du zip et rappel fonction listdir<br>";
                                }
                            }
                        }
                    }
                }
                closedir($dir);
            }
        }

function unzip_file($file, $destination) {
	// Créer l'objet
	$zip = new ZipArchive() ;
	// Ouvrir l'archive
	if ($zip->open($file) !== true) {
		return ('Impossible d\'ouvrir l\'archive');
	}
	// Extraire le contenu dans le dossier de destination
	$zip->extractTo($destination,'fichier.srt');
            
        echo "list_dir :<br>";
        list_dir($destination);
        
        // Fermer l'archive
	$zip->close();
	// Afficher un message de fin
	echo 'Archive extrait';
}
    
// Exemple d'utilisation
unzip_file('test.zip', './test');
?>





