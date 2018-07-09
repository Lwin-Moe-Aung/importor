<?php

/**
 * Interface FileIterator
 * The file iterator class that can let FileImportSource to iterate over a file
 * @package Import\ImportSource\ImportSource\Reader
 */
interface FileIterator{

    /**
     * Construct file import reader
     * @param string $filename
     */
    public function __construct($filename);

    /**
     * To check if given mime is readable by this reader
     * @return boolean
     */
    public function isReadable();

    /**
     * Read with the importer config
     * @param array $importer_conf
     */
    public function read($importer_conf);

    /**
     * Get the current row
     * @return int
     */
    public function row();

    /**
     * Get the current item and advance the index
     * @return array
     */
    public function current();

    /**
     * Check whether we have next item
     * @return boolean
     */
    public function hasNext();

     /**
     * Get ImportorInfo
     * @return array
     */
    public function getImportorInfo();
    /**
     * Set ImportorInfo
     * 
     */
    public function setImportorInfo($importorInfo);
}