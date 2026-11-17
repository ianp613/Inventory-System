(function () {
  'use strict';

  var distros = {};
  (window.DISTROS || []).forEach(function (d) { distros[d.id] = d; });

  var form = document.getElementById('form');
  var distroSel = document.getElementById('distro');
  var versionSel = document.getElementById('version');
  var versionWrap = document.getElementById('version-wrap');
  var infoBox = document.getElementById('distro-info');
  var existing = document.getElementById('existing_os');
  var freeWrap = document.getElementById('free-wrap');
  var resultBox = document.getElementById('result');
  var errBox = document.getElementById('form-errors');
  var goBtn = document.getElementById('go');

  // Small DOM helper. Everything is set with textContent, never innerHTML,
  // so server text can never inject markup.
  function el(tag, attrs, children) {
    var n = document.createElement(tag);
    if (attrs) {
      Object.keys(attrs).forEach(function (k) {
        if (k === 'class') n.className = attrs[k];
        else if (k === 'text') n.textContent = attrs[k];
        else n.setAttribute(k, attrs[k]);
      });
    }
    (children || []).forEach(function (c) {
      if (c) n.appendChild(typeof c === 'string' ? document.createTextNode(c) : c);
    });
    return n;
  }

  function safeUrl(u) {
    return typeof u === 'string' && /^https:\/\//.test(u) ? u : null;
  }

  function fmtGb(n) {
    if (n >= 100) return Math.round(n) + ' GB';
    return (Math.round(n * 10) / 10) + ' GB';
  }

  var METHOD = {
    guided_gui: 'Graphical installer',
    guided_tui: 'Text installer',
    manual: 'Manual install',
    image: 'Disk image (no installer)',
    live_usb: 'Live USB',
    special: 'Special case'
  };
  var SB = { yes: 'Supported', partial: 'Partial / manual', no: 'Not supported', unknown: 'Unknown' };

  // ---- Version dropdown and info card ------------------------------------
  var SUPPORT_SUFFIX = { ended: ' - support ended', limited: ' - limited support', ending_soon: ' - ending soon' };

  function selectedVersion(d) {
    if (!d || !d.versions || !d.versions.length) return null;
    for (var i = 0; i < d.versions.length; i++) {
      if (d.versions[i].id === versionSel.value) return d.versions[i];
    }
    return d.versions[0];
  }

  function fillVersions() {
    var d = distros[distroSel.value];
    versionSel.textContent = '';
    if (!d || !d.versions || !d.versions.length) { versionWrap.hidden = true; return; }
    var def = d.versions[0].id;
    d.versions.forEach(function (v) {
      if (v.default) def = v.id;
      versionSel.appendChild(el('option', { value: v.id, text: v.label + (SUPPORT_SUFFIX[v.support] || '') }));
    });
    versionSel.value = def;
    versionWrap.hidden = false;
  }

  function supportText(v) {
    switch (v.support) {
      case 'rolling': return 'Rolling release (no end date)';
      case 'ended': return 'Ended (' + v.eol_label + ')';
      case 'limited': return 'Limited updates until ' + v.eol_label;
      case 'ending_soon': return 'Ends ' + v.eol_label + ' (in ' + v.support_days + ' days)';
      case 'ok': return 'Until ' + v.eol_label;
      default: return 'Unknown';
    }
  }

  function specText(min, rec, unit) {
    if (min === null || min === undefined) return 'unknown';
    return min + ' ' + unit + ' min' + (rec === null || rec === undefined ? '' : ', ' + rec + ' ' + unit + ' recommended');
  }

  function showInfo() {
    var d = distros[distroSel.value];
    infoBox.textContent = '';
    if (!d) { infoBox.hidden = true; return; }
    infoBox.hidden = false;

    var v = selectedVersion(d);
    var s = v || d;   // a version carries its own merged specs

    var rows = [
      ['Installer', s.installer + ' (' + (METHOD[s.install_method] || '') + ')'],
      ['Architectures', s.archs.join(', ')],
      ['RAM', specText(s.min_ram_gb, s.rec_ram_gb, 'GB')],
      ['Disk', specText(s.min_disk_gb, s.rec_disk_gb, 'GB')],
      ['Secure Boot', SB[s.secure_boot] || 'Unknown']
    ];
    if (v) {
      rows.push(['Released', v.released || 'rolling']);
      rows.push(['Support', supportText(v)]);
    }
    var dl = el('dl');
    rows.forEach(function (r) { dl.appendChild(el('dt', { text: r[0] })); dl.appendChild(el('dd', { text: r[1] })); });
    infoBox.appendChild(dl);

    var badges = el('div', { class: 'badges' });
    if (d.status === 'legacy') badges.appendChild(el('span', { class: 'badge bad', text: 'Discontinued' }));
    if (d.status === 'unknown') badges.appendChild(el('span', { class: 'badge warn', text: 'Little data' }));
    if (v && v.support === 'ended') badges.appendChild(el('span', { class: 'badge bad', text: 'Support ended' }));
    if (v && v.support === 'limited') badges.appendChild(el('span', { class: 'badge warn', text: 'Limited support' }));
    if (v && v.support === 'ending_soon') badges.appendChild(el('span', { class: 'badge warn', text: 'Support ending soon' }));
    var verified = v ? v.verified : d.verified;
    if (v && v.verified && v.checked) badges.appendChild(el('span', { class: 'badge ok', text: 'Checked ' + v.checked }));
    else if (!verified) badges.appendChild(el('span', { class: 'badge warn', text: 'Data not yet verified' }));
    infoBox.appendChild(badges);

    // Preselect the first supported architecture if the current one is unsupported.
    var arch = form.elements.arch;
    if (s.archs.indexOf(arch.value) === -1) arch.value = s.archs[0];
  }

  distroSel.addEventListener('change', function () { fillVersions(); showInfo(); });
  versionSel.addEventListener('change', showInfo);
  existing.addEventListener('change', function () {
    freeWrap.hidden = existing.value === 'none';
  });

  // ---- Submit -------------------------------------------------------------
  function formData() {
    var f = form.elements;
    return {
      distro: f.distro.value,
      version: versionWrap.hidden ? '' : f.version.value,
      boot_mode: f.boot_mode.value,
      arch: f.arch.value,
      ram_gb: f.ram_gb.value,
      disk_gb: f.disk_gb.value,
      disk_type: f.disk_type.value,
      disk_device: f.disk_device.value,
      gpu: f.gpu.value,
      use: f.use.value,
      existing_os: f.existing_os.value,
      free_gb: f.free_gb.value,
      secure_boot: f.secure_boot.checked,
      hibernate: f.hibernate.checked,
      separate_home: f.separate_home.checked
    };
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    errBox.hidden = true;
    goBtn.disabled = true;
    goBtn.textContent = 'Working…';

    fetch('api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(formData())
    })
      .then(function (r) { return r.json().catch(function () { return { ok: false, errors: ['Unexpected server response.'] }; }); })
      .then(function (data) {
        if (!data.ok) { showErrors(data.errors || ['Something went wrong.']); return; }
        render(data);
      })
      .catch(function () { showErrors(['Could not reach the server.']); })
      .then(function () { goBtn.disabled = false; goBtn.textContent = 'Get my install plan'; });
  });

  function showErrors(list) {
    errBox.textContent = list.join(' ');
    errBox.hidden = false;
    errBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  // ---- Rendering ----------------------------------------------------------
  function render(data) {
    resultBox.textContent = '';
    resultBox.hidden = false;

    var d = data.distro;
    var head = el('div', { class: 'card' }, [
      el('div', { class: 'row between' }, [
        el('h2', { text: d.name + ' install plan' }),
        el('button', { type: 'button', class: 'ghost', id: 'print', text: 'Print' })
      ]),
      el('p', { class: 'muted', text: d.family + ' · ' + d.installer })
    ]);
    var links = el('p');
    var site = safeUrl(d.website), docs = safeUrl(d.docs);
    if (site) links.appendChild(el('a', { href: site, target: '_blank', rel: 'noopener noreferrer', text: 'Official website' }));
    if (site && docs) links.appendChild(document.createTextNode(' · '));
    if (docs) links.appendChild(el('a', { href: docs, target: '_blank', rel: 'noopener noreferrer', text: 'Official install guide' }));
    if (site || docs) head.appendChild(links);
    if (d.version) {
      var when = (d.version.released ? 'Released ' + d.version.released + ' · ' : '') +
        (d.version.eol_label ? 'Support until ' + d.version.eol_label : 'Rolling release');
      head.appendChild(el('p', { class: 'muted', text: when }));
      if (d.version.sources && d.version.sources.length) {
        var sp = el('p', { class: 'muted' }, ['Sources: ']);
        var first = true;
        d.version.sources.forEach(function (src) {
          var u = safeUrl(src.url);
          if (!u) return;
          if (!first) sp.appendChild(document.createTextNode(' · '));
          first = false;
          sp.appendChild(el('a', { href: u, target: '_blank', rel: 'noopener noreferrer', text: src.title }));
        });
        head.appendChild(sp);
      }
    }
    resultBox.appendChild(head);

    // Checks
    if (data.checks.length) {
      var c = el('div', { class: 'card' }, [el('h3', { text: 'Checks and warnings' })]);
      data.checks.forEach(function (k) {
        var label = { error: 'Problem', warn: 'Warning', info: 'Note' }[k.level];
        c.appendChild(el('div', { class: 'alert ' + k.level }, [el('b', { text: label + ': ' }), k.msg]));
      });
      resultBox.appendChild(c);
    }

    // Partitions
    if (data.partitions.length) {
      resultBox.appendChild(renderPartitions(data));
    }

    // Steps
    var steps = data.steps;
    var s = el('div', { class: 'card' });
    s.appendChild(el('h3', { text: 'Before you start' }));
    s.appendChild(ol(steps.prepare));

    if (steps.install.length) {
      s.appendChild(el('h3', { text: 'Install' }));
      s.appendChild(ol(steps.install));
    }
    if (data.commands.length) {
      s.appendChild(renderCommands(data.commands));
    }
    if (steps.post.length) {
      s.appendChild(el('h3', { text: 'After the install' }));
      s.appendChild(ol(steps.post));
    }
    resultBox.appendChild(s);

    document.getElementById('print').addEventListener('click', function () { window.print(); });
    resultBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function ol(items) {
    var list = el('ol');
    items.forEach(function (t) { list.appendChild(el('li', { text: t })); });
    return list;
  }

  function renderPartitions(data) {
    var sm = data.summary;
    var card = el('div', { class: 'card' }, [el('h3', { text: 'Partition layout' })]);

    var chips = el('div', { class: 'chips' });
    [
      ['Boot', sm.boot_mode],
      ['Table', sm.table === 'existing' ? 'existing' : sm.table.toUpperCase()],
      ['Space used', fmtGb(sm.space_gb)],
      ['Swap', sm.swap_style === 'partition' ? fmtGb(sm.swap_gb) + ' partition' : sm.swap_style],
      ['Root FS', sm.root_fs + (sm.lvm ? ' (LVM)' : '')]
    ].forEach(function (p) {
      chips.appendChild(el('span', { class: 'chip' }, [el('b', { text: p[0] + ': ' }), p[1]]));
    });
    card.appendChild(chips);

    var created = data.partitions.filter(function (p) { return p.create; });
    var total = created.reduce(function (a, p) { return a + p.size_mib; }, 0) || 1;
    var bar = el('div', { class: 'bar', role: 'img', 'aria-label': 'Proportional partition layout' });
    created.forEach(function (p) {
      var pct = Math.max(2, (p.size_mib / total) * 100);
      var seg = el('div', { class: 'seg k-' + p.key, title: p.label + ' ' + fmtGb(p.size_gb) }, [
        el('span', { text: pct > 9 ? p.label : '' })
      ]);
      seg.style.flexBasis = pct + '%';
      bar.appendChild(seg);
    });
    card.appendChild(bar);

    var table = el('table');
    table.appendChild(el('thead', null, [el('tr', null,
      ['#', 'Partition', 'Mount', 'Filesystem', 'Size', 'Notes'].map(function (h) { return el('th', { text: h }); }))]));
    var tb = el('tbody');
    var n = 0;
    data.partitions.forEach(function (p) {
      if (p.create) n++;
      var size = !p.create ? 'existing' : (p.rest ? fmtGb(p.size_gb) + ' (rest)' : fmtGb(p.size_gb));
      var note = p.note + (p.group ? ' Inside the ' + p.group + '.' : '');
      tb.appendChild(el('tr', { class: p.create ? '' : 'existing' }, [
        el('td', { text: p.create ? String(n) : '–' }),
        el('td', { text: p.label }),
        el('td', { text: p.mount || '–' }),
        el('td', { text: p.fs === 'none' ? '–' : p.fs }),
        el('td', { text: size }),
        el('td', { text: note })
      ]));
    });
    table.appendChild(tb);
    card.appendChild(el('div', { class: 'table-wrap' }, [table]));
    return card;
  }

  function renderCommands(cmds) {
    var wrap = el('details', { class: 'cmds' });
    wrap.appendChild(el('summary', { text: 'Command-line equivalent (advanced)' }));
    wrap.appendChild(el('p', { class: 'muted', text: 'Run from the live USB as root. These commands erase the disk. Check the device name with lsblk first.' }));
    var pre = el('pre', null, [el('code', { text: cmds.join('\n') })]);
    var btn = el('button', { type: 'button', class: 'ghost', text: 'Copy' });
    btn.addEventListener('click', function () {
      var text = cmds.join('\n');
      var done = function () { btn.textContent = 'Copied'; setTimeout(function () { btn.textContent = 'Copy'; }, 1500); };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(done, function () {});
      }
    });
    wrap.appendChild(btn);
    wrap.appendChild(pre);
    return wrap;
  }

  fillVersions();
  showInfo();
})();
