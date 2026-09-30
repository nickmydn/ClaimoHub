<?php

namespace App;

use Carbon\Carbon;

class Logs
{
	protected $fp;

	public function __construct($filename)
	{
		$filename = $filename . '_' . Carbon::now()->toDateString() . '.log';

		if( file_exists( storage_path('/logs/' . $filename) ) ) {

            $this->fp = fopen( storage_path('/logs/' . $filename), 'a');

            if(!$this->fp) {
                \Log::error("Can't file " . storage_path('/logs/' . $filename) );
            }

        } else {

            $this->fp = fopen( storage_path('/logs/' . $filename), 'w');

            if(!$this->fp) {
                \Log::error("Can't file " . storage_path('/logs/' . $filename) );
            }
        }
	}

	// public function write($section, $content)
	// {
	// 	fwrite($this->fp, str_pad(date("d.m.Y H:i:s"), 25, " ", STR_PAD_RIGHT).str_pad(strtoupper($section), 25, " ", STR_PAD_RIGHT).str_replace("\r\n", " ", str_replace("\t", "", str_replace("\t", "", $content)))."\r\n");
	// }

	public function write($section, $content)
	{
    	fwrite(
    	    $this->fp,
    	    sprintf(
    	        "[%s] %-30s %s\r\n",
    	        date('d.m.Y H:i:s'),
    	        strtoupper($section),
    	        $content
    	    )
    	);
	}

	public function __destruct() {
		fclose($this->fp);
	}
}
