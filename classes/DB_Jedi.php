<?php
/**
 * This class will handle setting up the database.
 */
class DB_Jedi {
	private $_dir;
	private $_dir_name;
	private $_applied_scripts = array();
	private $_available_scripts = array();
	private $_log = array();
	private $_errors = false;

	/**
	 * Build a new DB_Jedi based on a given directory of change scripts.
	 *
	 * @param sql_dir The path to the directory where our sequential change scripts live.
	 */
	public function __construct($sql_dir = null) {
		$this->_log("Constructing DB Jedi");
		$restricted_files = array( '.', '..', '.svn', '.git');

		$this->_checkBaseline();

		$this->_dir_name = $sql_dir;
		$this->_log($sql_dir);

		$this->_loadCurrentSchema();

		if(true == is_dir($sql_dir)) {
			if($this->_dir = opendir($sql_dir)) {
				while(($file = readdir($this->_dir)) !== false) {
					$this->_log($file);
					if(false == in_array($file, $restricted_files) && false == in_array($file, $this->_applied_scripts) && false == is_dir($file)) {
						$this->_available_scripts[] = $file;
					}
				}
				$this->_applyChanges();
			} else {
				//TODO: LOG ERROR SOMEWHERE
				$this->_log("ERROR: Could not open directory $sql_dir");
			}
		} else {
			//TODO: LOG ERROR SOMEWHERE
			$this->_log("ERROR: $sql_dir is not a directory!");
		}
	}

	/**
	 * @returns Returns whether or not this DB_Jedi has errors.
	 */
	public function hasErrors() {
		return $this->_errors;
	}

	/**
	 * Checks the baseline schema and/or creates a fresh new one if it can't find evidence of an existing one.
	 */
	private function _checkBaseline() {
		$sql = "SHOW TABLES";
		$baseline_flag = false;
		$structure_query = db_query($sql);
		while($structure_query->num_rows > 0 && $table = $structure_query->fetch_assoc()) {
			foreach($table as $key => $table_name) {
				if('schema_changes' == $table_name) {
					$baseline_flag = true;
					$this->_log('schema table exists');
				}
			}
		}
		if(false == $baseline_flag) {
			$schema_changes_table = "
				CREATE TABLE IF NOT EXISTS `schema_changes` (
  					`change_id` int(11) NOT NULL auto_increment,
  					`script_name` varchar(50) NOT NULL,
  					`date_added` timestamp NOT NULL default CURRENT_TIMESTAMP,
  					PRIMARY KEY  (`change_id`),
  					KEY `script_name` (`script_name`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
			db_query($schema_changes_table);
			$this->_log('Created schema table');
		}
	}


	/**
	 * Logs a message to the internal log.
	 *
	 * @param message Message to be logged.
	 */
	private function _log($message) {
		$this->_log[] = array(
					'date' => date('Y-m-d H:i:s'),
					'message' => $message
				);
	}

	/**
	 * Loads the current schema into an internal array.
	 */
	private function _loadCurrentSchema() {
		$this->_log("Loading current schema");
		$sql = "SELECT sc.script_name
			FROM `schema_changes` sc
			ORDER BY date_added";
		$query = db_query($sql);
		while($query->num_rows > 0 && $schema_version = $query->fetch_assoc()) {
			$this->_applied_scripts[] = $schema_version['script_name'];
		}
	}

	/**
	 * Apply the changes from the available change scripts to our schema.
	 */
	private function _applyChanges() {
		//sort them damn it!
		sort($this->_available_scripts);
		if(count($this->_available_scripts) > 0) {
			$this->_log("Change scripts found, applying change scripts");
			foreach($this->_available_scripts as $index => $script) {
				$script_name = $this->_dir_name . $script;
				if(true == ($sql = file_get_contents($script_name))) {
					$this->_log("Begin: Applying " . $script);
					//break the file down into digestable queries.

					/*new hotness*/
					$query = db_multi_query($sql);
					$db_error = db_errno();
					if($db_error > 0) {
						//log it and break out!
						$this->_log("ERROR APPLYING QUERY:\n" . $query . "\n MySQL Said:\n" . db_error());
						$this->_errors = true;
						$errors = true;
						break;
					}
					$link = DB::DB()->getLink();
					while($link->next_result()) {
						if($result = $link->store_result()) {
							$result->free();
						}
					}

					if(true == $errors) {
						break;
					} else {
						$data_array = array(
									'script_name' => $script,
									'date_added' => date('Y-m-d H:i:s')
								);
						db_perform('schema_changes', $data_array);
						$this->_applied_scripts[] = $script;
					}
					$this->_log("End: Applying " . $script);
				} else {
					//TODO ERROR OUT
					$this->_log('Error processing change script: ' . $script);
					break;
				}
			}
		} else {
			$this->_log("No changes found, doing nothing");
		}
	}

	/**
	 * @return Returns the internal log.
	 */
	public function getLog() {
		return $this->_log;
	}
}
?>