<?php
class Insomnia_Environment_Statistics_StatisticsRegister
{
	const SECURITYLEVEL_GREEN  = 3;
	const SECURITYLEVEL_YELLOW = 2;
	const SECURITYLEVEL_RED	   = 1;

	public function registerSQLInjectionTechniqueStatistics($statisticsDBFilePath, $techniqueName) {

		//$sqliteDBHandler = new SQLite3('E:/WebServer/apps/localhost/www/statistics/statistics.db');
		$sqliteDBHandler = new SQLite3( $statisticsDBFilePath );
		$sqliteDBHandler->query("UPDATE SQLInjectionTechniqueStats SET technique_count = technique_count + 1 WHERE technique_name = '{$techniqueName}'");	
	}

	public function registerSQLInjectionStatistics($statisticsDBFilePath, $securityLevel) {

			$sqliteDBHandler = new SQLite3( $statisticsDBFilePath );
	//$sqliteDBHandler = new SQLite3('E:/WebServer/apps/localhost/www/statistics/statistics.db');
		$sqliteDBHandler->query("UPDATE SQLInjecitonStats SET total_count = total_count + 1");

		switch($securityLevel) {
			case self::SECURITYLEVEL_GREEN:
				$sqliteDBHandler->query("UPDATE SQLInjecitonStats SET success_count = success_count + 1");
				break;

			case self::SECURITYLEVEL_YELLOW:
				$sqliteDBHandler->query("UPDATE SQLInjecitonStats SET vulnerable_count = vulnerable_count + 1");
				break;

			case self::SECURITYLEVEL_RED:
				$sqliteDBHandler->query("UPDATE SQLInjecitonStats SET exploitable_count = exploitable_count + 1");
				break;

			default:
				break;
		}

	}


	public function registerXSSTechniqueStatistics($statisticsDBFilePath, $techniqueName) {
		
		//$sqliteDBHandler = new SQLite3('E:/WebServer/apps/localhost/www/statistics/statistics.db');
		$sqliteDBHandler = new SQLite3( $statisticsDBFilePath );
		$sqliteDBHandler->query("UPDATE XSSTechniqueStats SET technique_count = technique_count + 1 WHERE technique_name = '{$techniqueName}'");
	}

	public function registerXSSStatistics($statisticsDBFilePath, $securityLevel) {
		
		//$sqliteDBHandler = new SQLite3('E:/WebServer/apps/localhost/www/statistics/statistics.db');
		$sqliteDBHandler = new SQLite3( $statisticsDBFilePath );
		$sqliteDBHandler->query("UPDATE XSSStats SET total_count = total_count + 1");

		switch($securityLevel) {
			case self::SECURITYLEVEL_GREEN:
				$sqliteDBHandler->query("UPDATE XSSStats SET success_count = success_count + 1");
				break;

			case self::SECURITYLEVEL_YELLOW:
				$sqliteDBHandler->query("UPDATE XSSStats SET vulnerable_count = vulnerable_count + 1");
				break;

			case self::SECURITYLEVEL_RED:
				$sqliteDBHandler->query("UPDATE XSSStats SET exploitable_count = exploitable_count + 1");
				break;

			default:
				break;				
		}
	}



	public function registerGeneralStatistics($statisticsDBFilePath, $securityLevel) {
		
		//$sqliteDBHandler = new SQLite3('E:/WebServer/apps/localhost/www/statistics/statistics.db');
		$sqliteDBHandler = new SQLite3( $statisticsDBFilePath );
		$sqliteDBHandler->query("UPDATE GeneralStats SET total_count = total_count + 1");

		switch($securityLevel) {
			case self::SECURITYLEVEL_GREEN:
				$sqliteDBHandler->query("UPDATE GeneralStats SET success_count = success_count + 1");
				break;

			case self::SECURITYLEVEL_YELLOW:
				$sqliteDBHandler->query("UPDATE GeneralStats SET vulnerable_count = vulnerable_count + 1");
				break;

			case self::SECURITYLEVEL_RED:
				$sqliteDBHandler->query("UPDATE GeneralStats SET exploitable_count = exploitable_count + 1");
				break;

			default:
				break;	
		}		
	}
}