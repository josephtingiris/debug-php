<?php
/**
 * This is a PHP class in the \josephtingiris namespace.
 *
 * @author      Current authors: Joseph Tingiris <joseph.tingiris@gmail.com>
 *                               (next author)
 *
 *              Original author: Joseph Tingiris <joseph.tingiris@gmail.com>
 *
 * @version     0.1.2
 */
namespace josephtingiris;

#$GLOBALS["Debug"]=500;

/**
 * The \josephtingiris\Debug class contains methods for debugging.
 */
class Debug
{

    /*
     * public variables.
     */

    /*
     * public functions.
     */

    public function __construct($debug_level_construct = null)
    {

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

        $this->debug("Debug_Level_Source = " . $this->Display_Level_Source,10);

    }

    public function __destruct()
    {

        $this->Stop_Time = microtime(true);
        //echo "start time = ".$this->Start_Time.$this->br();
        //echo "stop time = ".$this->Stop_Time.$this->br();

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

    public function debug($output = null, $debug_level = null, $debug_stdout = '', $debug_timestamp = true, $debug_append = null, $debug_line_number = null, $debug_tag = 'DEBUG')
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
            $debug_error_log = true;
        }

        // an array of regular expressions for terminals that support ansi color codes
        $color_terms = array(
            '/ansi/',
            '/xterm/',
            '/color/',
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

            foreach ($color_terms as $color_term) {
                if (preg_match($color_term,$term)) {
                    $debug_color = true;
                    break;
                } else {
                    $debug_color = false;
                }
            }
            unset($color_term);
        }

