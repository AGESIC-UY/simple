<?php
/*
 * Class: Safer eval()
 * Version: 0.2 (Alpha)
 * Web: http://evileval.sourceforge.net/
 * License: GPL
 *
 */

class SaferEval {

    var $source, $allowedCalls, $allowedTokens, $allowedVariables, $disallowedExpressions;

    function SaferEval() {
        $this->allowedCalls = array(
            // Function Handling Functions
            //	'func_get_arg',		// Return an item from the argument list
            //	'func_get_args',	// Returns an array comprising a function's argument list
            //	'func_num_args',	// Returns the number of arguments passed to the function
            //	'function_exists', 	// Return TRUE if the given function has been defined
            // Mathematical functions
            'ceil', // Round fractions up
            'floor', // Round fractions down
            'fmod', // Returns the floating point remainder (modulo) of the division of the arguments
            'log', // Natural logarithm
            'mt_rand', // Generate a better random value
            'mt_srand', // Seed the better random number generator
            'pow', // Exponential expression
            'rand', // Generate a random integer
            'sqrt', // Square root
            'srand', // Seed the random number generator
            // Variable handling
            'empty', // Determine whether a variable is empty
            'floatval', // Get float value of a variable
            'intval', // Get the integer value of a variable
            'is_array', // Finds whether a variable is an array
            'is_binary', // Finds whether a variable is a native binary string
            'is_bool', // Finds out whether a variable is a boolean
            'is_double', // Alias of is_float
            'is_float', // Finds whether the type of a variable is float
            'is_int', // Find whether the type of a variable is integer
            'is_integer', // Alias of is_int
            'is_long', // Alias of is_int
            'is_null', // Finds whether a variable is NULL
            'is_numeric', // Finds whether a variable is a number or a numeric string
            'is_real', // Alias of is_float
            'is_scalar', // Finds whether a variable is a scalar
            'is_string', // Find whether the type of a variable is string
            'is_unicode', // Finds whether a variable is a unicode string
            'isset', // Determine whether a variable is set
            'strval', // Get string value of a variable
            'unset', // Unset a given variable
            // Array functions
            'array_change_key_case', // Changes all keys in an array
            'array_chunk', // Split an array into chunks
            'array_combine', // Creates an array by using one array for keys and another for its values
            'array_count_values', // Counts all the values of an array
            'array_diff_assoc', // Computes the difference of arrays with additional index check
            'array_diff_key', // Computes the difference of arrays using keys for comparison
            'array_diff', // Computes the difference of arrays
            'array_fill_keys', // Fill an array with values, specifying keys
            'array_fill', // Fill an array with values
            'array_flip', // Exchanges all keys with their associated values in an array
            'array_intersect_assoc', // Computes the intersection of arrays with additional index check
            'array_intersect_key', // Computes the intersection of arrays using keys for comparison
            'array_intersect', // Computes the intersection of arrays
            'array_key_exists', // Checks if the given key or index exists in the array
            'array_keys', // Return all the keys of an array
            'array_merge_recursive', // Merge two or more arrays recursively
            'array_merge', // Merge one or more arrays
            'array_multisort', // Sort multiple or multi-dimensional arrays
            'array_pad', // Pad array to the specified length with a value
            'array_pop', // Pop the element off the end of array
            'array_product', // Calculate the product of values in an array
            'array_push', // Push one or more elements onto the end of array
            'array_rand', // Pick one or more random entries out of an array
            'array_reverse', // Return an array with elements in reverse order
            'array_search', // Searches the array for a given value and returns the corresponding key if successful
            'array_shift', // Shift an element off the beginning of array
            'array_slice', // Extract a slice of the array
            'array_splice', // Remove a portion of the array and replace it with something else
            'array_sum', // Calculate the sum of values in an array
            'array_unique', // Removes duplicate values from an array
            'array_unshift', // Prepend one or more elements to the beginning of an array
            'array_values', // Return all the values of an array
            'array', // Create an array
            'arsort', // Sort an array in reverse order and maintain index association
            'asort', // Sort an array and maintain index association
            'compact', // Create array containing variables and their values
            'count', // Count elements in an array, or properties in an object
            'current', // Return the current element in an array
            'each', // Return the current key and value pair from an array and advance the array cursor
            'end', // Set the internal pointer of an array to its last element
            'in_array', // Checks if a value exists in an array
            'key', // Fetch a key from an associative array
            'krsort', // Sort an array by key in reverse order
            'ksort', // Sort an array by key
            'natcasesort', // Sort an array using a case insensitive "natural order" algorithm
            'natsort', // Sort an array using a "natural order" algorithm
            'next', // Advance the internal array pointer of an array
            'pos', // Alias of current
            'prev', // Rewind the internal array pointer
            'range', // Create an array containing a range of elements
            'reset', // Set the internal pointer of an array to its first element
            'rsort', // Sort an array in reverse order
            'shuffle', // Shuffle an array
            'sizeof', // Alias of count
            'sort', // Sort an array
            'max', //Maximum element of array
            'min', //Minimum element of array
            // Strings Functions
            'chop', // Alias of rtrim
            'count_chars', // Return information about characters used in a string
            'explode', // Split a string by string
            'implode', // Join array elements with a string
            'join', // Alias of implode
            'levenshtein', // Calculate Levenshtein distance between two strings
            'ltrim', // Strip whitespace (or other characters) from the beginning of a string
            'metaphone', // Calculate the metaphone key of a string
            'money_format', // Formats a number as a currency string
            'number_format', // Format a number with grouped thousands
            'nl2br',  //Convierte newline a <br />
            'rtrim', // Strip whitespace (or other characters) from the end of a string
            'similar_text', // Calculate the similarity between two strings
            'soundex', // Calculate the soundex key of a string
            'str_getcsv', // Parse a CSV string into an array
            'str_ireplace', // Case-insensitive version of str_replace.
            'str_pad', // Pad a string to a certain length with another string
            'str_repeat', // Repeat a string
            'str_replace', // Replace all occurrences of the search string with the replacement string
            'str_rot13', // Perform the rot13 transform on a string
            'str_shuffle', // Randomly shuffles a string
            'str_split', // Convert a string to an array
            'str_word_count', // Return information about words used in a string
            'strcasecmp', // Binary safe case-insensitive string comparison
            'strchr', // Alias of strstr
            'strcmp', // Binary safe string comparison
            'strcspn', // Find length of initial segment not matching mask
            'stripos', // Find position of first occurrence of a case-insensitive string
            'stristr', // Case-insensitive strstr
            'strlen', // Get string length
            'strnatcasecmp', // Case insensitive string comparisons using a "natural order" algorithm
            'strnatcmp', // String comparisons using a "natural order" algorithm
            'strncasecmp', // Binary safe case-insensitive string comparison of the first n characters
            'strncmp', // Binary safe string comparison of the first n characters
            'strpbrk', // Search a string for any of a set of characters
            'strpos', // Find position of first occurrence of a string
            'strrchr', // Find the last occurrence of a character in a string
            'strrev', // Reverse a string
            'strripos', // Find position of last occurrence of a case-insensitive string in a string
            'strrpos', // Find position of last occurrence of a char in a string
            'strspn', // Find length of initial segment matching mask
            'strstr', // Find first occurrence of a string
            'strtolower', // Make a string lowercase
            'strtoupper', // Make a string uppercase
            'strtr', // Translate certain characters
            'substr_compare', // Binary safe comparison of 2 strings from an offset, up to length characters
            'substr_count', // Count the number of substring occurrences
            'substr_replace', // Replace text within a portion of a string
            'substr', // Return part of a string
            'trim', // Strip whitespace (or other characters) from the beginning and end of a string
            'ucfirst', // Make a string's first character uppercase
            'ucwords', // Uppercase the first character of each word in a string
            'wordwrap', // Wraps a string to a given number of characters
            //Date functions
            'date_create',
            'strftime',
            'strtotime',
	          'date',
            //Serializacion
            'json_encode',
            'json_decode',
            //Booleanos
            'true',
            'false',
            'NULL',
            //Helpers
            'matrix_to_html'
        );
        $this->allowedTokens = array(
            'T_AND_EQUAL', // assignment operators
            'T_ARRAY', // array(), array syntax
            'T_ARRAY_CAST', // type-casting
            'T_AS', // foreach
            'T_BOOLEAN_AND', // logical operators
            'T_BOOLEAN_OR', // logical operators
            'T_BOOL_CAST', // type-casting
            'T_BREAK', // break
            'T_CASE', // switch
            'T_CHARACTER', // ?
            //	'T_COMMENT',			// comments
            'T_CONCAT_EQUAL', // assignment operators
            'T_CONSTANT_ENCAPSED_STRING', // string syntax
            'T_CONTINUE', //
            'T_CURLY_OPEN', //
            'T_DEC', // incrementing/decrementing operators
            'T_DECLARE', // declare
            'T_DEFAULT', // switch
            'T_DIV_EQUAL', // assignment operators
            'T_DNUMBER', // floating point numbers
            'T_DO', // do..while
            'T_DOUBLE_ARROW', // array syntax
            'T_DOUBLE_CAST', // type-casting
            //	'T_ECHO',                       //
            'T_ELSE', // else
            'T_ELSEIF', // elseif
            'T_EMPTY', // empty()
            'T_ENCAPSED_AND_WHITESPACE', // ?
            'T_ENDDECLARE', // declare, alternative syntax
            'T_ENDFOR', // for, alternative syntax
            'T_ENDFOREACH', // foreach, alternative syntax
            'T_ENDIF', // if, alternative syntax
            'T_ENDSWITCH', // switch, alternative syntax
            'T_FOR', // for
            'T_FOREACH', // foreach
            'T_IF', // if
            'T_INC', // incrementing/decrementing operators
            'T_INT_CAST', // type-casting
            'T_ISSET', // isset()
            'T_IS_EQUAL', // comparison operators
            'T_IS_GREATER_OR_EQUAL', // comparison operators
            'T_IS_IDENTICAL', // comparison operators
            'T_IS_NOT_EQUAL', // comparison operators
            'T_IS_NOT_IDENTICAL', // comparison operators
            'T_IS_SMALLER_OR_EQUAL', // comparison operators
            'T_LNUMBER', // integers
            'T_LOGICAL_AND', // logical operators
            'T_LOGICAL_OR', // logical operators
            'T_LOGICAL_XOR', // logical operators
            'T_MINUS_EQUAL', // assignment operators
            //	'T_ML_COMMENT',			// comments (PHP 4 only)
            'T_MOD_EQUAL', // assignment operators
            'T_MUL_EQUAL', // assignment operators
            'T_NUM_STRING', // ?
            'T_OR_EQUAL', // assignment operators
            'T_PLUS_EQUAL', // assignment operators
            'T_RETURN', // returning values
            'T_SL', // bitwise operators
            'T_SL_EQUAL', // assignment operators
            'T_SR', // bitwise operators
            'T_SR_EQUAL', // assignment operators
            'T_STRING', // ?
            'T_STRING_CAST', // type-casting
            'T_STRING_VARNAME', // ?
            'T_SWITCH', // switch
            'T_UNSET', // unset()
            'T_UNSET_CAST', // (not documented; casts to NULL)
            'T_VARIABLE', // variables
            'T_WHILE', // while, do..while
            'T_WHITESPACE', //
            'T_XOR_EQUAL', // assignment operators
        );
        $this->globalVariables = array();
        $this->allowedVariables = array();
        $this->disallowedExpressions = array(// Probably unsafe to change these
            '/`/', // Shell execution operator: "`"
            '/\$\W/', // Variable variables: any "$" which is not "$_" or "$alphanumeric"
            '/(\|\})\s*\(/', // Variable functions: "] (" or "} ("
            '/\$\w\w*\s*\(/', // Variable functions: "$_ (" or "$alphanumeric"
            //	'/\$\w\w*\s*(\/\/|\/\*)/',	// Comment after variable: "$alphanumeric //" or "$alphanumeric /*"
            //	'/(\]|\})\s*\//',		// Comment after parentheses: "] /" or "} /"
        );
    }

