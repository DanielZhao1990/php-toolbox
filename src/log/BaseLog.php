<?php
namespace toolbox\log;
abstract class BaseLog {
	protected $debug = true;

	public function log($msg = '', $addNextLine = true) {
        $msg=$this->msg2str($msg);
		if ($this->debug) {
			$this->doLog($addNextLine ? $msg . $this->getNextLine() : $msg);
		}
	}
	function changeLogFile($path)
	{
	}
    public function msg2str($msg)
    {
           return print_r($msg,true);
    }
	public abstract function doLog($msg = '');

	public function getNextLine() {
		return "";
	}

}