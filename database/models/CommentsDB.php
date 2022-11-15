<?php

class CommentDB {
    private PDOStatement $statementCreateOne;
    private PDOStatement $statementUpdateOne;
    private PDOStatement $statementDeleteOne;
    private PDOStatement $statementReadOne;
    private PDOStatement $statementReadAll;
    private PDOStatement $statementAllComments;

    function __construct(private PDO $pdo)
    {
        $this->statementCreateOne = $pdo->prepare('
        INSERT INTO comments (
            date,
            content,
            author,
            article_id
          ) VALUES (
            NOW(),
            :content,
            :author,
            :article_id
          )
        ');
        $this->statementUpdateOne = $pdo->prepare('
        UPDATE comments
        SET
            date=:date,
            content=:content,
            author=:author
        WHERE id=:id
    ');

    $this->statementReadOne = $pdo->prepare('SELECT comments.*, user.firstname, user.lastname FROM comments LEFT JOIN user ON comments.author = user.id WHERE comments.id=:article_id');
    $this->statementReadAll = $pdo->prepare('SELECT comments.*, user.firstname, user.lastname FROM comments LEFT JOIN user ON comments.author = user.id');
    $this->statementDeleteOne = $pdo->prepare('DELETE FROM comments WHERE id=:id');
    $this->statementAllComments = $pdo->prepare('SELECT comments.*, user.firstname, user.lastname FROM comments JOIN user ON user.id = comments.author WHERE article_id = :article_id');
    }

    public function fetchAll(): array
  {
    $this->statementReadAll->execute();
    return $this->statementReadAll->fetchAll();
  }

  public function fetchOne(string $id): array | bool
  {
    $this->statementReadOne->bindValue(':article_id', $id);
    $this->statementReadOne->execute();
    return $this->statementReadOne->fetch();
  }

  public function deleteOne(string $id): string
  {
    $this->statementDeleteOne->bindValue(':id', $id);
    $this->statementDeleteOne->execute();
    return $id;
  }
  public function createOne($comments): array
  {
    // $this->statementCreateOne->bindValue('NOW()', $comments['date']);
    $this->statementCreateOne->bindValue(':content', $comments['content']);
    $this->statementCreateOne->bindValue(':author', $comments['author']);
    $this->statementCreateOne->bindValue(':article_id', $comments['article_id']);
    $this->statementCreateOne->execute();
    return $this->fetchOne($this->pdo->lastInsertId());
  }
  public function updateOne($comments): array
  {
    $this->statementUpdateOne->bindValue(':date', $comments['date']);
    $this->statementUpdateOne->bindValue(':content', $comments['content']);
    $this->statementUpdateOne->bindValue(':article_id', $comments['article_id']);
    $this->statementUpdateOne->bindValue(':author', $comments['author']);
    $this->statementUpdateOne->execute();
    return $comments;
  }

  public function fetchArticleComments(string $id): array
  {
    $this->statementAllComments->execute([':article_id' => $id]);
    return $this->statementAllComments->fetchAll();
  }
}

return new CommentDB($pdo);