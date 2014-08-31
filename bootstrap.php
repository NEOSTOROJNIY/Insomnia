<?php
require_once "lib/Autoloader.php";
// Registering autoloader function.
spl_autoload_register(array('Autoloader', 'includeClass'));
// Registering php classes folder.
Autoloader::registerLibraryPath(array('lib'));
Insomnia_InsomniaConfigurator::setup();
$facade = new Insomnia_InsomniaFacade(
	Insomnia_InsomniaConfigurator::$projectsSavingPath,
	Insomnia_InsomniaConfigurator::$reportsSavingPath,
	Insomnia_InsomniaConfigurator::getStatisticsDBFilePath()
);

if(isset($_POST['insomniaAction'])) {

	switch($_POST['insomniaAction']) {

		/***********************************************************/
		/*                <PROJECT.PHP> page actions.              */
		/***********************************************************/
		case 'project_startTesting':
			if( isset($_POST['testingData']))
				echo $facade->startTesting( $_POST['testingData'] );
			break;

		case 'project_saveProject':
			if( isset($_POST['saveData']) )
				echo $facade->saveProjectFile( $_POST['saveData']);
			break;

		case 'project_getProjectList':
			echo $facade->getProjectList();
			break;

		case 'project_deleteProject':
			if(isset($_POST['fileName']))
				$facade->deleteProject( $_POST['fileName']);				
			break;


		case 'project_getProject': // get report in the xml form
			if(isset($_POST['fileName']))
				echo $facade->getProject($_POST['fileName']);
			break;		
		/**************END OF <PROJECT.PHP> PAGE ACTIONS ***********/


		/***********************************************************/
		/*			 START OF <REPORT.PHP> PAGE ACTIONS            */
		/***********************************************************/
		case 'report_getReportList':
			echo $facade->getReportList();
			break;

		case 'report_getReport':
			if(isset($_POST['fileName']))
				echo $facade->getReport( $_POST['fileName']);
			break;

		case 'report_deleteReport':

			if(isset($_POST['fileName']))
				$facade->deleteReport( $_POST['fileName'] );
			break;
		/************** END OF <REPORT.PHP PAGE ACTIONS *************/

		/************************************************************/
		/*				START OF <STATISTICS.PHP> PAGE ACTIONS      */
		/************************************************************/
		case 'statistics_getGeneralStatistics':
			echo $facade->getGeneralStatistics();
			break;

		case 'statistics_getSQLInjectionStatistics':
			echo $facade->getSQLInjectionStatistics();
			break;

		case 'statistics_getXSSStatistics':
			echo $facade->getXSSStatistics();
			break;			

		/************** END IF <STATISTICS.PHP> PAGE ACTIONS ********/
		case 'statistics_clearStats':
			$facade->clearStatistics();
			break;

		default:
			break;
	}
}