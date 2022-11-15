<?php
require_once __DIR__ . '/database/database.php';
$authDB = require_once __DIR__ . '/database/security.php';
$currentUser = $authDB->isLoggedin();

$articleDB = require_once __DIR__ . '/database/models/ArticleDB.php';
$commentDB = require_once __DIR__ . '/database/models/CommentsDB.php';

$getComments = $commentDB->fetchAll();
$comments = [];

$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$id = $_GET['id'] ?? '';

if (!$id) {
  header('Location: /');
} else {
  $article = $articleDB->fetchOne($id);
  $comment = $commentDB->fetchOne($id);
}

if (count($getComments)) {
  $comtemp = array_map(fn ($a) => $a['article_id'],  $getComments);
  $comments = array_reduce($comtemp, function ($acc, $com) {
  if (isset($acc[$com])) {
    $acc[$com]++;
  } else {
    $acc[$com] = 1;
  }
  return $acc;
});
};
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <?php require_once 'includes/head.php' ?>
  <link rel="stylesheet" href="/public/css/show-article.css">
  <title>Article</title>
</head>

<body>
  <div class="container">
  <?php require_once 'includes/header.php' ?>
  <div class="content">
    <div class="article-container">
    <a class="article-back" href="/">Retour à la liste des articles</a>
    <div class="article-cover-img" style="background-image:url(<?= $article['image'] ?>)"></div>
    <h1 class="article-title"><?= $article['title'] ?></h1>
    <div class="separator"></div>
    <p class="article-content"><?= $article['content'] ?></p>
    <p class="article-author"><?= $article['firstname'] . ' ' . $article['lastname'] ?></p>
    <?php if ($currentUser && $currentUser['id'] === $article['author']) : ?>
      <div class="action">
      <a class="btn btn-secondary" href="/delete-article.php?id=<?= $article['id'] ?>">Supprimer</a>
      <a class="btn btn-primary" href="/form-article.php?id=<?= $article['id'] ?>">Éditer l'article</a>
      </div>
    <?php endif; ?>
    </div>
    <?php if (!$currentUser): ?>
    <a href="/auth-login.php">Ajouter un commentaire</a>
    <?php else: ?>
    <a href="/form-comments.php?id=<?= $article['id'] ?>">Ajouter un commentaire</a>
    <?php endif; ?>
    <?php foreach ($comments as $com => $num) :  ?>
      <h2><?= $com ?></h2>
    <div class="comments-container">
      <div class="comments-block">
        <p class="comment-date"><?= $comment['date'] ?></p>
        <p class="comment-content"><?= $comment['content'] ?></p>
        <p class="comment-author"><?= $comment['firstname'] . ' ' . $comment['lastname'] ?></p>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php require_once 'includes/footer.php' ?>
  </div>

</body>

</html>