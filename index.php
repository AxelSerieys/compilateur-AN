<!DOCTYPE html>

<html lang="fr" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
	<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
    <META HTTP-EQUIV="Expires" CONTENT="-1">
	<link rel="icon" type="image/jpeg" href="res/img/3il.jpeg" />
    <title>Compilateur notation polonaise</title>
    <link rel="stylesheet" type="text/css" href="res/css/index.css" />
</head>
<body>

    <div name="titre">
		<h1>Bienvenue !</h1>
    </div>

	<div id="form">
		<form action="result.php" method="POST" enctype="multipart/form-data">
			<div>
			

				<textarea placeholder="Renseignez ici vos calculs &agrave; compiler" name="texte" id="texte"></textarea>
			<p>
					Bienvenue sur cette page de compilateur de notation polonaise ! 
					Remplissez vous m&ecirc;me votre code dans la zone pr&eacute;vue pour,
					ou bien choisissez un fichier contenant votre code et uploadez le afin de le compiler !
					</br></br>Vous aurez ensuite la possibilit&eacute; de t&eacute;l&eacute;charger le r&eacute;sultat sur votre machine.
			</p>

			</div>
			<input type="file" name="file" id="file"/><br/><br/>
			<input type="submit" value="Compiler"/>
		</form>
		
	</div>

	<div id="explications">
		<h2>Qu'est ce que la notation polonaise inverse ?</h2> 
		La notation polonaise inverse (NPI) (en anglais RPN pour Reverse Polish Notation), également connue sous le nom de notation post-fixée, permet d'écrire de façon non ambiguë les formules arithmétiques sans utiliser de parenthèses. Dérivée de la notation polonaise présentée en 1924 par le mathématicien polonais Jan Łukasiewicz, elle s’en différencie par l’ordre des termes, les opérandes y étant présentés avant les opérateurs et non l’inverse.
		<br/><br/>
		Source : <a href=https://fr.wikipedia.org/wiki/Notation_polonaise_inverse target="_blank">https://fr.wikipedia.org/wiki/Notation_polonaise_inverse</a>
	</div>

    <script type="text/javascript" src="script.js"></script>

<footer>
    Réalisé par&nbsp;<div>Axel SERIEYS</div>,&nbsp;<div>Benoit KROK</div>&nbsp;et&nbsp;<div>Mathéo PORTUESE</div>&nbsp;en deuxième année à 3iL.
</footer>
</body>
<script>
document.getElementById('file').addEventListener('change', function() {
    var fr=new FileReader(); 
    fr.onload=function(){ 
        document.getElementById('texte').innerHTML=fr.result; 
    } 
              
    fr.readAsText(this.files[0]); 
}) 
</script>
</html>
