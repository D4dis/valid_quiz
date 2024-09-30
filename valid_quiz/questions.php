<?php

require 'data/data.php';

session_start();

function QuizSession($questions)
{
  if (isset($_POST['action']) && $_POST['action'] === 'restart') {
    $_SESSION['count'] = 0;
    $_SESSION['index'] = 1;
    unset($_SESSION['userAnswer']);
    unset($_SESSION['showAnswer']);
  } else {
    $_SESSION['count'] = $_SESSION['count'] ?? 0;
    $_SESSION['index'] = $_SESSION['index'] ?? 1;

    if (isset($_POST['radio'])) {
      $userAnswer = $_POST['radio'];
      $index = $_SESSION['index'] - 1;

      $_SESSION['userAnswer'] = $userAnswer;
      $_SESSION['answer'] = $questions[$index]['answer'];

      if ($_SESSION['userAnswer'] == $_SESSION['answer']) {
        $_SESSION['count']++;
      }

      $_SESSION['showAnswer'] = true;
    } elseif (isset($_POST['next'])) {
      $_SESSION['index']++;
      unset($_SESSION['userAnswer']);
      unset($_SESSION['showAnswer']);
    }
  }
}

QuizSession($questions);


$quizCompleted = $_SESSION['index'] > count($questions);
if ($quizCompleted) {
  $finalCount = $_SESSION['count'];
}

$currentQuestion = $questions[$_SESSION['index'] - 1] ?? null;

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quiz</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <style>
    .hidden {
      display: none;
    }

    .bg-success-subtle,
    .bg-danger-subtle {
      padding: 5px;
      border-radius: 5px;
    }
  </style>
</head>

<body class="bg-secondary-subtle">
  <div class="container">
    <h1 class="display-1 mb-5">Quiz</h1>

    <?php if ($quizCompleted) : ?>
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Score</h4>
          <p class="card-text">Votre score est de <?= $finalCount ?> / <?= count($questions) ?>.</p>
          <form action="" method="post">
            <input type="hidden" name="action" value="restart">
            <button type="submit" class="btn btn-primary">Nouvelle partie</button>
          </form>
        </div>
      </div>
    <?php elseif ($currentQuestion) : ?>
      <div class="card">
        <div class="card-body">
          <h4>Question n°<?= $_SESSION['index'] ?></h4>
          <p><?= $currentQuestion['question'] ?></p>
          <form action="" method="post">
            <?php foreach ($currentQuestion['options'] as $option) : ?>
              <?php
              $isCorrect = ($option == $currentQuestion['answer']);
              $isSelected = (isset($_SESSION['userAnswer']) && $option == $_SESSION['userAnswer']);
              $class = '';

              if (isset($_SESSION['showAnswer'])) {
                if ($isSelected) {
                  $class = $isCorrect ? 'bg-success-subtle' : 'bg-danger-subtle';
                } elseif ($isCorrect) {
                  $class = 'bg-success-subtle';
                }
              }
              ?>
              <div class="form-check">
                <input class="form-check-input <?= isset($_SESSION['showAnswer']) ? 'hidden' : '' ?>" type="radio" name="radio" id="radio<?= $option ?>" value="<?= $option ?>" <?= $isSelected ? 'checked' : '' ?>>
                <label class="form-check-label <?= $class ?>" for="radio<?= $option ?>">
                  <?= $option ?>
                </label>
              </div>
            <?php endforeach; ?>
            <?php if (!isset($_SESSION['showAnswer'])) : ?>
              <button type="submit" class="btn btn-primary mt-3">Valider la réponse</button>
            <?php endif; ?>
          </form>
          <?php if (isset($_SESSION['showAnswer'])) : ?>
            <form action="" method="post">
              <button type="submit" name="next" class="btn btn-primary mt-3">Question suivante</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>