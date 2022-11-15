<?php
require_once __DIR__ . '/database/database.php';
$authDB = require_once __DIR__ . '/database/security.php';
$currentUser = $authDB->isLoggedin();

// if (!$currentUser) {
//   header('Location: /');
// }
$commentDB = require_once __DIR__ . '/database/models/CommentsDB.php';
$articleDB = require_once __DIR__ . '/database/models/ArticleDB.php';
const ERROR_REQUIRED = 'Veuillez renseigner ce champ';
const ERROR_CONTENT_TOO_SHORT = 'Le commentaire est trop court';

$errors = [
  'content' => ''
];


$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$id = $_GET['id'] ?? '';
$articleID = $_GET['id'] ?? '';

if ($id) {
  $date = time();
  $article = $articleDB->fetchOne($id);
  $comment = $commentDB->fetchOne($id);
//   if ($comment['author'] !== $currentUser['id']) {
//     header('Location: /');
//   }
  $articleID = array_search($id, array_column($article, 'id'));
//   $content = $comment['content'];
//   $date = time();
}

// echo($articleID);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $_POST = filter_input_array(INPUT_POST, [
    'content' => [
      'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
      'flags' => FILTER_FLAG_NO_ENCODE_QUOTES
    ]
  ]);
  $content = $_POST['content'] ?? '';
  $articleID = array_search($id, array_column($article, 'id'));
  $date = time();

  if (!$content) {
    $errors['content'] = ERROR_REQUIRED;
  } elseif (mb_strlen($content) < 10) {
    $errors['content'] = ERROR_CONTENT_TOO_SHORT;
  }

  if (empty(array_filter($errors, fn ($e) => $e !== ''))) {
    // if ($id) {
    //   $comment['content'] = $content;
    //   $comment['author'] = $currentUser['id'];
    //   $commentDB->updateOne($comment);
    // } else {
      $commentDB->createOne([
        'date' => $date,
        'content' => $content,
        'author' => $currentUser['id'],
        'article_id' => $article['id']
      ]);
    // }
    header('Location: /');
  }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <?php require_once 'includes/head.php' ?>
  <title>Ajouter un commentaire</title>
</head>

<body>
  <div class="container">
    <?php require_once 'includes/header.php' ?>
    <div class="content">
      <div class="block p-20 form-container">
        <h1>Ajouter un commentaire</h1>
        <form action="/form-comments.php<?= $id ? "?id=$id" : '' ?>" , method="POST">
          <div class="form-control">
            <label for="content">Contenu du commentaire</label>
            <textarea name="content" id="content"><?= $content ?? '' ?></textarea>
            <?php if ($errors['content']) : ?>
              <p class="text-danger"><?= $errors['content'] ?></p>
            <?php endif; ?>
          </div>
          <div class="form-actions">
            <a href="/" class="btn btn-secondary" type="button">Annuler</a>
            <button class="btn btn-primary" type="submit">Ajouter</button>
          </div>
        </form>
      </div>
    </div>
    <?php require_once 'includes/footer.php' ?>
  </div>

</body>

</html>