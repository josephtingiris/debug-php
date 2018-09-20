<?php
/**
 * 
 * Debug.php
 * 
 * Copyright (C) 2015 Joseph Tingiris
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Current authors: Joseph Tingiris <joseph.tingiris@gmail.com>
 *                               (next author)
 *
 *              Original author: Joseph Tingiris <joseph.tingiris@gmail.com>
 *
 * @license     https://opensource.org/licenses/GPL-3.0
 *
 * @version     0.2.0
 */

namespace josephtingiris;

#$GLOBALS["Debug"]=500;

/**
 * The \josephtingiris\Debug class contains methods for debugging.
 */
class Debug
{

    /*
     * public properties.
     */

    public $Color_Bold_Max = 9;
    public $Color_Luminosity = 5;
    public $Hostname_Pad = 2;

    /*
     * private properties.
     */

    private $color_codes = array();
    private $json_colors = array();

    /*
     * public functions.
     */

    public function __construct($debug_level_construct = null)
    {

        // date() is used throughout; ensure a timezone is set

        if(!ini_get('date.timezone')) {
            $TZ=@date_default_timezone_get();
            if (empty($TZ)) {
                $TZ= getenv('TZ');
            }
            if (empty($TZ)) {
                date_default_timezone_set('UTC');
            } else {
                date_default_timezone_set($TZ);
            }
        }

        if (empty($this->Start_Time)) {
            $this->Start_Time = microtime(true);
        }
        $display_level_preferred = false;

        // first, prefer an explicit construct level

        if (!$display_level_preferred) {
            if ($debug_level_construct != null && is_integer($debug_level_construct)) {
                $display_level_preferred = true;
                $this->Display_Level = $debug_level_construct;
                $this->Display_Level_Source = "__construct";
                $this->debug('display level 1 = [' . $this->Display_Level_Source . '] ' . $this->Display_Level, 500);
            }
        }

        // second, prefer an explicit GLOBALS["Debug"]

        if (!$display_level_preferred) {
            if (isset($GLOBALS['Debug']) && (is_string($GLOBALS['Debug']) || is_integer($GLOBALS['Debug']))) {
                $display_level_preferred = true;
                $this->Display_Level = (int)$GLOBALS['Debug'];
                $this->Display_Level_Source = "GLOBALS(Debug)";
                $this->debug('display level 2 = [' . $this->Display_Level_Source . '] ' . $this->Display_Level, 500);
            }
        }

        // third, prefer an explicit GLOBALS["DEBUG"]

        if (!$display_level_preferred) {
            if (isset($GLOBALS['DEBUG']) && (is_string($GLOBALS['DEBUG']) || is_integer($GLOBALS['DEBUG']))) {
                $display_level_preferred = true;
                $this->Display_Level = (int)$GLOBALS['DEBUG'];
                $this->Display_Level_Source = "GLOBALS(DEBUG)";
                $this->debug('display level 2 = [' . $this->Display_Level_Source . '] ' . $this->Display_Level, 500);
            }
        }

        // fourth, prefer an explicit GLOBALS["debug"]

        if (!$display_level_preferred) {
            if (isset($GLOBALS['debug']) && (is_string($GLOBALS['debug']) || is_integer($GLOBALS['debug']))) {
                $display_level_preferred = true;
                $this->Display_Level = (int)$GLOBALS['debug'];
                $this->Display_Level_Source = "GLOBALS(debug)";
                $this->debug('display level 3 = [' . $this->Display_Level_Source . '] ' . $this->Display_Level, 500);
            }
        }

        // fifth, prefer an explicit DEBUG environment variable

        if (!$display_level_preferred) {
            $env_debug = (int) getenv('DEBUG');

            if (is_integer($env_debug)) {
                if ($env_debug > 0) {
                    $display_level_preferred = true;
                    $this->Display_Level = $env_debug;
                    $this->Display_Level_Source = "env(DEBUG)";
                    $this->debug('display level 4 = [' . $this->Display_Level_Source . '] ' . $this->Display_Level, 500);
                }
            }
        }

        // sixth, prefer an explicit debug environment variable

        if (!$display_level_preferred) {
            $env_debug = (int) getenv('debug');

            if (is_integer($env_debug)) {
                if ($env_debug > 0) {
                    $display_level_preferred = true;
                    $this->Display_Level = $env_debug;
                    $this->Display_Level_Source = "env(debug)";
                    $this->debug('display level 5 = [' . $this->Display_Level_Source . '] ' . $this->Display_Level, 500);
                }
            }
        }

        // seventh, prefer an explicit ../etc/debug file

        if (!$display_level_preferred) {

            $etc_debug="base-debug";
            $etc_debug_found=0;
            $etc_debug_paths=array(dirname(__FILE__), getcwd());
            foreach ($etc_debug_paths as $etc_debug_path) {
                if ($etc_debug_found == 1) break;
                while(strlen($etc_debug_path) > 0) {
                    if ($etc_debug_path == ".") $etc_debug_path=getcwd();
                    if ($etc_debug_path == "/") break;
                    if (is_readable($etc_debug_path . "/etc/" . $etc_debug) && !is_dir($etc_debug_path . "/etc/" . $etc_debug)) {
                        $etc_debug_found=1;
                        $etc_debug=$etc_debug_path . "/etc/" . $etc_debug;
                        break;
                    } else {
                        $etc_debug_path=dirname($etc_debug_path);
                    }
                }
            }
            unset($etc_debug_paths, $etc_debug_path);

            if ($etc_debug_found == 1) {

                if (is_readable($etc_debug)) {
                    $etc_debug_ini = parse_ini_file($etc_debug);

                    if (isset($etc_debug_ini['DEBUG'])) {
                        $display_level_preferred = true;
                        $this->Display_Level = (int) $etc_debug_ini['DEBUG'];
                        $this->Display_Level_Source = $etc_debug . "(DEBUG)";
                    }

                    if (!$display_level_preferred) {
                        if (isset($etc_debug_ini['debug'])) {
                            $display_level_preferred = true;
                            $this->Display_Level = (int) $etc_debug_ini['debug'];
                            $this->Display_Level_Source = $etc_debug . "(debug)";
                        }
                    }

                    if (!$display_level_preferred || !is_integer($this->Display_Level)) {
                        $this->Display_Level = 1;
                    }

                    $this->debug('display level 6 = [' . $this->Display_Level_Source . '] ' . $this->Display_Level, 500);
                }
            }
        }

        // eighth, force non integer & empty levels to 0

        if (!is_integer($this->Display_Level) || empty($this->Display_Level)) {
            $this->Display_Level = 0;
            $this->Display_Level_Source = "empty";
            $this->debug('display level 7 = [' . $this->Display_Level_Source . '] ' . $this->Display_Level, 500);
        }

        // nineth, ensure display_level is an integer, or throw an exception

        if (!is_integer($this->Display_Level)) {
            throw (new Exception('display_level not an integer'));
            $this->Display_Level_Source = "non-integer";
            $this->debug('display level 8 = [' . $this->Display_Level_Source . '] ' . $this->Display_Level, 500);
        }

        // finally, force negative integers to 0

        if ($this->Display_Level <= 0) {
            $this->Display_Level = 0;
            $this->Display_Level_Source = "negative integer";
            $this->debug('display level 9 = [' . $this->Display_Level_Source . '] ' . $this->Display_Level, 500);
        }

        if ($this->Display_Level >= 250) {
            $this->debug(__FILE__ . ' loaded', 250);
        }

        $this->debug("Class = " . __CLASS__, 20);

        $this->debug("Display_Level_Source = " . $this->Display_Level_Source . ", Display_Level=" . $this->Display_Level,10);

    }

