A scribus project manager
=========================

Scribus project management tools with shell scripts.

**Scribus doesnt provide any project management tools. These shell scripts mainly aims to compensate this.**

## What is a Scribus project
A scribus project is a book or a magazine made out of multiple scribus files.
Each scribus file is a chapter, an article, a part of the book.
All have to be concatenated to create the whole book.
All share some common properties like size of pages, styles or color profiles, but some of the chapters might have specific properties.

## What does a scribus-project-manager *manage* ?
A Scribus project manager is a tool that help manage multiple scribus files that are parts of a same project.

Particularly so as to
* check scribus produced files and PDFs before sending them to the printer.
  - ensure that used fonts are embeded or subset or vectorized
  - ensure that PDF format related constraints are satisfied (PDF version, color profiles, etc)
  - ensure that used images are stored in the local `images` file so the project can be safely saved
  - ensure all page dimensions and properties are conform to some project or chapter related settings
  - ensure that page numbers follow each other depending on the page number in each chapter
  - ensure PDF is updated when sla has been edited
  - ensure backups are created before altering files
* edit SLAs so they conform to the project standard or to the chapter specifics.
* produce the concatenated updated PDF
* look for strings in the text OR in the XML structure and replace them with some other text
* synchronise chapter's styles, masterpages or color definitions with those of a master document
* automaticaly create PDF bookmarks for each paragraph of a user-specified style, and produce .md file of the table of content of these bookmarks
* manage editor's comments of several types and produce .md file summary of these bookmarks, chapter by chapter
* produce a list of used image files, stating when inlined or out of local image folder
* optionnaly produce bleeds-free or image-free local-printer-friendly PDFs as an alternative to the main with-bleeds and with-image version
* optionnaly produce searchable font-embeded or non-searchable vectorized versions of the PDF
* possibly check or set all SLA possible options depending on your needs

And more.

## What is this repository

