<?php

namespace Emmetog\InputFilter;

use Emmetog\InputFilter\InputFilter;

class FilterCommandLineArg extends InputFilter
{

    protected static $unfilteredInputs = array();
    protected static $filteredInputs = array();

    final public static function setInput($inputs)
    {
        self::$filteredInputs = array();
        self::$unfilteredInputs = array();

        // Remove the name of the file.
        array_shift($inputs);

        foreach ($inputs as $input)
        {
            if (substr($input, 0, 2) == '--')
            {
                // Long option
                $input = substr($input, 2);
                $input = explode('=', $input);

                if (count($input) < 2)
                {
                    $input[1] = true;
                }
                self::$unfilteredInputs[$input[0]] = $input[1];
            }
            elseif (substr($input, 0, 1) == '-')
            {
                // Short option
                $input = substr($input, 1);

                $input = explode('=', $input);

                if (strlen($input[0]) > 1)
                {
                    // Multiple short option
                    $options = str_split($input[0], 1);
                    foreach ($options as $option)
                    {
                        self::$unfilteredInputs[$option] = true;
                    }
                    continue;
                }

                if (count($input) < 2)
                {
                    $input[1] = true;
                }
                self::$unfilteredInputs[$input[0]] = $input[1];
            }
        }
    }

    /**
     * Gets a filtered input from the command line
     * 
     * @param string $key The variable to get
     * @param type $filter The filter to apply to the input variable
     * @return mixed The filtered input
     */
    public static function getFilteredInput($key, $filter, $required = false)
    {
        // Init the inputs on the fly.
        if (isset($GLOBALS['argv']) && !empty($GLOBALS['argv']))
        {
            self::setInput($GLOBALS['argv']);
            unset($GLOBALS['argv']);
        }

        // First check if we have already filtered the input to save filtering it again.
        if (array_key_exists($key, self::$filteredInputs))
        {
            return self::$filteredInputs[$key];
        }

        // Check if the key exists in the unfiltered input array.
        if (!array_key_exists($key, self::$unfilteredInputs))
        {
            return null;
        }

        // Check if the filter is valid.
        if (!in_array($filter, self::$allowedFilters))
        {
            throw new InputFilterInvalidFilterException('Invalid filter specified: ' . $filter);
        }

        self::$filteredInputs[$key] = self::$filter(self::$unfilteredInputs[$key]);
        unset(self::$unfilteredInputs[$key]);

        if ($required && is_bool(self::$filteredInputs[$key]))
        {
            return null;
        }

        return self::$filteredInputs[$key];
    }

}

?>
