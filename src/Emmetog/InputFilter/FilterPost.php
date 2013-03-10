<?php

namespace Emmetog\InputFilter;

use Emmetog\InputFilter\InputFilter;

class FilterPost extends InputFilter
{
    protected static $unfilteredInputs = array();
    protected static $filteredInputs = array();

    final public static function setInput($input) {
	self::$filteredInputs = array();
	self::$unfilteredInputs = $input;
    }

    /**
     * 
     * @param type $key
     * @param type $filter
     * @return mixed 
     */
    public static function getFilteredInput($key, $filter) {
	// First check if we have already filtered the input to save filtering it again.
	if (array_key_exists($key, self::$filteredInputs)) {
	    return self::$filteredInputs[$key];
	}

	// Check if the key exists in the unfiltered input array.
	if (!array_key_exists($key, self::$unfilteredInputs)) {
	    return null;
	}

	// Check if the filter is valid.
	if (!in_array($filter, self::$allowedFilters)) {
	    throw new InputFilterInvalidFilterException('Invalid filter specified: ' . $filter);
	}
	
	self::$filteredInputs[$key] = self::$filter(self::$unfilteredInputs[$key]);
	unset(self::$unfilteredInputs[$key]);
	
	return self::$filteredInputs[$key];
    }
}

?>
