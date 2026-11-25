<?php
declare(strict_types=1);
require __DIR__ . '/../../app/lib.php';

header('Cache-Control: no-store');
$s = settings();

/* ---------- work out which screen to show ---------- */
$preview = isset($_GET['preview']) && admin_logged_in();
$view = 'form';
$remaining = 0;
$ctxToken = '';
$netName = '';

if ($preview) {
    $netName = 'Sample Network';
    $view = in_array($_GET['view'] ?? '', ['form', 'success', 'cooldown', 'resume'], true) ? $_GET['view'] : 'form';
    $remaining = 3 * 3600 + 25 * 60;
} else {
    $siteId = (string)($_GET['site'] ?? '');
    $mac = normalize_mac((string)($_GET['id'] ?? ''));
    $net = $siteId !== '' ? network_by_site($siteId) : null;
    if (!$net || !$mac) {
        $view = 'nodevice';
    } else {
        $netName = $net['name'];
        $ctxToken = make_ctx([
            'm' => $mac, 's' => $siteId, 'a' => normalize_mac((string)($_GET['ap'] ?? '')) ?: null,
            'ss' => mb_substr((string)($_GET['ssid'] ?? ''), 0, 64), 'u' => (string)($_GET['url'] ?? ''), 't' => time(),
        ]);
        $st = mac_state($mac);
        if ($st['state'] === 'active') {
            $view = 'resume';
            $remaining = $st['remaining'];
        } elseif ($st['state'] === 'cooldown') {
            $view = 'cooldown';
            $remaining = $st['remaining'];
        }
    }
}

/* ---------- theme ---------- */
function hex_rgb(string $hex): string
{
    $hex = ltrim($hex, '#');
    return hexdec(substr($hex, 0, 2)) . ',' . hexdec(substr($hex, 2, 2)) . ',' . hexdec(substr($hex, 4, 2));
}
function bg_css(array $s): string
{
    if ($s['portal_bg_type'] === 'image' && $s['portal_bg_image'] !== '') {
        return "url('../uploads/" . h($s['portal_bg_image']) . "') center/cover no-repeat fixed, " . $s['portal_bg_color'];
    }
    if ($s['portal_bg_type'] === 'gradient') {
        return 'linear-gradient(135deg,' . $s['portal_bg_color'] . ',' . $s['portal_bg_color2'] . ') fixed';
    }
    return $s['portal_bg_color'];
}
$fonts = [
    'system' => 'system-ui,-apple-system,"Segoe UI",Roboto,Helvetica,Arial,sans-serif',
    'serif' => 'Georgia,"Times New Roman",serif',
    'rounded' => '"Trebuchet MS","Segoe UI",Verdana,sans-serif',
    'mono' => 'ui-monospace,Menlo,Consolas,monospace',
];
$sub = str_replace('{network}', $netName, $s['portal_subtitle']);
$logo = $s['portal_logo'] !== '' ? '../uploads/' . $s['portal_logo'] : '';
$state = ['view' => $view, 'remaining' => $remaining, 'ctx' => $ctxToken, 'preview' => $preview,
    'success' => ['title' => $s['portal_success_title'], 'message' => $s['portal_success_message']],
    'cooldownMsg' => $s['portal_cooldown_message']];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title data-bind="portal_page_title"><?= h($s['portal_page_title']) ?></title>