        // just make sure color is off if error_log is on
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
            $GLOBALS['HOSTNAME'] = gethostname();
        }

        // use (or set) the debug_append & padding

        $debug_append_pad = 25;

        if (empty($debug_append)) {
            // automatically determine & append the backtrace from the caller
            $backtraces = debug_backtrace();

            $backtrace_file = null;
            $backtrace_line = null;
            $backtrace_class = null;
            $backtrace_function = null;

            $debug_append=null;

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

                if (isset($backtrace['file']) && !is_null($backtrace['file']) && $backtrace['file'] != '') {
                    if ($this->Display_Level >= 50) {
                        // display the entire path
                        $backtrace_file = $backtrace['file'];
                    } else {
                        // only display the file name
                        $backtrace_file = basename($backtrace['file']);
                    }
                }

                if (isset($backtrace['line']) && !is_null($backtrace['line']) && $backtrace['line'] != '') {
                    $backtrace_line = trim($backtrace['line']);
                }

                #print_r($backtrace); // testing

                if ($this->Display_Level >= 20) {

                    // display all files in backtraces; increase padding

                    $debug_append_pad = 55;

                    if ($this->Display_Level >= 30) {

                        // display classes in backtraces; increase padding

                        $debug_append_pad = 75;

                        if (isset($backtrace['class']) && !is_null($backtrace['class']) && $backtrace['class'] != '') {

                            // every class but this class ...
                            if ($backtrace['class'] != __CLASS__) {

                                $backtrace_class = $backtrace['class'];

                                if (isset($backtrace['function']) && !is_null($backtrace['function']) && $backtrace['function'] != '') {
                                    $backtrace_function = $backtrace['function'] . '()';
                                }

                                if (!is_null($backtrace_class) && $backtrace_class != '') {
                                    $debug_append = trim(trim($debug_append), ":");
                                    $debug_append .= " > " . $backtrace_class . " ";
                                }

                            }

                        }

                    }

                    if (!is_null($backtrace_function) && $backtrace_function != '') {

                        // every function but this function ...
                        if ($backtrace['function'] != __FUNCTION__) {
                            $debug_append = trim(trim($debug_append), ":");
                            $debug_append .= "->" . $backtrace_function . ":";
                        }

                    }

                    if (!is_null($backtrace_file) && $backtrace_file != '') {
                        $debug_append = trim(trim($debug_append), ":");
                        $debug_append .= " > " . $backtrace_file . ":";
                    }

                    if (!is_null($backtrace_line) && $backtrace_line != '') {
                        $debug_append .= $backtrace_line;
                    }

                } else {

                    // only display the first file in backtraces; use preset padding

                    if (is_null($debug_append)) {
                        if (!is_null($backtrace_file) && $backtrace_file != '') {
                            $debug_append .= $backtrace_file . ":";
                        }

                        if (!is_null($backtrace_line) && $backtrace_line != '') {
                            $debug_append .= $backtrace_line;
                        }

                        $debug_append = trim(trim($debug_append), ":");
                    }

                }

            }
            unset($backtrace);

            if ($debug_append == null) {
                if (!empty($backtraces)) {

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
                        }

                        if (isset($backtrace['line']) && !is_null($backtrace['line']) && $backtrace['line'] != '') {
                            $backtrace_line = trim($backtrace['line']);
                        }

                        if (!is_null($backtrace_file) && $backtrace_file != '') {
                            $debug_append .= $backtrace_file . ":";
                        }

                        if (!is_null($backtrace_line) && $backtrace_line != '') {
                            $debug_append .= $backtrace_line;
                        }

                        $debug_append = trim(trim($debug_append), ":");

                    }
                    unset($backtrace);
                } else {
                    $debug_append = 'main';
                }
            }

        } else {
            // use the debug_append given in the function argument
        }

        $debug_append=trim(trim($debug_append),"<|>|:");

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

        $hostname_pad = 2;

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

        if ($debug_append != null && $this->Display_Level >= 10) {
            // enhanced debuging; includes prefix, plus more
            $debug_format .= str_pad($debug_prefix, $debug_prefix_pad);
            if ($this->Display_Level >= 40) {
                $debug_format .= ' : ' . str_pad($GLOBALS['HOSTNAME'], $hostname_pad);
            }
            $debug_format .= ' : ' . str_pad($debug_append, $debug_append_pad);
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
            echo trim($debug_format) . "\n";
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

        $this->debug(trim($output), $debug_level);

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
     * private variables.
     */

    private $Display_Level = null;
    private $Display_Level_Source = null;
    private $Start_Time = null;
    private $Stop_Time = null;

    /*
     * private functions.
     */

    private function colorCode($debug_level = '')
    {

        // ansi color codes
        $ansi_reset = "\33[0;0m";
        $ansi_bold = "\33[1m";
        $ansi_bold_off = "\33[22m";
        $ansi_black = $ansi_bold_off . "\33[30m";
        $ansi_boldblack = $ansi_bold . "\33[30m";
        $ansi_background_black = $ansi_bold_off . "\33[40m";
        $ansi_background_boldblack = $ansi_bold . "\33[40m";
        $ansi_red = $ansi_bold_off . "\33[31m";
        $ansi_boldred = $ansi_bold . "\33[31m";
        $ansi_background_red = $ansi_bold_off . "\33[31m";
        $ansi_background_boldred = $ansi_bold . "\33[31m";
        $ansi_green = $ansi_bold_off . "\33[32m";
        $ansi_boldgreen = $ansi_bold . "\33[32m";
        $ansi_background_green = $ansi_bold_off . "\33[42m";
        $ansi_background_boldgreen = $ansi_bold . "\33[42m";
        $ansi_yellow = $ansi_bold_off . "\33[33m";
        $ansi_boldyellow = $ansi_bold . "\33[33m";
        $ansi_background_yellow = $ansi_bold_off . "\33[43m";
        $ansi_background_boldyellow = $ansi_bold . "\33[43m";
        $ansi_blue = $ansi_bold_off . "\33[34m";
        $ansi_boldblue = $ansi_bold . "\33[34m";
        $ansi_background_blue = $ansi_bold_off . "\33[44m";
        $ansi_background_boldblue = $ansi_bold . "\33[44m";
        $ansi_magenta = $ansi_bold_off . "\33[35m";
        $ansi_boldmagenta = $ansi_bold . "\33[35m";
        $ansi_background_magenta = $ansi_bold_off . "\33[45m";
        $ansi_background_boldmagenta = $ansi_bold . "\33[45m";
        $ansi_cyan = $ansi_bold_off . "\33[36m";
        $ansi_boldcyan = $ansi_bold . "\33[36m";
        $ansi_background_cyan = $ansi_bold_off . "\33[46m";
        $ansi_background_boldcyan = $ansi_bold . "\33[46m";
        $ansi_white = "\33[37m";
        $ansi_boldwhite = $ansi_bold . "\33[37m";
        $ansi_background_white = $ansi_bold_off . "\33[47m";
        $ansi_background_boldwhite = $ansi_bold . "\33[47m";
        $ansi_default = "\33[37m";
        $ansi_bolddefault = $ansi_bold . "\33[37m";
        $ansi_background_default = $ansi_bold_off . "\33[47m";
        $ansi_boldbackground_bolddefault = $ansi_bold . "\33[47m";
        // html color names (need to finish)
        $html_reset = '</font>';
        $html_bold = '<b>';
        $html_bold_off = '</b>';
        $html_black = '<font color="Black">';
        $html_boldblack = '<font color="Black">';
        $html_background_black = '<font color="Black">';
        $html_background_boldblack = '<font color="Black">';
        $html_red = '<font color="Red">';
        $html_boldred = '<font color="Red">';
        $html_background_red = '<font color="Red">';
        $html_background_boldred = '<font color="Red">';
        $html_green = '<font color="Green">';
        $html_boldgreen = '<font color="Green">';
        $html_background_green = '<font color="Green">';
        $html_background_boldgreen = '<font color="Green">';
        $html_yellow = '<font color="Yellow">';
        $html_boldyellow = '<font color="Yellow">';
        $html_background_yellow = '<font color="Yellow">';
        $html_background_boldyellow = '<font color="Yellow">';
        $html_blue = '<font color="Blue">';
        $html_boldblue = '<font color="Blue">';
        $html_background_blue = '<font color="Blue">';
        $html_background_boldblue = '<font color="Blue">';
        $html_magenta = '<font color="Purple">';
        $html_boldmagenta = '<font color="Purple">';
        $html_background_magenta = '<font color="Purple">';
        $html_background_boldmagenta = '<font color="Purple">';
        $html_cyan = '<font color="Aqua">';
        $html_boldcyan = '<font color="Aqua">';
        $html_background_cyan = '<font color="Aqua">';
        $html_background_boldcyan = '<font color="Aqua">';
        $html_white = '<font color="White">';
        $html_boldwhite = '<font color="White">';
        $html_background_white = '<font color="White">';
        $html_background_boldwhite = '<font color="White">';
        $html_default = '<font color="Black">';
        $html_bolddefault = '<font color="Black">';
        $html_background_default = '<font color="Black">';
        // uses standard HTML5 color names & previosly defined ansi color names

        if ($this->modPhp()) {
            $color_0 = $html_black;
            $color_1 = $html_boldblack;
            $color_2 = $html_green;
            $color_3 = $html_boldgreen;
            $color_4 = $html_cyan;
            $color_5 = $html_boldcyan;
            $color_6 = $html_blue;
            $color_7 = $html_boldblue;
            $color_8 = $html_magenta;
            $color_9 = $html_boldmagenta;
            $color_10 = $html_red;
            $color_100 = $html_boldred;
            $color_1000 = $html_boldblue;
            $color_reset = $html_reset;
        } else {
            $color_0 = $ansi_white;
            $color_1 = $ansi_boldwhite;
            $color_2 = $ansi_green;
            $color_3 = $ansi_boldgreen;
            $color_4 = $ansi_cyan;
            $color_5 = $ansi_boldcyan;
            $color_6 = $ansi_blue;
            $color_7 = $ansi_boldblue;
            $color_8 = $ansi_magenta;
            $color_9 = $ansi_boldmagenta;
            $color_10 = $ansi_red;
            $color_100 = $ansi_background_white . $ansi_boldred;
            $color_1000 = $ansi_boldyellow;
            $color_reset = $ansi_reset;
        }

        if (strtolower($debug_level) == 'reset') {
            return $color_reset;
        }

        $debug_level = (int) $debug_level;
        if ($debug_level == 0) {
            $color = $color_0;
        }
        if ($debug_level == 1) {
            $color = $color_1;
        }
        if ($debug_level == 2) {
            $color = $color_2;
        }
        if ($debug_level == 3) {
            $color = $color_3;
        }
        if ($debug_level == 4) {
            $color = $color_4;
        }
        if ($debug_level == 5) {
            $color = $color_5;
        }
        if ($debug_level == 6) {
            $color = $color_6;
        }
        if ($debug_level == 7) {
            $color = $color_7;
        }
        if ($debug_level == 8) {
            $color = $color_8;
        }
        if ($debug_level == 9) {
            $color = $color_9;
        }
        if ($debug_level >= 10 && $debug_level < 100) {
            $color = $color_10;
        }
        if ($debug_level >= 100 && $debug_level < 999) {
            $color = $color_100;
        }
        if ($debug_level >= 1000) {
            $color = $color_1000;
        }

        return $color;

    }

    /*
     * protected variables.
     */

    /*
     * protected functions.
     */

}
