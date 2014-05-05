__ This is an incomplete app that never left the idea stage __

# MailHandler
This is the process & processor responsible for turning PDF scanned images of USPS incomming mail in to
searchable resources attached to customer profiles within the CRM.  Amungst other things.

## Dependencies
 - Cuniform
 - ImageMagick
 - GhostScript
 - Linux

# Mailpiece PROCESS
 1. Recieve Mailpiece
 1. Scan Mailpiece
     - Single PDF File output
     - Multi-Page PDF including Envelope & Content
     - PDF File saved to 'Incomming Mail' folder
 1. [CRON] Import New Mail
     - Scripted Event runs against all available files in Incomming Mail folder at once.
     - MD5 Hash of PDF File generated
     - Folder in Mail Storage location created using MD5 Hash as Folder Name
     - PDF File copied to Storage Folder
     - New Mailpiece added to ProcessQueue
 1. PROCESS MailPiece
     - Scripted event runs against one Mailpiece in the ProcessQueue at a time.

     1. Convert PDF -> JPEG
        convert -density 300
        [IMAGEMAGICK] ﻿convert -density 300 -depth 8 -quality 85 trf_with_check.pdf trf_with_check.pdf.jpg
        [GHOSTSCRIPT] gs -SDEVICE=tiffg4 -r300x300 -sOutputFile="trf_with_check-%04d.tiff" -dNOPAUSE -dBATCH -- "trf_with_check.pdf"
