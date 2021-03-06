<?php





require_once INSTALL_DIR.'/step.inc';

require_once INSTALL_DIR.'/5ivestepaction.inc';

require_once INSTALL_DIR.'/html_form/html_form.inc';

require_once INSTALL_DIR.'/dbhandlers/dbhandler.inc';

/**

* DatabaseName

*

* This step collects the name of the database that is going to be used.

* The user can choose to create a new database (possibly overwriting an

* old install) or to enter a current database name.

*

* @author prince mbekwa

* @version $version$ - 1.0

* @package 5ive

* @subpackage installer

*/



class DatabaseName extends Step

{





	function DatabaseName()

	{

		$this->actions[] = new DbNameAction(get_class($this));
		$this->conf_required = true;

		$this->step_title = 'Database Name';

		$this->title_logo = 'database_name.gif';

		$this->help_topic = 'toc7';

		$this->step_help = "Select a name for your new database";

	}//end DatabaseName()





}//end class





/**

* DbNameAction

*

* This collects the name of the database the user is going to user for

* their 5ive installation. The user can also choose to create a new

* database, which may overwrite an existing install of 5ive.

*

*/

class DbNameAction extends FiveStepAction

{

	/**

	* To keep the db details collected unique (and make this action reusable), keep track

	* of what db is being processed by using a db_prefix

	* @var string

	*/

	var $db_prefix = 'db_';





	/**

	* Set the prefix for the db tables this collector uses.

	*

	* @param string $pr The prefix to use.

	* @return void

	* @access public

	*/

	function DbNameAction($ps, $pr='db_')

	{

		//$this->FiveStepAction($ps);
		
		$this->db_prefix = $pr;

		$this->step_help = "db name action,";

	}//end DbDetailsAction





	/**

	* Save the database name entered by the user

	*

	* If the database name exists, empty the database if the user has selected to.

	*

	* @return boolean Whether the database name entered is correct and usable

	* @access public

	*/

	function processAction()

	{



		$_SESSION[$this->db_prefix.'name'] = isset($_POST[$this->db_prefix.'name']) ? trim($_POST[$this->db_prefix.'name']) : '';

		$_SESSION['create_db'] = isset($_POST['create_db']) ? true : false;
		$_SESSION['install_type'] = 'install';


		if ($_SESSION[$this->db_prefix.'name'] == '') {

			$this->errors[] = 'You must provide a database name.';

			$this->success = false;

			return false;

		}


		$_SESSION['dbname'] = $_SESSION[$this->db_prefix.'name'];
		$dsn = $_SESSION['short_dsn'].$_SESSION[$this->db_prefix.'name'];



		/* @var  $handler DbHandler */

		// get a reference to the database handler.

		$handler = new DbHandler();
		if (isset($_POST['create_db'])) {

				// first, check if they've checked that they want to create a new database.

				// if so, go off and try to create the database



					if (!$this->_createDatabase($_SESSION[$this->db_prefix.'name'],$handler)) {

						$this->success = false;

						$this->errors[] = 'Could not access database '.$_SESSION[$this->db_prefix.'name'];

						return false;

					} else {
						
						$this->errors[] ='<span style="color: Green">Connected to '.(isset($_SESSION['dsn']) ? $_SESSION['dsn'] : '').'</span>';

						$this->success = true;



					}

				

		} else {

			// just want to test that the database entered exists and is connectable.

			// connect to the database using the values collected before

			$conn = &$handler->getDbHandler($dsn);

             $result=$conn->checkConnection($_SESSION[$this->db_prefix.'name']);
             
				if ($result!=TRUE) {
					$this->errors[] = 'Could not connect to database using the dsn '.$dsn.' This means that either the database does not exist, or the username and password you supplied are incorrect. Please create the database or check database create';
					$dsn = $_SESSION['short_dsn'];

					$this->success = false;

					return false;

				}

			$conn->success = true;
			// should the tables be created

			$num_tables = $handler->getNumberOfTables();
			
			if ($conn->success==FALSE) {

				$this->errors[] = 'The installer was unable to check if your system currently has db tables created. '.

								'You can continue, but please make sure there are no Chisimba tables in the database '.

								'specified.';
				$this->success = true;

			}

			// if there were already database tables created, and the install type is 'install',

			// need to quit.

			if (($num_tables != null) && ($_SESSION['install_type'] == 'install')) {

				$this->errors[] = 'The database given already contains tables, and must be emptied '.

									'before proceeding with the installation.';

				//$this->success = false;

			}

		}

		// so if after all this success is true, can set the dsn to being short_dsn.db_name
		
		if ($this->success) {

			$_SESSION['dsn'] = $_SESSION['short_dsn'].$_SESSION[$this->db_prefix.'name'];

		} else {

			$this->errors[] = 'Unable to set the default database';
			$this->success = false;

		}



		return parent::processAction();



	}//end processAction()





	/**

	* Return whether this action is required or not

	*

	* if they've already selected a sysroot and the sys_root exists, can skip

	*

	* @return boolean if this action is required to complete the step.

	* @access public

	*/

	function isRequired()

	{

		if (isset($_SESSION['dsn']) && $this->_testDSN($_SESSION['dsn'])) {

			return false;

		}



		return true;



	}//end isRequired()





	/**

	* Attempt to connect and create the database using the given DB connection

	*

	* @param string $db_name The name of the database to create

	* @param object $handler The database handler to use to empty the database.

	* @return boolean was the database successfully emptied

	* @access public

	*/

	function _createDatabase($db_name, &$handler)

	{

	//	echo $_SESSION['short_dsn'];
		// create the appropriate db handler and use it for emptying the database
		$handler = new DbHandler($_SESSION['short_dsn']);
		
		//$handler->dropDb($db_name);
		$dbs_old = $handler->listDatabases();
		$success = $handler->createDb($db_name);
		$dbs_new = $handler->listDatabases();
		
		if (!$success) {
			if ((!in_array($db_name,$dbs_old)) && (in_array($db_name,$dbs_new))) {
				$success = true;
			}
		}
       
		return $success;

	}//end _emptyDatabase()





	/**

	* Display a form for entering database details.

	*

	* @return void

	* @access public

	*/

	function paintAction()

	{

		$tpl = new Template(INSTALL_DIR.'/templates/dbname.tpl');





		ob_start();

			text_box($this->db_prefix.'name',isset($_SESSION[$this->db_prefix.'name'])?$_SESSION[$this->db_prefix.'name']:'');

			$name = ob_get_contents();

		ob_end_clean();

		ob_start();

			check_box("create_db",1,isset($_SESSION['create_db'])?$_SESSION['create_db']:true);

			$create_db = ob_get_contents();

		ob_end_clean();



		$tpl->set('db_name',$name);

		$tpl->set('create_db',$create_db);



		echo $tpl->fetch();



	}//end paintAction()

/**

	* Show what stuff was saved. Make note about the  lowercase url

	*

	* @return void

	* @access public

	*/

	



}//end class

?>
