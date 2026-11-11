"""Извлекает RU-контент статики в формат импорта темы (ru.json)."""
import json, re, sys, os
from bs4 import BeautifulSoup, NavigableString

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__)))) + '/'
OUT = sys.argv[1] if len(sys.argv) > 1 else 'ru.json'

SVC = {
    'apertura-de-puertas': 'svc-puertas', 'apertura-de-coches': 'svc-coches', 'apertura-de-cajas-fuertes': 'svc-cajas',
    'apertura-de-garajes': 'svc-garajes', 'apertura-de-locales': 'svc-locales', 'reparacion-de-cerraduras': 'svc-reparacion',
    'cambio-de-cerraduras-y-bombines': 'svc-cambio', 'instalacion-de-cerraduras': 'svc-instalacion',
    'extraccion-de-llaves-rotas': 'svc-extraccion',
}
PATHS = {'': 'home', 'servicios/': 'services', 'contacto/': 'contacts', 'politica-de-privacidad/': 'privacy',
         'politica-de-cookies/': 'cookies', 'condiciones-del-servicio/': 'terms', 'aviso-legal/': 'legal'}
for slug, key in SVC.items():
    PATHS['servicios/' + slug + '/'] = key

media = {}


def norm_path(base, href):
    """Относительная ссылка → путь от корня сайта."""
    parts = base.strip('/').split('/') if base.strip('/') else []
    for seg in href.split('/'):
        if seg == '..':
            parts = parts[:-1]
        elif seg in ('.', ''):
            continue
        else:
            parts.append(seg)
    return '/'.join(parts) + ('/' if parts else '')


def link_token(base, href):
    if re.match(r'^(https?:|tel:|mailto:|#)', href):
        return href
    path, _, frag = href.partition('#')
    p = norm_path(base, path) if path else norm_path(base, '.')
    key = PATHS.get(p)
    if key is None:
        print('!! unknown link', base, href)
        return href
    tok = '{home}' if key == 'home' else '{url:%s}' % key
    return tok + ('#' + frag if frag else '')


def html(el, base=''):
    """Внутренний HTML элемента (ссылки → токены, &nbsp; сохраняем)."""
    if el is None:
        return ''
    el = BeautifulSoup(str(el), 'html.parser').find()
    for a in el.find_all('a'):
        if a.get('href'):
            a['href'] = link_token(base, a['href'])
    s = el.decode_contents(formatter='minimal')
    return s.replace('\xa0', '&nbsp;').strip()


def text(el):
    if el is None:
        return ''
    return el.get_text().replace('\xa0', '&nbsp;').strip()


def own_text(el):
    """Текст без вложенных svg/span-иконок (для узлов вида <li><svg/>Текст</li>)."""
    if el is None:
        return ''
    parts = []
    for c in el.children:
        if isinstance(c, NavigableString):
            parts.append(str(c))
        elif c.name not in ('svg',) and c.get('aria-hidden') != 'true':
            parts.append(c.decode(formatter='minimal'))
    return ''.join(parts).replace('\xa0', '&nbsp;').strip()


def icon(el):
    if el is None:
        return ''
    u = el.find('use') if el.name != 'use' else el
    return u['href'].lstrip('#') if u else ''


def img(el):
    if el is None:
        return ''
    if el.name != 'img':
        el = el.find('img')
    src = el['src']
    f = re.sub(r'^(\.\./)*img/', '', src)
    if el.get('alt') and f not in media:
        media[f] = el['alt']
    return 'img:' + f


def sel(root, css):
    return root.select_one(css)


# ---------------- общие блоки ----------------

