<?php
$booksFile = __DIR__ . '/books.json';
if (!file_exists($booksFile)) die("❌ Error: books.json not found.");
$books = json_decode(file_get_contents($booksFile), true);
if (!is_array($books)) die("❌ Invalid books.json format.");

// --- BST Node & Book BST ---
class BSTNode {
    public $book, $left, $right;
    public function __construct($book) { $this->book = $book; $this->left = null; $this->right = null; }
}
class BookBST {
    public $root = null;
    public function insert($book) { $this->root = $this->insertRec($this->root, $book); }
    private function insertRec($node, $book) {
        if (!$node) return new BSTNode($book);
        if (strcasecmp($book['title'], $node->book['title']) < 0) $node->left = $this->insertRec($node->left, $book);
        else $node->right = $this->insertRec($node->right, $book);
        return $node;
    }
    public function search($title) { return $this->searchRec($this->root, strtolower(trim($title))); }
    private function searchRec($node, $title) {
        if (!$node) return null;
        $nodeTitle = strtolower($node->book['title']);
        if ($title === $nodeTitle) return $node->book;
        if ($title < $nodeTitle) return $this->searchRec($node->left, $title);
        return $this->searchRec($node->right, $title);
    }
    public function inorder($node, &$arr) { if ($node) { $this->inorder($node->left,$arr); $arr[]=$node->book; $this->inorder($node->right,$arr); } }
}

// --- Hashtable ---
class BookHashtable {
    private $table = [];
    public function insert($book) { $this->table[strtolower(trim($book['title']))] = $book; }
    public function search($title) { return $this->table[strtolower(trim($title))] ?? null; }
}

// --- Build BST and Hashtable ---
$bst = new BookBST();
$hashtable = new BookHashtable();
foreach ($books as $book) {
    $bst->insert($book);
    $hashtable->insert($book);
}

// --- Search ---
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$result = null;
if ($query !== '') {
    $result = $bst->search($query) ?: $hashtable->search($query);
}

// --- Sorted Books ---
$sortedBooks = [];
$bst->inorder($bst->root, $sortedBooks);

// --- Safe image function ---
function getBookImage($book) {
    foreach(['imageLink','image','cover'] as $k) if (!empty($book[$k])) return $book[$k];
    return 'https://via.placeholder.com/150x220?text=No+Image';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>📚 Maroon Library Explorer</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');
:root { --maroon:#800000; --gold:#ffd700; --light:#f8f5f5; --shadow:rgba(0,0,0,0.25);}
body { font-family:'Poppins',sans-serif; background:var(--maroon); color:var(--light); margin:0; padding:0;}
h1 { text-align:center; color:var(--gold); margin:30px 0 10px; text-shadow:0 2px 8px var(--shadow);}
.search-box { text-align:center; margin:20px 0;}
input[type="text"] { width:60%; padding:12px 18px; border-radius:30px; border:2px solid var(--gold); outline:none; font-size:16px; color:var(--maroon); transition: all 0.3s;}
input[type="text"]:focus { transform:scale(1.03); box-shadow:0 4px 12px var(--shadow);}
button { padding:12px 22px; background:var(--gold); border:none; border-radius:25px; font-weight:bold; color:var(--maroon); cursor:pointer; margin-left:10px; transition:0.3s;}
button:hover { background:#ffef85;}
.container { display:grid; grid-template-columns:repeat(auto-fill,minmax(230px,1fr)); gap:25px; padding:20px 50px 60px;}
.card { background:var(--light); border-radius:15px; color:var(--maroon); box-shadow:0 5px 15px var(--shadow); padding:15px; text-align:center; cursor:pointer; transition: transform 0.3s ease, box-shadow 0.3s ease;}
.card img { width:100%; height:250px; object-fit:contain; border-radius:10px; transition: transform 0.3s ease;}
.card strong { display:block; margin-top:10px; font-size:1.1em; color:var(--maroon);}
.small { font-size:0.9em; color:#4b0e0e; }

/* --- Modal Zoom --- */
#zoomModal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); justify-content:center; align-items:center; z-index:1000; animation:fadeIn 0.3s;}
#zoomModal.show { display:flex; }
#zoomModal .modal-content { background:var(--light); color:var(--maroon); padding:20px; border-radius:15px; max-width:350px; text-align:center; box-shadow:0 10px 25px var(--shadow); transform: scale(0); animation:zoomIn 0.3s forwards;}
#zoomModal img { width:100%; height:auto; object-fit:contain; border-radius:10px; }
#zoomModal button { margin-top:10px; padding:10px 20px; background:var(--gold); border:none; border-radius:10px; color:var(--maroon); font-weight:bold; cursor:pointer; }
#zoomModal button:hover { background:#ffef85; }

/* --- Animations --- */
@keyframes zoomIn { from {transform:scale(0);} to {transform:scale(1);} }
@keyframes fadeIn { from {opacity:0;} to {opacity:1;} }
</style>
</head>
<body>

<h1>📘 Maroon Library Explorer</h1>
<div class="search-box">
    <form method="GET">
        <input type="text" name="q" placeholder="🔍 Search book by title..." value="<?php echo htmlspecialchars($query); ?>">
        <button type="submit">Search</button>
    </form>
</div>

<h2 style="text-align:center;color:var(--gold);">✨ All Books</h2>
<div class="container">
<?php
$displayBooks = $sortedBooks;
if ($result) $displayBooks = [$result]; // Only show search result if found
foreach($displayBooks as $b):
    $img = getBookImage($b);
?>
    <div class="card" 
        data-title="<?php echo htmlspecialchars($b['title']); ?>"
        data-author="<?php echo htmlspecialchars($b['author']); ?>"
        data-year="<?php echo htmlspecialchars($b['year']); ?>"
        data-link="<?php echo htmlspecialchars($b['link'] ?? '#'); ?>"
        data-img="<?php echo htmlspecialchars($img); ?>">
      <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($b['title']); ?>">
      <strong><?php echo htmlspecialchars($b['title']); ?></strong>
      <div class="small">✍️ <?php echo htmlspecialchars($b['author']); ?></div>
      <div class="small">📅 <?php echo htmlspecialchars($b['year']); ?></div>
    </div>
<?php endforeach; ?>
</div>

<?php if($query && !$result): ?>
<p style="text-align:center;color:white;font-size:1.2em;">❌ Book not found in library.</p>
<?php endif; ?>

<!-- Zoom Modal -->
<div id="zoomModal">
    <div class="modal-content">
        <img id="modalImg" src="" alt="">
        <strong id="modalTitle"></strong>
        <div class="small" id="modalAuthor"></div>
        <div class="small" id="modalYear"></div>
        <a id="modalLink" href="#" target="_blank"><button>See More</button></a>
    </div>
</div>

<script>
// Modal functionality
const modal = document.getElementById('zoomModal');
const modalImg = document.getElementById('modalImg');
const modalTitle = document.getElementById('modalTitle');
const modalAuthor = document.getElementById('modalAuthor');
const modalYear = document.getElementById('modalYear');
const modalLink = document.getElementById('modalLink');

document.querySelectorAll('.card').forEach(card=>{
    card.addEventListener('click',()=>{
        modalImg.src = card.dataset.img;
        modalTitle.textContent = card.dataset.title;
        modalAuthor.textContent = '✍️ ' + card.dataset.author;
        modalYear.textContent = '📅 ' + card.dataset.year;
        modalLink.href = card.dataset.link;
        modal.classList.add('show');
    });
});

modal.addEventListener('click', e=>{
    if(e.target === modal) modal.classList.remove('show');
});
</script>

</body>
</html>
