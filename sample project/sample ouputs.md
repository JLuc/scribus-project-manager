
Sample output at some point in the unfinished workflow.

In this project,
`sources="p_1 couv p_4 rdevolution n_4 grafterre n_6 p_14 PA n_32 equivalence n_6 photovoltaique n_8 peindre n_7 patterns n_2 p_69"`

# Output 1
makbook loads performs checks and PDFs (when required) for each project chapter and issues following output :

```
Début page 1

couv/PAO/couv : page 1 (set)
Début page 4

rdevolution/PAO/rdevolution : page 4 (set)
Warning :  rdevolution/PAO/rdevolution.sla has been edited and saved later than last PDF output : rdevolution/PAO/rdevolution.sla = 2017-10-05 05:01 ; rdevolution/PAO/rdevolution.pdf = 2017-10-05 05:00
Export to rdevolution/PAO/rdevolution.pdf
OK rdevolution/PAO/rdevolution.pdf
4 pages

grafterre/PAO/grafterre : page 8 (computed)
Error :  in grafterre/PAO/grafterre.sla with "Wingdings Regular" : font is neither vectorized nor embeded
6 pages
Début page 14

PA/PAO/PA : page 14 (set)
                <ITEXT CH="Annonce p. AAA"/>
Error :  in PA/PAO/PA.sla with "Liberation Sans Regular" : font is neither vectorized nor embeded
32 pages

equivalence/PAO/equivalence : page 46 (computed)
6 pages

photovoltaique/PAO/photovoltaique : page 52 (computed)
                <ITEXT CH="Da­vid Mer­ce­reau voir ses annonces p. AAA"/>
8 pages

peindre/PAO/peindre : page 60 (computed)
7 pages

patterns/PAO/patterns : page 67 (computed)
2 pages
Début page 69
-rw-rw-r-- 1 jluc jluc 66605968 oct.   5 05:01 n64.pdf
n64.pdf created OK
```
During this run
- an outdate PDF was detected and renewed.
- some missing fonts were detected
- a user-set warning (AAA) was triggered (for a missing page number to be inserted in place)
- pages set in each sections document's settings and page numbers set in config were checked and found ok

# Output 2

The project's config was edited (DFONT set to *Montserrat Light*) and a specific config was added to `PA` chapter (DFONT set to *Dejavu condensed*).
When re-run, the script detects unconsistencies because the SLA files have not yet been updated to conform to the new configs.

```
Début page 1

couv/PAO/couv : page 1 (set)
Error : DFONT NOT OK ! In couv/PAO/couv.sla, found <DOCUMENT  DFONT="Liberation Sans Regular"\> (expected value for DFONT is Montserrat Light)
Warning : SLA file will be altered so it checks config
Set //DOCUMENT/@DFONT to Montserrat Light in couv/PAO/couv.sla
Début page 4

rdevolution/PAO/rdevolution : page 4 (set)
Error : DFONT NOT OK ! In rdevolution/PAO/rdevolution.sla, found <DOCUMENT  DFONT="Liberation Sans Regular"\> (expected value for DFONT is Montserrat Light)
Warning : SLA file will be altered so it checks config
Set //DOCUMENT/@DFONT to Montserrat Light in rdevolution/PAO/rdevolution.sla
4 pages

grafterre/PAO/grafterre : page 8 (computed)
Error : DFONT NOT OK ! In grafterre/PAO/grafterre.sla, found <DOCUMENT  DFONT="Liberation Sans Regular"\> (expected value for DFONT is Montserrat Light)
Warning : SLA file will be altered so it checks config
Set //DOCUMENT/@DFONT to Montserrat Light in grafterre/PAO/grafterre.sla
6 pages
Début page 14

PA/PAO/PA : page 14 (set)
                <ITEXT CH="Annonce p. AAA"/>
Error : DFONT NOT OK ! In PA/PAO/PA.sla, found <DOCUMENT  DFONT="Liberation Sans Regular"\> (expected value for DFONT is Montserrat Light)
Warning : SLA file will be altered so it checks config
Set //DOCUMENT/@DFONT to Montserrat Light in PA/PAO/PA.sla
32 pages

equivalence/PAO/equivalence : page 46 (computed)
Error : DFONT NOT OK ! In equivalence/PAO/equivalence.sla, found <DOCUMENT  DFONT="Liberation Sans Regular"\> (expected value for DFONT is Montserrat Light)
Warning : SLA file will be altered so it checks config
Set //DOCUMENT/@DFONT to Montserrat Light in equivalence/PAO/equivalence.sla
6 pages

photovoltaique/PAO/photovoltaique : page 52 (computed)
                <ITEXT CH="Da­vid Mer­ce­reau voir ses annonces p. AAA"/>
Error : DFONT NOT OK ! In photovoltaique/PAO/photovoltaique.sla, found <DOCUMENT  DFONT="Liberation Sans Regular"\> (expected value for DFONT is Montserrat Light)
Warning : SLA file will be altered so it checks config
Set //DOCUMENT/@DFONT to Montserrat Light in photovoltaique/PAO/photovoltaique.sla
8 pages

peindre/PAO/peindre : page 60 (computed)
Error : DFONT NOT OK ! In peindre/PAO/peindre.sla, found <DOCUMENT  DFONT="Liberation Sans Regular"\> (expected value for DFONT is Montserrat Light)
Warning : SLA file will be altered so it checks config
Set //DOCUMENT/@DFONT to Montserrat Light in peindre/PAO/peindre.sla
7 pages

patterns/PAO/patterns : page 67 (computed)
Error : DFONT NOT OK ! In patterns/PAO/patterns.sla, found <DOCUMENT  DFONT="Liberation Sans Regular"\> (expected value for DFONT is Montserrat Light)
Warning : SLA file will be altered so it checks config
Set //DOCUMENT/@DFONT to Montserrat Light in patterns/PAO/patterns.sla
2 pages
Début page 69
```

