<?php
function unzip_file($file, $destination) {
	// Créer l'objet
	$zip = new ZipArchive() ;
	// Ouvrir l'archive
	if ($zip->open($file) !== true) {
		return ('Impossible d\'ouvrir l\'archive');
	}
	// Extraire le contenu dans le dossier de destination
	$zip->extractTo($destination,'fichier.txt');
	// Fermer l'archive
	$zip->close();
	// Afficher un message de fin
	echo 'Archive extrait';
}
    
// Exemple d'utilisation
unzip_file('test.zip', '/test/');
?>





