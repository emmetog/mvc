<?php

namespace Emmetog\InputFilter;

abstract class InputFilter
{
    protected static $allowedFilters = array(
	'filterString',
	'filterInteger',
    );

    public static function filterString($unfilteredInputString) {
	return $unfilteredInputString;
    }
    
    public static function filterInteger($unfilteredInputInteger) {
	return (int) $unfilteredInputInteger;
    }

}

class InputFilterException extends \Exception
{
    
}

class InputFilterInvalidFilterException extends InputFilterException
{
    
}

?>
