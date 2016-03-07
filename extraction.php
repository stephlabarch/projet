<!DOCTYPE html>
<html>
    <head>
        <title>Extraction mot cle</title>
    </head>
    
	<?php  
        include "connect.php";
        echo 'projet_tut : <br>';
        echo 'test <br>';    
        
        function affiche($var)
        {
            echo "$var<br>\n";
        }
        
        function sans_accent($chaine)
        {
            /*encodage de la chaine*/
            $ch=  utf8_encode($chaine);
            
            /*suppression des accents*/
            $chsansaccent = $ch;
            $chsansaccent = preg_replace('#Ç#', 'C', $chsansaccent);
            $chsansaccent = preg_replace('#ç#', 'c', $chsansaccent);
            $chsansaccent = preg_replace('#è|é|ê|ë#','e',$chsansaccent);
            $chsansaccent = preg_replace('#È|É|Ê|Ë#', 'E',$chsansaccent);
            $chsansaccent = preg_replace('#à|á|â|ã|ä|å#', 'a',$chsansaccent);
            $chsansaccent = preg_replace('#@|À|Á|Â|Ã|Ä|Å#', 'A',$chsansaccent);
            $chsansaccent = preg_replace('#ì|í|î|ï#', 'i',$chsansaccent);
            $chsansaccent = preg_replace('#Ì|Í|Î|Ï#', 'I',$chsansaccent);
            $chsansaccent = preg_replace('#ð|ò|ó|ô|õ|ö#', 'o',$chsansaccent);
            $chsansaccent = preg_replace('#Ò|Ó|Ô|Õ|Ö#', 'O',$chsansaccent);
            $chsansaccent = preg_replace('#ù|ú|û|ü#', 'u',$chsansaccent);
            $chsansaccent = preg_replace('#Ù|Ú|Û|Ü#', 'U',$chsansaccent);
            $chsansaccent = preg_replace('#ý|ÿ#', 'y',$chsansaccent);
            $chsansaccent = preg_replace('#Ý#', 'Y',$chsansaccent);

            return ($chsansaccent);
        }
        
        function mot_rejete($mot)
        {
            require 'connect.php';
            $sql = 'SELECT count(*) from rejet where libelle='.$pdo->quote($mot);
            $req = $pdo->query($sql);
            while($row = $req->fetch())
            {
                return $row[0];
            }    
            $req->CloseCursor();
        }
        
        function present_occur($mot1)
        {
            require 'connect.php';
            $sql1 = 'SELECT count(*) from occurence where mot='.$pdo->quote($mot1);
            $req1 = $pdo->query($sql1);
            while($row = $req1->fetch())
            {
                return $row[0];
            }    
            $req->CloseCursor();
        }
        
       
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
                        if (!ctype_digit("$tab[$i]"))/*verifie qu'une chaine est un entier*/
                        {
                            if (strlen($tab[$i]) > 1)
                            {
                                if ( substr_count($tab[$i],"00:") == 0 )
                                {
                                    while ( strpos($tab[$i],"'") !== false)/*verifiez pour des mots comme aujourd'hui*/
                                    {
                                        $tab[$i]=substr($tab[$i],1);
                                    }
                                    $chaine=$tab[$i];
                                    $newch=sans_accent($chaine);//supprime les accents ou caractere spécial
                                    $ch=strtolower($newch);//met la chaine en minuscule
                                    $rej=mot_rejete($ch);
                                    if($rej==0)
                                    {
                                        $presence=present_occur($ch);
                                        if ($presence == 0)
                                        { 
                                            require 'connect.php';
                                            echo "-$ch- n'est pas dans la table occurence<br>";
                                            $req = $pdo->prepare("insert into occurence values (:mot,:occur)");
                                            $req->bindParam(':mot',$ch);
                                            $req->bindParam(':occur',$occ);
                                            $occ=1;
                                            $req->execute();
                                        }               
                                        else
                                        {
                                            require 'connect.php';
                                            echo "-$ch- est deja dans la table occurence<br>";
                                            $req1=$pdo->prepare('update occurence set nbo=nbo+1 where mot='.$pdo->quote($ch));
                                            $reussite=$req1->execute();
                                            if($reussite)
                                            {
                                                echo "update reussi<br>";
                                            }
                                            else
                                            {
                                                echo "update rate<br>";
                                            }
                                        }
                                    }
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
            set_time_limit(0);
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
        list_dir("./test");
        
	?>
</html>
