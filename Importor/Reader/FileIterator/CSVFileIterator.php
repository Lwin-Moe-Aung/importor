<?php

require_once 'Importor\Reader\BaseFileIterator.php';

/**
 * Class CSVFileIterator
 * Handling merely CSV file types
 *
 * Inspired from https://jonlabelle.com/snippets/view/php/csv-file-iterator-class
 *
 * @package Import\ImportSource\ImportSource\Reader\File
 */
class CSVFileIterator extends BaseFileIterator {

    protected $readableFileTypes = array(
        'text/csv',
        'text/x-asm',
        'text/x-c',
        'application/csv',
        'text/plain'
    );

    /**
     * The file handle
     * @var resource
     */
    private $file;

    /**
     * Cache the # of rows, in case they call it again
     * @var int
     */
    private $count = null;

    /**
     * The row counter
     * @var int
     */
    private $position = 0;


    /**
     * Read CSV data
     *
     * @param array $importer_conf
     */
    public function read($importer_conf = null) {
        //Convert the encoding
       // exec('sh ' . FILE_CONV . ' ' . escapeshellarg($this->filename) . ' ' . $importer_conf['encoding']);

        if (empty($this->file)) {
            $this->file = fopen($this->filename, 'r');           
        }
        
    }

    /**
     * Count # of rows in the CSV file. We can't use line breaks because a cell can
     * contains linebreaks, so line-break way of checking data is wrong and avoided.
     *
     * We use fgetcsv for a more proper way of implementing it. Takes longer time but
     * the result is always correct.
     *
     * @return int number of lines
     */
    public function count() {
        if ($this->count === null) {
            $lines = 0;            
            $fp = fopen($this->filename, 'rb');
            while (fgetcsv($fp)) {                
                ++$lines;
            }
            fclose($fp);
            $this->count = $lines;
        }
        return $this->count;
    }

    /**
     * Return the current element AND advance
     * @return array one row of data
     */
    public function current() {
        $data = fgetcsv($this->file);
        ++$this->position;
        return $data;
    }

    /**
     * Check whether we have next item
     * @return boolean
     */
    public function hasNext() {
        return !feof($this->file);
    }

    /**
     * Return the key of the current element
     * @return mixed scalar on success, or null on failure.
     */
    public function row() {
        return $this->position;
    }
}
