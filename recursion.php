<?php
$jsonData = file_get_contents("books.json");
$books = json_decode($jsonData, true);
if (!$books) die("❌ Error: Unable to load books.json");

$fiction = [];
$nonFiction = [];

foreach ($books as $book) {
    $title = strtolower($book['title']);
    if (
        str_contains($title, 'history') ||
        str_contains($title, 'science') ||
        str_contains($title, 'diary') ||
        str_contains($title, 'biography') ||
        str_contains($title, 'philosophy')
    ) {
        $book['type'] = 'Non-Fiction';
        $nonFiction[] = $book;
    } else {
        $book['type'] = 'Fiction';
        $fiction[] = $book;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>📘 Library — Fiction & Non-Fiction</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #1e3a8a, #2563eb, #60a5fa);
  color: #f1f5f9;
  margin: 0;
  padding: 0;
  min-height: 100vh;
}

header {
  text-align: center;
  padding: 40px 20px 10px;
  color: #f8fafc;
  text-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

h1 {
  font-size: 2.5em;
  margin-bottom: 10px;
}

h2 {
  text-align: center;
  color: #f8fafc;
  margin-top: 40px;
  text-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.search-container {
  text-align: center;
  margin: 20px 0 40px;
}

#searchInput {
  width: 60%;
  padding: 14px 18px;
  font-size: 17px;
  border: none;
  border-radius: 50px;
  outline: none;
  background: rgba(255,255,255,0.15);
  color: #fff;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  backdrop-filter: blur(6px);
  transition: all 0.3s ease;
}

#searchInput::placeholder {
  color: #cbd5e1;
}

#searchInput:focus {
  background: rgba(255,255,255,0.25);
  transform: scale(1.02);
}

.book-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 25px;
  padding: 0 50px 60px;
}

.card {
  background: rgba(255,255,255,0.15);
  border-radius: 15px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.2);
  backdrop-filter: blur(10px);
  text-align: center;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.card:hover {
  transform: translateY(-10px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.3);
}

.card img {
  width: 100%;
  height: 250px;
  object-fit: cover;
}

.card-content {
  padding: 15px;
  color: #f1f5f9;
}

.card-content h3 {
  font-size: 1.1em;
  margin: 10px 0 5px;
}

.card-content p {
  font-size: 0.9em;
  color: #e2e8f0;
}

.modal {
  display: none;
  position: fixed;
  z-index: 999;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
  animation: fadeIn 0.4s ease;
}

@keyframes fadeIn {
  from {opacity: 0;} to {opacity: 1;}
}

.modal-content {
  background: linear-gradient(135deg, rgba(37,99,235,0.95), rgba(147,197,253,0.95));
  border: 2px solid rgba(147,197,253,0.6);
  border-radius: 16px;
  padding: 25px;
  width: 90%;
  max-width: 420px;
  text-align: center;
  color: #f8fafc;
  box-shadow: 0 0 30px rgba(59,130,246,0.5);
  backdrop-filter: blur(15px);
  animation: popUp 0.4s ease;
}

@keyframes popUp {
  from {transform: scale(0.9); opacity: 0;}
  to {transform: scale(1); opacity: 1;}
}

.modal-content img {
  width: 100%;
  height: 260px;
  object-fit: cover;
  border-radius: 10px;
  margin-bottom: 15px;
  box-shadow: 0 4px 20px rgba(147,197,253,0.4);
}

.close-btn {
  position: absolute;
  top: 12px;
  right: 15px;
  background: rgba(239,68,68,0.9);
  border: none;
  color: white;
  font-size: 18px;
  padding: 6px 10px;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.2s;
}

.close-btn:hover {
  background: rgba(220,38,38,1);
}

