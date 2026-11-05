import sys, asyncio
from playwright.async_api import async_playwright
from PIL import Image, ImageChops
OUT='/tmp/claude-0/-home-claude/cd447148-5c01-51f0-b268-eef9f0a53687/scratchpad/shots/'
import os; os.makedirs(OUT, exist_ok=True)
PAGES=[('home','/','/'),('cat','/servicios/','/servicios/'),('ct','/contacto/','/contacto/'),('door','/servicios/apertura-de-puertas/','/servicios/apertura-de-puertas/'),('car','/servicios/apertura-de-coches/','/servicios/apertura-de-coches/'),('nf','NF','/nonexist/')]
if len(sys.argv)>2: PAGES=[p for p in PAGES if p[0] in sys.argv[2].split(',')]
WIDTHS=[int(w) for w in sys.argv[1].split(',')]
CSS='.lang{display:none!important} *{transition:none!important;animation:none!important;caret-color:transparent!important}'
async def shot(page,url,path,w):
    await page.set_viewport_size({'width':w,'height':900})
    await page.goto(url, wait_until='networkidle')
    await page.add_style_tag(content=CSS)
    await page.evaluate("""async()=>{for(let y=0;y<document.body.scrollHeight;y+=600){window.scrollTo(0,y);await new Promise(r=>setTimeout(r,30));} window.scrollTo(0,0); document.querySelectorAll('img').forEach(i=>i.loading='eager');}""")
    await page.wait_for_timeout(500)
    await page.screenshot(path=path, full_page=True)
async def main():
    async with async_playwright() as p:
        b=await p.chromium.launch()
        ctx=await b.new_context(device_scale_factor=1)
        page=await ctx.new_page()
        for key,st,wp in PAGES:
            for w in WIDTHS:
                a=OUT+f'{key}-{w}-static.png'; c=OUT+f'{key}-{w}-wp.png'
                await shot(page,('http://127.0.0.1:8097/lockbilbao/404.html' if st=='NF' else 'http://127.0.0.1:8098'+st),a,w)
                await shot(page,'http://localhost:8080'+wp,c,w)
                A=Image.open(a).convert('RGB'); C=Image.open(c).convert('RGB')
                if A.size!=C.size:
                    print(f'{key} {w}: SIZE static {A.size} wp {C.size}'); 
                    h=min(A.size[1],C.size[1]); A=A.crop((0,0,w,h)); C=C.crop((0,0,w,h))
                diff=ImageChops.difference(A,C).convert('L').point(lambda v:255 if v>24 else 0)
                n=sum(diff.histogram()[255:]); tot=A.size[0]*A.size[1]
                bbox=diff.getbbox()
                print(f'{key} {w}: {100*n/tot:.3f}% bbox={bbox}')
                if n: diff.save(OUT+f'{key}-{w}-diff.png')
        await b.close()
asyncio.run(main())
