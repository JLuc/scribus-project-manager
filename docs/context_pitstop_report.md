## Context : My workflow

I produce and edit a magazine and books. 
So as to do so i create differents scribus files : one for each article / chapter.

(Why not create a single big scribus file ? scribus becomes slow on long files havin linked text frames + scribus document becomes a mess when editing to much the styles or when importing pages or copy and pasting from a different document)

When editing is finished, i produce the PDF for each part, and i use pdftk to merge the PDFs into a single big PDF.
I send this to the printer. Most of the time, the PDF is OK. 
Sometime it isnt.

## Pitstop report : Errors to avoid

Sometime, the printer first refuses the book final pdf.
Enfocus Pitstop is the software to diagnose the produced PDF.
Here is the text out of a sample pitstop report :

```
### Rapport de contrôle en amont
#### Des erreurs ont été trouvées aux pages 1-68

* Les pages du document n'ont pas toutes le même format (basé sur Zone de rogne) (42x aux pages 4-22,
46-68)
* La police Fo0S0, Fo1S0, Fo2S0, Fo3S0 est une police Type 3 (277x aux pages 2-22,49,52-55,57,63-68)
* Couleur RVB est utilisé(e) (549x à la page 7)
* Le ton direct All est utilisé pour un élément de la zone de rogne (1x à la page 66)
* La résolution de image couleur ou en gamme de gris est inférieur(e) à 149 ppi (1x à la page 2)
* Des objets transparents ont été trouvés (117x aux pages 1-3,7,15,23-45,51,57,60-61,63,66-68)
* La police DejaVuSans, DejaVuSansCondensed, DejaVuSansCondensed-Bold, DejaVuSerifCondensed,
DejaVuSerifCondensed-Bold, LiberationSans-Bold, LiberationSansNarrow n'est pas incorporée (9131x aux pages 23-45)

#### Avertissements
* Gris RVB ou gris CMJN impur est utilisé(e) (44x à la page 7)
* Noir RVB ou noir CMJN impur est utilisé(e) (327x aux pages 1-2)
```

All of these errors or warning were not fatal.
* transparencies did not have to be fixed (PDF version 1.5)
* It could be that there were no type3 PS fonts (i could not find any) and it wasnt anymore a problem later
* RVB color was sometime used in glyph's shadows - not so bad an issue

Main issues to be avoided are :
* different sizes of pages. All pages were supposed to be same size, but there were 0.5 mm differences for some of them.
* missing fonts, neither embeded not vectorized
