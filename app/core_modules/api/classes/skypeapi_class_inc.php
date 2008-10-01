<?php
/**
 * Annotate interface class
 * 
 * XML-RPC (Remote Procedure call) class
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   api
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: ffmpegapi_class_inc.php 3183 2007-12-19 10:01:02Z paulscott $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * Annotate XML-RPC Class
 * 
 * Class to provide Chisimba ADM XML-RPC functionality
 * 
 * @category  Chisimba
 * @package   api
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class skypeapi extends object
{
	
	/**
     * init method
     * 
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init()
	{
		try {
			$this->objConfig = $this->getObject('altconfig', 'config');
			$this->objLanguage = $this->getObject('language', 'language');
        	$this->objUser = $this->getObject('user', 'security');
        	$this->objFiles = $this->getObject('dbfile', 'filemanager');
            $this->objFileIndexer = $this->getObject('indexfileprocessor', 'filemanager');
    	}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	
	
	public function chat($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$msg = $param->scalarval();
    	
		// Load up the IM dbtable derived class and place the message in.
		log_debug($msg);
		$ret = "Nanks dude!";
		
		$val = new XML_RPC_Value($ret, 'string');
		return new XML_RPC_Response($val);
		// Ooops, couldn't open the file so return an error message.
		return new XML_RPC_Response(0, $XML_RPC_erruser+1, $this->objLanguage->languageText("mod_packages_fileerr", "packages"));
	}
	
	public function soundbite($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$file = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$username = $param->scalarval();
    	
    	// base64 decode the file and write it down
    	$file = base64_decode($file);
    	$userid = $this->objUser->getUserId($username);
		
		if(!file_exists($this->objConfig->getContentBasePath().'users/'.$userid."/"))
		{
			@mkdir($this->objConfig->getContentBasePath().'users/'.$userid."/");
			@chmod($this->objConfig->getContentBasePath().'users/'.$userid."/", 0777);
		}
		
		$filename = time()."_skypecall.wav";
		$localfile = $this->objConfig->getContentBasePath().'users/'.$userid."/".$filename;
		file_put_contents($localfile, $file);
		
    	// A quick conversion
		$media = $this->getObject('media', 'utilities');
		$mp3 = $media->convertWav2Mp3($localfile, $this->objConfig->getContentBasePath().'users/'.$userid."/");
		
		// Now add to list of podcasts
		$filesize = filesize($mp3);
		if(extension_loaded('fileinfo'))
		{
			$finfo = finfo_open(FILEINFO_MIME);
			$type = finfo_file($finfo, $filename);
		}
		else {
			$type = mime_content_type($filename);
		}

		$mimetype = $type; //mime_content_type($mp3);
		$category = '';
		$version =1;
		
		$fmname = basename($localfile, ".wav");
		$fmname = $fmname.".mp3"; 
		$fmpath = 'users/'.$userid.'/'.$fmname;
		$path = $this->objConfig->getContentBasePath().'users/'.$userid."/";
		
		$idcomment = NULL;
		// add the MP3 to the user's filemanager set
		$fileId = $this->objFiles->addFile($fmname, $fmpath, $filesize, $mimetype, $category, $version, $userid, $idcomment);
		
		// now take the generated FileID and insert the podcast to the podcast module.
		$pod = $this->getObject('dbpodcast', 'podcast');
		$ret = $pod->addPodcast($fileId, $userid, basename($filename, ".wav"));
		
    	$val = new XML_RPC_Value("File saved to $localfile", 'string');
		return new XML_RPC_Response($val);
		// Ooops, couldn't open the file so return an error message.
		return new XML_RPC_Response(0, $XML_RPC_erruser+1, $this->objLanguage->languageText("mod_packages_fileerr", "packages"));
	}
}
?>