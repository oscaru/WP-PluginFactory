<?php
// php-export-data by Eli Dickinson, http://github.com/elidickinson/php-export-data

/**
How USE IT 

// When executed in a browser, this script will prompt for download 
// of 'test.xls' which can then be opened by Excel or OpenOffice.

require 'php-export-data.class.php';

// 'browser' tells the library to stream the data directly to the browser.
// other options are 'file' or 'string'
// 'test.xls' is the filename that the browser will use when attempting to 
// save the download
$exporter = new ExportDataExcel('browser', 'test.xls');

$exporter->initialize(); // starts streaming data to web browser

// pass addRow() an array and it converts it to Excel XML format and sends 
// it to the browser
$exporter->addRow(array("This", "is", "a", "test")); 
$exporter->addRow(array(1, 2, 3, "123-456-7890"));

// doesn't care how many columns you give it
$exporter->addRow(array("foo")); 

$exporter->finalize(); // writes the footer, flushes remaining data to browser.

exit(); // all done

*/


namespace PluginCore\libraries\ExportData;

/**
 * ExportData is the base class for exporters to specific file formats. See other
 * classes below.
 */
abstract class ExportData {
	protected $exportTo; // Set in constructor to one of 'browser', 'file', 'string'
	protected $stringData; // stringData so far, used if export string mode
	protected $tempFile; // handle to temp file (for export file mode)
	protected $tempFilename; // temp file name and path (for export file mode)

	public $filename; // file mode: the output file name; browser mode: file name for download; string mode: not used

	public function __construct($exportTo = "browser", $filename = "exportdata") {
		if(!in_array($exportTo, array('browser','file','string') )) {
			throw new Exception("$exportTo is not a valid ExportData export type");
		}
		$this->exportTo = $exportTo;
		$this->filename = $filename;
	}
	
	public function initialize() {
		
		switch($this->exportTo) {
			case 'browser':
				$this->sendHttpHeaders();
				break;
			case 'string':
				$this->stringData = '';
				break;
			case 'file':
				$this->tempFilename = tempnam(sys_get_temp_dir(), 'exportdata');
				$this->tempFile = fopen($this->tempFilename, "w");
				break;
		}
		
		$this->write($this->generateHeader());
	}
	
	public function addRow($row) {
		$this->write($this->generateRow($row));
	}
	
	public function finalize() {
		
		$this->write($this->generateFooter());
		
		switch($this->exportTo) {
			case 'browser':
				flush();
				break;
			case 'string':
				// do nothing
				break;
			case 'file':
				// close temp file and move it to correct location
				fclose($this->tempFile);
				rename($this->tempFilename, $this->filename);
				break;
		}
	}
	
	public function getString() {
		return $this->stringData;
	}
	
	abstract public function sendHttpHeaders();
	
	protected function write($data) {
		switch($this->exportTo) {
			case 'browser':
				echo $data;
				break;
			case 'string':
				$this->stringData .= $data;
				break;
			case 'file':
				fwrite($this->tempFile, $data);
				break;
		}
	}
	
	protected function generateHeader() {
		// can be overridden by subclass to return any data that goes at the top of the exported file
	}
	
	protected function generateFooter() {
		// can be overridden by subclass to return any data that goes at the bottom of the exported file		
	}
	
	// In subclasses generateRow will take $row array and return string of it formatted for export type
	abstract protected function generateRow($row);
	
}

	