def prices(s, base):
    sec = sel(s, '#prices')
    rows = []
    for tr in sec.select('tbody tr'):
        th, td = tr.th, tr.td
        price = ''
        if 'is-tbd' not in (td.get('class') or []):
            b = td.find('b')
            price = '…' if 'is-tbd-price' in (b.get('class') or []) else text(b).replace('&nbsp;€', '').replace(' €', '')
        rows.append({'icon': icon(th), 'label': own_text(th), 'price': price})
    extras = []
    for li in sec.select('.pr-extras li'):
        a = li.a
        spans = a.find_all('span', recursive=False)
        t = spans[1].decode_contents(formatter='minimal').replace('<br/>', '\n').replace('<br>', '\n')
        extras.append({'icon': icon(spans[0]), 'text': t.replace('\xa0', '&nbsp;'), 'link': link_token(base, a['href'])})
    gug = sel(sec, '.pr__gug img')
    f = {
        'pr_eyebrow': text(sel(sec, '.pr__eyebrow')), 'pr_title': html(sel(sec, '.pr__title')), 'pr_lead': html(sel(sec, '.pr__lead')),
        'pr_photo': img(sel(sec, '.pr__photo')), 'pr_photo_small': img(gug) if gug else '',
        'pr_caption': text(sel(sec, 'caption')),
        'pr_col_1': text(sec.select('thead th')[0]), 'pr_col_2': text(sec.select('thead th')[1]),
        'pr_rows': rows, 'pr_extras': extras,
        'pr_notes': [{'text': own_text(li)} for li in sec.select('.pr-notes li')],
        'pr_cta_title': text(sel(sec, '.pr-cta__text b')), 'pr_cta_text': text(sel(sec, '.pr-cta__text > span')),
    }
    return f


def why(s):
    sec = sel(s, '#why')
    ph = sel(sec, '.why-photo')
    return {
        'why_kicker': own_text(sel(sec, '.kicker')), 'why_title': html(sel(sec, '.why__title')), 'why_lead': html(sel(sec, '.why__lead')),
        'why_cards': [{'icon': icon(sel(c, '.why-card__ic')), 'title': html(sel(c, '.why-card__title')), 'text': html(sel(c, '.why-card__text'))} for c in sec.select('.why-card')],
        'why_photo': img(ph), 'why_photo_title': html(sel(ph, '.why-card__title')), 'why_photo_text': html(sel(ph, '.why-card__text')),
    }


def flow(s):
    sec = sel(s, '#process')
    cta = sel(sec, '.flow-cta')
    return {
        'flow_kicker': own_text(sel(sec, '.kicker')), 'flow_title': html(sel(sec, '.flow__title')),
        'flow_steps': [{'icon': icon(sel(li, '.flow-step__ic')), 'title': html(sel(li, '.flow-step__title')), 'text': html(sel(li, '.flow-step__text'))} for li in sec.select('.flow-step')],
        'flow_cta_title': html(sel(cta, '.flow-cta__title')), 'flow_cta_sub': html(sel(cta, '.flow-cta__sub')), 'flow_cta_btn': own_text(sel(cta, '.btn')),
    }


def reviews(s):
    sec = sel(s, '#reviews')
    items = []
    for li in sec.select('.review'):
        demo = 'review--demo' in li['class']
        items.append({'name': text(sel(li, '.review__name')), 'date': '' if demo else text(sel(li, '.review__date')),
                      'tag': text(sel(li, '.review__tag')), 'text': text(sel(li, '.review__text')), 'demo': 1 if demo else 0})
    cta = sel(sec, '.rv__cta .btn')
    return {
        'rv_kicker': own_text(sel(sec, '.kicker')), 'rv_title': html(sel(sec, 'h2')),
        'rv_links': [{'text': own_text(a), 'url': ''} for a in sec.select('.rv__links a')],
        'rv_aria': sel(sec, '.rv-slider')['aria-label'], 'rv_items': items,
        'rv_cta_text': own_text(cta), 'rv_cta_type': 'maps' if 'btn--ghost' in cta['class'] else 'call',
    }


def geo(s):
    sec = sel(s, '#areas')
    f = {'geo_kicker': own_text(sel(sec, '.kicker')), 'geo_title': html(sel(sec, 'h2')), 'geo_lead': html(sel(sec, '.sec-head__lead'))}
    for n, card in enumerate(sec.select('.area-card'), 1):
        f['geo_card_%d_title' % n] = html(sel(card, '.area-card__title'))
        f['geo_card_%d_count' % n] = text(sel(card, '.area-card__count'))
        f['geo_card_%d_areas' % n] = [{'area': p['data-area'], 'label': text(p)} for p in card.select('.pill')]
        f['geo_card_%d_note' % n] = html(sel(card, '.area-card__note'))
    return f


