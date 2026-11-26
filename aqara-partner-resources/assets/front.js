/* Compare selection + enquiry list, saved in localStorage. No pricing, no accounts. */
(function () {
  var MAX = (window.APR && APR.max) || 4;
  function read(k) {
    try {
      var parsed = JSON.parse(localStorage.getItem(k) || '[]');
      return Array.isArray(parsed) ? parsed : [];
    } catch (e) { return []; }
  }
  function write(k, v) {
    try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) {}
    document.dispatchEvent(new CustomEvent('apr:change'));
  }

  // ---- Compare ----
  function compare() { return read('apr-compare').filter(function (x) { return typeof x === 'string' && x; }).slice(0, MAX); }
  function renderCompare() {
    var items = compare();
    document.querySelectorAll('[data-apr-compare]').forEach(function (b) {
      var on = items.indexOf(b.dataset.aprCompare) > -1;
      b.setAttribute('aria-pressed', String(on));
      b.classList.toggle('is-on', on);
      b.textContent = on ? 'In comparison' : 'Compare';
      b.disabled = !on && items.length >= MAX;
      b.title = b.disabled ? 'Compare up to ' + MAX + ' products' : '';
    });
    var tray = document.querySelector('[data-apr-tray]');
    if (tray) {
      var enq = enquiry();
      tray.hidden = !items.length && !enq.length;
      var count = tray.querySelector('[data-apr-tray-count]');
      if (count) count.textContent = (items.length ? items.length + ' of ' + MAX + ' to compare' : '') + (items.length && enq.length ? ' · ' : '') + (enq.length ? enq.length + ' in enquiry list' : '');
      var link = tray.querySelector('[data-apr-tray-link]');
      if (link) {
        link.hidden = !items.length;
        var base = (window.APR && APR.compareUrl) || '/compare/';
        link.href = base + (base.indexOf('?') > -1 ? '&' : '?') + 'items=' + items.map(encodeURIComponent).join(',');
      }
      var send = tray.querySelector('[data-apr-tray-enquiry]');
      if (send) send.hidden = !enq.length;
    }
  }

  // ---- Enquiry list ----
  function enquiry() { return read('apr-enquiry').filter(function (x) { return x && typeof x.slug === 'string' && x.slug; }); }
  function renderEnquiry() {
    var items = enquiry();
    document.querySelectorAll('button[data-apr-enquiry]').forEach(function (b) {
      var on = items.some(function (i) { return i.slug === b.dataset.aprEnquiry; });
      b.setAttribute('aria-pressed', String(on));
      b.classList.toggle('is-on', on);
      b.textContent = on ? 'On enquiry list' : 'Add to enquiry';
    });
    document.querySelectorAll('div[data-apr-enquiry]').forEach(function (box) {
      var ul = box.querySelector('[data-apr-enquiry-items]'), empty = box.querySelector('[data-apr-enquiry-empty]');
      if (ul) {
        ul.textContent = '';
        items.forEach(function (item) {
          var li = document.createElement('li');
          var span = document.createElement('span');
          span.textContent = String(item.name || item.slug);
          var btn = document.createElement('button');
          btn.type = 'button';
          btn.setAttribute('data-remove', item.slug);
          btn.setAttribute('aria-label', 'Remove ' + String(item.name || item.slug));
          btn.textContent = '×';
          li.appendChild(span);
          li.appendChild(btn);
          ul.appendChild(li);
        });
      }
      if (empty) empty.hidden = !!items.length;
      var send = box.querySelector('[data-apr-enquiry-send]'); if (send) send.hidden = !items.length;
      var clear = box.querySelector('[data-apr-enquiry-clear]'); if (clear) clear.hidden = !items.length;
    });
  }

  function renderAll() { renderCompare(); renderEnquiry(); }

  document.addEventListener('click', function (e) {
    var t = e.target.closest('[data-apr-compare],[data-apr-enquiry],[data-apr-tray-clear],[data-apr-enquiry-clear],[data-remove]');
    if (!t) return;
    if (t.dataset.aprCompare !== undefined && t.tagName === 'BUTTON') {
      var items = compare(), s = t.dataset.aprCompare, i = items.indexOf(s);
      if (i > -1) items.splice(i, 1); else if (items.length < MAX) items.push(s);
      write('apr-compare', items);
    } else if (t.dataset.aprEnquiry !== undefined && t.tagName === 'BUTTON') {
      var list = enquiry(), slug = t.dataset.aprEnquiry, idx = list.findIndex(function (x) { return x.slug === slug; });
      if (idx > -1) list.splice(idx, 1); else list.push({ slug: slug, name: t.dataset.name || slug });
      write('apr-enquiry', list);
    } else if (t.dataset.remove !== undefined) {
      write('apr-enquiry', enquiry().filter(function (x) { return x.slug !== t.dataset.remove; }));
    } else if (t.hasAttribute('data-apr-tray-clear')) {
      write('apr-compare', []); write('apr-enquiry', []);
    } else if (t.hasAttribute('data-apr-enquiry-clear')) {
      write('apr-enquiry', []);
    }
  });

  document.addEventListener('apr:change', renderAll);
  window.addEventListener('storage', renderAll);
  if (document.readyState !== 'loading') renderAll(); else document.addEventListener('DOMContentLoaded', renderAll);
})();