This repo contains the scripts I use to create the quaterly [Passerelle Eco](http://www.passerelleco.info/) magazine and books. This magazine  is made of 8 to 20 parts with same page size, sharing lots of common styles, but some parts being very differents. 
After creating dedicated config files, the scripts can be used for other projects having differents characteristics.

### Book related tools

* **makebook script** : call relevant tools on relevant files and create final PDF (no generic makebook is published, but an example makebook file is available).  
* - apply master styles or masterpage or color to all chapters
* - create text only version (no images) or no-bleeds version for personal printing
* - check settings fit requirement
* - and more

### SLA document related tools

* **slacheck** :
*   - performs all possible checks and reports issues before sending PDF to printer. 
*   - Optionnaly edit the SLA so it conforms to the defined standard.  
* **slacheckimages** : checks that the used images are all stored in 'images' subfolder.  
* **slacheckfonts** : checks that all used fonts are either embeded or subseted.  

* **slasync** : updates some parts of a scribus document following a master document

### Lower level scripts
* **slacheckattr** : checks the value of some xml attribute in the scribus file
* **slasetattr** : set some XML attr in the SLA file to its correct value
   - various backup are created : .sla.first.sla, .sla.last.sla and a hiden .filename.sla.tmp.bak  
* **strtrim** : basic string trim

### Environnement tools
This project has been created and used on Ubuntu 14.04, 16.04, 17.10, 18.04, 22.04 and 24.04 using `bash` and `zbash`

It includes the following tools, that might work or not depending on your OS :
* **sla_icon** : adds specific icon on SLA file(s) for nautilus view  
* **clean_icon** : removes user-added icon on file or folder

These scripts can be made callable via nautilus action's menus.

Also :
* **shellcolors** : create variables to easily bring colors to console output using escape sequences. It's used to issue error or warning messages, and search result or such.

# Setup for the project manager

You ought to describe your project and how you want to manage it.

## Set of files composing the project ##

The set of files is described in a string made out of all filenames in their book order.  
Each file is sometime refered as a chapter but it can be a single page or a whole section of the project.

As easy as :
`sources = "filename1 filename2 ... lastfilename"`

Rq : The `.sla` extension, being a constant of a scribus filename, must be omited.

Since each chapter SLA file is usually the end result of a work requiring more documents, images, files, it may requires a whole folder to store these documents, and the SLA file is only one of them.

So as to accomodate this, 3 possible standard organisations of files in the project folder are proposed and make project description more easy :
* 0 : no change to filename in sources
* 1 : filename stands for filename/filename(.sla)
* 2 : filename stands for filename/PAO/filename(.sla)

Simple example : 
```
# 5 files project, no sourcing pattern
sources="CoverBegin		Inside/Summary  Inside/InsidePages		Ads/Adverts  CoverEnd"
sourcespattern=0
```

Example where all SLA files are stored in filename/filename.sla :
```
# 5 files project, basic sourcing pattern where the chapter SLA are each at the root of their chapter folder
sources="CoverBegin		Summary  InsidePages		Adverts  CoverEnd"
sourcespattern=1
```

Example where all SLA files are stored in filename/PAO/filename.sla, so other subfolders as `recordings`, `data` or `notes` can also exist in `filename/`  :
```
# more complex store pattern where SLA files are in PAO subfolder of each chapter folder
sources="CoverBegin		Summary  InsidePages		Adverts  CoverEnd"
sourcespattern=2
```

Page numbers specifications can also be embeded in this source spec. See above in dedicated part of this readme.

You can specify which document should be used as master for styles, masterpage or color synchronisation : `syncmaster="master_chap"`
This masterdocument is only used when `makebook -sync` is called (for the whole book) or when `slasync` is called (for a single chapter).

## `makebook` configuration and use

Configuration is a set of values for **page sizes**, **bleeds**, **marks**, **color profiles** and all other book creation characteristics of your book or magazine... Specifying those enable the project manager to check that the provided documents fit these requirement. None is compulsory.

Each of these configs is an attribute of the SLA XML file tags.
Config files are shell scripts that set variables : usualy there is one variable for each XML attribute you need to check/set.

The configuration values are parameters for slacheck script and can be set
- at global tool level in the slacheck.defaultconfig file
- at project level in the project related local slacheck.config file
- at file level in the file-related local filename.config file

So as to create a config file from scratch you'll have to
- open an SLA with a text editor and understand its structure. 
- findout the relevant tags and attributes you need.
- findout their correct values
- create a config file composed with all "attribute=value" settings.

The provided default config file has done all this tedious job and you can also simply look at it and edit a copy in you work space so as to make it fit your need. It will be rather simple.

In case some of the chapters require different settings than the rest of the book, then you have to create the required file-related restricted settings.

So as to check whether the project's SLA documents have the correct settings, just launch `makebook`. It will warn in case a chapter doesnt verify the required values, but it wont edit the document.

In case an error is detected, you can launch a new run with the `-set` option : it will  fix the bad SLA parameters to their correct value.

# Running

The `makebook` script does run `slacheck` on each file of the source and performs various tests and actions, depending on the command line or config files options.

* Default : 
  - it does test whether files and project conforms to the standards described in the config files : color management, image file storage, etc
  - when page numbers are specified in the source, it tests whether the globaly produced PDF conforms with these pages specs and warns in case it doesnt (use `-set` option so as to force page numbers) 
  - it updates the PDF when they are out of dates compared to their SLA origin. This ensure the produced concatenated PDF is up to date. The previous PDF version is archived as a `.bak`
  - it concatenates all chapter's PDF into a big PDF.
  - it creates 3 md files for 1) user comments, 2) images, 3) PDF bookmarks (read above)

* with `-set` option, it edits the SLA so it conforms to the specified config.
   * the starting page of each chapter is set to conform with the sources declaration or to the page number of the preceding chapter (see later).
   * The previous .SLA version is archived as a `.bak`. 
   * `-set` doesn't update the PDFs. A later call without `-set` option is required tp produce the updated PDFs.

* Other options ... provide more options ! just ask with `-?` or `-h`

## Page numbering : set and check consistency ##

The project can check or/and set the correct numberging of pages in each file of the project.