    public function __destruct()
    {

        $this->Stop_Time = microtime(true);
        $this->debug(__CLASS__ . " start time = ".$this->Start_Time,20);
        $this->debug(__CLASS__ . " stop time = ".$this->Stop_Time,20);

    }

    public function br($input=null)
    {

        if ($this->modPhp()) {
            //echo "this is being run via apache";
            $br = "$input<br />\n";
        } else {
            //echo "this is being run via cli";
            $br = "$input\n";
        }

        return $br;
    }

    public function debug($output = null, $debug_level = null, $debug_stdout = '', $debug_timestamp = true, $debug_message = null, $debug_line_number = null, $debug_tag = 'DEBUG')
    {

        // cast debug_level as an integer
        $debug_level = (int)$debug_level;

        // return immediately for negative $debug_level
        if ($debug_level < 0) {
            return;
        }

        // get (or set) the global debug level
        if (empty($this->Display_Level) || $this->Display_Level <= 0) {
            $this->Display_Level = 0;
        }

        // if the global debug level is greater than the local debug level, then produce debug output
        if ($this->Display_Level >= $debug_level) {
            $display_output = true;
        } else {
            $display_output = false;
        }

        // always produce debug output if the local debug level is null or 0
        if ($debug_level == null || $debug_level == 0) {
            $display_output = true;
        }

        // this has to be true for the following logic; no sense in trying otherwise
        if (!$display_output) {
            return;
        }

        // the defaults for mod_php wont echo debug messages, they'll go to the apache error_log() instead
        // the defaults for terminals will echo debug messages on stdout, there is no system logging logic (yet)
        // mod_php doesn't set TERM
        $term = getenv('TERM');

        if (!empty($term)) {
            if ($debug_stdout !== true) {
                if ($debug_stdout !== false) {
                    // default to echo messages to stdout on cli terminals; preserve if the user wants (FALSE)
                    $debug_stdout = true;
                } else {
                    // echo "null debug_stdout=".(bool)$debug_stdout."\n";
                }
            }
        }

        // in case debug echo is not a boolean (still a string); then enforce the type
        $debug_stdout = filter_var($debug_stdout, FILTER_VALIDATE_BOOLEAN);

        if (!is_bool($debug_stdout)) {
            $debug_stdout = false;
        }

        // let the global DEBUG_ERROR_LOG also (try to) turn on output to apache error_log()
        $debug_error_log = false;

        if (!empty($GLOBALS['DEBUG_ERROR_LOG'])) {
            $debug_error_log = filter_var($GLOBALS['DEBUG_ERROR_LOG'], FILTER_VALIDATE_BOOLEAN);
        }

        // an array of regular expressions for terminals that support ansi color codes
        $color_terms = array(
            '/ansi/i',
            '/xterm/i',
            '/color/i',
        );

        $debug_color = false;

        if ($this->modPhp()) {
            // if applicable, enable mod_php color output

            if ($debug_stdout) {
                $debug_color = true;
            } else {
                $debug_color = false;
                $debug_error_log = true;
            }
        } else {
            // terminals can't use apache error_log()
            $debug_error_log = false;

            if (empty($term)) {
                $debug_color = false;
            } else {
                foreach ($color_terms as $color_term) {
                    if (preg_match($color_term,$term)) {
                        $debug_color = true;
                        break;
                    } else {
                        $debug_color = false;
                    }
                }
            }

            unset($color_term);
        }

        // make sure color is off if error_log is on
        if ($debug_error_log) {
            $debug_color = false;
        }

        if ($debug_stdout && $debug_error_log) {
            // for mod_php, it's either echo or log or but not both (prefer echo over log because it defaults to false (must be explicit))
            $debug_error_log = false;
        }

        // try one last time, for cron
        if (!$debug_stdout && !$debug_error_log) {
            $xdg_session_id = getenv('XDG_SESSION_ID');
            if (!empty($xdg_session_id)) {
                $debug_stdout = true;
            }
        }

        // one of these has to be true for the following logic; no sense in trying otherwise
        if (!$debug_stdout && !$debug_error_log) {
            return;
        }

        // get (or set) the global hostname

        if (empty($GLOBALS['HOSTNAME'])) {
            $hostname = gethostname();
        } else {
            $hostname = $GLOBALS['HOSTNAME'];
        }

        // use (or set) the debug_message & padding

        // display main file in backtraces; default padding
        $debug_message_pad = 25;
        $backtrace_files = false;
        $backtrace_classes = false;

        if ($this->Display_Level >= 20) {
            // display all files in backtraces; increase padding
            $debug_message_pad = 35;
            $backtrace_files = true;
        }

        if ($this->Display_Level >= 30) {
            // display classes in backtraces; increase padding
            $debug_message_pad = 75;
            $backtrace_classes = true;
        }

        #echo "debug_message_pad=$debug_message_pad\n";

        if (($debug_message == null || $debug_message == '') && ($backtrace_files || $backtrace_classes)) {

            // automatically determine & append the backtrace from the caller
            $backtraces = debug_backtrace();

            $backtrace_file = null;
            $backtrace_line = null;
            $backtrace_class = null;
            $backtrace_function = null;

            $debug_message=null;

            foreach ($backtraces as $backtrace) {

                // skip this function
                if (isset($backtrace['function']) && $backtrace['function'] == __FUNCTION__) {
                    continue;
                }

                // skip call_user_func*; they are essentially aliases
                if (isset($backtrace['function']) && $backtrace['function'] == 'call_user_func') {
                    continue;
                }

                if (isset($backtrace['function']) && $backtrace['function'] == 'call_user_func_array') {
                    continue;
                }

                #print_r($backtrace); // testing

                if ($backtrace_files) {
                    if (isset($backtrace['file']) && !is_null($backtrace['file']) && $backtrace['file'] != '') {
                        if ($this->Display_Level >= 50) {
                            // display the entire path
                            $backtrace_file .= $backtrace['file'];
                        } else {
                            // only display the file name
                            $backtrace_file = basename($backtrace['file']);
                        }
                    }

                    if (isset($backtrace['line']) && !is_null($backtrace['line']) && $backtrace['line'] != '') {
                        $backtrace_line = $backtrace['line'];
                    }

                    if ($backtrace_classes) {
                        if (isset($backtrace['class']) && !is_null($backtrace['class']) && $backtrace['class'] != '') {

                            $backtrace_class = null;

                            // every class but this class ...
                            if ($backtrace['class'] != __CLASS__) {

                                $backtrace_class = $backtrace['class'];

                                if (isset($backtrace['function']) && !is_null($backtrace['function']) && $backtrace['function'] != '') {
                                    $backtrace_function = $backtrace['function'] . '()';
                                }


                            } else {
                                // echo "no class ".__CLASS__."\n";
                            }

                            if (!is_null($backtrace_class) && $backtrace_class != '') {
                                if ($debug_message == null || $debug_message == '') {
                                    $debug_message = $backtrace_class . " ";
                                } else {
                                    $debug_message .= " > " . $backtrace_class . " ";
                                }
                            }

                            if (!is_null($backtrace_function) && $backtrace_function != '') {

                                // every function but this function ...
                                if ($backtrace['function'] != __FUNCTION__) {
                                    if ($debug_message == null || $debug_message == '') {
                                        $debug_message = $backtrace_function . ":";
                                    } else {
                                        $debug_message .= "->" . $backtrace_function . ":";
                                    }
                                }

                            }
                        }

                        if (!is_null($backtrace_file) && $backtrace_file != '') {
                            if ($debug_message == null || $debug_message == '') {
                                $debug_message = $backtrace_file . ":";
                            } else {
                                $debug_message .= " > " . $backtrace_file . ":";
                            }
                        }

                        if (!is_null($backtrace_line) && $backtrace_line != '') {
                            $debug_message .= $backtrace_line;
                        }
                    }

                } else {

                    // only display the first file in backtraces; use preset padding

                    if (is_null($debug_message)) {
                        if (!is_null($backtrace_file) && $backtrace_file != '') {
                            $debug_message = $backtrace_file . ":";
                        }

                        if (!is_null($backtrace_line) && $backtrace_line != '') {
                            $debug_message .= $backtrace_line;
                        }

                    }

                }

            }
            unset($backtrace);

            #echo "backtrace_file=$backtrace_file\n";
            #echo "backtrace_line=$backtrace_line\n";

            if ($debug_message == null || $debug_message == '') {

                foreach ($backtraces as $backtrace) {

                    #print_r($backtrace);

                    if (isset($backtrace['file']) && !is_null($backtrace['file']) && $backtrace['file'] != '') {
                        if ($this->Display_Level >= 50) {
                            // display the entire path
                            $backtrace_file = $backtrace['file'];
                        } else {
                            // only display the file name
                            $backtrace_file = basename($backtrace['file']);
                        }
                    } else {
                        continue;
                    }

                    if (isset($backtrace['line']) && !is_null($backtrace['line']) && $backtrace['line'] != '') {
                        $backtrace_line = $backtrace['line'];
                    }

                    if (!is_null($backtrace_file) && $backtrace_file != '') {
                        $debug_message .= $backtrace_file . ":";
                    }

                    if (!is_null($backtrace_line) && $backtrace_line != '') {
                        $debug_message .= $backtrace_line;
                    }

                }
                unset($backtrace);
            } else {
                # use existing debug_message
            }

        } else {
            // use the debug_message given in the function argument
        }

        // initialize debug format string
        $debug_format = '';

        if ($debug_color && !$debug_error_log) {
            // if applicable, start color formatting
            $debug_format = $this->colorCode($debug_level);
        }

        // the parts of the debug format, regardless of color
        if (empty($debug_tag)) {
            $debug_tag = 'DEBUG';
        }

        $debug_prefix_pad = 14;

        if (empty($debug_level)) {
            $debug_prefix = str_pad("$debug_tag ", $debug_prefix_pad);
        } else {
            $debug_prefix = str_pad("$debug_tag [$this->Display_Level][$debug_level]", $debug_prefix_pad);
        }

        if (!empty($GLOBALS['UUID'])) {
            // if it's not empty then add the global uuid
            $debug_prefix = '[' . $GLOBALS['UUID'] . '] ' . $debug_prefix;
        }

        if ($debug_timestamp) {
            // only if it's not going to be error_log()'ed (apache timestamps are redundent)

            if (!$debug_error_log) {
                // add the debug timestamp
                $debug_prefix = $this->timeStamp($debug_prefix);
            }
        }

        if ($output == null) {
            $output = 'NULL';
        }

        if ($debug_message != null && $this->Display_Level >= 10) {
            // enhanced debuging; includes prefix, plus more
            $debug_format .= str_pad($debug_prefix, $debug_prefix_pad);
            if ($this->Display_Level >= 40) {
                $debug_format .= ' : ' . str_pad($hostname, $this->Hostname_Pad);
            }
            $debug_format .= ' : ' . str_pad($debug_message, $debug_message_pad);
            $debug_format .= ' : ' . $output;
        } else {
            // simplified debuging; just prefix & output
            $debug_format .= str_pad($debug_prefix, $debug_prefix_pad);
            $debug_format .= ' : ' . $output;
        }

        // if applicable, stop color formatting

        if ($debug_color && !$debug_error_log) {
            $debug_format .= $this->colorCode('reset');
        }
        $debug_format .= $this->br();

        if ($debug_error_log && $this->modPhp()) {
            // note;
            // this will strip ANYTHING in between <>, including mail addresses, eg. <noreplay@domain>
            error_log(trim(strip_tags($debug_format)));
        }

        if ($debug_stdout) {
            echo $debug_format;
        }

    }

