<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Mail Handler
 *
 * PHP version 5.3.5
 *
 * @category  MailHandler
 * @package   IIMGroup_Platform
 * @author    Josh Houghtelin <Josh@FindSomeHelp.com>
 * @copyright 2014 IMPACT INTERNATIONAL MARKETING Inc.
 * @license   (C)2014 IMPACT INTERNATIONAL MARKETING Inc.
 * @since     2014/02/26
 * @link      http://www.iimgroup.com
 */

/**
 * Standard Namespace
 */
//namespace MailHandler;

/**
 * MailHandler
 */
class MailHandler
{

    /**
     * @var $this->incomingFolder Full path to file folder where incoming mail is temporarily stored before processing
     */
    var $incomingFolder;

    /**
     * @var $this->storageFolder Full path to file folder where mail is permanently stored
     */
    var $storageFolder;

    /**
     * @var $this->logFile  Log File Full Path+Filename;
     */
    var $logFile;

    /**
     * @var $this->logfileResource  Log File Reference Handler
     */
    var $logfileResource;



    ///////////////////////////////////
    //// CONSTRUCTS
    ///////////////////////////////////

    /**
     * Construct
     */
    public function __construct($incomingFolder,$storageFolder,$logFile)
    {
        $this->setIncomingFolder($incomingFolder);
        $this->setStorageFolder($storageFolder);
        $this->setLogFile($logFile);

        $this->startLogger();
    }

    /**
     * Create Log File Handler
     */
    private function startLogger() {
        $this->logfileResource = fopen($this->logFile,'a+');
    }



    ///////////////////////////////////
    //// DESTRUCTS
    ///////////////////////////////////

    /**
     * Destruct
     */
    public function __destruct()
    {
        $this->stopLogger();
    }

    /**
     * Destroy Log File Handler
     */
    private function stopLogger() {
        fclose($this->logfileResource);
    }



    ///////////////////////////////////
    //// GETTERS & SETTERS
    ///////////////////////////////////

    /**
     * Setter for $this->incomingFolder
     *
     * @param  $incomingFolder
     */
    public function setIncomingFolder($incomingFolder)
    {
        $this->incomingFolder = $incomingFolder;
    }

    /**
     * @return
     */
    public function getIncomingFolder()
    {
        return $this->incomingFolder;
    }

    /**
     * @param $storageFolder
     */
    public function setStorageFolder($storageFolder)
    {
        $this->storageFolder = $storageFolder;
    }

    /**
     * @return mixed
     */
    public function getStorageFolder()
    {
        return $this->storageFolder;
    }

    /**
     * @param  $logFile
     */
    public function setLogFile($logFile) {
        $this->logFile = $logFile;
    }

    /**
     * @return
     */
    public function getLogFile() {
        return $this->logFile;
    }



    ///////////////////////////////////
    //// THE REST OF THE MESS
    ///////////////////////////////////

    /**
     * Write Log Entry Data to File
     *
     * @param string $entry Log Entry Text
     *
     * @return bool Returns true every time becuase I love you. <3 -- and there's nothin u can do about it.
     *      Go ahead.. Delete the return line.  Nevermore elinore...  Nevermore... ^_^
     */
    private function addLogEntry($entry){
        $timestamp = date('Y-m-d H:i:s');
        fwrite($this->logfileResource,$timestamp." ".$entry);

        return true;
    }

    /**
     * Check for New Mail
     */
    public function checkNewMail() {
        // Open Incomming Mail Directory
        $dir = dir($this->incomingFolder);
        while (false !== ($entry = $dir->read())) {
            // Loop through all files within dir & engage the PDF's
            if (strtolower(substr($entry,-3)) == "pdf") {
                //File Extension is 'pdf' get MD5 Hash
                $md5 = md5_file($this->incomingFolder."/".$entry);
                // Check if we alraedy stored & processed the mail piece
                if ($this->checkMailExists($md5)===false) {
                    //Mail Piece has not been stored & processed - do so now
                    $this->storeNewMailpiece($entry);
                } else {
                    // Mail Piece has been stored & processed - burn the original
                    unlink($this->storageFolder.DIRECTORY_SEPARATOR.$entry);
                    // LOG our psychobabble
                    $this->addLogEntry($entry.' was found in permanent storage.  Removing file from incoming mail folder');
                }
            }
        }
    }

    /**
     * Check to see if Mail has already been processed & stored
     *
     * @param $md5  MD5 Hash value of PDF File
     *
     * @return bool True if Mail exists in storage folder false otherwise
     */
    public function checkMailExists($md5) {
        if (is_dir($this->storageFolder.DIRECTORY_SEPARATOR.$md5)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Save mailpiece from incomming folder to permanent storage
     *
     *  1. Get md5 Hash value from mailpiece file
     *  2. Create folder in storage named md5 hash value of mail piece
     *  3. Copy mailpiece to newly created destination
     *  4. Create Log file for mailpiece within newly created folder
     *  5. Log our actions
     *
     * @param string $filename File Name of new Mailpiece to store
     *
     * @return bool     Returns true becuase I <3 you.  What now? Awkward....
     */
    public function storeNewMailpiece($filename) {
        $md5 = md5_file($this->incomingFolder.DIRECTORY_SEPARATOR.$filename);
        

        return true;
    }

}