So as to ensure consistency with the config, we ought to run `makbook -set`. It produces following output :

```
Début page 1

couv/PAO/couv : page 1 (set)
Error : DFONT NOT OK ! In couv/PAO/couv.sla, found <DOCUMENT  DFONT="Liberation Sans Regular"\> (expected value for DFONT is Montserrat Light)
Warning : SLA file will be altered so it checks config
Set //DOCUMENT/@DFONT to Montserrat Light in couv/PAO/couv.sla
Début page 4

rdevolution/PAO/rdevolution : page 4 (set)
Error : DFONT NOT OK ! In rdevolution/PAO/rdevolution.sla, found <DOCUMENT  DFONT="Liberation Sans Regular"\> (expected value for DFONT is Montserrat Light)
Warning : SLA file will be altered so it checks config
Set //DOCUMENT/@DFONT to Montserrat Light in rdevolution/PAO/rdevolution.sla
4 pages

grafterre/PAO/grafterre : page 8 (computed)
Error : DFONT NOT OK ! In grafterre/PAO/grafterre.sla, found <DOCUMENT  DFONT="Liberation Sans Regular"\> (expected value for DFONT is Montserrat Light)
Warning : SLA file will be altered so it checks config
Set //DOCUMENT/@DFONT to Montserrat Light in grafterre/PAO/grafterre.sla
6 pages
Début page 14

PA/PAO/PA : page 14 (set)
                <ITEXT CH="Annonce p. AAA"/>
Error : DFONT NOT OK ! In PA/PAO/PA.sla, found <DOCUMENT  DFONT="Liberation Sans Regular"\> (expected value for DFONT is Montserrat Light)
Warning : SLA file will be altered so it checks config
Set //DOCUMENT/@DFONT to Montserrat Light in PA/PAO/PA.sla
32 pages

equivalence/PAO/equivalence : page 46 (computed)
Error : DFONT NOT OK ! In equivalence/PAO/equivalence.sla, found <DOCUMENT  DFONT="Liberation Sans Regular"\> (expected value for DFONT is Montserrat Light)
Warning : SLA file will be altered so it checks config
Set //DOCUMENT/@DFONT to Montserrat Light in equivalence/PAO/equivalence.sla
6 pages

photovoltaique/PAO/photovoltaique : page 52 (computed)
                <ITEXT CH="Da­vid Mer­ce­reau voir ses annonces p. AAA"/>
Error : DFONT NOT OK ! In photovoltaique/PAO/photovoltaique.sla, found <DOCUMENT  DFONT="Liberation Sans Regular"\> (expected value for DFONT is Montserrat Light)
Warning : SLA file will be altered so it checks config
Set //DOCUMENT/@DFONT to Montserrat Light in photovoltaique/PAO/photovoltaique.sla
8 pages

peindre/PAO/peindre : page 60 (computed)
Error : DFONT NOT OK ! In peindre/PAO/peindre.sla, found <DOCUMENT  DFONT="Liberation Sans Regular"\> (expected value for DFONT is Montserrat Light)
Warning : SLA file will be altered so it checks config
Set //DOCUMENT/@DFONT to Montserrat Light in peindre/PAO/peindre.sla
7 pages

patterns/PAO/patterns : page 67 (computed)
Error : DFONT NOT OK ! In patterns/PAO/patterns.sla, found <DOCUMENT  DFONT="Liberation Sans Regular"\> (expected value for DFONT is Montserrat Light)
Warning : SLA file will be altered so it checks config
Set //DOCUMENT/@DFONT to Montserrat Light in patterns/PAO/patterns.sla
2 pages
Début page 69
```

With `-set` option, .SLA files are edited and fixed, but the PDF are not updated.
We ought to call `makbook` to do so and get new report.

```
Début page 1

couv/PAO/couv : page 1 (set)
Warning :  couv/PAO/couv.sla has been edited and saved later than last PDF output : couv/PAO/couv.sla = 2017-10-05 05:16 ; couv/PAO/couv.pdf = 2017-10-05 05:00
Export to couv/PAO/couv.pdf
OK couv/PAO/couv.pdf
Début page 4

rdevolution/PAO/rdevolution : page 4 (set)
Warning :  rdevolution/PAO/rdevolution.sla has been edited and saved later than last PDF output : rdevolution/PAO/rdevolution.sla = 2017-10-05 05:16 ; rdevolution/PAO/rdevolution.pdf = 2017-10-05 05:01
Export to rdevolution/PAO/rdevolution.pdf
OK rdevolution/PAO/rdevolution.pdf
4 pages

...etc...
```
