/* LockBilbao — main.js (vanilla, без зависимостей; Leaflet подгружается лениво) */
(function () {
  'use strict';

  /* ---------------- Настройки ---------------- */
  var CONFIG = {
    // TODO: URL обработчика заявок (PHP/CRM/Telegram-бот). Пусто — демо-режим без отправки.
    formEndpoint: '',
    phone: '+34 944 123 456',
    phoneHref: 'tel:+34944123456'
  };

  // Зоны выезда. TODO: когда появятся цены выезда по зонам — добавить в text.
  var ZONES = {
    1: { title: 'Обслуживаем — центр нашего дежурства', text: 'Стоимость выезда и ориентировочное время приезда диспетчер назовёт при звонке.' },
    2: { title: 'Обслуживаем', text: 'Стоимость выезда и время приезда диспетчер назовёт при звонке.' },
    3: { title: 'Обслуживаем', text: 'Стоимость выезда зависит от расстояния — уточните её и время приезда при звонке.' }
  };

  var AREAS = [
    { group: 'Районы Бильбао', items: [
      ['abando', 'Абандо', 'Abando', 43.2620, -2.9360, 1],
      ['deusto', 'Деусто', 'Deusto', 43.2710, -2.9460, 1],
      ['ibaiondo', 'Ибайондо (Каско-Вьехо)', 'Ibaiondo', 43.2545, -2.9215, 1],
      ['begona', 'Бегонья', 'Begoña', 43.2590, -2.9130, 1],
      ['uribarri', 'Урибарри', 'Uribarri', 43.2700, -2.9250, 1],
      ['otxarkoaga', 'Очаркоага-Чурдинага', 'Otxarkoaga-Txurdinaga', 43.2600, -2.8960, 1],
      ['errekalde', 'Эррекальде', 'Errekalde', 43.2500, -2.9460, 1],
      ['basurto', 'Басурто-Сорроса', 'Basurto-Zorrotza', 43.2610, -2.9620, 1]
    ]},
    { group: 'Левый берег', items: [
      ['barakaldo', 'Баракальдо', 'Barakaldo', 43.2975, -2.9890, 2],
      ['sestao', 'Сестао', 'Sestao', 43.3095, -3.0070, 2],
      ['portugalete', 'Португалете', 'Portugalete', 43.3200, -3.0200, 2],
      ['santurtzi', 'Сантурци', 'Santurtzi', 43.3290, -3.0330, 2],
      ['alonsotegi', 'Алонсотеги', 'Alonsotegi', 43.2455, -2.9870, 2]
    ]},
    { group: 'Правый берег', items: [
      ['getxo', 'Гечо', 'Getxo', 43.3560, -3.0110, 2],
      ['leioa', 'Лейоа', 'Leioa', 43.3280, -2.9870, 2],
      ['erandio', 'Эрандио', 'Erandio', 43.3040, -2.9730, 2],
      ['berango', 'Беранго', 'Berango', 43.3650, -2.9960, 3]
    ]},
    { group: 'Горнорудная зона', items: [
      ['trapagaran', 'Трапагаран', 'Valle de Trápaga', 43.3030, -3.0330, 2],
      ['ortuella', 'Ортуэлья', 'Ortuella', 43.3110, -3.0570, 3],
      ['abanto', 'Абанто-Сьервана', 'Abanto-Zierbena', 43.3200, -3.0750, 3],
      ['zierbena', 'Сьервана', 'Zierbena', 43.3520, -3.0810, 3],
      ['muskiz', 'Мускис', 'Muskiz', 43.3210, -3.1130, 3]
    ]},
    { group: 'Долина Асуа', items: [
      ['sondika', 'Сондика', 'Sondika', 43.2990, -2.9250, 2],
      ['derio', 'Дерио', 'Derio', 43.2920, -2.8810, 2],
      ['loiu', 'Лоиу', 'Loiu', 43.3170, -2.9380, 2],
      ['zamudio', 'Самудио', 'Zamudio', 43.2850, -2.8660, 2],
      ['lezama', 'Лесама', 'Lezama', 43.2730, -2.8330, 3],
      ['larrabetzu', 'Ларрабецу', 'Larrabetzu', 43.2610, -2.7960, 3]
    ]},
    { group: 'Верхний Нервион', items: [
      ['basauri', 'Басаури', 'Basauri', 43.2390, -2.8860, 2],
      ['galdakao', 'Гальдакао', 'Galdakao', 43.2300, -2.8430, 2],
      ['etxebarri', 'Эчеварри', 'Etxebarri', 43.2470, -2.8900, 2],
      ['arrigorriaga', 'Арригоррьяга', 'Arrigorriaga', 43.2060, -2.8870, 2],
      ['zaratamo', 'Саратамо', 'Zaratamo', 43.2120, -2.8720, 3]
    ]}
  ];

  var AREA_BY_ID = {};
  AREAS.forEach(function (g) {
    g.items.forEach(function (it) {
      AREA_BY_ID[it[0]] = { id: it[0], ru: it[1], es: it[2], lat: it[3], lng: it[4], zone: it[5], group: g.group };
    });
  });

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var $ = function (sel, ctx) { return (ctx || document).querySelector(sel); };
  var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); };

  /* ---------------- Header ---------------- */
  var header = $('[data-header]');
  var mobileBar = $('[data-mobile-bar]');
  var hero = $('.hero');

  function onScroll() {
    var y = window.scrollY || window.pageYOffset;
    if (header) header.classList.toggle('is-scrolled', y > 10);
    if (mobileBar && hero) {
      mobileBar.classList.toggle('is-visible', y > hero.offsetHeight * 0.55);
    }
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---------------- Mobile menu ---------------- */
  var burger = $('[data-burger]');
  var menu = $('[data-mobile-menu]');

  function setMenu(open) {
    if (!burger || !menu) return;
    burger.setAttribute('aria-expanded', String(open));
    burger.setAttribute('aria-label', open ? 'Закрыть меню' : 'Открыть меню');
    menu.hidden = !open;
    document.documentElement.style.overflow = open ? 'hidden' : '';
    if (open) header.classList.add('is-scrolled'); else onScroll();
  }
  if (burger) {
    burger.addEventListener('click', function () {
      setMenu(burger.getAttribute('aria-expanded') !== 'true');
    });
    $$('a', menu).forEach(function (a) { a.addEventListener('click', function () { setMenu(false); }); });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && burger.getAttribute('aria-expanded') === 'true') { setMenu(false); burger.focus(); }
    });
    window.matchMedia('(min-width: 1025px)').addEventListener('change', function (e) { if (e.matches) setMenu(false); });
  }

  /* ---------------- Hero: параллакс и счётчик ---------------- */
  var stageItems = $$('[data-parallax]');
  if (!reduceMotion && window.matchMedia('(hover: hover) and (pointer: fine)').matches && stageItems.length) {
    var raf = null, mx = 0, my = 0;
    hero.addEventListener('pointermove', function (e) {
      var r = hero.getBoundingClientRect();
      mx = (e.clientX - r.left) / r.width - 0.5;
      my = (e.clientY - r.top) / r.height - 0.5;
      if (!raf) raf = requestAnimationFrame(applyParallax);
    });
    hero.addEventListener('pointerleave', function () { mx = 0; my = 0; if (!raf) raf = requestAnimationFrame(applyParallax); });
  }
  function applyParallax() {
    raf = null;
    stageItems.forEach(function (el) {
      var k = parseFloat(el.getAttribute('data-parallax')) || 0;
      el.style.translate = (mx * k).toFixed(1) + 'px ' + (my * k).toFixed(1) + 'px';
    });
  }

  var counterNum = $('[data-counter-num]');
  var counterLabel = $('[data-counter-label]');
  var counterItems = ['Аварийная служба', 'Открытие автомобилей', 'Вскрытие сейфов', 'Замена замков'];
  if (counterNum && counterLabel && !reduceMotion) {
    var ci = 0;
    setInterval(function () {
      if (document.hidden) return;
      ci = (ci + 1) % counterItems.length;
      counterLabel.classList.add('is-out');
      setTimeout(function () {
        counterNum.textContent = '0' + (ci + 1);
        counterLabel.textContent = counterItems[ci];
        counterLabel.classList.remove('is-out');
      }, 350);
    }, 4200);
  }

  /* ---------------- Слайдер услуг ---------------- */
  $$('[data-slider]').forEach(function (root) {
    var track = $('[data-slider-track]', root);
    var section = root.closest('section') || document;
    var slides = $$('[data-slide]', track);
    var prev = $('[data-slider-prev]', section);
    var next = $('[data-slider-next]', section);
    var curEl = $('[data-slider-current]', section);
    var bar = $('[data-slider-bar]', section);
    if (!track || !slides.length) return;

    var isSlider = function () { return getComputedStyle(track).display === 'flex'; };
    var step = function () {
      var gap = parseFloat(getComputedStyle(track).columnGap) || 0;
      return slides[0].getBoundingClientRect().width + gap;
    };
    var maxScroll = function () { return track.scrollWidth - track.clientWidth; };
    var current = 0;

    function update() {
      if (!isSlider()) return;
      var max = maxScroll();
      var idx = track.scrollLeft >= max - 4 ? slides.length - 1 : Math.round(track.scrollLeft / step());
      idx = Math.max(0, Math.min(slides.length - 1, idx));
      if (idx !== current) {
        slides[current].classList.remove('is-current');
        slides[idx].classList.add('is-current');
        current = idx;
      }
      if (curEl) curEl.textContent = (idx < 9 ? '0' : '') + (idx + 1);
      if (bar) bar.style.width = ((idx + 1) / slides.length * 100).toFixed(2) + '%';
      if (prev) prev.disabled = track.scrollLeft <= 4;
      if (next) next.disabled = track.scrollLeft >= max - 4;
    }

    var ticking = false;
    track.addEventListener('scroll', function () {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(function () { ticking = false; update(); });
    }, { passive: true });
    window.addEventListener('resize', update);

    function go(dir) {
      track.scrollBy({ left: dir * step(), behavior: reduceMotion ? 'auto' : 'smooth' });
    }
    if (prev) prev.addEventListener('click', function () { go(-1); });
    if (next) next.addEventListener('click', function () { go(1); });

    track.addEventListener('keydown', function (e) {
      if (e.target !== track) return;
      if (e.key === 'ArrowRight') { e.preventDefault(); go(1); }
      if (e.key === 'ArrowLeft') { e.preventDefault(); go(-1); }
    });

    // Перетаскивание мышью (тач и трекпад листают нативно)
    var drag = null;
    track.addEventListener('pointerdown', function (e) {
      if (e.pointerType !== 'mouse' || e.button !== 0 || !isSlider()) return;
      e.preventDefault(); // не начинать выделение текста при перетаскивании
      drag = { x: e.clientX, left: track.scrollLeft, moved: false };
    });
    track.addEventListener('dragstart', function (e) { e.preventDefault(); });
    window.addEventListener('pointermove', function (e) {
      if (!drag) return;
      var dx = e.clientX - drag.x;
      if (!drag.moved && Math.abs(dx) > 5) { drag.moved = true; track.classList.add('is-dragging'); }
      if (drag.moved) track.scrollLeft = drag.left - dx;
    });
    window.addEventListener('pointerup', function () {
      if (!drag) return;
      var moved = drag.moved;
      drag = null;
      if (!moved) return;
      track.classList.remove('is-dragging');
      var target = Math.round(track.scrollLeft / step()) * step();
      track.scrollTo({ left: Math.min(target, maxScroll()), behavior: reduceMotion ? 'auto' : 'smooth' });
      // гасим клик по ссылке, который браузер отправит сразу после перетаскивания
      var suppress = function (ev) { ev.preventDefault(); ev.stopPropagation(); };
      track.addEventListener('click', suppress, true);
      setTimeout(function () { track.removeEventListener('click', suppress, true); }, 0);
    });

    // Фокус с клавиатуры на карточке — прокручиваем только трек (не scrollIntoView, он двигает и предков)
    track.addEventListener('focusin', function (e) {
      var li = e.target.closest('[data-slide]');
      if (!li || !isSlider() || !e.target.matches(':focus-visible')) return;
      var left = li.offsetLeft - slides[0].offsetLeft;
      if (left < track.scrollLeft || left + li.offsetWidth > track.scrollLeft + track.clientWidth) {
        track.scrollTo({ left: Math.min(left, maxScroll()), behavior: reduceMotion ? 'auto' : 'smooth' });
      }
    });

    update();
  });

  /* ---------------- Tabs (типы дверей / замков) ---------------- */
  $$('[data-tablist]').forEach(function (list) {
    var tabs = $$('[role="tab"]', list);
    function select(tab, focus) {
      tabs.forEach(function (t) {
        var on = t === tab;
        t.setAttribute('aria-selected', String(on));
        t.tabIndex = on ? 0 : -1;
        var panel = document.getElementById(t.getAttribute('aria-controls'));
        if (panel) panel.hidden = !on;
      });
      if (focus) tab.focus();
    }
    tabs.forEach(function (tab, i) {
      tab.addEventListener('click', function () { select(tab); });
      tab.addEventListener('keydown', function (e) {
        var next = null;
        if (e.key === 'ArrowRight') next = tabs[(i + 1) % tabs.length];
        if (e.key === 'ArrowLeft') next = tabs[(i - 1 + tabs.length) % tabs.length];
        if (e.key === 'Home') next = tabs[0];
        if (e.key === 'End') next = tabs[tabs.length - 1];
        if (next) { e.preventDefault(); select(next, true); }
      });
    });
  });

  /* ---------------- Фильтр отзывов ---------------- */
  var filter = $('[data-review-filter]');
  if (filter) {
    var reviews = $$('[data-reviews] [data-cat]');
    $$('[data-filter]', filter).forEach(function (btn) {
      btn.addEventListener('click', function () {
        var f = btn.getAttribute('data-filter');
        $$('[data-filter]', filter).forEach(function (b) {
          var on = b === btn;
          b.classList.toggle('is-active', on);
          b.setAttribute('aria-pressed', String(on));
        });
        reviews.forEach(function (r) { r.classList.toggle('is-hidden', f !== 'all' && r.getAttribute('data-cat') !== f); });
      });
    });
  }

  /* ---------------- FAQ: одна открытая панель ---------------- */
  var acc = $('[data-accordion]');
  if (acc) {
    $$('details', acc).forEach(function (d) {
      d.addEventListener('toggle', function () {
        if (!d.open) return;
        $$('details', acc).forEach(function (o) { if (o !== d) o.open = false; });
      });
    });
  }

  /* ---------------- Селекты районов ---------------- */
  $$('[data-area-select]').forEach(function (sel) {
    AREAS.forEach(function (g) {
      var og = document.createElement('optgroup');
      og.label = g.group;
      g.items.forEach(function (it) {
        var o = document.createElement('option');
        o.value = it[0];
        o.textContent = it[1];
        og.appendChild(o);
      });
      sel.appendChild(og);
    });
  });

  /* ---------------- Проверка района + карта ---------------- */
  var checker = $('[data-area-checker]');
  var checkerResult = $('[data-checker-result]');
  var mapApi = null;

  function renderCheck(id) {
    var a = AREA_BY_ID[id];
    if (!a) {
      checkerResult.innerHTML = '<p>Выберите район, чтобы узнать, выезжаем ли мы к вам и как уточнить время приезда.</p>';
      return;
    }
    var z = ZONES[a.zone];
    checkerResult.innerHTML =
      '<div class="ok"><svg aria-hidden="true"><use href="#i-check"/></svg><div>' +
      '<b>' + a.ru + ' — ' + z.title.toLowerCase() + '</b>' +
      '<p>' + z.text + '</p>' +
      '</div></div>' +
      '<a class="btn btn--lime btn--block checker__call" href="' + CONFIG.phoneHref + '">' +
      '<svg aria-hidden="true"><use href="#i-phone"/></svg>Уточнить время приезда</a>';
    if (mapApi) mapApi.focus(id);
  }
  if (checker) checker.addEventListener('change', function () { renderCheck(checker.value); });

  var mapEl = $('[data-map]');
  if (mapEl) {
    var loadMap = function () {
      loadCss('vendor/leaflet/leaflet.css');
      loadScript('vendor/leaflet/leaflet.js', initMap);
    };
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        if (entries[0].isIntersecting) { io.disconnect(); loadMap(); }
      }, { rootMargin: '400px' });
      io.observe(mapEl);
    } else {
      loadMap();
    }
  }

  function initMap() {
    if (!window.L) return;
    var L = window.L;
    mapEl.innerHTML = '';
    var map = L.map(mapEl, {
      center: [43.285, -2.965],
      zoom: 11,
      scrollWheelZoom: false,
      zoomControl: true,
      attributionControl: true
    });
    // TODO: для коммерческого трафика можно подключить тайлы MapTiler / Stadia по ключу
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    L.circle([43.2600, -2.9300], {
      radius: 4200, color: '#176B45', weight: 1.5, fillColor: '#C8F54A', fillOpacity: .18
    }).addTo(map);

    var markers = {};
    Object.keys(AREA_BY_ID).forEach(function (id) {
      var a = AREA_BY_ID[id];
      if (a.zone === 1) return; // районы города покрыты кругом Бильбао
      var m = L.marker([a.lat, a.lng], {
        icon: L.divIcon({ className: '', html: '<span class="map-pin"></span>', iconSize: [16, 16], iconAnchor: [8, 8] }),
        title: a.ru
      }).addTo(map);
      m.bindTooltip(a.ru, { className: 'map-label', direction: 'top', offset: [0, -10] });
      m.on('click', function () { if (checker) { checker.value = id; renderCheck(id); } });
      markers[id] = m;
    });

    var main = L.marker([43.2630, -2.9350], {
      icon: L.divIcon({ className: '', html: '<span class="map-pin map-pin--main"></span>', iconSize: [26, 26], iconAnchor: [13, 13] }),
      title: 'Бильбао'
    }).addTo(map);
    main.bindTooltip('Бильбао', { className: 'map-label', direction: 'top', offset: [0, -14], permanent: true });

    var active = null;
    mapApi = {
      focus: function (id) {
        var a = AREA_BY_ID[id];
        if (!a) return;
        map.flyTo([a.lat, a.lng], a.zone === 1 ? 14 : 13, { duration: reduceMotion ? 0 : 0.8 });
        if (active) { active.getElement() && active.getElement().firstChild.classList.remove('is-active'); active.closeTooltip(); }
        active = markers[id] || null;
        if (active) { active.getElement().firstChild.classList.add('is-active'); active.openTooltip(); }
      }
    };
    if (checker && checker.value) mapApi.focus(checker.value);
  }

  function loadCss(href) {
    var l = document.createElement('link');
    l.rel = 'stylesheet'; l.href = href;
    document.head.appendChild(l);
  }
  function loadScript(src, cb) {
    var s = document.createElement('script');
    s.src = src; s.async = true; s.onload = cb;
    document.head.appendChild(s);
  }

  /* ---------------- Модалка ---------------- */
  $$('[data-modal-open]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var dlg = document.getElementById(btn.getAttribute('data-modal-open'));
      if (!dlg) return;
      if (typeof dlg.showModal === 'function') {
        dlg.showModal();
        var first = $('input[type="tel"]', dlg);
        if (first) first.focus();
      } else {
        // браузеры без <dialog> — ведём к основной форме
        location.hash = '#request';
      }
    });
  });
  $$('dialog').forEach(function (dlg) {
    $$('[data-modal-close]', dlg).forEach(function (b) { b.addEventListener('click', function () { dlg.close(); }); });
    dlg.addEventListener('click', function (e) { if (e.target === dlg) dlg.close(); });
  });

  /* ---------------- Формы ---------------- */
  var DIAL_CODES = ['+380', '+351', '+34', '+33', '+44', '+49', '+39', '+48', '+40'];

  $$('[data-lead-form]').forEach(function (form) {
    var phone = $('input[type="tel"]', form);
    var dial = $('[data-dial]', form);
    var errEl = phone ? document.getElementById(phone.getAttribute('aria-describedby')) : null;
    var fileInput = $('[data-file]', form);
    var status = $('[data-form-status]', form);
    var consent = $('input[name="consent"]', form);

    // Автовыбор кода страны, если номер вставили с «+»
    if (phone && dial) {
      phone.addEventListener('input', function () {
        var v = phone.value.replace(/[^\d+]/g, '');
        if (v.charAt(0) === '+') {
          var matched = false;
          for (var i = 0; i < DIAL_CODES.length; i++) {
            if (v.indexOf(DIAL_CODES[i]) === 0) {
              dial.value = DIAL_CODES[i];
              v = v.slice(DIAL_CODES[i].length);
              matched = true;
              break;
            }
          }
          if (!matched) return; // код ещё вводится — не трогаем поле
          phone.value = v;
        } else if (v.indexOf('0034') === 0) {
          dial.value = '+34';
          phone.value = v = v.slice(4);
        }
        if (dial.value === '+34') phone.value = formatEs(v);
        if (phone.getAttribute('aria-invalid') === 'true') validatePhone();
      });
      phone.addEventListener('blur', function () { if (phone.value) validatePhone(); });
    }

    function formatEs(v) {
      var d = v.replace(/\D/g, '').slice(0, 9);
      return d.replace(/^(\d{3})(\d{0,3})(\d{0,3}).*/, function (_, a, b, c) {
        return [a, b, c].filter(Boolean).join(' ');
      });
    }

    function validatePhone() {
      var digits = phone.value.replace(/\D/g, '');
      var msg = '';
      if (!digits) msg = 'Укажите номер телефона, чтобы мастер мог перезвонить.';
      else if (phone.value.trim().charAt(0) === '+') msg = 'Выберите код страны в списке и введите номер без него.';
      else if (dial && dial.value === '+34' && !/^[6789]\d{8}$/.test(digits)) msg = 'Испанский номер — 9 цифр, начинается с 6, 7, 8 или 9.';
      else if (digits.length < 6 || digits.length > 14) msg = 'Проверьте номер: в нём должно быть от 6 до 14 цифр.';
      phone.setAttribute('aria-invalid', msg ? 'true' : 'false');
      if (errEl) { errEl.textContent = msg; errEl.hidden = !msg; }
      return !msg;
    }

    if (fileInput) {
      var label = form.querySelector('label[for="' + fileInput.id + '"]');
      var nameEl = label ? $('[data-file-name]', label) : null;
      var defaultName = nameEl ? nameEl.textContent : '';
      fileInput.addEventListener('change', function () {
        var f = fileInput.files && fileInput.files[0];
        if (f && f.size > 10 * 1024 * 1024) {
          fileInput.value = '';
          f = null;
          setStatus('Фото больше 10 МБ — выберите снимок поменьше.', 'error');
        }
        if (label) label.classList.toggle('has-file', !!f);
        if (nameEl) nameEl.textContent = f ? f.name : defaultName;
      });
    }

    function setStatus(text, type) {
      if (!status) return;
      status.textContent = text;
      status.className = 'form-status' + (type ? ' is-' + type : '');
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      setStatus('');
      var ok = phone ? validatePhone() : true;
      if (consent) {
        var cl = consent.closest('.consent');
        if (cl) cl.classList.toggle('is-invalid', !consent.checked);
        if (!consent.checked && ok) { setStatus('Отметьте согласие с политикой конфиденциальности.', 'error'); ok = false; }
      }
      if (!ok) { if (phone && phone.getAttribute('aria-invalid') === 'true') phone.focus(); return; }

      var data = new FormData(form);
      data.set('phone_full', (dial ? dial.value : '') + ' ' + phone.value);
      data.set('page', location.href);
      // UTM-метки из URL — для учёта рекламы
      new URLSearchParams(location.search).forEach(function (v, k) { if (/^utm_/.test(k)) data.set(k, v); });

      form.classList.add('is-loading');
      var done = function () {
        form.classList.remove('is-loading');
        form.reset();
        $$('.file', form).forEach(function (l) { l.classList.remove('has-file'); });
        $$('[data-file-name]', form).forEach(function (n) { n.textContent = 'Фото замка'; });
        setStatus('Заявка отправлена. Диспетчер перезвонит вам в ближайшие минуты.', 'success');
      };
      var fail = function () {
        form.classList.remove('is-loading');
        setStatus('Не удалось отправить заявку. Позвоните нам: ' + CONFIG.phone, 'error');
      };

      if (!CONFIG.formEndpoint) { setTimeout(done, 600); return; }
      fetch(CONFIG.formEndpoint, { method: 'POST', body: data })
        .then(function (r) { if (!r.ok) throw new Error(r.status); done(); })
        .catch(fail);
    });
  });

  /* ---------------- Год в футере ---------------- */
  var y = $('[data-year]');
  if (y) y.textContent = new Date().getFullYear();
})();
