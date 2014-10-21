A scribus project manager using shell scripts
=============================================

Scribus project management tools with shell scripts.

Scribus doesnt provide any project management tools. These shell scripts mainly aim to compensate this.

## What is a Scribus project
A scribus project is a book or a magazine made out of multiple scribus files.
Each scribus file is a chapter, an article, a part.
All have to be concatenated to create the whole book.
All share some common properties like size of pages, styles or color profiles.

## What does a  Scribus project manager ?
A Scribus project manager is a tool that help manage multiple scribus files that are parts of a same project.

Particularly it helps
* checking scribus produced files and PDFs before sending them to the printer
* producing the concatenated PDF

Some day, it could bring help to clean styles and share styles or objects accross files (compensate for scribus bugs with styles).

## What is this repo

This repo contains the scripts i use to create the quaterly Passerelle Eco magazine.
This magazine is made of 8 to 20 parts with same page size, sharing lots of common styles, some parts being very differents.

## Available scripts

* slacheck : performs all possible checks and reports issues before sending PDF to printer
* checkimages : checks that the used images are all stored in 'images' subfolder. This script is bound to be refactored and made more simple.
* slacheckfonts : checks that the fonts are all embeded or subseted. 
* makbook : call relevant tools on relevant files and create final PDF (not published yet)

Lower level tools :
* slacheckattr : checks the value of some xml attribute in the scribus file
* strtrim : basic string trim

## todo / could be done

* more attributes checked in slacheck
* separated config files to define 
** the checklist
** the list of files included in the project
* sync of masterdocument's styles toward project files

## Rules when proposiing a script for this repo
* name of the script should be self-understandable and should give hints as what the script does
* you should provide a detailed description of the intent, required conditions and action of the script, as comments at the script's begining
* scripts should provide help on how to use them (-h option)
