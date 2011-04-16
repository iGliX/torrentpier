<?php

class datastore_file extends datastore_common
{
	var $dir = null;
	
	function datastore_file ($dir)
	{
		$this->dir = $dir;
	}
	
	function store ($title, $var)
	{
		$this->data[$title] = $var;
		
		$filename   = $this->dir . clean_filename($title) . '.php';

		$filecache = "<?php\n";
		$filecache .= "if (!defined('BB_ROOT')) die(basename(__FILE__));\n";
		$filecache .= '$filestore = ' . var_export($var, true) . ";\n";
		$filecache .= '?>';		

		return (bool) file_write($filecache, $filename, false, true, true);
	}

	function clean ()
	{
		$dir = $this->dir;
		
		if (is_dir($dir)) 
		{
			if ($dh = opendir($dir)) 
			{
				while (($file = readdir($dh)) !== false) 
				{
					if ($file != "." && $file != "..") 
					{ 
						$filename = $dir . $file;
					
						unlink($filename);						
					}
				}
				closedir($dh);
			}
		}
	}

	function _fetch_from_store ()
	{
		if (!$items = $this->queued_items)
		{
			$src = $this->_debug_find_caller('enqueue');
			trigger_error("Datastore: item '$item' already enqueued [$src]", E_USER_ERROR);
		}

		foreach($items as $item)
		{
			$filename = $this->dir . $item . '.php';
			
			if(file_exists($filename))
			{
				require($filename);
				
				$this->data[$item] = $filestore;
			}
		}
	}
}