def longread(s, base):
    sec = sel(s, '#guide')
    body = BeautifulSoup(str(sel(sec, '.longread__body')), 'html.parser').find()
    lead = body.find(class_='longread__lead')
    lead_html = html(lead, base)
    lead.decompose()
    for h in body.find_all('h3'):
        del h['id']
    return {'lr_kicker': own_text(sel(sec, '.kicker')), 'lr_title': html(sel(sec, 'h2')), 'lr_lead': lead_html,
            'lr_body': html(body, base)}


def faq(s, base):
    sec = sel(s, '#faq')
    items = []
    for d in sec.select('details.qa'):
        items.append({'q': text(d.summary.h3), 'a': html(sel(d, '.qa__body'), base)})
    help_ = sel(sec, '.faq__help')
    return {'faq_kicker': own_text(sel(sec, '.kicker')), 'faq_title': html(sel(sec, 'h2')),
            'faq_help_title': html(sel(help_, '.faq__help-title')), 'faq_help_text': html(help_.find('p', class_=False)),
            'faq_items': items}


def final(s):
    sec = sel(s, '#request')
    srv = sec.select_one('input[name="service"]')
    f = {'final_kicker': own_text(sel(sec, '.kicker')), 'final_title': html(sel(sec, 'h2')), 'final_lead': html(sel(sec, '.final__lead')),
         'final_hours': own_text(sel(sec, '.final__hours')),
         'final_guarantees': [{'text': own_text(li)} for li in sec.select('.final__guarantees li')],
         'final_form_title': html(sel(sec, '.form-card__title'))}
    if srv:
        f['form_service'] = srv['value']
    return f


def modal(s):
    dlg = sel(s, 'dialog')
    return {'modal_title': html(sel(dlg, '.modal__title')), 'modal_text': html(sel(dlg, '.modal__text')),
            'modal_file': text(sel(dlg, '[data-file-name]')),
            'modal_options': '\n'.join(text(o) for o in dlg.select('select.select option'))}


def seo(s, service=False):
    head = s.head
    f = {'seo_title': text(head.title), 'seo_desc': head.find('meta', attrs={'name': 'description'})['content'],
         'og_desc': head.find('meta', attrs={'property': 'og:description'})['content']}
    if service:
        for sc in head.find_all('script', type='application/ld+json'):
            d = json.loads(sc.string)
            for g in d.get('@graph', []):
                if g.get('@type') == 'Service':
                    f['schema_name'], f['schema_type'], f['schema_desc'] = g['name'], g['serviceType'], g['description']
    return f


def crumbs_last(s):
    c = sel(s, '.crumbs [aria-current]')
    return text(c) if c else ''


def load(path):
    return BeautifulSoup(open(ROOT + path, encoding='utf-8').read(), 'html.parser')


def menus(s, base):
    def items(ul):
        out = []
        for a in ul.find_all('a'):
            href = a['href']
            if href.startswith('#') or '#' in href and not href.split('#')[0].strip('./'):
                link = '#' + href.split('#')[1]
            else:
                tok = link_token(base, href)
                m = re.match(r'\{url:([a-z0-9-]+)\}$', tok)
                link = 'page:' + m.group(1) if m else tok
            out.append({'title': text(a), 'link': link})
        return out
    return {'menu-header': items(sel(s, '.nav__list')), 'menu-mobile': items(sel(s, '.mobile-menu__list')),
            'menu-footer': items(sel(s, '.footer__bottom ul'))}


pages = {}

