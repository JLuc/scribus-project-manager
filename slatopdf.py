import os
import scribus

if scribus.haveDoc() :
    filename = os.path.splitext(scribus.getDocName())[0]
    pdf = scribus.PDFfile()
    pdf.file = filename+".pdf"
    pdf.version = 15
    pdf.save()
else :
    print("Error : no file open")