When no specific declaration is added to the "sources" variable, the first page of the created book is 1 and each chapter's page numbers follows.
Using the "sources" variable, it's possible to create page numbers jumps and checks :
- adding "n_6" after the name of a chapter enables to check that this chapter is realy 6 pages long as intended. In case of mismatch, an error is issued and the script stops.
- adding "p_13" before the name of a chapter ensures that the starting page number for this chapter is 13. In case of mismatch, a warning (not an error) is issued and the script continues.

Its possible to mix n_ and p_ data, and this makes it possible to do stronger page numbering checks

Examples :
* ```sources="p_1 CoverBegin		p_3 Summary  p_7 InsidePages	p_15	Notes  p_20 CoverEnd"```
* ```sources="n_2 CoverBegin		Summary  n_8 InsidePages	n_5	Notes  p_20 CoverEnd"``` 

So as to take effect into the PDF created files, these pagestart have to be recorded inside each SLA file. So as to do so : call `makebook` with the `-set` option. It will set all chapter's starting page according to `p_xxx` and `n_xxx` declarations. When there is no such declaration, `makebook` counts the pages and ensure the next chapteur begins so it follows previous chapter's last page. 
NB : All pages of all chapters are included in the produced PDF. There is no way to exclude some pages. In case you want some part of your document to be not exported in the book, place it outside of the existing pages !

## Checking PDF files validity ##

`makebook` and `slacheck` checks that the previously created PDF are uptodate. 
When the PDF is older than the SLA, it creates a newer PDF (saving the previously existing one as .bak)
It's possible to avoid this automatic behaviour using `-pdfignore` or `-pdfcheck` options :
- `-pdfignore` : dont check whether pdf exists and dont compare PDF and SLA last edit dates"
- `-pdfcheck` : check PDF, but dont re-create it in case it is older than .SLA

## Ensuring project long term safety and portability ##

slacheck checks that the project has been correctly collected for output : 
it checks that all used images are stored into the local images/ folder

slacheck checks that the fonts are either embeded or subset
An option also exists also to use vectorized versions of the PDFs

## Searching text in whole book

You can either look for a string in the document's textframes or for a string appearing in the SLA document's XML.

* `-find "a string"` searches string in textes of all chapters
- `-xmlfind "CNAME='mystyle'"` searches string in the wholocal le XML of all chapters (technical uses only)

The example makebook script takes care of deleting all conditional hyphens before performing the search (and also the xmlsearch)
Results are displayed and also stored in the .found.tmp file

There is also a `-replace` option

## Project notes and reminders

When editing a project, it happens often that one needs to take notes about some part of a chapter. 
Example :
- "Find higher resolution picture"
- "Add credit and licence"
- "Check numbers"
- "todo : use vectorized logo"

The project manager provides a way of doing so and being automaticaly reminded of these notes :
- in the book text of an existing textframe, use "AAA" or "XXX" next to the text of your note (avoid this with long texts)
- OR create a dedicated small textframe and type your note along with AAA, XXX or PPP prefix

When project's manager `makebook` is called,
- it reminds you of all such notes it finds in the chapters of your book
- it creates a `book_comments.md` summary of notes for all chapters

Example of such a produced comment's summary, for `N77` book :
```
## N77 chapters
# Inline comments

## 10.helene/10.helene.sla
    AAA « I totaly rewrote this part. Can you check ? »
    RRR « Please give more detailed example »

## 20.forêts/20.forêts.sla
    AAA « Check photo's licence »

## 35.creerforet/35.creerforet.sla
    RRR « New version »
```

## Generate table of content and create PDF Bookmarks accordingly

Set the `pdf_bookmark_style` variable in your book config file to one of defined styles 
and `makebook` will list all such bookmarks in a separate book_bookmarks.md file 

Example config : `pdf_bookmark_style="Titre Principal Grand"`

For better rendering, conditional hyphens and end of lines are deleted out of the TOC entry.