    public function debugValue($variable_name, $debug_level = 9, $variable_comment = null)
    {

        $output = '';
        // convert everything to a string
        $variable_name = print_r($variable_name, true);
        $variable_comment = print_r($variable_comment, true);

        if (isset($GLOBALS[$variable_name])) {
            $global_variable = true;
            $output = 'GLOBAL : ' . str_pad($variable_name, 32) . ' = ';
        } else {
            $global_variable = false;
            $output = 'local  : ' . str_pad($variable_name, 32) . ' = ';
        }

        if ($global_variable) {
            if ($GLOBALS[$variable_name] == null) {
                $output .= 'NULL';
            } else {
                $output .= print_r($GLOBALS[$variable_name], true);
            }

            if ($variable_comment != '' && $variable_comment != null) {
                $output .= " ($variable_comment)";
            }
        } else {
            if ($variable_comment != '' && $variable_comment != null) {
                $output .= "($variable_comment)";
            }
        }

        $this->debug($output, $debug_level);

    }

    /**
     * return determined Display_Level (aka debug_level)
     */
    public function level()
    {

        if (empty($this->Display_Level)) {
            return 0;
        } else {
            return $this->Display_Level;
        }

    }

    public function modPhp()
    {

        if (!empty($_SERVER['SERVER_NAME'])) {
            return true;
        }

        return false;

    }

