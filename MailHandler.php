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
 * @TODO    Verify during initial class construct stage that all necessary files & folders are writeable.
 *          - Log File
 *          - Storage Folder
 *          - Incomming Folder
 *          - Process Queue File
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
    var $logFile = "MailHandler.log";

    /**
     * @var $this->logfileResource  Log File Reference Handler
     */
    var $logfileResource;

    /**
     * @var $this->processQueueFile     Queue list of mail pieces ready to be processed through OCR
     */
    var $processQueueFile =  "ProcessQueue";

    /**
     * @var $this->logLevel sets the level of detail output log.
     *
     * 0 = LOGGING DISABLED
     * 1 = Minimum Detail
     * 2 = ?
     * 3 = Maximum Detail
     */
    var $logLevel = 1;

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

    /**
     * Log Level Setter
     *
     * @param  $logLevel
     *
     * @return true cuz I <3 u
     */
    public function setLogLevel($logLevel)
    {
        if(is_integer($logLevel) && ($logLevel >= 0) && ($logLevel <= 3)){
            $this->logLevel = $logLevel;
        } else {
            // Value passed to set log level was not understood.
            $this->addLogEntry('Failed to change Log Detail Level.  Value "'.$logLevel.'" was is not an acceptable value.',1);
            return false;
        }

    }



    ///////////////////////////////////
    //// THE REST OF THE MESS
    ///////////////////////////////////

    /**
     * Add Log Entry
     *
     * @param string $entry    Log Entry Text
     * @param int    $severity Log Entry Severity Level (Lower Number = Higher Severity)
     *
     * @return bool Returns true every time becuase I love you. <3 -- and there's nothin u can do about it.
     *      Go ahead.. Delete the return line.  Nevermore elinore...  Nevermore... ^_^
     */
    private function addLogEntry($entry,$severity=1){
        $timestamp = date('Ymd-His');
        if($severity <= $this->logLevel){
            // Entry written to log file
            fwrite($this->logfileResource,$timestamp." ".$entry."\r\n");
            return true;
        } else {
            // Entry not written to log file
            return false;
        }
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
                    //unlink($this->storageFolder.DIRECTORY_SEPARATOR.$entry);
                    // LOG our psychobabble
                    //$this->addLogEntry($entry.' was found in permanent storage.  Removing file from incoming mail folder');
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
        if($this->checkMailExists($md5)){
            // If the Mailpiece already exists in storage - exit process
            return false;
        }else{
            // Mailpiece does not exist in storage - Create container
            if(mkdir($this->storageFolder.DIRECTORY_SEPARATOR.$md5)){
                // Directory Created
                $this->addLogEntry('New Mailpiece storage container created: '.$this->storageFolder.DIRECTORY_SEPARATOR.$md5,1);
                // Create Mailpiece log file within storage folder
                $this->createMailpieceLogFile($md5);
                // COPY Mailpiece to Storage Container
                if(copy($this->incomingFolder.DIRECTORY_SEPARATOR.$filename,$this->storageFolder.DIRECTORY_SEPARATOR.$md5.DIRECTORY_SEPARATOR.$filename)){
                    // Mailpiece copied successfully
                    $this->addLogEntry('Mailpiece "'.$filename.'" copied to storage container "'.$md5.'"',3);
                    $this->addMailpiece2ProcessQueue($filename,$md5);
                } else {
                    // FAILED TO COPY Mailpiece
                    $this->addLogEntry('FAILED to copy Mailpiece "'.$filename.'" to storage folder "'.$md5.'"',1);
                    return false;
                }

            } else {
                // FAILED to create directory
                $this->addLogEntry('Failed to create Mailpiece storage folder: '.$this->storageFolder.DIRECTORY_SEPARATOR.$md5,1);
                return false;
            }
        }

        return true;
    }

    /**
     * Add Stored Mailpiece to OCR Processing Queue
     *
     * @param $filename File name of Mailpiece to be processed (this hsould have PDF extension)
     * @param $folder   Full Path to folder Mailpiece is permentaently stored in
     *
     * @todo Add error handling if writing to process queue file fails.
     */
    public function addMailpiece2ProcessQueue($filename,$folder) {
        $resource = fopen($this->processQueueFile,'a+');
        fwrite($resource,$this->storageFolder.DIRECTORY_SEPARATOR.$folder.",".$filename."\r\n");
        fclose($resource);
        $this->addLogEntry("Added Mailpiece \"".$filename."\" in folder \"".$folder."\" to process queue.",3);
    }


    /**
     * Create log file within Mailpiece storage container
     *
     * @param $md5
     *
     * @return true becuase I <3 u! <3 <3 <3...    You're seriously weird.
     */
    private function createMailpieceLogFile($md5) {
        $resource = fopen($this->storageFolder.DIRECTORY_SEPARATOR.$md5.DIRECTORY_SEPARATOR."Process.log","a+");
        fwrite($resource,date('Ymd-His')." LOG FILE CREATED.\r\n");
        fclose($resource);
        $this->addLogEntry("Created Mailpiece Processlog Log File");
        return true;
    }
}