<style>
:root{
  --primary:<?= h($s['portal_primary_color']) ?>;--btntext:<?= h($s['portal_button_text_color']) ?>;
  --card:rgba(<?= hex_rgb($s['portal_card_bg']) ?>,<?= $s['portal_card_opacity'] / 100 ?>);
  --text:<?= h($s['portal_text_color']) ?>;--muted:<?= h($s['portal_muted_color']) ?>;
  --radius:<?= (int)$s['portal_radius'] ?>px;--overlay:<?= $s['portal_bg_type'] === 'image' ? $s['portal_bg_overlay'] / 100 : 0 ?>;--logo:<?= (int)$s['portal_logo_size'] ?>px;
  --font:<?= $fonts[$s['portal_font']] ?>;--shadow:<?= $s['portal_card_opacity'] < 5 ? 'none' : '0 20px 60px rgba(0,0,0,.28)' ?>;--footer:<?= h($s['portal_footer_color']) ?>;
}
*{box-sizing:border-box}
html,body{margin:0;min-height:100%}
body{font-family:var(--font);color:var(--text);background:<?= bg_css($s) ?>;display:flex;min-height:100vh;align-items:center;justify-content:center;padding:20px}
.overlay{position:fixed;inset:0;background:rgba(0,0,0,var(--overlay));pointer-events:none}
.card{position:relative;width:100%;max-width:380px;background:var(--card);border-radius:var(--radius);padding:32px 28px;box-shadow:var(--shadow);text-align:center}
.logo{max-height:var(--logo);max-width:70%;margin:0 auto 14px;display:block}
h1{font-size:1.9rem;margin:0 0 6px;line-height:1.25}
.sub{color:var(--text);margin:0 0 6px;font-size:.95rem;line-height:1.45}.sub:empty{display:none}
label{display:block;text-align:center;font-size:1rem;font-weight:700;margin:18px 0 10px}
.otp{display:flex;gap:10px;justify-content:center;margin:6px 0 4px}
.otp input{width:44px;height:48px;text-align:center;font-size:1.4rem;font-weight:600;border:1px solid rgba(70,90,100,.55);border-radius:8px;background:rgba(255,255,255,.18);color:var(--text);outline:none;padding:0;font-family:inherit}
.otp input:focus{border-color:var(--primary);background:rgba(255,255,255,.4);box-shadow:0 0 0 3px rgba(21,116,130,.25)}
.btn{margin-top:24px;width:100%;padding:11px;font-size:1rem;font-weight:400;border:0;border-radius:calc(var(--radius) * .6);background:var(--primary);color:var(--btntext);cursor:pointer}
.btn:disabled{opacity:.6;cursor:wait}
.err{background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:10px;padding:10px 12px;font-size:.88rem;margin-top:14px;text-align:left}
.terms{font-size:.74rem;color:var(--muted);margin-top:18px;line-height:1.4}
.foot{font-size:.95rem;font-weight:700;color:var(--footer);margin-top:24px}
.big{font-size:2.1rem;font-weight:800;margin:12px 0;font-variant-numeric:tabular-nums}
.icon{font-size:3rem;line-height:1}
.hidden{display:none!important}
<?= str_ireplace('</style', '', $s['portal_custom_css']) ?>
</style>
</head>
<body>
<div class="overlay"></div>
<main class="card">
  <img class="logo <?= $logo ? '' : 'hidden' ?>" id="logo" src="<?= h($logo) ?>" alt="">

  <!-- voucher form -->
  <section id="v-form" class="<?= in_array($view, ['form'], true) ? '' : 'hidden' ?>">
    <h1 data-bind="portal_title"><?= h($s['portal_title']) ?></h1>
    <p class="sub" id="sub"><?= h($sub) ?></p>
    <form id="f" autocomplete="off">
      <label for="d0" data-bind="portal_input_label"><?= h($s['portal_input_label']) ?></label>
      <div class="otp" id="otp"><?php for ($i = 0; $i < 6; $i++): ?><input type="text" id="d<?= $i ?>" inputmode="numeric" pattern="[0-9]*" maxlength="1" autocomplete="one-time-code" aria-label="Digit <?= $i + 1 ?>"><?php endfor; ?></div>
      <div class="err hidden" id="err" role="alert"></div>
      <button class="btn" id="btn" type="submit" data-bind="portal_button_text"><?= h($s['portal_button_text']) ?></button>
    </form>
    <div class="terms <?= $s['portal_terms'] === '' ? 'hidden' : '' ?>" id="terms" data-bind="portal_terms"><?= nl2br(h($s['portal_terms'])) ?></div>
  </section>

  <!-- welcome back / reconnecting -->
  <section id="v-resume" class="<?= $view === 'resume' ? '' : 'hidden' ?>">
    <div class="icon">&#x1F4F6;</div>
    <h1>Welcome back</h1>
    <p class="sub" id="resume-msg">Reconnecting you&hellip;</p>
    <div class="big" id="resume-time"></div>
    <div class="err hidden" id="resume-err"></div>
    <button class="btn hidden" id="resume-retry" type="button">Try again</button>
  </section>

  <!-- connected -->
  <section id="v-success" class="<?= $view === 'success' ? '' : 'hidden' ?>">
    <div class="icon">&#x2705;</div>
    <h1 id="ok-title" data-bind="portal_success_title"><?= h($s['portal_success_title']) ?></h1>
    <p class="sub" id="ok-msg"><?= h(str_replace('{time}', fmt_duration($remaining), $s['portal_success_message'])) ?></p>
    <div class="big" id="ok-time"></div>
  </section>

  <!-- cooldown -->
  <section id="v-cooldown" class="<?= $view === 'cooldown' ? '' : 'hidden' ?>">
    <div class="icon">&#x23F3;</div>
    <h1 id="cd-title" data-bind="portal_cooldown_title"><?= h($s['portal_cooldown_title']) ?></h1>
    <p class="sub" id="cd-msg"><?= h(str_replace('{time}', fmt_duration($remaining), $s['portal_cooldown_message'])) ?></p>
    <div class="big" id="cd-time"></div>
  </section>

  <!-- opened without being redirected by the controller -->
  <section id="v-nodevice" class="<?= $view === 'nodevice' ? '' : 'hidden' ?>">
    <div class="icon">&#x1F4F6;</div>
    <h1>Connect to the WiFi first</h1>
    <p class="sub">This page is opened automatically after you join the WiFi network. Please connect to the network, then open any website.</p>
  </section>

  <div class="foot <?= $s['portal_footer'] === '' ? 'hidden' : '' ?>" id="foot" data-bind="portal_footer"><?= h($s['portal_footer']) ?></div>
