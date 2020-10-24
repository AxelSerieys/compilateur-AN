﻿<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<?php header("Cache-Control: no-cache, must-revalidate"); ?>
<head>
    <meta charset="utf-8" />
	<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
    <META HTTP-EQUIV="Expires" CONTENT="-1">
	<link rel="icon" type="image/jpeg" href="3il.jpeg" />
    <title>Compilateur notation polonaise</title>
    <link rel="stylesheet" type="text/css" href="css.css" />
</head>
<body>
    <div name="titre">
        <h1 style="text-align:center">Bienvenue !</h1>
    </div>

	<div id="form">
		<form action="result.php" method="POST" enctype="multipart/form-data">
			<div>
				<textarea placeholder="Renseignez ici vos calculs &agrave; compiler" name="texte" id="texte"><?php echo @$contents ; ?></textarea>
				<p>
					Bienvenue sur cette page de compilateur de notation polonaise ! 
					Remplissez vous m&ecirc;me votre code dans la zone pr&eacute;vue pour, ou bien choisissez un fichier contenant votre code et uploadez le afin de le compiler !
					</br></br>Vous aurez ensuite la possibilit&eacute; de t&eacute;l&eacute;charger le r&eacute;sultat sur votre machine.
				</p>
			</div>
			<input type="file" name="file" onchange="readTextFile('test.src')"/><br/><br/><br/>
			<input type="submit" value="Compiler"/>
		</form>
	</div>
	<button onclick="readTextFile('test.src')">Bonjour</button>

	<div id="explications">
		<h2>Qu'est ce que la notation polonaise ?</h2>
		La notation polonaise inverse (NPI) (en anglais RPN pour Reverse Polish Notation), également connue sous le nom de notation post-fixée, permet d'écrire de façon non ambiguë les formules arithmétiques sans utiliser de parenthèses. Dérivée de la notation polonaise présentée en 1924 par le mathématicien polonais Jan Łukasiewicz, elle s’en différencie par l’ordre des termes, les opérandes y étant présentés avant les opérateurs et non l’inverse.
		</br></br>Source : <a href=https://fr.wikipedia.org/wiki/Notation_polonaise_inverse target="_blank">https://fr.wikipedia.org/wiki/Notation_polonaise_inverse</a>
	</div>
    <script type="text/javascript" src="script.js"></script>
</body>
<script>
function readTextFile(file)
{
    var rawFile = new XMLHttpRequest();
    rawFile.open("GET", file, false);
    rawFile.onreadystatechange = function ()
    {
        if(rawFile.readyState === 4)
        {
            if(rawFile.status === 200 || rawFile.status == 0)
            {
                var allText = rawFile.responseText;
                document.getElementById("texte").innerHTML = allText;
            }
        }
    }
    rawFile.send(null);
}
</script>
</html>
