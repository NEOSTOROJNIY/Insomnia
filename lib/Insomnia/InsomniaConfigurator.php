<?php
class Insomnia_InsomniaConfigurator
{
	public static $reportsSavingPath  = 'reports';
	public static $projectsSavingPath = 'projects';
	
	public static $statisticsDBSavingPath = 'statistics';
	public static $statisticsDBFileName   = 'statistics.db';

	public static function setup() {	
		self::includeAdditionals();
		self::makeDataFolders();
		self::makeStatisticsDatabase();
	}

	private static function includeAdditionals() {
		require_once "Additionals" . DIRECTORY_SEPARATOR . "simple_html_dom.php";
		require_once "Additionals" . DIRECTORY_SEPARATOR . "snoopy.php";
	}

	private static function makeDataFolders() {
		if(!file_exists(self::$reportsSavingPath))
			mkdir(self::$reportsSavingPath);
		if(!file_exists(self::$projectsSavingPath))
			mkdir(self::$projectsSavingPath);
		if(!file_exists(self::$statisticsDBSavingPath))
			mkdir(self::$statisticsDBSavingPath);
	}

	public static function getStatisticsDBFilePath() {
		return self::$statisticsDBSavingPath . DIRECTORY_SEPARATOR . self::$statisticsDBFileName;
	}

	private static function makeStatisticsDatabase() {

		if(!file_exists(self::getStatisticsDBFilePath())) {

			// Make/open database file
			$statisticsDBHandle = new SQLite3(self::getStatisticsDBFilePath());

			// Create General Statistics Table
			$statisticsDBHandle->exec(
				'CREATE TABLE GeneralStats (
					id INTEGER  NOT NULL PRIMARY KEY AUTOINCREMENT,
					total_count INTEGER NOT NULL,
					success_count INTEGER NOT NULL,
					vulnerable_count INTEGER NOT NULL,
					exploitable_count INTEGER NOT NULL)'
			);

			// Create SQL Injection Statistics Table
			$statisticsDBHandle->exec(
				'CREATE TABLE SQLInjecitonStats (
					id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
					total_count INTEGER NOT NULL,
					success_count INTEGER NOT NULL,
					vulnerable_count INTEGER NOT NULL,
					exploitable_count INTEGER NOT NULL)'
			);
			
			// Create SQL Injection Technique Statistics Table
			$statisticsDBHandle->exec(
				'CREATE TABLE SQLInjectionTechniqueStats (
					id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
					technique_name TEXT NOT NULL,
					technique_count INTEGER NOT NULL)'
			);

			// Create XSS Statistics Table
			$statisticsDBHandle->exec(
				'CREATE TABLE XSSStats (
					id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
					total_count INTEGER NOT NULL,
					success_count INTEGER NOT NULL,
					vulnerable_count INTEGER NOT NULL,
					exploitable_count INTEGER NOT NULL)'
			);
	
			// Create XSS Techniqeu Statistics Table
			$statisticsDBHandle->exec(
				'CREATE TABLE XSSTechniqueStats (
					id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
					technique_name TEXT NOT NULL,
					technique_count INTEGER NOT NULL)'
			);

			// Make standart values for stats tables
			// SQL Injection Techniques
			$statisticsDBHandle->exec("INSERT INTO SQLInjectionTechniqueStats VALUES (null, 'Magic Quotes', 0)");
			$statisticsDBHandle->exec("INSERT INTO SQLInjectionTechniqueStats VALUES (null, 'Boolean Operations (Blind)', 0)");
			$statisticsDBHandle->exec("INSERT INTO SQLInjectionTechniqueStats VALUES (null, 'Grouping Operations', 0)");
			$statisticsDBHandle->exec("INSERT INTO SQLInjectionTechniqueStats VALUES (null, 'Numerical Operations (Blind)', 0)");
			$statisticsDBHandle->exec("INSERT INTO SQLInjectionTechniqueStats VALUES (null, 'Column Count', 0)");
			$statisticsDBHandle->exec("INSERT INTO SQLInjectionTechniqueStats VALUES (null, 'Base Info', 0)");
			$statisticsDBHandle->exec("INSERT INTO SQLInjectionTechniqueStats VALUES (null, 'System DB Check', 0)");

			// XSS Techniques
			$statisticsDBHandle->exec("INSERT INTO XSSTechniqueStats VALUES (null, 'Unique Vector', 0)");
			$statisticsDBHandle->exec("INSERT INTO XSSTechniqueStats VALUES (null, 'Numeric Code', 0)");
			$statisticsDBHandle->exec("INSERT INTO XSSTechniqueStats VALUES (null, 'Remote Code', 0)");
			$statisticsDBHandle->exec("INSERT INTO XSSTechniqueStats VALUES (null, 'Data Protocol', 0)");
			$statisticsDBHandle->exec("INSERT INTO XSSTechniqueStats VALUES (null, 'Hands-Free', 0)");				

			$statisticsDBHandle->query('INSERT INTO GeneralStats VALUES (null, 0, 0, 0, 0)');
			$statisticsDBHandle->query('INSERT INTO SQLInjecitonStats VALUES (null, 0, 0, 0, 0)');
			$statisticsDBHandle->query('INSERT INTO XSSStats VALUES (null, 0, 0, 0, 0)');
		}
	}

	private static function verifyServerSettings() {
		// max_execution_time 0
		// php_version 5.4.12 (100% works!)
		// curl
		// sockets
		// pecl HTTP 1.7.5
		// SQLite 3.0
	}
}