# ---------------- главная ----------------
s = load('index.html')
base = ''
hero = sel(s, '.hero')
bubbles = hero.select('.stage__bubble img')
f = {
    'hero_eyebrow': text(sel(hero, '.eyebrow')), 'hero_title': html(sel(hero, '.hero__title')),
    'hero_hours': own_text(sel(hero, '.hero__hours')).split('</span>')[-1].strip(),
    'hero_photo': img(sel(hero, '.stage__master')), 'hero_bubble_1': img(bubbles[0]), 'hero_bubble_2': img(bubbles[1]),
    'hero_chip': own_text(sel(hero, '.stage__chip')),
    'hero_script': re.sub(r'<svg.*', '', sel(hero, '.stage__script').decode_contents(formatter='minimal'), flags=re.S).replace('<br/>', '\n').strip(),
    'svc_kicker': own_text(sel(s, '#services .kicker')), 'svc_title': html(sel(s, '#services h2')), 'svc_lead': html(sel(s, '.svc__lead')),
}
f.update(prices(s, base)); f.update(why(s)); f.update(flow(s))
kinds = sel(s, '#types')
cards = lambda panel: [{'icon': icon(sel(li, '.type-card__ic')), 'title': html(sel(li, '.type-card__title')), 'text': html(sel(li, '.type-card__text'))} for li in panel.select('.type-card')]
f.update({
    'kinds_kicker': own_text(sel(kinds, '.kicker')), 'kinds_title': html(sel(kinds, 'h2')),
    'kinds_tab_1': text(sel(kinds, '#tab-doors')), 'kinds_tab_2': text(sel(kinds, '#tab-locks')),
    'kinds_doors': cards(sel(kinds, '#panel-doors')), 'kinds_locks': cards(sel(kinds, '#panel-locks')),
    'brands_label': text(sel(kinds, '.brands__label')), 'brands_list': '\n'.join(text(li) for li in kinds.select('.brands__list li')),
})
saf = sel(s, '#safety')
f.update({
    'safety_photo': img(sel(saf, '.safety__photo')), 'safety_badge': own_text(sel(saf, '.safety__badge')),
    'safety_kicker': own_text(sel(saf, '.kicker')), 'safety_title': html(sel(saf, 'h2')), 'safety_quote': html(sel(saf, '.safety__quote')),
    'safety_items': [{'title': html(li.h3), 'text': html(li.p)} for li in saf.select('.safety__list li')],
})
f.update(reviews(s))
ab = sel(s, '#about')
f.update({
    'about_kicker': own_text(sel(ab, '.kicker')), 'about_title': html(sel(ab, 'h2')), 'about_lead': html(sel(ab, '.lead')),
    'about_text': html(sel(ab, '.about__text')),
    'about_facts': [{'icon': icon(sel(li, '.fact__ic')), 'num': html(sel(li, '.fact__num')), 'text': html(sel(li, '.fact__text'))} for li in ab.select('.fact')],
    'about_photo': img(sel(ab, '.about__photo')), 'about_chip': own_text(sel(ab, '.about__chip')),
})
f.update(geo(s)); f.update(longread(s, base)); f.update(faq(s, base)); f.update(final(s)); f.update(modal(s)); f.update(seo(s))
pages['home'] = {'title': 'Главная', 'slug': 'inicio', 'fields': f}
MENUS = menus(s, base)

# ---------------- каталог ----------------
s = load('servicios/index.html')
base = 'servicios/'
hero = sel(s, '.sh')
f = {
    'crumb': crumbs_last(s), 'sh_title': html(sel(hero, '.sh__title')), 'sh_lead': html(sel(hero, '.sh__lead')),
    'cat_perks': [{'text': own_text(li)} for li in hero.select('.sh__perks li')],
    'cat_call_label': own_text(sel(hero, '.cat-call__label')), 'cat_call_text': html(sel(hero, '.cat-call__text')),
}
for n, gid in ((1, 'opening'), (2, 'repair')):
    g = sel(s, '#' + gid)
    f['grp_%d_kicker' % n] = own_text(sel(g, '.kicker'))
    f['grp_%d_title' % n] = html(sel(g, 'h2'))
    f['grp_%d_lead' % n] = html(sel(g, '.sec-head__lead'))
help_ = sel(s, '.cat-help')
f.update({'cat_help_title': html(sel(help_, '.cat-help__title')), 'cat_help_text': html(sel(help_, '.cat-help__text')),
          'cat_help_link': own_text(sel(help_, '.cat-help__link'))})
ch = sel(s, '#choose')
f.update({
    'route_kicker': own_text(sel(ch, '.kicker')), 'route_title': html(sel(ch, 'h2')), 'route_lead': html(sel(ch, '.route-head__lead')),
    'route_call': own_text(sel(ch, '.route-head__call')),
    'routes': [{'icon': icon(sel(a, '.route__ic')), 'q': text(sel(a, '.route__q')), 'a': text(sel(a, '.route__a')),
                'page': 'page:' + SVC[a['href'].strip('/')]} for a in ch.select('a.route')],
})
f.update(prices(s, base)); f.update(why(s)); f.update(flow(s)); f.update(faq(s, base)); f.update(final(s)); f.update(seo(s))
pages['services'] = {'title': 'Услуги', 'slug': 'servicios', 'fields': f}
# данные карточек из каталога: тег, теги, текст
catalog_cards = {}
for li in s.select('.cat-grid .svc-card'):
    slug = li.select_one('.svc-card__title a')['href'].strip('/')
    catalog_cards[SVC[slug]] = {
        'group': 'opening' if li.find_parent(class_='cat-group')['id'] == 'opening' else 'repair',
        'tag': text(sel(li, '.svc-card__tag')), 'tags': '\n'.join(text(t) for t in li.select('.svc-card__tags li')),
        'text_cat': html(sel(li, '.svc-card__text')),
    }

