<?php

require_once __DIR__ . '/CSVFileIterator.php';


/**
 * Class XLSFileIterator
 * Handling XLS XLSX file types
 *
 * @package Import\ImportSource\ImportSource\Reader\File
 */
class XLSFileIterator extends CSVFileIterator {

    protected $readableFileTypes = array(
        "application/vnd.ms-office",
        "application/vnd.ms-excel",
        "application/msexcel",
        "application/x-msexcel",
        "application/x-ms-excel",
        "application/x-excel",
        "application/x-dos_ms_excel",
        "application/xls",
        "application/x-xls",
        "application/octet-stream",
        //mime for XLS/XLSX in "file -bi"
        "application/zip",
        //http://stackoverflow.com/questions/6595183/docx-file-type-in-php-finfo-file-is-application-zip
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
    );
    
    const SOFFICE_CONVERTER_SHELL = 'soffice --headless --convert-to "csv:Text - txt - csv (StarCalc):44,34,76,1,1/1" ';
    
    /**
     * Read XLS data
     *
     * @param array $importer_conf
     * @throws \Exception might throw exception from PHPExcel
     */
    public function read($importer_conf = null) {
        $outDir = '--outdir D:\wamp\www\import\csv ';        
        
        exec(self::SOFFICE_CONVERTER_SHELL.$outDir.$this->filename);
        $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $this->filename);
        $convertedFile = $withoutExt.'.csv'; 

        if(file_exists($convertedFile)) {
            rename($convertedFile, $this->filename);                    
            parent::read();
        }
    }
}
