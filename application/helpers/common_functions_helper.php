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

// Sanitize input fields
if (! function_exists('sanitizer')) {
  function sanitizer($string = "") {
    //$sanitized_string = preg_replace("/[^@ -.a-zA-Z0-9]+/", "", html_escape($string));
    $sanitized_string = html_escape($string);
    return $sanitized_string;
  }
}

//print old form data
if (!function_exists('old')) {
    function old($field)
    {
        $ci =& get_instance();
        if (isset($ci->session->flashdata('form_data')[$field])) {
            return html_escape($ci->session->flashdata('form_data')[$field]);
        }
    }
}

//count item
if (!function_exists('item_count')) {
    function item_count($items)
    {
        if (!empty($items) && is_array($items)) {
            return count($items);
        }
        return 0;
    }
}


//generate token
if (!function_exists('generate_token')) {
    function generate_token()
    {
        $token = uniqid("", TRUE);
        $token = str_replace(".", "-", $token);
        return $token . "-" . rand(10000000, 99999999);
    }
}

//generate slug
if (!function_exists('str_slug')) {
    function str_slug($str)
    {
        $str = trim($str);
        return url_title(convert_accented_characters($str), "-", true);
    }
}

//clean slug
if (!function_exists('clean_slug')) {
    function clean_slug($slug)
    {
        $ci =& get_instance();
        $slug = urldecode($slug);
        $slug = $ci->security->xss_clean($slug);
        $slug = remove_special_characters($slug, true);
        return $slug;
    }
}

//clean number
if (!function_exists('clean_number')) {
    function clean_number($num)
    {
        $ci =& get_instance();
        $num = @trim($num);
        $num = $ci->security->xss_clean($num);
        $num = intval($num);
        return $num;
    }
}

//clean string
if (!function_exists('clean_str')) {
    function clean_str($str)
    {
        $ci =& get_instance();
        $str = trim($str);
        $str = strip_tags($str);
        $str = $ci->security->xss_clean($str);
        $str = remove_special_characters($str, false);
        return $str;
    }
}

//remove special characters
if (!function_exists('remove_special_characters')) {
    function remove_special_characters($str, $is_slug = false)
    {
        $str = trim($str);
        $str = str_replace('#', '', $str);
        $str = str_replace(';', '', $str);
        $str = str_replace('!', '', $str);
        $str = str_replace('"', '', $str);
        $str = str_replace('$', '', $str);
        $str = str_replace('%', '', $str);
        $str = str_replace('(', '', $str);
        $str = str_replace(')', '', $str);
        $str = str_replace('*', '', $str);
        $str = str_replace('+', '', $str);
        $str = str_replace('/', '', $str);
        $str = str_replace('\'', '', $str);
        $str = str_replace('<', '', $str);
        $str = str_replace('>', '', $str);
        $str = str_replace('=', '', $str);
        $str = str_replace('?', '', $str);
        $str = str_replace('[', '', $str);
        $str = str_replace(']', '', $str);
        $str = str_replace('\\', '', $str);
        $str = str_replace('^', '', $str);
        $str = str_replace('`', '', $str);
        $str = str_replace('{', '', $str);
        $str = str_replace('}', '', $str);
        $str = str_replace('|', '', $str);
        $str = str_replace('~', '', $str);
        if ($is_slug == true) {
            $str = str_replace(" ", '-', $str);
            $str = str_replace("'", '', $str);
        }
        return $str;
    }
}

//remove forbidden characters
if (!function_exists('remove_forbidden_characters')) {
    function remove_forbidden_characters($str)
    {
        $str = str_replace(';', '', $str);
        $str = str_replace('"', '', $str);
        $str = str_replace('$', '', $str);
        $str = str_replace('%', '', $str);
        $str = str_replace('*', '', $str);
        $str = str_replace('/', '', $str);
        $str = str_replace('\'', '', $str);
        $str = str_replace('<', '', $str);
        $str = str_replace('>', '', $str);
        $str = str_replace('=', '', $str);
        $str = str_replace('?', '', $str);
        $str = str_replace('[', '', $str);
        $str = str_replace(']', '', $str);
        $str = str_replace('\\', '', $str);
        $str = str_replace('^', '', $str);
        $str = str_replace('`', '', $str);
        $str = str_replace('{', '', $str);
        $str = str_replace('}', '', $str);
        $str = str_replace('|', '', $str);
        $str = str_replace('~', '', $str);
        return $str;
    }
}

