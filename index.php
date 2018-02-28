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

if (isset($_POST['ajouter'])  and !empty ($_POST['tache'])){ //Si on appuie sur le boutton ajouter...
  $add_tache =sanitize( $_POST['tache']); //je récupère la valeur que je veux ajouter

  //Export vers la db
  $a_faire= $bd->query("INSERT INTO todolist(id, A_FAIRE, STATUT) VALUES (null,'".$add_tache."','N')");
  //afficher les données de la bd
}
//transformer les tâches en "fait"
if (isset($_POST['check'])  and isset($_POST['list'])) {//Si j'enregistre et que la db existe et que j'ai coché...
  for ($i = 0 ; $i < count($_POST['list']); $i++){
  $bd->query("UPDATE todolist SET STATUT='F' WHERE id='".$_POST['list'][$i]."'");
  }//Le statut passe de N à F...
}
$todo=$bd->query("SELECT A_FAIRE, id FROM todolist WHERE STATUT='N'");
$todo_done=$bd->query("SELECT A_FAIRE FROM todolist WHERE STATUT='F'");//Je reprend les tâches avec F.
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
        //Afficher ce qui est à faire
        while ($donnees = $todo->fetch())
        {
          echo "<input type='checkbox' name='list[]' value='".$donnees ['id']."'/>
          <label for='choix'>".$donnees ['A_FAIRE']."</label><br />"; // injecter input.//;
        }

        ?>
        <input type="submit" name="check" value="check" >
      </form>
    </section>
    <section class="archive">
      <h2>Fait</h2>
      <form action="index.php" method="post" name="formchecked">
        <div class="done">
          <?php

          while ($archive=$todo_done ->fetch())
          {
            echo "<input type='checkbox' style='text-decoration:line-through, red;' name='list_done[]' value='".$archive['A_FAIRE']."'checked/>
            <label for='choix'>".$archive['A_FAIRE']."</label><br />";
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
<?php
$todo->closeCursor();
$todo_done->closeCursor();
?>
</html>
