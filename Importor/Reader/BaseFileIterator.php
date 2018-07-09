<?php

include_once 'Importor\Reader\FileIterator.php';

abstract class BaseFileIterator implements FileIterator {

    /**
     * The file types that this file iterator supports
     * @var array
     */
    protected $readableFileTypes = array();

    protected $importorInfo = array();

    /**
     * The filename
     * @var string
     */
    protected $filename;

    /**
     * Construct the iterator using filename
     * @param string $filename
     */
    public function __construct($filename) {        
        $this->filename = $filename;
    }

    public function setImportorInfo($importorInfo){
        $this->importorInfo = $importorInfo;
    }

    public function getImportorInfo(){
        return $this->importorInfo;
    }

    /**
     * To check if given mime is readable by this reader
     * @return boolean
     */
    public function isReadable() {       
        $mime = $this->getMimeType($this->filename);
        return in_array($mime, $this->readableFileTypes);
    }

    /**
     * Get the mime-type of a file
     *
     * @param string $path
     * @return string|boolean The mime-type or false on failure
     */
    protected function getMimeType($path) {
        $mime = false;
        if (is_file($path)) {

            if (!stristr(ini_get("disable_functions"), "shell_exec")) {
                // http://stackoverflow.com/a/134930/1593459
                $file = escapeshellarg($path);
                $mime = trim(shell_exec("file -b --mime-type " . $file));
            }

            if (empty($mime) && function_exists("finfo_file")) {
                $fInfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($fInfo, $path);
                finfo_close($fInfo);
            }

            if (empty($mime) && function_exists("mime_content_type")) {
                $mime = mime_content_type($path);
            }
        }
        return $mime;
    }
}