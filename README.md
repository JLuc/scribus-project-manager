A scribus project manager using shell scripts
=============================================

Scribus project management tools with shell bash scripts.

Scribus doesnt provide any project management tools. These shell scripts mainly aim to compensate this.

## What is a Scribus project
A scribus project is a book or a magazine made out of multiple scribus files.
Each scribus file is a chapter, an article, a part.
All have to be concatenated to create the whole book.
All share some common properties like size of pages, styles or color profiles.

## What does a  Scribus project manager ?
A Scribus project manager is a tool that help manage multiple scribus files that are parts of a same project.

Particularly it helps
* check scribus produced files and PDFs before sending them to the printer
* edit SLAs so they conform to the project standard
* produce the concatenated PDF

Some day, it could help to clean styles and share styles or objects accross files (compensate for scribus bugs with styles).

## What is this repo

This repo contains the scripts i use to create the quaterly Passerelle Eco magazine.
This magazine is made of 8 to 20 parts with same page size, sharing lots of common styles, some parts being very differents.

### Main tools

* slacheck : performs all possible checks and reports issues before sending PDF to printer. Optionnaly edit the SLA so it conforms to the defined standard
* slacheckimages : checks that the used images are all stored in 'images' subfolder.
* slacheckfonts : checks that all used fonts are either embeded or subseted
* makbook : call relevant tools on relevant files and create final PDF (not published yet)

### Lower level scripts
* slacheckattr : checks the value of some xml attribute in the scribus file
* slasetattr : set some XML attr in the SLA file to its correct value
* strtrim : basic string trim

### Environnement tools
This project is primarily created and used on Ubuntu 14.04
It includes the following tools, that might depend on the OS :
* sla_icon : adds specific icon on SLA file(s) for nautilus view
* clean_icon : removes user-added icon on file or folder

## Configuration data

Configuration is : page sizes, bleeds, marks, color profiles, etc

As for now, the configuration values are set inside slacheck script file.
You have to edit this file so as to adapt the values to your project's standard.
Doing this requires editing a correct SLA that fit your standards, and look for the values there so as to set them as the "standard" goal for your project.

## todo / could be done

* automatically updates start page-numbers for all documents in project
* optionnaly state which attribut have to be corrected and which should remain unchanged when using -set option
* separated config files to define 
** the checklist and the values for each project-defining attribute. Probably using some table structure with attribute, value, message string, other options fot this attribute
** the list of files included in the project
* smart sync of masterdocument's styles toward project files
* smart merge of SLAs (manage styles, masterpages and other conflicts)
* more attributes checked in slacheck : which ?

## Rules when proposiing a script for this repo
* name of the script should be self-understandable and should give hints as what the script does
* you should provide a detailed description of the intent, required conditions and action of the script, as comments at the script's begining
* scripts should provide help on how to use them (-h option)
