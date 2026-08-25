<?php
/* ============================================================
   AUTO PHOTO SCAN — runs every time the page loads.
   Reads whatever image files actually exist in img/mahagnao,
   img/inapusong, and img/selfies. Add or remove photos in those
   folders, refresh the page, done — nothing to run, nothing to edit.
============================================================ */

function scan_photos(string $folder): array {
    $extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $files = [];

    if (is_dir($folder)) {
        foreach (scandir($folder) as $name) {
            if ($name === '.' || $name === '..') continue;
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (in_array($ext, $extensions, true)) {
                $files[] = $name;
            }
        }
        sort($files, SORT_NATURAL | SORT_FLAG_CASE);
    }

    return $files;
}

$manifest = [
    'mahagnao'  => scan_photos(__DIR__ . '/img/mahagnao'),
    'inapusong' => scan_photos(__DIR__ . '/img/inapusong'),
    'selfies'   => scan_photos(__DIR__ . '/img/selfies'),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Our Scrapbook</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@500;600;700&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Special+Elite&display=swap" rel="stylesheet">

<!--
  HOW TO CUSTOMIZE THIS PAGE
  ==========================
  1. Search for [HER NAME] and [YOUR NAME] and replace with real names.
  2. Photos are picked up automatically from img/mahagnao, img/inapusong,
     and img/selfies — just add/remove files in those folders and refresh.
     Each folder becomes ONE album card. Tap it to page through every
     photo in that folder, looping back to the first after the last.
  3. Edit each folder's label in the FOLDERS array in the script near
     the bottom of this file.
  4. Edit the letter text inside <div class="letter-content"> to make it yours.

  IMPORTANT: this file needs to run through PHP, it will NOT work by
  double-clicking it. See the note at the very bottom of this file for
  how to run it locally.
-->

<style>
  :root{
    --paper: #ece1c8;
    --paper-edge: #ddcda3;
    --ink: #3b2e27;
    --ink-soft: #5c4a3d;
    --rose: #b96b76;
    --blue: #5f7f9c;
    --gold: #a9812f;
    --string: #8b7355;
    --cream-card: #fbf6e9;
  }

  *{ box-sizing: border-box; }

  html{ scroll-behavior: smooth; }

  body{
    margin:0;
    min-height:100vh;
    background:
      radial-gradient(ellipse at top, rgba(255,255,255,0.25), transparent 60%),
      repeating-linear-gradient(0deg, rgba(139,115,85,0.035) 0px, rgba(139,115,85,0.035) 1px, transparent 1px, transparent 3px),
      var(--paper);
    color: var(--ink);
    font-family: 'Cormorant Garamond', serif;
    overflow-x:hidden;
    padding-bottom: 80px;
  }

  header{
    text-align:center;
    padding: 56px 20px 20px;
    position:relative;
  }

  .tape{
    position:absolute;
    width:120px; height:34px;
    background: rgba(95,127,156,0.35);
    border: 1px solid rgba(95,127,156,0.5);
    top: 18px; left:50%;
    transform: translateX(-50%) rotate(-3deg);
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
  }

  h1{
    font-family:'Caveat', cursive;
    font-weight:700;
    font-size: clamp(2.6rem, 7vw, 4.2rem);
    margin: 30px 0 6px;
    color: var(--ink);
    letter-spacing: 0.5px;
  }

  .subtitle{
    font-family:'Special Elite', monospace;
    font-size: 0.78rem;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    color: var(--ink-soft);
    opacity:0.75;
  }

  .subtitle .heart{ color: var(--rose); }

  .board{
    max-width: 1080px;
    margin: 50px auto 10px;
    padding: 0 24px;
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 46px 28px;
    position:relative;
    justify-items:center;
  }

  .polaroid{
    position:relative;
    width: 100%;
    max-width: 280px;
    height: 320px;
    cursor:pointer;
    background: var(--cream-card);
    border: 1px solid rgba(0,0,0,0.06);
    box-shadow: 0 10px 22px rgba(59,46,39,0.18), 0 2px 4px rgba(59,46,39,0.1);
    padding: 14px 14px 0;
    display:flex;
    flex-direction:column;
  }

  /* stacked "album" edges peeking out behind the top card */
  .polaroid::before, .polaroid::after{
    content:"";
    position:absolute;
    inset: 6px -6px -6px 6px;
    background: var(--cream-card);
    border: 1px solid rgba(0,0,0,0.06);
    box-shadow: 0 6px 14px rgba(59,46,39,0.14);
    z-index:-1;
  }
  .polaroid::after{
    inset: 12px -12px -12px 12px;
    z-index:-2;
    opacity:0.75;
  }

  .illustration{
    flex:1;
    background: linear-gradient(160deg, #eef1ea, #dfe6dd);
    display:flex; align-items:center; justify-content:center;
    overflow:hidden;
    position:relative;
  }

  .illustration img{
    width:100%; height:100%;
    object-fit:cover;
    display:block;
    transition: opacity 0.25s ease;
  }
  .illustration img.swapping{ opacity:0; }

  .card-content{
    display:flex;
    flex-direction:column;
    flex:1;
    min-height:0;
    transition: opacity 0.25s ease;
  }
  .card-content.swapping{ opacity:0; }

  .caption-view{
    flex:1;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    text-align:center;
    background: linear-gradient(160deg, #eef1ea, #dfe6dd);
    padding: 20px;
  }
  .caption-view p{
    font-family:'Caveat', cursive;
    font-size: 1.5rem;
    line-height:1.35;
    color: var(--ink);
    margin:0 0 10px;
  }

  .album-badge{
    position:absolute;
    top:8px; right:8px;
    width:26px; height:26px;
    border-radius:50%;
    background: rgba(59,46,39,0.6);
    color:#fbf6e9;
    display:flex; align-items:center; justify-content:center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.25);
    pointer-events:none;
  }

  .polaroid-label{
    font-family:'Caveat', cursive;
    font-size: 1.25rem;
    text-align:center;
    padding: 8px 0 4px;
    color: var(--ink-soft);
  }

  .photo-counter{
    font-family:'Special Elite', monospace;
    font-size: 0.68rem;
    letter-spacing: 1px;
    text-align:center;
    color: var(--ink-soft);
    opacity:0.6;
    padding-bottom: 10px;
  }

  .pin{
    position:absolute; top:-10px; left:50%;
    transform: translateX(-50%);
    width:16px; height:16px;
    border-radius:50%;
    background: radial-gradient(circle at 35% 30%, #fff, var(--rose) 60%);
    box-shadow: 0 3px 4px rgba(0,0,0,0.3);
    z-index:2;
  }
  .pin.pin-blue{
    background: radial-gradient(circle at 35% 30%, #fff, var(--blue) 60%);
  }

  .flip-hint{
    text-align:center;
    font-family:'Special Elite', monospace;
    font-size:0.7rem;
    color: var(--ink-soft);
    opacity:0.55;
    margin: -20px 0 0;
    letter-spacing:1px;
    margin-top: 50px;
  }

  .letter-section{
    max-width: 640px;
    margin: 100px auto 40px;
    text-align:center;
    padding: 0 24px;
  }

  .envelope-wrap{
    perspective: 1600px;
    width: 280px;
    height: 190px;
    margin: 0 auto;
    cursor:pointer;
  }

  .envelope{
    position:relative;
    width:100%; height:100%;
  }

  .env-back{
    position:absolute; inset:0;
    background: linear-gradient(160deg, #e4d5b0, #d3c090);
    box-shadow: 0 14px 30px rgba(59,46,39,0.25);
  }

  .env-pocket{
    position:absolute; inset:0;
    clip-path: polygon(0 100%, 50% 46%, 100% 100%);
    background: linear-gradient(160deg,#dcc99f,#c9b483);
    z-index:2;
  }

  .env-flap{
    position:absolute;
    top:0; left:0; width:100%; height:100%;
    clip-path: polygon(0 0, 100% 0, 50% 55%);
    background: linear-gradient(200deg,#e9dab7,#d6c295);
    transform-origin: top;
    transition: transform 0.8s cubic-bezier(.4,.2,.2,1);
    z-index:3;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
  }
  .envelope.open .env-flap{ transform: rotateX(180deg); z-index:1; }

  .wax-seal{
    position:absolute;
    top: 52%; left:50%;
    transform: translate(-50%,-50%);
    width:42px; height:42px;
    border-radius:50%;
    background: radial-gradient(circle at 35% 30%, #d68a90, var(--rose) 65%);
    color:#fff2ee;
    display:flex; align-items:center; justify-content:center;
    font-size:1.2rem;
    box-shadow: 0 3px 6px rgba(0,0,0,0.3);
    z-index:4;
    transition: opacity 0.3s ease;
  }
  .envelope.open .wax-seal{ opacity:0; pointer-events:none; }

  .hint{
    font-family:'Special Elite', monospace;
    font-size:0.75rem;
    letter-spacing:1.5px;
    color: var(--ink-soft);
    opacity:0.65;
    margin-top: 18px;
  }

  .letter-overlay{
    position: fixed; inset:0;
    background: rgba(59,46,39,0.55);
    display:flex; align-items:center; justify-content:center;
    padding: 30px;
    opacity:0; pointer-events:none;
    transition: opacity 0.4s ease;
    z-index: 50;
  }
  .letter-overlay.show{ opacity:1; pointer-events:all; }

  .letter-content{
    background: var(--cream-card);
    max-width: 520px;
    width:100%;
    max-height: 82vh;
    overflow-y:auto;
    padding: 46px 40px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.4);
    position:relative;
    transform: scale(0.85) translateY(30px);
    transition: transform 0.4s cubic-bezier(.2,.6,.3,1);
  }
  .letter-overlay.show .letter-content{ transform: scale(1) translateY(0); }

  .letter-content h2{
    font-family:'Caveat', cursive;
    font-size: 2.1rem;
    margin: 0 0 18px;
    color: var(--rose);
  }
  .letter-content p{
    font-size: 1.15rem;
    line-height: 1.75;
    color: var(--ink);
    margin: 0 0 16px;
    text-align:left;
  }
  .letter-signoff{
    font-family:'Caveat', cursive;
    font-size: 1.6rem;
    text-align:right;
    color: var(--ink-soft);
    margin-top: 24px;
  }

  .close-letter{
    position:absolute; top:14px; right:18px;
    background:none; border:none;
    font-family:'Special Elite', monospace;
    font-size:0.85rem;
    color: var(--ink-soft);
    cursor:pointer;
    opacity:0.6;
  }
  .close-letter:hover{ opacity:1; }

  footer{
    text-align:center;
    font-family:'Special Elite', monospace;
    font-size:0.7rem;
    letter-spacing:1.5px;
    color: var(--ink-soft);
    opacity:0.5;
    margin-top: 40px;
  }

  @media (prefers-reduced-motion: reduce){
    *{ transition: none !important; }
  }
</style>
</head>
<body>

<header>
  <div class="tape"></div>
  <h1>Our Little Scrapbook</h1>
  <p class="subtitle">for Florelyn <span class="heart">&hearts;</span> from Paul</p>
</header>

<main>
  <section class="board" id="board"></section>

  <p class="flip-hint">tap an album to see more photos — a note waits at the end</p>

  <section class="letter-section">
    <div class="envelope-wrap" id="envelopeWrap">
      <div class="envelope" id="envelope">
        <div class="env-back"></div>
        <div class="env-pocket"></div>
        <div class="env-flap"></div>
        <div class="wax-seal">&hearts;</div>
      </div>
    </div>
    <p class="hint">tap the envelope to open your letter</p>
  </section>
</main>

<div class="letter-overlay" id="letterOverlay">
  <div class="letter-content">
    <button class="close-letter" id="closeLetter">CLOSE ✕</button>
    <h2>My Love Florelyn,</h2>
    <p>Happy Monthsary Love ❤️, dik sure kun pira na kita ka months. Dik maaram gihap kun anu ak ig reregalo haim LDR man kita yana.</p>
    <p>Naawod ak haim pag vivideo call kay dik maaram kun anu ak mga ig sstorya tas ban liwat may mga nabati ngada haha. Love sorry kun danay napapabug at ko pa tim makapoy na adlaw ngada, kun may mga unreasonable ak na gin rerequest or gin hihimo.</p>
    <p>Wish ko na bisan sugad at kabutang yana, maging successful kita tat mga goals, na para sunod kun baga mag kita na kita utro, atleast may ada kita tagsa tagsa na improvement haat kalugaringon. Proud ak haim kay nakakaya mo tim kabutang yana, nabilib ak na sugad ka ka ilob para ha future tas tim pagiging understanding.</p>
    <p>Ayla pag inisipa tim pag ka miss haak labis na tak pag ka miss haim, kay magkikita man kita soon. Bawion nat nala tat mga travels na waray kapapadayon. I love you pirme ngan stay healthy ❤️🥰 MISS YOU NA</p>
    <p class="letter-signoff">— yours, always, Paul Ian na cute</p>
  </div>
</div>

<footer>made with a lot of missing you</footer>

<script>
  // Filled in directly by PHP above — always reflects what's really in the folders.
  const FOLDERS = [
    {
      key: 'mahagnao',
      path: 'img/mahagnao/',
      label: 'Mahagnao Lake Travel',
      caption: 'One of our most important chapter in life 🥰.',
      files: <?php echo json_encode($manifest['mahagnao']); ?>
    },
    {
      key: 'inapusong',
      path: 'img/inapusong/',
      label: 'Mt. Inapusong Hike',
      caption: 'Mahusay an kadagatan, pero mas mahusay ka syempre 😊.',
      files: <?php echo json_encode($manifest['inapusong']); ?>
    },
    {
      key: 'selfies',
      path: 'img/selfies/',
      label: 'Us',
      caption: 'Just us, being us.',
      files: <?php echo json_encode($manifest['selfies']); ?>
    }
  ];

  function makePin(index){
    const pin = document.createElement('div');
    pin.className = 'pin' + (index % 2 === 1 ? ' pin-blue' : '');
    return pin;
  }

  // Each folder becomes ONE album card, no flip. Tapping it just steps to
  // the next photo in that folder, looping back to the first after the last.
  function makeAlbumEl(folder, rotateDeg, pinIndex){
    const photos = folder.files;
    const total = photos.length;
    const stateCount = total + 1; // last state (index === total) is the caption
    let state = 0;

    const wrap = document.createElement('div');
    wrap.className = 'polaroid';
    wrap.style.transform = `rotate(${rotateDeg}deg)`;

    const content = document.createElement('div');
    content.className = 'card-content';

    function render(){
      content.innerHTML = '';

      if (state < total){
        const illustration = document.createElement('div');
        illustration.className = 'illustration';

        const img = document.createElement('img');
        img.alt = folder.label;
        img.src = folder.path + photos[state];
        illustration.appendChild(img);

        if (total > 1){
          const badge = document.createElement('div');
          badge.className = 'album-badge';
          badge.title = `${total} photos in this album`;
          badge.innerHTML = `
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="5" y="5" width="14" height="14" rx="1.5"></rect>
              <path d="M2 9v9a1.5 1.5 0 0 0 1.5 1.5H13"></path>
            </svg>`;
          illustration.appendChild(badge);
        }

        const label = document.createElement('p');
        label.className = 'polaroid-label';
        label.textContent = folder.label;

        const counter = document.createElement('p');
        counter.className = 'photo-counter';
        counter.textContent = `${state + 1} / ${total}`;

        content.appendChild(illustration);
        content.appendChild(label);
        content.appendChild(counter);
      } else {
        const captionView = document.createElement('div');
        captionView.className = 'caption-view';
        captionView.innerHTML = `
          <p>${folder.caption}</p>
          <p class="photo-counter">end of album</p>
        `;
        content.appendChild(captionView);
      }
    }
    render();

    wrap.appendChild(makePin(pinIndex));
    wrap.appendChild(content);

    wrap.addEventListener('click', () => {
      content.classList.add('swapping');
      setTimeout(() => {
        state = (state + 1) % stateCount;
        render();
        content.classList.remove('swapping');
      }, 200);
    });

    return wrap;
  }

  function buildBoard(){
    const board = document.getElementById('board');
    const rotations = [-4, 3, -2, 4, -3, 2];
    let pinIndex = 0;

    const activeFolders = FOLDERS.filter(f => f.files.length > 0);

    activeFolders.forEach((folder) => {
      const el = makeAlbumEl(folder, rotations[pinIndex % rotations.length], pinIndex);
      board.appendChild(el);
      pinIndex++;
    });

    if (activeFolders.length === 0){
      board.innerHTML = `<p style="grid-column:1/-1;text-align:center;font-family:'Special Elite',monospace;opacity:0.6;line-height:1.8;">
        no photos found yet — add photos to img/mahagnao, img/inapusong,<br>
        and img/selfies, then refresh this page.
      </p>`;
    }
  }

  buildBoard();

  const envelope = document.getElementById('envelope');
  const envelopeWrap = document.getElementById('envelopeWrap');
  const overlay = document.getElementById('letterOverlay');
  const closeBtn = document.getElementById('closeLetter');

  envelopeWrap.addEventListener('click', () => {
    envelope.classList.add('open');
    setTimeout(() => overlay.classList.add('show'), 400);
  });

  function closeLetter(){
    overlay.classList.remove('show');
    setTimeout(() => envelope.classList.remove('open'), 300);
  }
  closeBtn.addEventListener('click', (e) => { e.stopPropagation(); closeLetter(); });
  overlay.addEventListener('click', (e) => { if(e.target === overlay) closeLetter(); });
</script>

</body>
</html>
<!--
  RUNNING THIS FILE
  =================
  Browsers cannot execute PHP directly — double-clicking this file will
  just show or download raw code instead of the page. You need a PHP
  server running. Easiest option if you have PHP installed:

    1. Open a terminal in this folder (the one containing index.php and img/)
    2. Run:  php -S localhost:8000
    3. Open http://localhost:8000 in your browser

  If "php" isn't recognized, install PHP first (php.net/downloads,
  or XAMPP/WAMP on Windows which bundles PHP with a one-click start).
  When you're ready to send this to her, upload the whole folder
  (index.php + img/) to any PHP-enabled web host.
-->