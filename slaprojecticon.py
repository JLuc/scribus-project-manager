#!/usr/bin/python

import os
from os.path import join

cwdir = str(os.popen('pwd').readline()).replace('\n','')

for root, dirs, files in os.walk(cwdir): 
	for name in files:					# name est le nom du fichier, root est le chemin sans le / final
		if name.endswith('.sla'):
			os.system('gvfs-set-attribute "'+root+'/'+name+'" metadata::custom-icon file:///home/jluc/dev/scribus/svn/resources/icons/other/scribus-icon.svg')
