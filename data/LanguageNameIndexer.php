<?php
/**
 * Script to create language names index.
 *
 * Copyright (C) 2012 Alolita Sharma, Amir Aharoni, Arun Ganesh, Brandon Harris,
 * Niklas Laxström, Pau Giner, Santhosh Thottingal, Siebrand Mazeland and other
 * contributors. See CREDITS for a list.
 *
 * UniversalLanguageSelector is dual licensed GPLv2 or later and MIT. You don't
 * have to do anything special to choose one license or the other and you don't
 * have to notify anyone which license you are using. You are free to use
 * UniversalLanguageSelector in commercial projects as long as the copyright
 * header is left intact. See files GPL-LICENSE and MIT-LICENSE for details.
 *
 * @file
 * @ingroup Extensions
 * @licence GNU General Public Licence 2.0 or later
 * @licence MIT License
 */

$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__  . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";

class LanguageNameIndexer extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->addDescription( 'Script to create language names index.' );
	}

	public function execute() {
		$languages = Language::fetchLanguageNames( null, 'all' );

		$buckets = [];
		foreach ( $languages as $sourceLanguage => $autonym ) {
			$translations = LanguageNames::getNames( $sourceLanguage, 0, 2 );
			foreach ( $translations as $targetLanguage => $translation ) {
				$translation = mb_strtolower( $translation );
				// Remove directionality markers used in Names.php: users are not
				// going to type these.
				$translation = str_replace( "\xE2\x80\x8E", '', $translation );
				$bucket = LanguageNameSearch::getIndex( $translation );
				$buckets[$bucket][$translation] = $targetLanguage;
			}
		}

		$lengths = array_values( array_map( 'count', $buckets ) );
		$count = count( $buckets );
		$min = min( $lengths );
		$max = max( $lengths );
		$median = $lengths[ceil( $count / 2 )];
		$avg = array_sum( $lengths ) / $count;
		$this->output( "Bucket stats:\n - $count buckets\n - smallest has $min entries\n" );
		$this->output( " - largest has $max entries\n - median size is $median entries\n" );
		$this->output( " - average size is $avg entries\n" );

		$this->generateFile( $buckets );
	}

	private function generateFile( array $buckets ) {
		$template = <<<PHP
<?php
// This file is generated by script!
class LanguageNameSearchData {
	public static \$buckets = ___;
}

PHP;

		ksort( $buckets );
		// Format for short array format
		$data = var_export( $buckets, true );
		$data = str_replace( "array (", '[', $data );
		$data = str_replace( "),", '],', $data );
		// Closing of the array, add correct indendation
		$data = preg_replace( "/\)$/", "\t]", $data );
		// Remove newlines after =>s
		$data = preg_replace( '/(=>)\s+(\[)/m', '\1 \2', $data );
		// Convert spaces to tabs. Since we are not top-level need more tabs.
		$data = preg_replace( '/^    /m', "\t\t\t", $data );
		$data = preg_replace( '/^  /m', "\t\t", $data );

		$template = str_replace( '___', $data, $template );

		file_put_contents( __DIR__ . '/LanguageNameSearchData.php', $template );
	}
}

$maintClass = 'LanguageNameIndexer';
require_once RUN_MAINTENANCE_IF_MAIN;