.more-info-btn {
  background: linear-gradient(135deg, #3b82f6, #1e40af);
  color: white;
  padding: 10px 18px;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  cursor: pointer;
  margin-top: 12px;
  text-decoration: none;
  display: inline-block;
  box-shadow: 0 3px 10px rgba(30,64,175,0.4);
  transition: all 0.2s;
}

.more-info-btn:hover {
  background: linear-gradient(135deg, #2563eb, #1e3a8a);
  transform: translateY(-2px);
}

.no-results {
  text-align: center;
  color: white;
  font-size: 1.1em;
  margin-top: 30px;
}
</style>
</head>
<body>

<header>
  <h1>📚 Library Collection</h1>
</header>

<div class="search-container">
  <input type="text" id="searchInput" placeholder="🔍 Search for a book, author, or type...">
</div>

<h2>✨ Fiction Books</h2>
<div class="book-grid" id="fictionGrid">
<?php foreach ($fiction as $b): ?>
  <div class="card" data-title="<?php echo strtolower($b['title'].' '.$b['type']); ?>" onclick='showBook(<?php echo json_encode($b); ?>)'>
    <img src="<?php echo htmlspecialchars($b['imageLink']); ?>" alt="<?php echo htmlspecialchars($b['title']); ?>">
    <div class="card-content">
      <h3><?php echo htmlspecialchars($b['title']); ?></h3>
      <p><?php echo htmlspecialchars($b['author']); ?></p>
      <p><em><?php echo htmlspecialchars($b['type']); ?></em></p>
    </div>
  </div>
<?php endforeach; ?>
</div>

<h2>📖 Non-Fiction Books</h2>
<div class="book-grid" id="nonFictionGrid">
<?php foreach ($nonFiction as $b): ?>
  <div class="card" data-title="<?php echo strtolower($b['title'].' '.$b['type']); ?>" onclick='showBook(<?php echo json_encode($b); ?>)'>
    <img src="<?php echo htmlspecialchars($b['imageLink']); ?>" alt="<?php echo htmlspecialchars($b['title']); ?>">
    <div class="card-content">
      <h3><?php echo htmlspecialchars($b['title']); ?></h3>
      <p><?php echo htmlspecialchars($b['author']); ?></p>
      <p><em><?php echo htmlspecialchars($b['type']); ?></em></p>
    </div>
  </div>
<?php endforeach; ?>
</div>

<p id="noResults" class="no-results" style="display:none;">❌ No books found.</p>

<div class="modal" id="bookModal">
  <div class="modal-content" id="modalContent">
    <button class="close-btn" onclick="closeModal()">✖</button>
    <img id="modalImage" src="" alt="Book Image">
    <h2 id="modalTitle"></h2>
    <p><strong>👤 Author:</strong> <span id="modalAuthor"></span></p>
    <p><strong>🌍 Country:</strong> <span id="modalCountry"></span></p>
    <p><strong>🗣️ Language:</strong> <span id="modalLanguage"></span></p>
    <p><strong>📖 Pages:</strong> <span id="modalPages"></span></p>
    <p><strong>📅 Year:</strong> <span id="modalYear"></span></p>
    <p><strong>📂 Type:</strong> <span id="modalType"></span></p>
    <a id="modalLink" href="#" target="_blank" class="more-info-btn" style="display:none;">More Info ↗</a>
  </div>
</div>

<script>
function showBook(book) {
  document.getElementById('bookModal').style.display = 'flex';
  document.getElementById('modalTitle').textContent = book.title;
  document.getElementById('modalAuthor').textContent = book.author;
  document.getElementById('modalCountry').textContent = book.country || '—';
  document.getElementById('modalLanguage').textContent = book.language || '—';
  document.getElementById('modalPages').textContent = book.pages || '—';
  document.getElementById('modalYear').textContent = book.year || '—';
  document.getElementById('modalType').textContent = book.type;
  document.getElementById('modalImage').src = book.imageLink || 'https://via.placeholder.com/150x220?text=No+Image';
  const link = document.getElementById('modalLink');
  if (book.link) { link.style.display = 'inline-block'; link.href = book.link; }
  else { link.style.display = 'none'; }
}

function closeModal() {
  document.getElementById('bookModal').style.display = 'none';
}

window.onclick = function(e) {
  const modal = document.getElementById('bookModal');
  if (e.target == modal) modal.style.display = 'none';
}

document.getElementById('searchInput').addEventListener('input', function() {
  const filter = this.value.toLowerCase();
  const cards = document.querySelectorAll('.card');
  let visibleCount = 0;
  cards.forEach(card => {
    const title = card.getAttribute('data-title');
    if (title.includes(filter)) {
      card.style.display = 'block';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });
  document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
});
</script>
</body>
</html>
apps-fileview.texmex_20251017.01_p0
recursive (3).php
Zoomed into item. 