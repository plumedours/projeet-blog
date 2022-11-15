<?php
require_once __DIR__ . '/database/database.php';
$authDB = require_once __DIR__ . '/database/security.php';
$currentUser = $authDB->isLoggedin();

$articleDB = require_once __DIR__ . '/database/models/ArticleDB.php';
$commentDB = require_once __DIR__ . '/database/models/CommentsDB.php';

$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$id = $_GET['id'] ?? '';

if (!$id) {
  header('Location: /');
} else {
  $article = $articleDB->fetchOne($id);
  $comment = $commentDB->fetchOne($id);
}
$getComments = $commentDB->fetchArticleComments($article['id']);

var_dump($getComments);

var_dump($getComments['lastname']);
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
    <?php foreach ($getComments as $num) :  ?>
    <div class="comments-container">
      <div class="comments-block">
        <p class="comment-date"><?= $num['date'] ?></p>
        <p class="comment-content"><?= $num['content'] ?></p>
        <p class="comment-author"><?= $num['firstname'] . ' ' . $num['lastname'] ?></p>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php require_once 'includes/footer.php' ?>
  </div>

</body>

</html>