if (!function_exists('time_ago')) {
    function time_ago($timestamp)
    {
        $time_ago = strtotime($timestamp);
        $current_time = time();
        $time_difference = $current_time - $time_ago;
        $seconds = $time_difference;
        $minutes = round($seconds / 60);           // value 60 is seconds
        $hours = round($seconds / 3600);           //value 3600 is 60 minutes * 60 sec
        $days = round($seconds / 86400);          //86400 = 24 * 60 * 60;
        $weeks = round($seconds / 604800);          // 7*24*60*60;
        $months = round($seconds / 2629440);     //((365+365+365+365+366)/5/12)*24*60*60
        $years = round($seconds / 31553280);     //(365+365+365+365+366)/5 * 24 * 60 * 60
        if ($seconds <= 60) {
            echo get_phrase("just_now");
        } else if ($minutes <= 60) {
            if ($minutes == 1) {
                echo "1 " . get_phrase("minute_ago");
            } else {
                echo "$minutes " . get_phrase("minutes_ago");
            }
        } else if ($hours <= 24) {
            if ($hours == 1) {
                echo "1 " . get_phrase("hour_ago");
            } else {
                echo "$hours " . get_phrase("hours_ago");
            }
        } else if ($days <= 30) {
            if ($days == 1) {
                echo "1 " . get_phrase("day_ago");
            } else {
                echo "$days " . get_phrase("days_ago");
            }
        } else if ($months <= 12) {
            if ($months == 1) {
                echo "1 " . get_phrase("month_ago");
            } else {
                echo "$months " . get_phrase("months_ago");
            }
        } else {
            if ($years == 1) {
                echo "1 " . get_phrase("year_ago");
            } else {
                echo "$years " . get_phrase("years_ago");
            }
        }
    }
}

if (!function_exists('is_user_online')) {
    function is_user_online($timestamp)
    {
        $time_ago = strtotime($timestamp);
        $current_time = time();
        $time_difference = $current_time - $time_ago;
        $seconds = $time_difference;
        $minutes = round($seconds / 60);
        if ($minutes <= 2) {
            return true;
        } else {
            return false;
        }
    }
}

//print date
if (!function_exists('formatted_date')) {
    function formatted_date($timestamp)
    {
        return date("Y-m-d / H:i", strtotime($timestamp));
    }
}

//print formatted hour
if (!function_exists('formatted_hour')) {
    function formatted_hour($timestamp)
    {
        return date("H:i", strtotime($timestamp));
    }
}

if (!function_exists('convert_to_xml_character')) {
    function convert_to_xml_character($string)
    {
        return str_replace(array('&', '<', '>', '\'', '"'), array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;'), $string);
    }
}



if (!function_exists('has_message')) {
    function has_message($table,$where = ''){ 
        $CI =& get_instance();
        
        $result = $CI->db->where($where)->get($table);

        if ($result->num_rows()) {
            return true;
        } else {
            return false;
        }        

    }
}


if (!function_exists('get_count')) {
    function get_count($field,$table,$where = ''){ 
        if(!empty($where))
            $where = "where ".$where;

        $CI =& get_instance();
        $query = $CI->db->query("SELECT COUNT(".$field.") as total FROM ".$table." ".$where." AND read_status=0 ");
        $res = $query->result_array();
        if(!empty($res)){
            return $res[0]['total'];
        }

    }
}

if (!function_exists('get_user')) {
    function get_user($field,$table,$where = ''){ 
        if(!empty($where))
            $where = "where ".$where;

        $CI =& get_instance();
        $query = $CI->db->query("SELECT * FROM ".$table." ".$where." ");
        $res = $query->result_array();
        if(!empty($res)){
         foreach($res as $re){}    
            return $re[$field];
        }

    }
}
?>