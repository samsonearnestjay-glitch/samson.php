<?php

$jsonData = file_get_contents('books.json');
$books = json_decode($jsonData, true);

$bookHashtable = [];
foreach ($books as $book) {
    $key = strtolower(trim($book['title']));
    $bookHashtable[$key] = $book;
}

$result = null;
$error = null;

if (isset($_GET['query']) && !empty($_GET['query'])) {
    $search = strtolower(trim($_GET['query']));
    if (isset($bookHashtable[$search])) {
        $result = $bookHashtable[$search];
    } else {
        $error = "❌ Book not found in library.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>📚 Book Explorer (Hashtable Search)</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #0b5f6e;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            color: white;
        }

        h1 {
            text-align: center;
            padding: 30px 0 10px;
        }

        .search-container {
            display: flex;
            justify-content: center;
            margin: 20px auto 40px;
        }

        .search-bar {
            width: 50%;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 25px 0 0 25px;
            border: none;
            outline: none;
        }

        .search-btn {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 0 25px 25px 0;
            background-color: #0077b6;
            color: white;
            cursor: pointer;
        }

        .search-btn:hover {
            background-color: #005f8e;
        }

        .result-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
        }

        .book-card {
            background-color: white;
            color: #333;
            border-radius: 12px;
            width: 280px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: 0.3s;
        }

        .book-card img {
            width: 100%;
            height: 350px;
            object-fit: cover;
            border-radius: 10px;
        }

        .book-card h3 {
            color: #0077b6;
            margin-top: 15px;
        }

        .book-card p {
            font-size: 14px;
            margin: 5px 0;
        }

        .error {
            color: #ffd6d6;
            font-size: 18px;
            background: rgba(255, 0, 0, 0.2);
            padding: 10px 20px;
            border-radius: 8px;
        }

        .footer {
            text-align: center;
            color: #ccc;
            margin-top: 50px;
            padding-bottom: 20px;
        }

        a {
            color: #0b5f6e;
            text-decoration: none;
            font-weight: bold;
        }

    </style>
</head>
<body>
    <h1>📘 Book Explorer (Hashtable Search)</h1>

    <div class="search-container">
        <form method="GET" action="">
            <input type="text" name="query" class="search-bar" placeholder="🔍 Enter book title..." value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
            <button type="submit" class="search-btn">Search</button>
        </form>
    </div>

    <div class="result-container">
        <?php if ($result): ?>
            <div class="book-card">
                <img src="<?php echo htmlspecialchars($result['imageLink']); ?>" alt="Book cover">
                <h3><?php echo htmlspecialchars($result['title']); ?></h3>
                <p>✍️ Author: <?php echo htmlspecialchars($result['author']); ?></p>
                <p>🌍 Country: <?php echo htmlspecialchars($result['country']); ?></p>
                <p>🗣 Language: <?php echo htmlspecialchars($result['language']); ?></p>
                <p>📖 Pages: <?php echo htmlspecialchars($result['pages']); ?></p>
                <p>📅 Year: <?php echo htmlspecialchars($result['year']); ?></p>
                <a href="<?php echo htmlspecialchars($result['link']); ?>" target="_blank">🌐 View More</a>
            </div>
        <?php elseif ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php else: ?>
            <p style="font-size: 18px; color: #eee;">🔎 Use the search box above to look for a book.</p>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>Made with ❤️ using PHP Hashtable Search</p>
    </div>
</body>
</html>
