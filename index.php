<?php

//sanitization
function sanitize($key, $filter=FILTER_SANITIZE_STRING){
  $sanitized_variable = null;
  if(isset($_POST['tache'])OR isset($_POST['boutton'])){
    if(is_array($key)){ // si la valeur est un tableau...
      $sanitized_variable = filter_var_array($key, $filter);
    }
    else { // sinon ...
      $sanitized_variable = filter_var($key, $filter);
    }
  }
  return $sanitized_variable;
}


if (isset($_POST['ajouter'])  and !empty ($_POST['tache'])){ //Si on appuie sur le boutton ajouter...
  $add_tache =sanitize( $_POST['tache']); //je récupère la valeur que je veux ajouter
  try
  {
    // On se connecte à MySQL
    $bd = new PDO('mysql:host=localhost;dbname=todo_list;charset=utf8', 'root', 'user');
  }
  catch(Exception $e)
  {
    // En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
  }
  //Export vers la db

  $a_faire= $bd->query("INSERT INTO todolist(id, A_FAIRE, STATUT) VALUES (null,'".$add_tache."','N')");
  //afficher les données de la bd
  //echo
}
if (isset($_POST['button'])  and isset($_POST['list']) and isset($bd)) {//Si j'enregistre et que la db existe et que j'ai coché...
  $fait= $bd->query("UPDATE todolist SET STATUT='F' WHERE STATUT= 'N'");//Le statut passe de N à F...
  $fait= $bd->query("SELECT A_FAIRE FROM todolist WHERE statut='F'");//Je reprend les tâches avec F...
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- <link rel="stylesheet" href="style.css"> -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css?family=Mukta+Malar" rel="stylesheet">
  <title>Pense-bete</title>
</head>
<body>
  <div class="page">
    <section class="afaire">
      <h2>A faire</h2>
      <form action="index.php" method="post" name="formafaire">
        <?php
        if (isset($bd)) {//si la bd existe et que l'on a ajouté une tâche...
          $a_faire= $bd->query("SELECT A_FAIRE FROM todolist WHERE statut='N'");
          while ($donnees = $a_faire->fetch())
          {
            echo "<input type='checkbox' name='list[]' value='".$donnees ['A_FAIRE']."'/>
            <label for='choix'>".$donnees ['A_FAIRE']."</label><br />"; // injecter input.//;
          }
          $a_faire->closeCursor();
        }
        ?>
        <input type="submit" name="button" value="check" >
      </form>
    </section>
    <section class="archive">
      <h2>Fait</h2>
      <form action="index.php" method="post" name="formchecked">
        <div class="done">
          <?php
          if (isset ($_POST['button']) and !empty($_POST['tache']) and isset($bd)) {//Si j'enregistre et que la db existe et que j'ai coché...

            while ($donnees=$fait ->fetch())
            {
              echo "<input type='checkbox' style='text-decoration:line-through, red;' name='list[]' value='".$donnees['A_FAIRE']."'checked/>
              <label for='choix'>".$donnees['A_FAIRE']."</label><br />";
            }
          }
          ?>
        </form>
      </div>
    </section>
    <hr>
    <footer class="tache">
      <h2>Ajouter une tâche</h2>
      <form class="" action="index.php" method="post">
        <!-- <label for="tache">La tâche à effectuer</label> -->
        <input type="text" name="tache" value="">
        <input type="submit" name="ajouter" value="Ajouter">
      </form>
    </footer>
  </div>
</body>
</html>
