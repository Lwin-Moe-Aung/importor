<?php
	require_once __DIR__ . '/DBSource.php';

	class FileImportor {
	    protected $fileiterator;
	    
	    public function __construct(FileIterator $fileiterator)
	    {
	        $this->fileiterator = $fileiterator;
	        return $this->fileiterator;
	    }	

	    public function setImportorInfo($importorInfo){
	    	$this->fileiterator->setImportorInfo($importorInfo);
	    }   
	
	    public function read(){

	        $importorInfo = $this->fileiterator->getImportorInfo();	 	    	
	    	 	
	    	
    		$this->fileiterator->read();
    		$headers = $this->fileiterator->current();	    		
    		$filtered_headers = array_filter($headers, array($this, 'isAllEmpty'));

    		if (!empty($filtered_headers)) {

    			 array_walk($headers, function (&$h, $i) {
                        $h .= ' (Col ' . ($i + 1) . ')';
                    });

    			 $dbobj = new DBSource();

    			 do {
                        $data = $this->fileiterator->current();

                        if($data != false){
                            $row = $this->fileiterator->row();                             
                            // Check if array is all empty or not
                           
                            $filtered = array_filter($data, array($this, 'isAllEmpty'));
                            
                            if (!empty($filtered)) {
                                //Trim data
                                $data = array_map('trim', $data);


                                 $dbobj->insertRecord(
                                		array_combine($headers, $data), array(
		                                    'row' => $row,
		                                    'filename' => $importorInfo["filename"]
		                                ), $importorInfo 
                                );

                              
                            }
                        }
                    } while ($this->fileiterator->hasNext());
    		}
    		
	    	
	    	return true;
	    }
	   
	    /**
	     * Check if all array element empty or not
	     * @param string /integer $d
	     * @return boolean
	     */
	    private function isAllEmpty($d) {
	        if (isset($d)) {
	            return '' !== trim($d);
	        }
	        return false;
	    }

	    
	   
	}
?>