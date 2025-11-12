<?php
session_start();

// Word categories
 $categories = [
    'Animals' => ['elephant', 'giraffe', 'penguin', 'dolphin', 'kangaroo', 'butterfly', 'crocodile', 'octopus'],
    'Countries' => ['australia', 'brazil', 'canada', 'denmark', 'egypt', 'france', 'germany', 'india'],
    'Fruits' => ['apple', 'banana', 'cherry', 'dragonfruit', 'elderberry', 'fig', 'grape', 'honeydew'],
    'Sports' => ['basketball', 'cricket', 'football', 'golf', 'hockey', 'tennis', 'volleyball', 'baseball']
];

// Initialize game if not already started
if (!isset($_SESSION['word']) || isset($_POST['newGame'])) {
    // Select a random category and word
    $category = array_rand($categories);
    $word = $categories[$category][array_rand($categories[$category])];
    
    $_SESSION['word'] = $word;
    $_SESSION['category'] = $category;
    $_SESSION['guessedLetters'] = [];
    $_SESSION['wrongGuesses'] = 0;
    $_SESSION['score'] = isset($_SESSION['score']) ? $_SESSION['score'] : 0;
    $_SESSION['gamesPlayed'] = isset($_SESSION['gamesPlayed']) ? $_SESSION['gamesPlayed'] : 0;
    $_SESSION['gamesWon'] = isset($_SESSION['gamesWon']) ? $_SESSION['gamesWon'] : 0;
}

// Process letter guess
if (isset($_POST['letter'])) {
    $letter = strtolower($_POST['letter']);
    
    // Check if letter was already guessed
    if (!in_array($letter, $_SESSION['guessedLetters'])) {
        $_SESSION['guessedLetters'][] = $letter;
        
        // Check if letter is in the word
        if (strpos($_SESSION['word'], $letter) === false) {
            $_SESSION['wrongGuesses']++;
        }
    }
}

// Check if game is won or lost
 $wordGuessed = true;
foreach (str_split($_SESSION['word']) as $letter) {
    if (!in_array($letter, $_SESSION['guessedLetters'])) {
        $wordGuessed = false;
        break;
    }
}

 $gameOver = $_SESSION['wrongGuesses'] >= 6 || $wordGuessed;

if ($gameOver) {
    $_SESSION['gamesPlayed']++;
    if ($wordGuessed) {
        $_SESSION['score'] += 10 - $_SESSION['wrongGuesses'];
        $_SESSION['gamesWon']++;
    }
}

// Generate the word display with blanks
 $displayWord = '';
foreach (str_split($_SESSION['word']) as $letter) {
    if (in_array($letter, $_SESSION['guessedLetters'])) {
        $displayWord .= $letter . ' ';
    } else {
        $displayWord .= '_ ';
    }
}

