<?php
/**
 * Base class of all SQL Injection Check techniques.
 * Contains:
 * - possible DB Type info, 
 * - original URL value,
 * - position of vulnerable param in GET request,
 * - param name, 
 * - POST param flag (signals that current param is POST param).
 */
abstract class Insomnia_Workflow_Research_SQLInjection_SQLInjectionCheckTechnique 
	extends Insomnia_Workflow_Research_CheckTechnique
{
	/**
	 * Error messages associated with data bases.
	 */
	public static $errorMessages = array (
		"MySQL" => array (
			"/SQL syntax.*MySQL/",
			"/Warning.*mysql_.*/",
			"/Warning.*mysqli_.*/",
			"/valid MySQL result/",
			"/MySqlClient\./"
		),

		"PostgreSQL" => array (
			"/Warning.*\Wpg_.*/",
			"/valid PostgreSQL result/",
			"/Npgsql\./",
			"/Query error/"
		),

		"SQLite" => array (
			"/SQLite\/JDBCDriver/",
			"/SQLite.Exception/",
			"/System.Data.SQLite.SQLiteException/",
			"/Warning.*sqlite_.*/",
        	"/Warning.*SQLite3::/"
		),

		"OracleSQL" => array(
			"/ORA-[0-9][0-9][0-9][0-9]/",
			"/Oracle error/",
			"/Oracle.*Driver/",
			"/Warning.*\Woci_.*/",
			"/Warning.*\Wora_.*/"
		)

		/* Microsoft SQL Server
		"Driver.* SQL[\-\_\ ]*Server"
        "OLE DB.* SQL Server"
        "(\W|\A)SQL Server.*Driver"
        "Warning.*mssql_.*"
        "(\W|\A)SQL Server.*[0-9a-fA-F]{8}"
        "(?s)Exception.*\WSystem\.Data\.SqlClient\."
        "(?s)Exception.*\WRoadhouse\.Cms\."
        */
	);
}