<?php
/**
 * Autoloader Class
 * This class autoloads unknown classes, that objects initialized in program scripts.
 */
class Autoloader
{
	/**
	 * @var protected array $_libraryFoldersPaths Set of libraries paths
 	 * @var const string CLASSNAME_SEPARATOR Class name separator
 	 */
	
	protected static $_libraryFoldersPaths = array();
	const CLASSNAME_SEPARATOR = '_';

	/**
	 * Registering new class labry path.
	 * @param array $path
	 * @return void
	 */

	public static function registerLibraryPath($libraryFolderPath)
	{
		$libraryFolderPath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $libraryFolderPath);

		if( !in_array($libraryFolderPath, self::$_libraryFoldersPaths) )
			self::$_libraryFoldersPaths[] = $libraryFolderPath;
	}

	/**
	 * Including non standart php class, defined in program.
	 * @param string $className
	 * @return void
	 */

	public static function includeClass($className)
	{
		$classNamePath = explode(self::CLASSNAME_SEPARATOR, $className);

		foreach(self::$_libraryFoldersPaths as $libraryFolderPath) {

			$classFileName = $libraryFolderPath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $classNamePath) . '.php';

			/* If the class file exists and readable - class will be included.
			 * Otherwise - not. */
			if( is_readable($classFileName) )
				require_once($classFileName);
		}
	}
}