# карточки на главной (текст, цена, иконка)
home = load('index.html')
home_cards = {}
for li in home.select('#services .svc-card'):
    a = li.select_one('.svc-card__title a')
    key = SVC[a['href'].split('/')[1]]
    b = sel(li, '.svc-card__price b')
    home_cards[key] = {'card_title': html(a), 'card_text': html(sel(li, '.svc-card__text')),
                       'card_price': text(b).replace('&nbsp;€', ''), 'card_icon': img(sel(li, '.svc-card__icon'))}

# ---------------- услуги ----------------
for slug, key in SVC.items():
    s = load('servicios/%s/index.html' % slug)
    base = 'servicios/%s/' % slug
    hero = sel(s, '.sh')
    f = {'crumb': crumbs_last(s)}
    f.update(home_cards[key])
    cc = catalog_cards[key]
    f.update({'card_group': cc['group'], 'card_tag': cc['tag'], 'card_tags': cc['tags'],
              'card_text_catalog': cc['text_cat'] if cc['text_cat'] != f['card_text'] else ''})
    f.update({
        'sh_title': html(sel(hero, '.sh__title')), 'sh_lead': html(sel(hero, '.sh__lead')),
        'sh_photo': img(sel(hero, '.stage__photo')), 'sh_bubble': img(sel(hero, '.stage__bubble img')),
        'sh_chip': own_text(sel(hero, '.sh__chip')),
    })
    q = sel(s, '.quick')
    f['quick_perks'] = [{'icon': icon(sel(li, '.quick__ic')), 'title': html(sel(li, '.quick__txt b')),
                         'text': re.sub(r'^<b>.*?</b>', '', html(sel(li, '.quick__txt')))} for li in q.select('.quick__perks li')]
    f['quick_form_title'] = html(sel(q, '.sh-form__title'))
    sit = sel(s, '#situations')
    cta = sel(sit, '.flow-cta')
    f.update({
        'sit_kicker': own_text(sel(sit, '.kicker')), 'sit_title': html(sel(sit, 'h2')), 'sit_lead': html(sel(sit, '.sec-head__lead')),
        'sit_cards': [{'icon': icon(sel(li, '.sit-card__ic')), 'title': html(sel(li, '.sit-card__title')), 'text': html(sel(li, '.sit-card__text'))} for li in sit.select('.sit-card')],
        'sit_cta_title': html(sel(cta, '.flow-cta__title')), 'sit_cta_sub': html(sel(cta, '.flow-cta__sub')), 'sit_cta_btn': own_text(sel(cta, '.btn')),
    })
    sp = sel(s, '#prices')
    rows = []
    for tr in sp.select('tbody tr'):
        td = tr.td
        price = ''
        if 'is-tbd' not in (td.get('class') or []):
            b = td.find('b')
            price = '…' if 'is-tbd-price' in (b.get('class') or []) else text(b).replace('&nbsp;€', '')
        rows.append({'icon': icon(tr.th), 'label': own_text(tr.th), 'price': price})
    f.update({
        'sp_kicker': own_text(sel(sp, '.kicker')), 'sp_title': html(sel(sp, 'h2')), 'sp_lead': html(sel(sp, '.sec-head__lead')),
        'sp_caption': text(sel(sp, 'caption')), 'sp_col_1': text(sp.select('thead th')[0]), 'sp_col_2': text(sp.select('thead th')[1]),
        'sp_rows': rows,
        'sp_notes': [{'title': html(d.dt), 'text': html(d.dd)} for d in sp.select('.sp-notes > div')],
        'sp_cta_title': text(sel(sp, '.pr-cta__text b')), 'sp_cta_text': text(sel(sp, '.pr-cta__text > span')),
    })
    calc = sel(s, '.calc-sec')
    tot = sel(calc, '.calc-item--total')
    f.update({
        'calc_kicker': own_text(sel(calc, '.kicker')), 'calc_title': html(sel(calc, 'h2')), 'calc_lead': html(sel(calc, '.sec-head__lead')),
        'calc_items': [{'icon': icon(sel(li, '.calc-item__ic')), 'title': html(sel(li, '.calc-item__title')), 'text': html(sel(li, '.calc-item__text'))}
                       for li in calc.select('.calc-item') if 'calc-item--total' not in li['class']],
        'calc_total_title': html(sel(tot, '.calc-item__title')), 'calc_total_text': html(sel(tot, '.calc-item__text')),
        'calc_note': own_text(sel(calc, '.calc-sec__note')),
    })
    est = sel(s, '#call')
    f.update({'est_kicker': own_text(sel(est, '.kicker')), 'est_title': html(sel(est, 'h2')), 'est_lead': html(sel(est, '.est__lead'))})
    ty = sel(s, '#types')
    f.update({
        'types_kicker': own_text(sel(ty, '.kicker')), 'types_title': html(sel(ty, 'h2')), 'types_lead': html(sel(ty, '.sec-head__lead')),
        'types_cards': [{'icon': icon(sel(li, '.type-card__ic')), 'title': html(sel(li, '.type-card__title')), 'text': html(sel(li, '.type-card__text'))} for li in ty.select('.type-card')],
    })
    nd = sel(s, 'section.nd')
    f.update({
        'nd_id': nd['id'], 'nd_photo': img(sel(nd, '.nd__cover')), 'nd_badge': own_text(sel(nd, '.nd__badge')),
        'nd_kicker': own_text(sel(nd, '.kicker')), 'nd_title': html(sel(nd, 'h2')), 'nd_lead': html(sel(nd, '.nd__lead')),
        'nd_items': [{'icon': icon(sel(it, '.nd-item__ic')), 'title': html(sel(it, '.nd-item__title')),
                      'text': ''.join(html(BeautifulSoup('<div>%s</div>' % str(p), 'html.parser').div, base) and str(p) for p in it.find_all('p'))} for it in nd.select('.nd-item')],
    })
    # абзацы nd/docs с токенами ссылок
    for it_field, css in (('nd_items', '.nd-item'), ):
        for i, it in enumerate(nd.select(css)):
            f[it_field][i]['text'] = ''.join('<p>%s</p>' % html(p, base) for p in it.find_all('p'))
    f.update(flow(s))
    ed = sel(s, '#why')
    photos = ed.select('.edge__photo img')
    f.update({
        'edge_photo': img(photos[0]), 'edge_city': img(photos[1]), 'edge_chip': own_text(sel(ed, '.why-photo__chip')),
        'edge_kicker': own_text(sel(ed, '.kicker')), 'edge_title': html(sel(ed, 'h2')),
        'edge_items': [{'icon': icon(sel(li, '.edge-item__ic')), 'title': html(sel(li, '.edge-item__title')), 'text': html(sel(li, '.edge-item__text'))} for li in ed.select('.edge-item')],
    })
    f.update(reviews(s))
    dc = sel(s, '#documents')
    f.update({
        'docs_kicker': own_text(sel(dc, '.kicker')), 'docs_title': html(sel(dc, 'h2')),
        'docs_card_text': html(sel(dc, '.docs__card-text')), 'docs_card_label': text(sel(dc, '.docs__card-label')),
        'docs_chips': [{'text': text(li), 'es': 1 if li.get('lang') == 'es' else 0} for li in dc.select('.docs__chips li')],
        'docs_items': [{'title': html(sel(it, '.docs-item__title')), 'text': ''.join('<p>%s</p>' % html(p, base) for p in it.find_all('p'))} for it in dc.select('.docs-item')],
    })
    f.update(geo(s)); f.update(longread(s, base)); f.update(faq(s, base))
    mo = sel(s, '#more')
    f.update({'more_kicker': own_text(sel(mo, '.kicker')), 'more_title': html(sel(mo, 'h2'))})
    f.update(final(s)); f.update(modal(s)); f.update(seo(s, True))
    pages[key] = {'title': re.sub(r'<[^>]+>', '', f['sh_title']).replace('&nbsp;', ' '), 'slug': slug, 'fields': f}

