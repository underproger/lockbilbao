"""Сегменты для перевода.
  seg.py dump KEY            — пронумерованные RU-сегменты страницы (или _site)
  seg.py build LANG          — собрать import/LANG.json из ru.json + i18n/LANG/*.txt
"""
import json, re, sys, os, copy

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
I18N = os.path.join(ROOT, 'i18n-src') + '/'
THEME = os.path.join(ROOT, 'wp-theme', 'cerrajero24', 'import') + '/'
RU = json.load(open(I18N + 'ru.json'))

SKIP_KEYS = {'icon', 'area', 'link', 'page', 'url', 'demo', 'es', 'type', 'card_group', 'nd_id', 'rv_cta_type', 'card_price', 'price', 'date'}
SKIP_FIELDS = {'brands_list'}


def translatable(key, v):
    if key in SKIP_KEYS or key in SKIP_FIELDS:
        return False
    if not isinstance(v, str) or not v.strip():
        return False
    if re.match(r'^(img:|page:|#|\{url:|\{home\}|https?:)', v):
        return False
    if re.fullmatch(r'[\d\s.,:/€%+–—-]*', v):
        return False
    return True


def segments(obj, path=()):
    """[(path, piece_index, text)] — многострочные значения режутся по \n."""
    out = []
    if isinstance(obj, dict):
        for k, v in obj.items():
            if isinstance(v, (dict, list)):
                out += segments(v, path + (k,))
            elif translatable(k, v):
                for i, piece in enumerate(v.split('\n')):
                    if piece.strip():
                        out.append((path + (k,), i, piece))
    elif isinstance(obj, list):
        for i, v in enumerate(obj):
            out += segments(v, path + (i,))
    return out


def page_obj(key):
    if key == '_site':
        return {'menus': RU['menus'], 'media': {k: v for k, v in RU['media'].items()}}
    p = RU['pages'][key]
    return {'fields': p['fields'], 'content': p.get('content', '')}


def dump(key):
    for n, (path, i, t) in enumerate(segments(page_obj(key)), 1):
        print('%d\t%s' % (n, t))


def read_tr(lang, key):
    f = I18N + lang + '/' + key + '.txt'
    tr = {}
    for line in open(f, encoding='utf-8'):
        line = line.rstrip('\n')
        if not line.strip() or line.startswith('#'):
            continue
        n, _, t = line.partition('\t')
        tr[int(n)] = t
    return tr


def get(obj, path):
    for p in path:
        obj = obj[p]
    return obj


def setv(obj, path, v):
    for p in path[:-1]:
        obj = obj[p]
    obj[path[-1]] = v


def apply(lang, key, obj):
    segs = segments(obj)
    tr = read_tr(lang, key)
    missing = [n for n in range(1, len(segs) + 1) if n not in tr]
    extra = [n for n in tr if n > len(segs)]
    if missing or extra:
        raise SystemExit('%s/%s: missing %s extra %s (segments %d)' % (lang, key, missing[:20], extra[:20], len(segs)))
    pieces = {}
    for n, (path, i, t) in enumerate(segs, 1):
        pieces.setdefault(path, {})[i] = tr[n]
    for path, parts in pieces.items():
        orig = get(obj, path).split('\n')
        for i, t in parts.items():
            orig[i] = t
        setv(obj, path, '\n'.join(orig))
    return obj


SLUGS = json.load(open(I18N + 'tools/slugs.json'))


def strip_es_lang(v):
    """В испанской версии испанские термины не нужно помечать lang="es"."""
    if isinstance(v, str):
        return re.sub(r'<span lang="es">(.*?)</span>', r'\1', v)
    if isinstance(v, list):
        return [strip_es_lang(x) for x in v]
    if isinstance(v, dict):
        return {k: strip_es_lang(x) for k, x in v.items()}
    return v


def build(lang):
    out = {'pages': {}, 'media': {}, 'menus': {}, 'strings': {}}
    site = apply(lang, '_site', copy.deepcopy(page_obj('_site')))
    out['media'] = site['media']
    out['menus'] = site['menus']
    for key, p in RU['pages'].items():
        obj = apply(lang, key, copy.deepcopy(page_obj(key)))
        fields = obj['fields']
        if lang == 'es':
            fields = strip_es_lang(fields)
            for chip in fields.get('docs_chips', []):
                chip['es'] = 0
        title = SLUGS[key]['title'][lang] if 'title' in SLUGS[key] else re.sub(r'<[^>]+>', '', fields.get('sh_title', '')).replace('&nbsp;', ' ')
        page = {'title': title, 'slug': SLUGS[key][lang], 'fields': fields}
        if obj.get('content'):
            page['content'] = obj['content']
        out['pages'][key] = page
    strings = json.load(open(THEME + 'strings.json'))
    out['strings'] = {k: (v[lang] if lang != 'es' else '') for k, v in strings.items()}
    if lang == 'es':
        out['strings'] = {}
    json.dump(out, open(THEME + lang + '.json', 'w'), ensure_ascii=False, indent=1)
    print('built', lang, len(out['pages']))


if __name__ == '__main__':
    if sys.argv[1] == 'dump':
        dump(sys.argv[2])
    elif sys.argv[1] == 'count':
        for k in ['_site'] + list(RU['pages']):
            print(k, len(segments(page_obj(k))))
    elif sys.argv[1] == 'build':
        build(sys.argv[2])