    /* deprecated */
    public function out()
    {

        return call_user_func_array(array(
            $this,
            'debug',
        ), func_get_args());

    }

    /* deprecated */
    public function outArray()
    {

        return call_user_func_array(array(
            $this,
            'debugValue',
        ), func_get_args());

    }

    /* deprecated */
    public function outValue()
    {

        return call_user_func_array(array(
            $this,
            'debugValue',
        ), func_get_args());

    }

    /* deprecated */
    public function outVariable()
    {

        return call_user_func_array(array(
            $this,
            'debugValue',
        ), func_get_args());

    }

    public function prototypeFunction()
    {

        $this->debug('Debug World!', 0);

    }

    public function timeStamp($timestamp = null, $timestamp_force = true)
    {

        if ($timestamp_force) {
            if ($timestamp != null && $timestamp != '') {
                $timestamp = date('Y-m-d H:i:s') . ' : ' . $timestamp;
            } else {
                $timestamp = date('Y-m-d H:i:s');
            }
        }

        return $timestamp;

    }

    /*
     * private properties.
     */

    private $Display_Level = null;
    private $Display_Level_Source = null;
    private $Start_Time = null;
    private $Stop_Time = null;

    /*
     * private functions.
     */

    private function colorCode($color_code = null)
    {

        $color = null;

        if ($color_code == null) {
            return $color;
        }

        if (is_string($color_code)) {
            $color_code=trim(strtolower($color_code));
        } else {
            $color_code = (int)$color_code; // recast; only int & string are supported
            if ($color_code >= 255) {
                $color_code = ($color_code % 242)+14; // modulo 256
            }
        }

        $html=$this->modPhp();

        // if it exists then promptly return cached value
        if (!empty($this->color_codes) && isset($this->color_codes[$color_code])) {
            if ($html) {
                // html
                if (isset($this->color_codes[$color_code][0])) {
                    return $this->color_codes[$color_code][0];
                }
            } else {
                // ansi (other)
                if (isset($this->color_codes[$color_code][1])) {
                    return $this->color_codes[$color_code][1];
                }
            }
        }

        $color_attributes=array();
        $color_attributes['bold_off']=array(
            "</b>", // html
            "\033[20m", // ansi; only (all) attributes off
        );
        $color_attributes['bold_on']=array(
            "<b>", // html
            "\033[1m", // ansi; bold on
        );
        $color_attributes['reset']=array(
            "</p>", // html
            "\033[0m", // ansi
        );

        $color_data=false;

        $color_background=null;
        $color_foreground=null;

        if (is_string($color_code)) {
            if (isset($color_attributes[$color_code][0]) && isset($color_attributes[$color_code][1])) {
                if ($html) {
                    // html
                    $color.=$color_attributes[$color_code][0];
                    $this->color_codes[$color_code][0]=$color; // cache value
                } else {
                    // ansi
                    $color.=$color_attributes[$color_code][1];
                    $this->color_codes[$color_code][1]=$color; // cache value
                }
                return $color; // return the attribute value
            }
            $color_data = true; // load color data
        } else {
            $color_data = true; // load color data
        }

        if ($color_data) {

            $color_luminosity=0;

            if (empty($this->json_colors)) {
                if (!is_readable(dirname(__FILE__)."/color-data.json")) {
                    return null;
                }
                $this->json_colors=json_decode(file_get_contents(dirname(__FILE__)."/color-data.json"), true);
            }

            $color_background_data=null;
            $color_background_r=0;
            $color_background_g=0;
            $color_background_b=0;
            $color_background_luminosity = 0;

            $color_foreground_data=null;
            $color_foreground_r=0;
            $color_foreground_g=0;
            $color_foreground_b=0;
            $color_foreground_luminosity = 0;


            // get color_foreground_data
            foreach ($this->json_colors as $json_color) {
                if (!empty($color_foreground_data)) {
                    break;
                }
                if (isset($json_color['colorId']) && $json_color['colorId'] === $color_code) {
                    $color_foreground_data=$json_color;
                    break;
                }
                if (isset($json_color['hexString']) && $json_color['hexString'] === $color_code) {
                    $color_foreground_data=$json_color;
                    break;
                }
                if (isset($json_color['name']) && $json_color['name'] === $color_code) {
                    $color_foreground_data=$json_color;
                    break;
                }
                unset($json_color);
            }
            unset($json_color);

            if (empty($color_foreground_data)) {
                // no color_foreground_data
            } else {
                #print_r($color_foreground_data);

                if (isset($color_foreground_data['rgb']['r'])) {
                    $color_foreground_r=(int)$color_foreground_data['rgb']['r'];
                }

                if (isset($color_foreground_data['rgb']['g'])) {
                    $color_foreground_g=(int)$color_foreground_data['rgb']['g'];
                }

                if (isset($color_foreground_data['rgb']['b'])) {
                    $color_foreground_b=(int)$color_foreground_data['rgb']['b'];
                }

                $color_foreground_luminosity = 0.2126 * pow($color_foreground_r/255, 2.2) +
                    0.7152 * pow($color_foreground_g/255, 2.2) +
                    0.0722 * pow($color_foreground_b/255, 2.2);

                // get color_background_data (based on luminosity)
                foreach ($this->json_colors as $json_color) {
                    if (empty($json_color)) {
                        break;
                    }

                    if (isset($json_color['rgb']['r'])) {
                        $color_background_r=(int)$json_color['rgb']['r'];
                    }

                    if (isset($json_color['rgb']['g'])) {
                        $color_background_g=(int)$json_color['rgb']['g'];
                    }

                    if (isset($json_color['rgb']['b'])) {
                        $color_background_b=(int)$json_color['rgb']['b'];
                    }

                    $color_background_luminosity = 0.2126 * pow($color_background_r/255, 2.2) +
                        0.7152 * pow($color_background_g/255, 2.2) +
                        0.0722 * pow($color_background_b/255, 2.2);

                    if ($color_foreground_luminosity > $color_background_luminosity) {
                        $color_luminosity=($color_foreground_luminosity+0.05) / ($color_background_luminosity+0.05);
                    } else {
                        $color_luminosity=($color_background_luminosity+0.05) / ($color_foreground_luminosity+0.05);
                    }

                    if ($color_luminosity >= $this->Color_Luminosity) {
                        $color_background_data=$json_color;
                        break;
                    }
                    unset($json_color);
                }
                unset($json_color);
            }

            if ($html) {
                if (is_integer($color_code) && $color_code <= $this->Color_Bold_Max) {
                    if (isset($color_attributes['bold_on'][0])) {
                        $color.=$color_attributes['bold_on'][0];
                    }
                }
                if (isset($color_background_data['hexString']) && (isset($color_foreground_data['hexString']))) {
                    $color.="<p style=\"color: " . strtoupper($color_foreground_data['hexString']) . "; background-color: " .strtoupper($color_background_data['hexString']) . ";\">";
                } else {
                    if (isset($color_background_data['hexString'])) {
                        $color.="<p style=\"background-color: " .strtoupper($color_background_data['hexString']) . ";\">";
                    }
                    if (isset($color_foreground_data['hexString'])) {
                        $color.="<p style=\"color: " . strtoupper($color_foreground_data['hexString']) . ";\">";
                    }
                }
                $this->color_codes[$color_code][0]=$color; // cache value
            } else {
                if (is_integer($color_code) && $color_code <= $this->Color_Bold_Max) {
                    if (isset($color_attributes['bold_on'][1])) {
                        $color.=$color_attributes['bold_on'][1];
                    }
                    if (isset($color_foreground_data['colorId'])) {
                        $color.="\033[38;5;".(int)$color_foreground_data['colorId']."m";
                    }
                } else {
                    if (isset($color_foreground_data['colorId'])) {
                        $color.="\033[38;5;".(int)$color_foreground_data['colorId']."m";
                    }
                    if (isset($color_background_data['colorId'])) {
                        $color.="\033[48;5;".(int)$color_background_data['colorId']."m";
                    }
                }
                $this->color_codes[$color_code][1]=$color; // cache value
            }

            #echo "\ncolor_code=$color_code, foreground rgb = $color_foreground_r,$color_foreground_g,$color_foreground_b = $color_foreground_luminosity\n";
            #echo "color_code=$color_code, background rgb = $color_background_r,$color_background_g,$color_background_b = $color_background_luminosity, color_luminosity=$color_luminosity\n\n";

        } else {
            // color_data is false
        }

        #echo "color_code=$color_code, (".escapeshellcmd($color).")\n";

        return $color;

    }

    /*
     * protected properties.
     */

    /*
     * protected functions.
     */

}
