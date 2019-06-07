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

Particularly it helps
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
* looking for strings in the text or in the XML structure and replace them with some other text
* synchronise chapter's styles, masterpages or color definitions with those of a master document

And more.

## What is this repository

This repo contains the scripts I use to create the quaterly [Passerelle Eco](http://www.passerelleco.info/) magazine. This magazine is made of 8 to 20 parts with same page size, sharing lots of common styles, but some parts being very differents. 
After creating dedicated config files, the scripts can be used for other projects having differents characteristics.

### Main tools

* **slacheck** : 
*   - performs all possible checks and reports issues before sending PDF to printer. 
*   - Optionnaly edit the SLA so it conforms to the defined standard.  
* **slacheckimages** : checks that the used images are all stored in 'images' subfolder.  
* **slacheckfonts** : checks that all used fonts are either embeded or subseted.  

Also :
* **mak book script** : call relevant tools on relevant files and create final PDF (no generic makbook is published yet, but an example makbook file is available).  WARNING : not uptodate but see example with it.

### Lower level scripts
* **slacheckattr** : checks the value of some xml attribute in the scribus file
* **slasetattr** : set some XML attr in the SLA file to its correct value
   - various backup are created : .sla.first.sla, .sla.last.sla and a hiden .filename.sla.tmp.bak  
* **strtrim** : basic string trim

### Environnement tools
This project has been created and used on Ubuntu 14.04, 16.04, 17.10, 18.04

It includes the following tools, that might depend on the OS :
* **sla_icon** : adds specific icon on SLA file(s) for nautilus view  
* **clean_icon** : removes user-added icon on file or folder

These scripts can be made callable via nautilus action's menus.

Also :
* **shellcolors** : create variables to easily bring colors to console output using escape sequences. it is used to issue error or warning messages, and search result or such.

## Set of files composing the project ##

The set of files is described in a string made out of all filenames in their appearing order.  

As easy as :
`sources = "filename1 filename2 ... lastfilename"`

Rq : The `.sla` extension, being a constant of a scribus filename, must be omited.

Since each chapter SLA file is usually the end result of a work requiring more documents, images, files, it may requires a whole folder to store these documents, and the SLA file is only one of them.

So as to accomodate this, 3 possible standard organisation of files in the project folder are proposed and make project description more easy :
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

Example where all SLA files are stored in filename/PAO/filename.sla, so other subfolders exist in filename/ :
```
# more complex store pattern where SLA files are in PAO subfolder of each chapter folder
sources="CoverBegin		Summary  InsidePages		Adverts  CoverEnd"
sourcespattern=2
```

Page number specifications can also be embeded in this source spec. See above in dedicated part of this readme.

## Configuration data

Configuration is a set of values for **page sizes**, **bleeds**, **marks**, **color profiles** and all other book creation issues that are required for your book or magazine etc... None of this is require, it all depends of what you need to check and ensure.

These config are attributes of the SLA XML file tags.
Config files are shell scripts that set variables : usualy there is one variable for each XML attribute you need to check/set.

The configuration values are parameters for slacheck script and can be set
- at global tool level in the slacheck.defaultconfig file
- at project level in the project related local slacheck.config file
- at file level in the file-related local filename.config file

So as to create a config file from scratch you'll have to
- open an SLA with a text editor and understand its structure. 
- findout which attributes you need.
- findout their correct values
- create a config file composed with all "attribute=value" settings.

The default config file has done all this tedious job and you can also simply look at it and edit a copy in you work space so as to make it fit your need. It will be rather simple.

In case some of the file of your project requires different settings, then you have to create the required file-related restricted settings.
Doing this requires 
- edit a correct SLA that fit your standards, 
- look for all the attributes names and values there 
- and set them as the goal for your project or file in the corresponding config file

So as to check whether the SLA document have the correct settings, just launch the script.
In case an error is detected, you can fix it and set the the SLA parameters to the correct (project or document specific) specified values, use the `-set` option.

# Running

The `makbook` script does run `slacheck` on each file of the source and performs various tests and actions, depending on the command line or config files options.

* Default : 
  - it does test whether files and project conforms to the standards described in the config files : color management, image file storage, etc
  - when page numbers are specified in the source, it tests whether the globaly produced PDF conforms with these pages specs.
  - it updates the PDF when they are out of dates compared to their SLA origin. This ensure the produced concatenated PDF is up to date.
  - it concatenates all chapter's PDF into a big PDF.

* with `-set` option, it edits the SLA so it conforms to the specified config. When doing so, it doesn not update the PDFs. A later call without `-set` option will produce the updated PDFs.

* Other options ... provide more options ! just ask with `-?`

## Page numbering ##

The project can check and set the correct numberging of pages in each file of the project.
2 ways to do so for each file of the project : 
- specify the number of pages of the file, using n_6 (for a 6 pages document)
- specify the starting page number for the file, using p_13 (for a document starting on page 13)

Equivalent examples :
* ```sources="p_1 CoverBegin		p_3 Summary  p_7 InsidePages	p_15	Notes  p_20 CoverEnd"```
* ```sources="n_2 CoverBegin		n_4 Summary  n_8 InsidePages	n_5	Notes  p_20 CoverEnd"```

So as to set the correct starting page numbering *inside* the sla file, use `-set` option

Note : 
* its possible to mix n_ and p_ data, and this makes it possible to do stronger page numbering checks
* in case some page numbering is not valid, slacheck issues a warning

## Checking PDF files validity ##

slacheck checks that the previously created PDF are uptodate. 
When the PDF is older than the SLA, then it creates a newer PDF (saving the previously existing one as .bak)
It's possible to avoid this automatic behaviour using -pdfignore or -pdfcheck options :
- -pdfignore : dont check whether pdf exists and dont compare PDF and SLA last edit dates"
- -pdfcheck : check pdf, but dont re-create it in case required"

## Ensuring project long term safety and portability ##

slacheck checks that the project has been correctly collected for output : 
it checks that all used images are stored into the local images/ folder

slacheck checks that the fonts are either embeded or subset
An option also exists also to use vectorized versions of the PDFs

## Searching text in whole book

You can either look for a string in the document's textframes or for a string appearing in the SLA document's XML.

* `-find "a string"` searches string in textes of all chapters
- `-xmlfind "CNAME='mystyle'"` searches string in the whole XML of all chapters (technical uses only)

The example makbook script takes care of deleting all conditional hyphens before performing the search (and also the xmlsearch)
Results are displayed and also stored in the .found.tmp file

## Project notes and reminders

When editing a project, it happens often that one needs to take notes about some part of a chapter. 
Example :
- "Find higher resolution picture"
- "Add credit and licence"
- "Check numbers"
- "todo : use vectorized logo"

The project manager provides a way of doing so and being automaticaly reminded of these notes :
- in the book text of an existing textframe, use "AAA" or "XXX" next to the text of your note (avoid this with long texts)
- OR create a dedicated small textframe and type your note along with AAA or XXX prefix
- when project's manager `makbook` is called, it reminds you of all such notes it finds in the chapters of your book

## NEW : update chapters with master

Choose a document and use it as master document for styles, colors and masterpages

`slasync` command enables to update chapters accordingly to master document. 

Some paragraph and character styles can be preserved as is. These "localy defined" styles should be named with a specific prefix or suffix.

Example : 
- `slasync -s masterdoc chapter2` : updates defined styles
- `slasync -ps masterdoc chapter2` : updates defined paragraph styles only
- `slasync -c masterdoc chapter2` : updates defined colors
- `slasync -a masterdoc chapter2` : updates both styles, masterpages and colors
- `slasync -keeps loc -cs masterdoc chapter2` : updates styles but keeps all styles whose name contains 'loc' 

See -h option for more

WORK IN PROGRESS : 
- slasync has not yet been thoroughfully tested and more features have to be coded
- as for now, slasync doesnt change the chapter document at all : it produces a result.sla file out of the synced chapter
- slasync should be called from within mak script so as to possibly apply to all chapters of a book project

## Other options

Try `-?` option for each tool so as to see main options. Mainly makbook example  (to be split in 2 files : makbook and project.config)

#Other

## could be done

Next steps :
* Create separated config files for the list of files included in the project so as to release a generic makebook script
* Create as a dedicated C++ app

Other usefull features :
* import or overwrite some specific style in all parts of the project
* use one of the document as the `masterdocument` and use it for smart synchronization for
   * masterdocuments 
   * styles (optionnaly state which styles should NOT be synced)
* optionnaly state that some attribut should remain unchanged when using -set option

Other not so usefull features :
* smart merge of SLAs (manage styles, masterpages and other conflicts) (usefull since merge of SLA is one of scribus Most Annoying Bug, but complex and not so usefull with the help of this project manager)

## Rules when proposing a script or push request for this repo
* name of the script should be self-understandable and should give hints as what the script does
* you should provide a detailed description of the intent, required conditions and action of the script, as comments at the script's begining
* same for the push requests
* scripts should provide help on how to use them (-h option)