Example of created TOC :
```
## N77 chapters
# Bookmarks with 'Titre Principal Grand' style

Page  File                      Title  
1.    100.haies/100.haies       Les haies fruitières
11.   110.leterme/110.leterme   Haies à haute biodiversité
14.   120.helene/120.helene     Le verger de Hélène
17.   130.forêts/130.forêts     Forêt fruitière
```

In case a chapter has no paragraph with `pdf_bookmark_style` style, but you wish it to be part of the TOC, then you can create a dedicated text frame in some hidden place, with a paragraph of this style to record the TOC entry and bookmark name

If you wish to get a PDF bookmark for all of these TOC entry, makebook can create them : call it with `-ab` option ('-ab' stands for `add bookmarks`).
`makebook -ab` will create a PDF bookmark for all TOC entry (= paragraphs with `pdf_bookmark_style` style) that do not have a PDF bookmark yet.

This is only possible for one such bookmark in each chapter.

## Generate image list

Unless the `-noimagelist` option is provided, `makebook` script creates an .md file detailing used images for each chapter.
Inlined images are listed apart.

Example of created `book_imagelist.md` :
```
## N77 chapters
# Bookmarks with 'Titre Principal Grand' style

## 100.haies/100.haies.sla
        images/biodiv_gouv W1.png
        images/elagage-haie.jpeg

## 120.helene/120.helene.sla
        images/CouvN34.jpg
        images/SAM_5451_BRF.JPG
inline  images/planet-food.png

## 130.forêts/130.forêts.sla
        images/couvresol CCBYSA A. Talin.jpg
        images/fig2-17.jpg
```

## Sync : update and harmonise styles, colors or masterpages 

### for the whole book 

Choose a document, eg `masterchap.sla` and use it as a master document for styles, colors and masterpages.
Add `syncmaster="masterchap"` in your book's config file.

Then use various options :
- `makebook -sync -s` : sync all chapter files styles with master's styles
- `makebook -sync -ps` : sync all chapter files paragraph styles with master's paragraph styles
- `makebook -sync -cs` : sync all chapter files character styles with master's character styles
- `makebook -sync -c` : sync all chapter files colors with master's colors
- `makebook -sync -m` : sync all chapter files masterpages with master's masterpage

### for a specific chapter and master file

`slasync` command enables to update chapters accordingly to master document. 

Some paragraph and character styles can be preserved as is. These "localy defined" styles should be named with a specific prefix or suffix.

Example : 
- `slasync -sur spip.nets masterdoc chapter2` : updates defined styles
- `slasync -ps masterdoc chapter2` : updates defined paragraph styles only
- `slasync -c masterdoc chapter2` : updates defined colors
- `-m` option : replaces masterpages    DOES NOT WORK : masterpage names are imported but not their definition

More options for chapters :
- `slasync -keeps loc -cs masterdoc chapter2` : updates styles but keeps all styles whose name contains 'loc' 
- `slasync -a masterdoc chapter2` : updates both styles, masterpages and colors

- `-ns` option : replaces notes styles
- `-nodes <node1|node2|node3>` : replaces all SLA (XML) nodes specified in pipe separated list

See -h option for more

## Produce PDF version for personal printer 

A deskjet printer version doesnt require bleeds. 
`makebook -nobleeds` will produce a PDF without cropmarks and with document's settings 0 bleeds.
This option produces a PDF with a `-nobleeds` suffix.

It's also possible to produce an ink-thrifty and focus-friendly version without images, that will be perfect for proofreading : 
`makebook -noimagess`
This option produces a PDF with a `-noimages` suffix.

## Other options

Try `-?` option for each tool so as to see main options. Mainly makebook example  (to be split in 2 files : makebook and project.config)

# Other

## TODO

* ¿ Optionnaly state that some attribut should remain unchanged when using `-set` option ?
* Create as a dedicated C++ app !


## Rules when proposing a script or push request for this repo
* name of the script should be self-understandable and should give hints as what the script does
* you should provide a detailed description of the intent, required conditions and action of the script, as comments at the script's begining
* same for the push requests
* scripts should provide help on how to use them (-h option)
