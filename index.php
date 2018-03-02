<?php
//variables date
$date=$_POST['date'];
$date_todo= date("Y-m-d");

//sanitization
function sanitize($key, $filter=FILTER_SANITIZE_STRING){
  $sanitized_variable = null;
  if( isset($_POST['date'])OR($_POST['tache'])OR isset($_POST['check'])){
    if(is_array($key)){ // si la valeur est un tableau...
      $sanitized_variable = filter_var_array($key, $filter);
    }
    else { // sinon ...
      $sanitized_variable = filter_var($key, $filter);
    }
  }
  return $sanitized_variable;
}
//////////////////////////////////////////////
try
{//on se connecte à SQL (webhost)
$bd = new PDO('mysql:host=localhost;dbname=id4745934_todo_list;charset=utf8', 'id4745934_ludovic', 'user@');

// // //   // On se connecte à MySQL(localhost)
// $bd = new PDO('mysql:host=localhost;dbname=todo_list;charset=utf8', 'root', 'user');
}
catch(Exception $e)
{
  // En cas d'erreur, on affiche un message et on arrête tout
  die('Erreur : '.$e->getMessage());
}
if (isset($_POST['ajouter'])  and !empty ($_POST['tache'])){ //Si on appuie sur le boutton ajouter...
  $add_tache =sanitize( $_POST['tache']); //je récupère la valeur que je veux ajouter

  //Export vers la db
   $a_faire= $bd->query("INSERT INTO todolist(id, A_FAIRE, STATUT, ECHEANCE) VALUES (null,'".$add_tache."','N','".$date."')");

  //afficher les données de la bd
}
//supprimer de la bd. On supprime avant de réécrire dans la bd
if (isset($_POST['delete'])) {//Si j'appuie sur le bouton delete
  $delete=$bd->query("DELETE  FROM `todolist`");//Je supprime les données de la table
}
//transformer les tâches en "fait"
if (isset($_POST['check'])  and isset($_POST['list'])) {//Si j'enregistre et que la db existe et que j'ai coché...
  for ($i = 0 ; $i < count($_POST['list']); $i++){
    $bd->query("UPDATE todolist SET STATUT='F' WHERE id='".$_POST['list'][$i]."'");
  }//Le statut passe de N à F...
}
$todo=$bd->query("SELECT A_FAIRE, id,ECHEANCE FROM todolist WHERE STATUT='N'");//Je sélectionne les tâches à faire
//print_r($todo->fetchAll());
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
          if ( $donnees ['ECHEANCE']<=$date_todo) {//Si j'appuie sur ajouter et qu'il y'a une date...
            echo "<input type='checkbox' name='list[]' value='".$donnees ['id']."'/>
            <label style='color:red;' for='choix'>".$donnees ['A_FAIRE']."</label><br />"; // injecter input en rouge
          }else {
            echo "<input type='checkbox' name='list[]' value='".$donnees ['id']."'/>
            <label for='choix'>".$donnees ['A_FAIRE']."</label><br />"; // injecter input
          }
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
          while ($archive=$todo_done->fetch())
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
        <input type="date" name="date" value="Echéance"><br>
        <input type="text" name="tache" value="">
        <input type="submit" name="ajouter" value="Ajouter">
        <input type="submit" name="delete" value="Nouvelle journée">
      </form>
    </footer>
  </div>
</body>
<?php
// $todo->closeCursor();
// $todo_done->closeCursor();
?>
</html>