# ---------------- контакты ----------------
s = load('contacto/index.html')
base = 'contacto/'
hero = sel(s, '.sh')
chs = hero.select('.ct-ch')
f = {
    'crumb': crumbs_last(s), 'sh_title': html(sel(hero, '.sh__title')), 'sh_lead': html(sel(hero, '.sh__lead')),
    'ct_call_label': own_text(sel(chs[0], '.ct-ch__label')), 'ct_call_text': html(sel(chs[0], '.ct-ch__text')),
    'ct_wa_text': html(sel(chs[1], '.ct-ch__text')), 'ct_mail_text': html(sel(chs[2], '.ct-ch__text')),
    'ct_facts': [{'icon': icon(d.dt), 'title': own_text(d.dt), 'text': html(d.dd.a) if d.dd.a else html(d.dd),
                  'link': d.dd.a['href'] if d.dd.a else ''} for d in hero.select('.ct-facts > div')],
    'ct_form_title': html(sel(hero, '.form-card__title')), 'ct_form_lead': html(sel(hero, '.ct-form__lead')),
}
br = sel(s, '#brief')
f.update({
    'brief_kicker': own_text(sel(br, '.kicker')), 'brief_title': html(sel(br, 'h2')), 'brief_lead': html(sel(br, '.brief__lead')),
    'brief_sos_text': html(sel(br, '.docs__card-text')), 'brief_sos_label': html(sel(br, '.brief__sos-num span')),
    'brief_items': [{'title': html(sel(li, '.docs-item__title')), 'text': ''.join('<p>%s</p>' % html(p, base) for p in li.find_all('p'))} for li in br.select('.docs-item')],
})
f.update(geo(s))
co = sel(s, '#company')
req = []
for d in co.select('.ct-req__list > div'):
    dd = d.dd
    t = 'text'
    if 'is-tbd' in (dd.get('class') or []):
        t = 'nif'
    elif dd.a and dd.a['href'].startswith('tel:'):
        t = 'phone'
    elif dd.a and dd.a['href'].startswith('mailto:'):
        t = 'email'
    req.append({'label': text(d.dt), 'type': t, 'value': html(dd) if t == 'text' else ''})
