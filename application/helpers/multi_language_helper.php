<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* CodeIgniter
*
* An open source application development framework for PHP 5.1.6 or newer
*
* @package		CodeIgniter
* @author		ExpressionEngine Dev Team
* @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
* @license		http://codeigniter.com/user_guide/license.html
* @link		http://codeigniter.com
* @since		Version 1.0
* @filesource
*/


if ( ! function_exists('get_list_of_directories_and_files'))
{
	function get_list_of_directories_and_files($dir = APPPATH, &$results = array()) {
		$files = scandir($dir);
		foreach($files as $key => $value){
			$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
			if(!is_dir($path)) {
				$results[] = $path;
			} else if($value != "." && $value != "..") {
				get_list_of_directories_and_files($path, $results);
				$results[] = $path;
			}
		}
		return $results;
	}
}
if ( ! function_exists('get_all_php_files'))
{
	function get_all_php_files() {
		$all_files = get_list_of_directories_and_files();
		foreach ($all_files as $file) {
			$info = pathinfo($file);
			if( isset($info['extension']) && strtolower($info['extension']) == 'php') {
				// echo $file.' <br/> ';
				if ($fh = fopen($file, 'r')) {
					while (!feof($fh)) {
						$line = fgets($fh);
						preg_match_all('/get_phrase\(\'(.*?)\'\)\;/s', $line, $matches);
						foreach ($matches[1] as $matche) {
							get_phrase($matche);
						}
					}
					fclose($fh);
				}
			}
		}

		echo 'I Am So Lit';
	}
}
if ( ! function_exists('get_list_of_language_files'))
{
	function get_list_of_language_files($dir = APPPATH.'/language', &$results = array()) {
		$files = scandir($dir);
		foreach($files as $key => $value){
			$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
			if(!is_dir($path)) {
				$results[] = $path;
			} else if($value != "." && $value != "..") {
				get_list_of_directories_and_files($path, $results);
				$results[] = $path;
			}
		}
		return $results;
	}
}
if ( ! function_exists('get_all_languages'))
{
	function get_all_languages() {
		$language_files = array();
		$all_files = get_list_of_language_files();
		foreach ($all_files as $file) {
			$info = pathinfo($file);
			if( isset($info['extension']) && strtolower($info['extension']) == 'json') {
				$file_name = explode('.json', $info['basename']);
				array_push($language_files, $file_name[0]);
			}
		}

		return $language_files;
	}
}    

// This function helps us to get the translated phrase from the file. If it does not exist this function will save the phrase and by default it will have the same form as given
if ( ! function_exists('get_phrase'))
{
	function get_phrase($phrase = '') {
		$CI	=&	get_instance();
		$CI->load->database();
		//$language_code = $CI->db->get_where('settings' , array('type' => 'language'))->row()->description;
		$query = $CI->db->get_where('settings', array('id' => 1));
        if( $query->num_rows() == 1 )
        {
            $row = $query->row_array();
        }        
		$language_code = $row['language'];
		//$language_code = $CI->db->select('language')->from('settings')->where('id',1)->get();
        //var_dump($row); exit;
		$key = strtolower(preg_replace('/\s+/', '_', $phrase));
        $languages = get_all_languages();
        foreach($languages as $language){

            $langArray = openJSONFile($language);
            if (array_key_exists($key, $langArray)) {
            } else {
                $langArray[$key] = ucfirst(str_replace('_', ' ', $key));
                $jsonData = json_encode($langArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                file_put_contents(APPPATH.'language/'.$language.'.json', stripslashes($jsonData));
            }
            
        }
        
        $langArray_default = openJSONFile($language_code);

		return ucwords($langArray_default[$key]);
	}
}

// This function helps us to decode the language json and return that array to us
if ( ! function_exists('openJSONFile'))
{
	function openJSONFile($code)
	{
		$jsonString = [];
		if (file_exists(APPPATH.'language/'.$code.'.json')) {
			$jsonString = file_get_contents(APPPATH.'language/'.$code.'.json');
			$jsonString = json_decode($jsonString, true);
		}
		return $jsonString;
	}
}

// This function helps us to create a new json file for new language
if ( ! function_exists('saveDefaultJSONFile'))
{
	function saveDefaultJSONFile($language_code){
		$language_code = strtolower($language_code);
		if(file_exists(APPPATH.'language/'.$language_code.'.json')){
			$newLangFile 	= APPPATH.'language/'.$language_code.'.json';
			$enLangFile   = APPPATH.'language/english.json';
			copy($enLangFile, $newLangFile);
		}else {
			$fp = fopen(APPPATH.'language/'.$language_code.'.json', 'w');
			$newLangFile = APPPATH.'language/'.$language_code.'.json';
			$enLangFile   = APPPATH.'language/english.json';
			copy($enLangFile, $newLangFile);
			fclose($fp);
		}
	}
}

// This function helps us to update a phrase inside the language file.
if ( ! function_exists('saveJSONFile'))
{
	function saveJSONFile($language_code, $updating_key, $updating_value){
		$jsonString = [];
		if(file_exists(APPPATH.'language/'.$language_code.'.json')){
			$jsonString = file_get_contents(APPPATH.'language/'.$language_code.'.json');
			$jsonString = json_decode($jsonString, true);
			$jsonString[$updating_key] = $updating_value;
		}else {
			$jsonString[$updating_key] = $updating_value;
		}
		$jsonData = json_encode($jsonString, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
		file_put_contents(APPPATH.'language/'.$language_code.'.json', stripslashes($jsonData));
	}
}


// ------------------------------------------------------------------------
/* End of file language_helper.php */
/* Location: ./system/helpers/language_helper.php */
