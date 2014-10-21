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
A Scribus project manager is a tool 
* that help manage multiple scribus files that are parts of a same project
* that make scribus even more powerfull
* that compensate for scribus bugs or lack of features

Particularly it helps
[X] checking scribus produced files and PDFs before sending them to the printer
[X] producing the concatenated PDF
[ ] cleaning styles, sharing styles or objects accross files

## What is this repo

This repo contains the scripts i use to create the Passerelle Eco magazine.
This magazine is made of 8 to 20 parts with same page size, sharing lots of common styles, some parts being very differents.

## Rules when proposiing a script for this repo
* name of the script should be self-understandable and should give hints as what the script does
* you should provide a detailed description of the intent, required conditions and action of the script, as comments at the script's begining
* scripts should provide help on how to use them (-h option)