// Generate alphabet buttons
 $alphabet = range('a', 'z');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hangman Game</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .game-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .info-item {
            text-align: center;
        }
        
        .info-label {
            font-size: 0.9rem;
            color: #666;
        }
        
        .info-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2575fc;
        }
        
        .game-area {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .hangman-container {
            flex: 1;
            min-width: 300px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .hangman {
            width: 200px;
            height: 250px;
            position: relative;
        }
        
        .hangman-part {
            position: absolute;
            background-color: #333;
            transition: opacity 0.3s ease;
        }
        
        .gallows-base {
            width: 150px;
            height: 10px;
            bottom: 0;
            left: 25px;
        }
        
        .gallows-pole {
            width: 10px;
            height: 200px;
            bottom: 10px;
            left: 25px;
        }
        
        .gallows-top {
            width: 100px;
            height: 10px;
            top: 40px;
            left: 25px;
        }
        
        .gallows-noose {
            width: 2px;
            height: 30px;
            top: 50px;
            left: 115px;
        }
        
        .head {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            top: 80px;
            left: 101px;
            opacity: <?php echo $_SESSION['wrongGuesses'] >= 1 ? '1' : '0'; ?>;
        }
        
        .body {
            width: 2px;
            height: 60px;
            top: 110px;
            left: 115px;
            opacity: <?php echo $_SESSION['wrongGuesses'] >= 2 ? '1' : '0'; ?>;
        }
        
        .left-arm {
            width: 30px;
            height: 2px;
            top: 130px;
            left: 85px;
            transform: rotate(30deg);
            opacity: <?php echo $_SESSION['wrongGuesses'] >= 3 ? '1' : '0'; ?>;
        }
        
        .right-arm {
            width: 30px;
            height: 2px;
            top: 130px;
            left: 115px;
            transform: rotate(-30deg);
            opacity: <?php echo $_SESSION['wrongGuesses'] >= 4 ? '1' : '0'; ?>;
        }
        
        .left-leg {
            width: 30px;
            height: 2px;
            top: 170px;
            left: 85px;
            transform: rotate(30deg);
            opacity: <?php echo $_SESSION['wrongGuesses'] >= 5 ? '1' : '0'; ?>;
        }
        
        .right-leg {
            width: 30px;
            height: 2px;
            top: 170px;
            left: 115px;
            transform: rotate(-30deg);
            opacity: <?php echo $_SESSION['wrongGuesses'] >= 6 ? '1' : '0'; ?>;
        }
        
        .word-container {
            flex: 1;
            min-width: 300px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .category {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 20px;
        }
        
        .word {
            font-size: 2.5rem;
            letter-spacing: 10px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .wrong-letters {
            font-size: 1.2rem;
            color: #e74c3c;
            margin-top: 20px;
        }
        
        .alphabet-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .alphabet {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }
        
        .letter-btn {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 5px;
            background-color: #3498db;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .letter-btn:hover:not(:disabled) {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .letter-btn:disabled {
            background-color: #bdc3c7;
            cursor: not-allowed;
            opacity: 0.7;
        }
        
        .game-controls {
            text-align: center;
            margin-top: 20px;
        }
        
        .new-game-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #2ecc71;
            color: white;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .new-game-btn:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
        }
        
        .message {
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            font-size: 1.3rem;
            font-weight: bold;
        }
        
        .win-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .lose-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .game-area {
                flex-direction: column;
            }
            
            .word {
                font-size: 2rem;
                letter-spacing: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Hangman Game</h1>
            <p>Guess the word before the hangman is complete!</p>
        </header>
        
        <div class="game-info">
            <div class="info-item">
                <div class="info-label">Score</div>
                <div class="info-value"><?php echo $_SESSION['score']; ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Games Played</div>
                <div class="info-value"><?php echo $_SESSION['gamesPlayed']; ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Games Won</div>
                <div class="info-value"><?php echo $_SESSION['gamesWon']; ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Win Rate</div>
                <div class="info-value"><?php echo $_SESSION['gamesPlayed'] > 0 ? round($_SESSION['gamesWon'] / $_SESSION['gamesPlayed'] * 100) : 0; ?>%</div>
            </div>
        </div>
        
        <?php if ($gameOver): ?>
            <div class="message <?php echo $wordGuessed ? 'win-message' : 'lose-message'; ?>">
                <?php 
                if ($wordGuessed) {
                    echo "Congratulations! You guessed the word correctly!";
                } else {
                    echo "Game Over! The word was: " . $_SESSION['word'];
                }
                ?>
            </div>
        <?php endif; ?>
        
        <div class="game-area">
            <div class="hangman-container">
                <div class="hangman">
                    <div class="hangman-part gallows-base"></div>
                    <div class="hangman-part gallows-pole"></div>
                    <div class="hangman-part gallows-top"></div>
                    <div class="hangman-part gallows-noose"></div>
                    <div class="hangman-part head"></div>
                    <div class="hangman-part body"></div>
                    <div class="hangman-part left-arm"></div>
                    <div class="hangman-part right-arm"></div>
                    <div class="hangman-part left-leg"></div>
                    <div class="hangman-part right-leg"></div>
                </div>
            </div>
            
            <div class="word-container">
                <div class="category">Category: <?php echo $_SESSION['category']; ?></div>
                <div class="word"><?php echo $displayWord; ?></div>
                <div class="wrong-letters">
                    Wrong guesses: <?php echo implode(', ', array_filter($_SESSION['guessedLetters'], function($letter) {
                        return strpos($_SESSION['word'], $letter) === false;
                    })); ?>
                </div>
            </div>
        </div>
        
        <div class="alphabet-container">
            <div class="alphabet">
                <form method="post" action="" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px;">
                    <?php foreach ($alphabet as $letter): ?>
                        <button type="submit" name="letter" value="<?php echo $letter; ?>" 
                                class="letter-btn" 
                                <?php echo in_array($letter, $_SESSION['guessedLetters']) || $gameOver ? 'disabled' : ''; ?>>
                            <?php echo strtoupper($letter); ?>
                        </button>
                    <?php endforeach; ?>
                </form>
            </div>
        </div>
        
        <div class="game-controls">
            <form method="post" action="">
                <button type="submit" name="newGame" class="new-game-btn">New Game</button>
            </form>
        </div>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Hangman Game | Built with PHP</p>
        </footer>
    </div>
</body>
</html>