</main>
<style id="custom-css"></style>
<script>
(function () {
  var S = <?= json_encode($state, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
  var $ = function (id) { return document.getElementById(id); };

  function show(v) {
    ['form', 'resume', 'success', 'cooldown', 'nodevice'].forEach(function (n) {
      $('v-' + n).classList.toggle('hidden', n !== v);
    });
  }
  function fmt(sec) {
    sec = Math.max(0, Math.floor(sec));
    var d = Math.floor(sec / 86400), h = Math.floor(sec % 86400 / 3600), m = Math.floor(sec % 3600 / 60), s = sec % 60;
    var p = function (n) { return (n < 10 ? '0' : '') + n; };
    return (d ? d + 'd ' : '') + p(h) + ':' + p(m) + ':' + p(s);
  }
  function countdown(el, secs, done) {
    var end = Date.now() + secs * 1000;
    (function tick() {
      var left = Math.ceil((end - Date.now()) / 1000);
      el.textContent = fmt(left);
      if (left <= 0) { if (done) done(); return; }
      setTimeout(tick, 1000);
    })();
  }
  function post(body) {
    body.ctx = S.ctx;
    return fetch('../portal/api.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) })
      .then(function (r) { return r.json(); });
  }
  function connected(res) {
    $('ok-title').textContent = res.title;
    $('ok-msg').textContent = res.message;
    show('success');
    countdown($('ok-time'), res.remaining);
    if (res.redirect) setTimeout(function () { location.href = res.redirect; }, 3500);
  }
  function showCooldown(secs) {
    var msg = S.cooldownMsg;
    $('cd-msg').textContent = msg.replace('{time}', fmt(secs));
    show('cooldown');
    countdown($('cd-time'), secs, function () { location.reload(); });
  }

  if (S.preview) {
    if (S.view === 'success') countdown($('ok-time'), S.remaining);
    if (S.view === 'cooldown') countdown($('cd-time'), S.remaining);
    if (S.view === 'resume') { $('resume-msg').textContent = 'Time left on your voucher'; countdown($('resume-time'), S.remaining); }
    $('f').addEventListener('submit', function (e) { e.preventDefault(); });
    window.addEventListener('message', function (e) {
      if (e.origin !== location.origin || !e.data || e.data.type !== 'theme') return;
      applyTheme(e.data.s, e.data.view);
    });
    function rgb(hex) { hex = hex.replace('#', ''); return parseInt(hex.substr(0, 2), 16) + ',' + parseInt(hex.substr(2, 2), 16) + ',' + parseInt(hex.substr(4, 2), 16); }
    function applyTheme(s, view) {
      var r = document.documentElement.style;
      r.setProperty('--primary', s.portal_primary_color); r.setProperty('--btntext', s.portal_button_text_color);
      r.setProperty('--card', 'rgba(' + rgb(s.portal_card_bg) + ',' + (s.portal_card_opacity / 100) + ')');
      r.setProperty('--text', s.portal_text_color); r.setProperty('--muted', s.portal_muted_color);
      r.setProperty('--radius', s.portal_radius + 'px'); r.setProperty('--overlay', s.portal_bg_type === 'image' ? s.portal_bg_overlay / 100 : 0);
      r.setProperty('--logo', s.portal_logo_size + 'px');
      r.setProperty('--font', { system: 'system-ui,-apple-system,"Segoe UI",Roboto,sans-serif', serif: 'Georgia,serif', rounded: '"Trebuchet MS","Segoe UI",sans-serif', mono: 'ui-monospace,Menlo,Consolas,monospace' }[s.portal_font]);
      var bg = s.portal_bg_color;
      if (s.portal_bg_type === 'gradient') bg = 'linear-gradient(135deg,' + s.portal_bg_color + ',' + s.portal_bg_color2 + ') fixed';
      if (s.portal_bg_type === 'image' && s.portal_bg_image) bg = "url('../uploads/" + s.portal_bg_image + "') center/cover no-repeat fixed, " + s.portal_bg_color;
      document.body.style.background = bg;
      document.querySelectorAll('[data-bind]').forEach(function (el) {
        var k = el.getAttribute('data-bind'); if (s[k] !== undefined) el.textContent = s[k];
      });
      $('sub').textContent = s.portal_subtitle.replace('{network}', 'Sample Network');
      $('ok-msg').textContent = s.portal_success_message.replace('{time}', '3h 25m');
      $('cd-msg').textContent = s.portal_cooldown_message.replace('{time}', '29m');
      $('terms').classList.toggle('hidden', !s.portal_terms); $('foot').classList.toggle('hidden', !s.portal_footer);
      r.setProperty('--footer', s.portal_footer_color); r.setProperty('--shadow', +s.portal_card_opacity < 5 ? 'none' : '0 20px 60px rgba(0,0,0,.28)');
      $('logo').classList.toggle('hidden', !s.portal_logo); if (s.portal_logo) $('logo').src = '../uploads/' + s.portal_logo;
      $('custom-css').textContent = (s.portal_custom_css || '').replace(/<\/style/gi, '');
      if (view) show(view);
    }
    return;
  }

  if (S.view === 'cooldown') showCooldown(S.remaining);

  if (S.view === 'resume') {
    var attempt = function () {
      $('resume-err').classList.add('hidden'); $('resume-retry').classList.add('hidden');
      $('resume-msg').textContent = 'Reconnecting you…';
      post({ action: 'resume' }).then(function (j) {
        if (j.ok) connected(j);
        else if (j.state === 'free' || j.state === 'cooldown') location.reload();
        else { $('resume-err').textContent = j.error; $('resume-err').classList.remove('hidden'); $('resume-retry').classList.remove('hidden'); }
      }).catch(function () {
        $('resume-err').textContent = 'Network error. Please try again.'; $('resume-err').classList.remove('hidden'); $('resume-retry').classList.remove('hidden');
      });
    };
    $('resume-retry').onclick = attempt;
    countdown($('resume-time'), S.remaining);
    attempt();
  }

  var boxes = Array.prototype.slice.call(document.querySelectorAll('#otp input'));
  boxes.forEach(function (b, i) {
    b.addEventListener('input', function () {
      var v = b.value.replace(/\D/g, '');
      if (v.length > 1) { fill(v, i); return; }
      b.value = v; if (v && boxes[i + 1]) boxes[i + 1].focus();
    });
    b.addEventListener('keydown', function (e) {
      if (e.key === 'Backspace' && !b.value && boxes[i - 1]) { boxes[i - 1].value = ''; boxes[i - 1].focus(); e.preventDefault(); }
      if (e.key === 'ArrowLeft' && boxes[i - 1]) boxes[i - 1].focus();
      if (e.key === 'ArrowRight' && boxes[i + 1]) boxes[i + 1].focus();
    });
    b.addEventListener('paste', function (e) {
      var t = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
      if (t) { e.preventDefault(); fill(t, i); }
    });
    b.addEventListener('focus', function () { b.select(); });
  });
  function fill(digits, from) {
    digits.split('').slice(0, boxes.length - from).forEach(function (d, k) { boxes[from + k].value = d; });
    boxes[Math.min(from + digits.length, boxes.length - 1)].focus();
  }
  function codeValue() { return boxes.map(function (b) { return b.value; }).join(''); }
  $('f').addEventListener('submit', function (e) {
    e.preventDefault();
    var btn = $('btn'), err = $('err');
    err.classList.add('hidden');
    if (codeValue().length !== 6) { err.textContent = 'Please enter the 6-digit code.'; err.classList.remove('hidden'); return; }
    btn.disabled = true;
    post({ action: 'redeem', code: codeValue() }).then(function (j) {
      btn.disabled = false;
      if (j.ok) return connected(j);
      if (j.state === 'cooldown') return showCooldown(j.remaining);
      err.textContent = j.error || 'Something went wrong.'; err.classList.remove('hidden');
    }).catch(function () {
      btn.disabled = false; err.textContent = 'Network error. Please try again.'; err.classList.remove('hidden');
    });
  });
})();
</script>
</body>
</html>
