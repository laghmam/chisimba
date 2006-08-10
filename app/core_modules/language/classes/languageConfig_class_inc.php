<?PHP
/* -------------------- LANGUAGE CONFIG CLASS ----------------*/
/*Description of file
  *This is a Language config class for kewlNextGen
  *@author Prince Mbekwa
  *@copyright (c) 200-2006 University of the Western Cape
  *@Version 0.1
  *@author  Prince Mbekwa
*/

require_once 'Translation2.php';
require_once 'I18Nv2.php';
define('TABLE_PREFIX', 'tbl_');

/**
*Language Config class for KEWL.NextGen. Provides language setup properties,
* the main one being to call the PEAR Translation2 object and setup
* all language table layouts.
* Setup all locales
* Allow MDB2 to take over language Item maintainance
*
*/
class languageConfig extends object 
{
	/**
     * Public variable to hold the new language config object 
     *@access public
     * @var string
    */
    public $lang;
    
    /**
     * Public variable to hold the site config object 
     *@access private
     * @var string
    */
    private $_siteConf;
    
    /**
     * The global error callback for altconfig errors
     *
     * @access public
     * @var string
    */
    public $_errorCallback;
    /**
     * Config object
     *
     * @var objConfig
     */
    public $objConfig =  null;
    
	/**
     * Constructor for the languageConf class.
     *
    */
		
	public function init(){
		
		//set first time run Locale 
		 $this->objConfig = &$this->getObject('altconfig','config');
    	 $lan = $this->objConfig->getdefaultLanguage();
		 I18Nv2::setLocale("{$lan}");
	}
	/**
     * Setup for the languageConf class.
     * tell Translation2 about our db-tables structure,
     * setup primary language
     * setup the group of module strings we want to fetch from
     * add a Lang decorator to provide a fallback language
     * add another Lang decorator to provide another fallback language,
	 * in case some strings are not translated in all languages that exist in KINKY
    */
	public function setup()
	{
		try {
			//Define table properties so that MDB2 knows about them
			$params = array(
			'langs_avail_table' => TABLE_PREFIX.'langs_avail',
			'lang_id_col'     => 'id',
			'lang_name_col'   => 'name',
			'lang_meta_col'   => 'meta',
			'lang_errmsg_col' => 'error_text',
			'strings_tables'  => array(
								'en' => TABLE_PREFIX.'en',
			
									),
			'string_id_col'      => 'id',
			'string_page_id_col' => 'pageID',
			'string_text_col'    => '%s'  //'%s' will be replaced by the lang code
			);
			$driver = 'MDB2';
			
			//instantiate class
			$this->_siteConf = $this->getObject('altconfig','config');	
			
			$this->lang =& Translation2::factory($driver, $this->_siteConf->getDsn(), $params);
			if (PEAR::isError($this->lang)) {
				throw new Exception('Could not load Translation class');
			}
			// set primary language
			if(!is_object($this->lang)) throw new Exception('Translation class not loaded');
			$this->lang->setLang("en");
			
			// set the group of strings you want to fetch from
			$this->lang->setPageID('defaultGroup');
			
			// add a Lang decorator to provide a fallback language
			$this->lang =& $this->lang->getDecorator('Lang');
			$this->lang->setOption('fallbackLang', 'en');
			// add a default text decorator to deal with empty strings
			$this->lang = & $this->lang->getDecorator('DefaultText');
			// add a memory-based cache decorator, to do some basic prefetching and
			// reduce the load on the db
			$this->lang = & $this->lang->getDecorator('CacheMemory');
			$this->lang = & $this->lang->getDecorator('CacheLiteFunction');
			$this->lang->setOption('cacheDir', 'cache/');
			$this->lang->setOption('lifeTime', 3600*24);
			// add a special chars decorator to replace special characters with the html entity
			$this->lang = & $this->lang->getDecorator('SpecialChars');
			
			// add a UTF-8 decorator to automatically decode UTF-8 strings
			$this->lang = & $this->lang->getDecorator('UTF8');
		
			// use a custom fallback text
			return $this->lang;
		}catch (Exception $e){
			$this->errorCallback ('Caught exception: '.$e->getMessage());
    		exit();	
			
		}
		
		
	}
	
	/**
    * The error callback function, defers to configured error handler
    *
    * @param string $error
    * @return void
    * @access public
    */
    public function errorCallback($exception)
    {
    	echo customException::cleanUp($exception);
    }
	
		
	
	
	
}
?>