    function evalSyntax($code) { // Separate function for checking syntax without breaking the script
        ob_start(); // Catch potential parse error messages
        $code = eval('if(0){' . "\n" . $code . "\n" . '}'); // Put $code in a dead code sandbox to prevent its execution
        ob_end_clean();
        return $code !== false;
    }

    function checkScript($code, $execute) {
        $this->execute = $execute;
        $this->code = $code;
        $this->tokens = token_get_all('<?php ' . $this->code . ' ?>');
        $this->errors = array();
        $this->braces = 0;

        // STEP 1: SYNTAX - Check if braces are balanced
        foreach ($this->tokens as $token) {
            if ($token == '{')
                $this->braces = $this->braces + 1;
            else if ($token == '}')
                $this->braces = $this->braces - 1;
            if ($this->braces < 0) { // Closing brace before one is open
                $this->errors[0]['name'] = 'Syntax error.';
                break;
            }
        }

        if (empty($this->errors)) {
            if ($this->braces)
                $this->errors[0]['name'] = 'Unbalanced braces.';
        }

        // STEP 2: SYNTAX - Check if syntax is valid
        else if (!$this->evalSyntax($this->code)) {
            $this->errors[0]['name'] = 'Syntax error.';
        }

        // STEP 3: EXPRESSIONS - Check against various insecure elements
        if (empty($this->errors))
            foreach ($this->disallowedExpressions as $disallowedExpression) {
                unset($matches);
                preg_match($disallowedExpression, $this->code, $matches);
                if ($matches) {
                    $this->errors[0]['name'] = 'Execution operator / variable function name / variable variable name detected.';
                    break;
                }
            }

        // STEP 4: TOKENS
        if (empty($this->errors)) {
            unset($this->tokens[0]);
            unset($this->tokens[0]);
            array_pop($this->tokens);
            array_pop($this->tokens);

            $i = 0;
            foreach ($this->tokens as $key => $token) {
                $i++;
                if (is_array($token)) {
                    $id = token_name($token[0]);
                    switch ($id) {
                        case('T_VARIABLE'):
                            if (in_array($token[1], $this->allowedVariables) === false) {
                                $this->errors[$i]['name'] = 'Illegal variable: ' . $token[1];
                                $this->errors[$i]['line'] = $token[2];
                            }
                            break;
                        case('T_STRING'):
                            if (in_array($token[1], $this->allowedCalls) === false) {
                                $this->errors[$i]['name'] = 'Illegal function: ' . $token[1];
                                $this->errors[$i]['line'] = $token[2];
                            }
                            break;
                        default:
                            if (in_array($id, $this->allowedTokens) === false) {
                                $this->errors[$i]['name'] = 'Illegal token: ' . $token[1];
                                $this->errors[$i]['line'] = $token[2];
                            }
                            break;
                    }
                }
            }
        }

        if (!empty($this->errors)) {
            return $this->errors;
        } else if ($this->execute) {
            foreach ($this->globalVariables as $globalVariable) {
                global $$globalVariable;
            }
            eval($this->code);
        }
    }

    function htmlErrors($errors = null) {
        print_r($errors);
        if ($errors) {
            $this->errors = $errors;
            $this->errorsHTML = '<h2>Errors:</h2>';
            $this->errorsHTML .= '<dl>';
            foreach ($this->errors as $error) {
                if ($error['line']) {
                    $this->errorsHTML .= '<dt>Line ' . $error['line'] . '</dt>';
                }
                $this->errorsHTML .= '<dd>' . $error['name'] . '</dd>';
            }
            $this->errorsHTML .= '</dl>';
            return($this->errorsHTML);
        }
    }

}
