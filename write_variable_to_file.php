<?php

	/***********************************************************************************************
	 *
	 * write variable to file library - (c) 2007 Jason Baker
	 *
	 * written by Jason Baker (jason@onejasonforsale.com)
	 * on github: https://github.com/codercowboy/web-scripts
	 * more info: http://www.codercowboy.com
	 * 
	 *
	 * This library helps php developers save the current state of a variable for future usage.
	 * developers can use the library to generate a valid php file with variable initialization code
	 * generated from the variable you provide, and then execute an include statement in another php
	 * file to include the instantiated variable.
	 *
	 * Note added October 2012: 
	 * 
	 * I wrote this a few years back, a few years out of college, before learning about proper
	 * serialization. The script can still have some advantages for seasoned developers, as the
	 * serialization format here is a bit more simplistic/human-readable/human-editable than
	 * php's native serialization format. Beware, I'm certain the simplicity and assumptions within
	 * the code will bite you if you start throwing unicode bytes and other tricks into the mix.
	 *
	 * In particular, PHP developers, you will be interested in these methods to serialized and
	 * deserialize your objects using the native serializers:
	 *
	 *   http://php.net/manual/en/language.oop5.serialization.php
	 * 
	 * Developers, if you want to write a variable or an object at all really to a file, what you 
	 * are doing is called "serialization", that is, serializing a series of bytes that represent
	 * something into a byte stream that's written to disk as a file, or perhaps to the network.
	 * 
	 * Many programming languages such as php, java, objective-c, and such have serialization built
	 * right into the language. If they don't, someone's certainly written a serialization library
	 * for you already. 
	 *
	 * Serialization can be a difficult concept to get correct, because with it comes all kinds of
	 * headaches such as the question of what to do when version 2.0 of a program needs to read in
	 * the serialized version 1.0 format of a file. 
	 * 
	 * Read about serialization on wikipedia:
	 *
	 *   http://en.wikipedia.org/wiki/Serialization
	 *
	 ***********************************************************************************************
	 *
	 * UPDATES:
	 *
	 * 2012/10/08
	 *  - Added updated notes to header.
	 * 2007/2/28
	 *  - Initial version.
	 *
	 ***********************************************************************************************
	 * 
	 * Copyright (c) 2012, Coder Cowboy, LLC. All rights reserved.
	 *  
	 * Redistribution and use in source and binary forms, with or without
	 * modification, are permitted provided that the following conditions are met:
	 * 1. Redistributions of source code must retain the above copyright notice, this
	 * list of conditions and the following disclaimer.
	 * 2. Redistributions in binary form must reproduce the above copyright notice,
	 * this list of conditions and the following disclaimer in the documentation
	 * and/or other materials provided with the distribution.
	 *  
	 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
	 *  
	 * The views and conclusions contained in the software and documentation are those
	 * of the authors and should not be interpreted as representing official policies,
	 * either expressed or implied.
	 * 
	 ***********************************************************************************************/




	/*
	 * get_variable_php - get php variable initialization code in string form, handles arrays and objects as well as primitives
	 *
	 * arguments:
	 *  $variable - the variable to generate initialization code for
	 *  $variable_name - the name of the variable that will be used in the initialization code
	 *
	 * example usage:
	 *
	 *   $my_cat = new Cat();
	 *   $my_cat->name = "samwise";
	 *   $my_cat->weight = 123;
	 *   $my_cat->favorite_toys = array();
	 *   $my_cat->favorite_toys[0] = "stuffed mouse";
	 *   $my_cat->favorite_toys[1] = "stuffed mouse #2";
	 *   $my_cat->favorite_toys["absolute favorite"] = "stuffed mouse #3";
	 *
	 *   echo get_variable_php($my_cat, "the_best_cat_ever");
	 *
	 * exampe output:
	 *   $the_best_cat_ever = new Cat();
	 *   $the_best_cat_ever->name = "samwise";
	 *   $the_best_cat_ever->weight = 123;
	 *   $the_best_cat_ever->favorite_toys = array();
	 *   $the_best_cat_ever->favorite_toys[0] = "stuffed mouse";
	 *   $the_best_cat_ever->favorite_toys[1] = "stuffed mouse #2";
	 *   $the_best_cat_ever->favorite_toys["absolute favorite"] = "stuffed mouse #3";
	 *
	 * warning for classes without no-argument constructors:
	 *
	 *   code generated by this function always instantiates objects with the no-argument constructor. php will throw
	 *   a warning like the following if your class doesnt have this type of constructor:
	 *
	 *     PHP Warning:  Missing argument 2 for cat() in cat.php
	 *
	 *   this warning can usually be ignored.
	 */

	function get_variable_php($variable, $variable_name)
	{
		$value = "";

		//write out the variable at the beginning of the line if it's specified
		if ($variable_name != "")
		{
			$value .= "$" . $variable_name . " = ";
		}

		if (is_array($variable))
		{
			$value .= "array();\n";
			foreach($variable as $arraykey => $arrayvalue)
			{
				$value .= get_variable_php($arrayvalue, $variable_name . "[" . $arraykey . "]");
			}
			$variable_name = ""; //dont let the end statement be added later in the function
		}
		else if (is_string($variable))
		{
			$value .= "\"" . $variable . "\"";
		}
		else if (is_integer($variable) || is_double($variable) || is_float($variable) || is_long($variable))
		{
			$value .= $variable;
		}
		else if (is_bool($variable))
		{
			if ($variable)
			{
				$value .= "true";
			}
			else
			{
				$value .= "false";
			}

		}
		else if (is_object($variable))
		{
			//instantiate the class with no-arg constructor
			$value .= "new " . get_class($variable) . "();\n";

			//iterate through and instantiate each property of the object
			$vars = get_object_vars($variable);
			foreach ($vars as $varkey  => $varvalue)
			{
				$value .= get_variable_php($varvalue, $variable_name . "->" . $varkey);
			}

			$variable_name = ""; //dont let the end statement be added later in the function
		}
		else if (is_null($variable))
		{
			$value .= "NULL";
		}
		else
		{
			echo "get_variable_php does not know how to handle vars of type " . gettype($variable) . "\n";
			exit;
		}

		if ($variable_name != "")
		{
			//if the variable name is specified, write out the semicolon and the end of the line
			$value .= ";\n";
		}

		return $value;
	}


	/*
	 * write_variable_to_file - creates a php file with the initialization code for $var
	 *
	 * arguments:
	 *   $var - the variable to generate php initialization code for
	 *   $var_name - the name to call the variable in the generated code
	 *   $filename - the filename to output the file to
	 */
	function write_variable_to_file($var, $var_name, $filename)
	{
		$output = "<?php\n\n" . get_variable_php($var, $var_name) . "\n?>\n";

		if (!$handle = fopen($filename, 'w+'))
		{
			echo "Cannot open file ($filename)";
			exit;
		}

		if (fwrite($handle, $output) === FALSE)
		{
			echo "Cannot write to file ($filename)";
			exit;
		}

		fclose($handle);
	}

?>