# Исходники переводов

Тексты сайта для импорта в WordPress-тему (`wp-theme/cerrajero24/import/*.json`).

- `ru.json` — исходный контент, извлечённый из статической вёрстки (`tools/extract.py`).
- `es/`, `eu/`, `en/` — переводы по сегментам: `<номер>\t<текст>`, номер — порядковый номер сегмента страницы (`tools/seg.py dump <ключ>`).
- `tools/slugs.json` — слаги и названия страниц на каждом языке.

Сборка: `python3 tools/seg.py build es` (и `eu`, `en`) — пишет `wp-theme/cerrajero24/import/<lang>.json`.
