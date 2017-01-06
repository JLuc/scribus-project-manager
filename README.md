A scribus project manager using shell scripts
=============================================

Scribus project management tools with shell bash scripts.

**Scribus doesnt provide any project management tools. These shell scripts mainly aim to compensate this.**

## What is a Scribus project
A scribus project is a book or a magazine made out of multiple scribus files.
Each scribus file is a chapter, an article, a part.
All have to be concatenated to create the whole book.
All share some common properties like size of pages, styles or color profiles.

## What does a scribus-project-manager *manage* ?
A Scribus project manager is a tool that help manage multiple scribus files that are parts of a same project.

Particularly it helps
* check scribus produced files and PDFs before sending them to the printer.
* edit SLAs so they conform to the project standard.
* produce the concatenated PDF.

Some day, it could help to clean styles and share styles or objects accross files (compensate for scribus bugs with styles).

## What is this repository

This repo contains the scripts I use to create the quaterly [Passerelle Eco](http://www.passerelleco.info/) magazine. This magazine is made of 8 to 20 parts with same page size, sharing lots of common styles, some parts being very differents. Of course, the scripts can be used for other projects having differents characteristics.

### Main tools

* **slacheck** : 
*   - performs all possible checks and reports issues before sending PDF to printer. 
*   - Optionnaly edit the SLA so it conforms to the defined standard.  
* **slacheckimages** : checks that the used images are all stored in 'images' subfolder.  
* **slacheckfonts** : checks that all used fonts are either embeded or subseted.  
* **makbook** : call relevant tools on relevant files and create final PDF (no generic makbook is published yet, but an example makbook file is available).  

### Lower level scripts
* **slacheckattr** : checks the value of some xml attribute in the scribus file.  
* **slasetattr** : set some XML attr in the SLA file to its correct value.  
   - various backup are created : .sla.first.sla, .sla.last.sla and a hiden .filename.sla.tmp.bak  
* **strtrim** : basic string trim.  

### Environnement tools
This project is primarily created and used on Ubuntu 14.04.

It includes the following tools, that might depend on the OS :
* **sla_icon** : adds specific icon on SLA file(s) for nautilus view  
* **clean_icon** : removes user-added icon on file or folder

These scripts can be made callable via nautilus action's menus.

## Configuration data

Configuration is value for **page sizes**, **bleeds**, **marks**, **color profiles** and all other book creation issues etc...

The configuration values are parmeters for slacheck script and can be set
- at global tool level in the slacheck.defaultconfig file
- at project level in the project related local slacheck.config file
- at file level in the file-related local filename.config file

You have to edit the defaultconfig file or create a project config file so as to adapt the values to your project's standard. In case some of the file of your project requires different settings, then you have to create the required file-related restricted settings.

Doing this requires 
- edit a correct SLA that fit your standards, 
- look for all the attributes names and values there 
- and set them as the goal for your project or file in the corresponding config file

## todo / could be done

Next step and blocking release issues :
* Create separated config files for :
- the list of files included in the project. This will enable to release a generic makebook script.
- DONE the checklist and the values for each project-defining attribute. Probably using some table structure with attribute, value, message string, other options fot this attribute. This will enable to make slacheck script entirely generic.
 
Other usefull features :
* DONE automatically updates starting page-numbers for all documents in project (WIP)
* optionnaly state that some attribut should remain unchanged when using -set option
* use one of the document as the `masterdocument` and use it for smart synchronization for
   * masterdocuments 
   * styles (optionnaly state which styles should NOT be synced)

Other not so usefull features :
* smart merge of SLAs (manage styles, masterpages and other conflicts) (usefull since merge of SLA is one of scribus Most Annoying Bug, but complex and not so usefull with this project manager)


## Rules when proposing a script or push request for this repo
* name of the script should be self-understandable and should give hints as what the script does
* you should provide a detailed description of the intent, required conditions and action of the script, as comments at the script's begining
* same for the push requests
* scripts should provide help on how to use them (-h option)
