import sys,re,difflib,urllib.request
from bs4 import BeautifulSoup, NavigableString, Comment
def lines(html):
    s=BeautifulSoup(html,'html.parser')
    b=s.body
    for t in b.find_all(['script','style']): t.decompose()
    sp=b.find('svg',class_='sprite')
    if sp: sp.decompose()
    for t in b.find_all(class_=['lang','mobile-menu__langs']): t.decompose()
    for t in b.find_all('input',attrs={'type':'hidden'}): t.decompose()
    for c in b.find_all(string=lambda x:isinstance(x,Comment)): c.extract()
    out=[]
    def walk(el,d):
        for c in el.children:
            if isinstance(c,NavigableString):
                t=re.sub(r'\s+',' ',str(c)).strip()
                if t: out.append('  '*d+'"'+t+'"')
            elif c.name:
                attrs=[]
                for k in sorted(c.attrs):
                    if k in ('src','srcset','href','sizes','width','height','decoding','loading','fetchpriority','value','imagesrcset','alt','data-default','lang','action'): continue
                    v=c.attrs[k]; v=' '.join(v) if isinstance(v,list) else v
                    attrs.append('%s=%s'%(k,v))
                if c.name=='img': attrs.append('img')
                out.append('  '*d+'<'+c.name+' '+' '.join(attrs)+'>')
                walk(c,d+1)
    walk(b,0)
    return out
a=lines(open(sys.argv[1]).read())
b=lines(urllib.request.urlopen(sys.argv[2]).read().decode())
d=list(difflib.unified_diff(a,b,lineterm='',n=1))
print('\n'.join(d[:int(sys.argv[3]) if len(sys.argv)>3 else 150]))
print('TOTAL diff lines',len(d))