legal = []
for a in co.select('.ct-legal a'):
    tok = link_token(base, a['href'])
    legal.append({'text': html(a.span), 'page': 'page:' + re.match(r'\{url:([a-z0-9-]+)\}', tok).group(1)})
f.update({
    'company_kicker': own_text(sel(co, '.kicker')), 'company_title': html(sel(co, 'h2')), 'company_lead': html(sel(co, '.sec-head__lead')),
    'req_title': text(sel(co, '.ct-req__title')), 'req_rows': req,
    'google_title': own_text(sel(co, '.ct-google__head')), 'google_text': html(sel(co, '.ct-google__text')),
    'google_btn_1': own_text(co.select('.ct-google__actions a')[0]), 'google_btn_2': own_text(co.select('.ct-google__actions a')[1]),
    'legal_title': text(sel(co, '.ct-legal__title')), 'legal_links': legal,
})
f.update(faq(s, base)); f.update(seo(s))
pages['contacts'] = {'title': 'Контакты', 'slug': 'contacto', 'fields': f}

# правовые страницы: заглушки
for key, slug, title in (('privacy', 'politica-de-privacidad', 'Политика конфиденциальности'), ('cookies', 'politica-de-cookies', 'Политика cookies'),
                         ('terms', 'condiciones-del-servicio', 'Условия оказания услуг'), ('legal', 'aviso-legal', 'Юридическая информация')):
    pages[key] = {'title': title, 'slug': slug, 'content': '<p>Текст документа будет добавлен.</p>', 'fields': {'seo_title': title + ' — Cerrajero24', 'seo_desc': ''}}

strings = json.load(open(ROOT + 'wp-theme/cerrajero24/import/strings.json'))
out = {'pages': pages, 'media': media, 'menus': MENUS, 'strings': {k: v['ru'] for k, v in strings.items()}}
json.dump(out, open(OUT, 'w'), ensure_ascii=False, indent=1)
print('ok', len(pages), 'pages', len(media), 'media')
