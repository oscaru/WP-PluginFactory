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
 * ExportDataCSV - Exports to CSV (comma separated value) format.
 */
class ExportDataCSV extends ExportData {
	
	function generateRow($row) {
		foreach ($row as $key => $value) {
			// Escape inner quotes and wrap all contents in new quotes.
			// Note that we are using \" to escape double quote not ""
			$row[$key] = '"'. str_replace('"', '\"', $value) .'"';
		}
		return implode(",", $row) . "\n";
	}
	
	function sendHttpHeaders() {
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=".basename($this->filename));
	}
}

