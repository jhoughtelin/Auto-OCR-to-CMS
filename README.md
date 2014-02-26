# MailHandler
This is the process & processor responsible for turning PDF scanned images of USPS incomming mail in to
searchable resources attached to customer profiles within the CRM.  Amungst other things.

## Dependencies
 - Cuniform
 - ImageMagick
 - GhostScript
 - Linux

# OCR PROCESS

 1. Convert PDF -> JPEG
        [IMAGEMAGICK] ﻿convert -density 300 -depth 8 -quality 85 trf_with_check.pdf trf_with_check.pdf.jpg
        [GHOSTSCRIPT] gs -SDEVICE=tiffg4 -r300x300 -sOutputFile="trf_with_check-%04d.tiff" -dNOPAUSE -dBATCH -- "trf_with_check.pdf"
