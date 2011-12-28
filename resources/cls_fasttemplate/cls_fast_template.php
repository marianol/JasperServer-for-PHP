<?php
/**
 *CVS Revision. 1.5.2
    $Id: cls_fast_template.php,v 1.64 2006/12/31 13:36:48 lvalics Exp $
 *
 * <b>Description</b>: PHP extension for managing templates and performing variable interpolation
 *
 *
 * <b>What is a template?</b>
 *
 * A template is a text file with variables in it. When a template is parsed, the variables are interpolated to text. (The text can be a few bytes or a few hundred kilobytes.) Here is a simple template with one variable ('{NAME}'):
 *
 *<kbd>Hello {NAME}.  How are you?</kbd>
 *
 *<b>When are templates useful?</b>
 *
 *Templates are very useful for CGI programming, because adding HTML to your PHP code clutters your code and forces you to do any HTML modifications. By putting all of your HTML in seperate template files, you can let a graphic or interface designer change the look of your application without having to bug you, or let them muck around in your PHP code.
 *
 *<b>Why use FastTemplate?</b>
 *
 *<i>Speed</i>
 *
 *FastTemplate parses with a single regular expression. It just does simple variable interpolation (i.e. there is no logic that you can add to templates - you keep the logic in the code). That's why it's has 'Fast' in it's name!
 *
 *<i>Flexibility</i>
 *
 *The API is robust and flexible, and allows you to build very complex HTML documents/interfaces. It is also completely written in PHP and (should) work on Unix or NT. Also, it isn't restricted to building HTML documents -- it could be used to build any ascii based document (postscript, XML, email - anything).
 *
 *What are the steps to use FastTemplate?
 *
 *The main steps are:
 *1. define
 *2. assign
 *3. parse
 *4. FastPrint
 *
 *
 *<b>Variables</b>
 *
 *A variable is defined as:<br><kbd>{([A-Z0-9_]+)}</kbd><br>
 *This means, that a variable must begin with a curly brace '{'. The second and remaining characters must be uppercase letters or digits 'A-Z0-9'. Remaining characters can include an underscore. The variable is terminated by a closing curly brace '}'.<br>
 *For example, the following are valid variables:<br>{FOO}<br>{F123F}<br>{TOP_OF_PAGE}
 *
 *<b>Variable Interpolation (Template Parsing)</b>
 *
 *If a variable cannot be resolved to anything, a warning is printed to STDERR. See strict() and no_strict() for more info. <br>
 *Some examples will make this clearer:
 *
 *Assume
 *<code>
 *$FOO = "foo";
 *$BAR = "bar";
 *$ONE = "1";
 *$TWO = "2";
 *$UND = "_";
 *</code>
 *
 *
 *Variable    Interpolated/Parsed<br>
 *------------------------------------
 * - {FOO}            <i>foo</i>
 * - {FOO}-{BAR}      <i>foo-bar</i>
 * - {ONE_TWO}        <i>{ONE_TWO}</i> // {ONE_TWO} is undefined!
 * - {ONE}{UND}{TWO}  <i>1_2</i>
 * - ${FOO}           <i>$foo</i>
 * - $25,000          <i>$25,000</i>
 * - {foo}            <i>{foo}</i>     // Ignored, it's not valid, nor will it, generate any error messages.
 *
 *
 * <b>FULL EXAMPLE</b>
 *
 *This example will build an HTML page that will consist of a table. The table will have 3 numbered rows. The first step is to decide what templates we need. In order to make it easy for the table to change to a different number of rows, we will have a template for the rows of the table, another for the table, and a third for the head/body part of the HTML page.
 *
 *Below are the templates. (Pretend each one is in a separate file.)
 *
 *<code>
 *<!-- NAME: main.html -->
 *<html>
 *<head><title>{TITLE}</title>
 *</head>
 *<body>
 *{MAIN}
 *</body>
 *</html>
 *<!-- END: main.html -->
 *</code>
 *<code>
 *<!-- NAME: table.html -->
 *<table>
 *{ROWS}
 *</table>
 *<!-- END: table.html -->
 *</code>
 *<code>
 *<!-- NAME: row.html -->
 *<tr>
 *<td>{NUMBER}</td>
 *<td>{BIG_NUMBER}</td>
 *</tr>
 *<!-- END: row.html -->
 *</code>
 *
 *Now we can start coding...
 *
 *<code>
 *
 *<?
 *include("cls_fast_template.php");
 *$tpl = new FastTemplate("/path/to/templates");
 *$tpl->define( array( main   => "main.html",
 *table  => "table.html",row    => "row.html"    ));
 *
 *$tpl->assign(TITLE,"FastTemplate Test");
 *
 *for ($n=1; $n <= 3; $n++)
 *{
 *$Number = $n;
 *$BigNum = $n*10;
 *$tpl->assign( array(  NUMBER      =>  $Number,
 *BIG_NUMBER  =>  $BigNum ));
 *$tpl->parse(ROWS,".row");
 *}
 *$tpl->parse(MAIN, array("table","main"));
 *Header("Content-type: text/plain");
 *$tpl->FastPrint();
 *exit;
 *?>
 *</code>
 *
 *
 * When run it returns:
 *<code>
 *<!-- NAME: main.html -->
 *<html>
 *<head><title>FastTemplate Test</title>
 *</head>
 *<body>
 *<!-- NAME: table.html -->
 *<table>
 *<!-- NAME: row.html -->
 *<tr>
 *<td>1</td>
 *<td>10</td>
 *</tr>
 *<!-- END: row.html -->
 *<!-- NAME: row.html -->
 *<tr>
 *<td>2</td>
 *<td>20</td>
 *</tr>
 *<!-- END: row.html -->
 *<!-- NAME: row.html -->
 *<tr>
 *<td>3</td>
 *<td>30</td>
 *</tr>
 *<!-- END: row.html -->
 *
 *</table>
 *<!-- END: table.html -->
 *</body>
 *</html>
 *<!-- END: main.html -->
 *
 *</code>
 *
 * If you're thinking you could have done the same thing in a few lines of plain PHP, well yes you probably could. But, how would a graphic designer tweak the resulting HTML? How would you have a designer editing the HTML while you're editing another part of the code? How would you save the output to a file, or pipe it to another application? How would you make your application multi-lingual? How would you build an application that has options for high graphics, or text-only? FastTemplate really starts to shine when you are building mid to large scale web applications, simply because it begins to seperate the application's generic logic from the specific implementation.
 *
 */

/**  Modified By: MarianoL (Mariano Luna from Buenos Aires, Argentina)
*   This Mod allows you to use nested templates and multimple templates in a single file
*   The format in the define is:
$used_tpl = array(
	'footer' => 'template.tpl#FOOTER',
	'header' => 'template.tpl#HEADER',
	'main' => 'template.tpl#MAIN'
	);
*   Template File Format (template.tpl):
<!-- START TEMPLATE HEADER -->
<html>
<title>{PAGE_TITLE}</title>
</head><body>
<!-- END TEMPLATE HEADER -->
<!-- START TEMPLATE MAIN -->
<p>Here goes the Main content {VAR} </p>
<!-- END TEMPLATE MAIN -->
<!-- START TEMPLATE FOOTER -->
<p>This is the footer text. {FOOTVAR}</P>
</body></html>
<!-- END TEMPLATE FOOTER -->
*
*   Based on the modification by Donatien in the originaly class
*   (Sebastian Grignoli, from Buenos Aires, Argentina)
*   in order to support nested templates in a single file.
*/

/*
* Original Perl module CGI::FastTemplate by Jason Moore jmoore@sober.com
* PHP3 port by CDI cdi@thewebmasters.net
* PHP3 Version Copyright (c) 1999 CDI, cdi@thewebmasters.net,
* All Rights Reserved.
* Perl Version Copyright (c) 1998 Jason Moore jmoore@sober.com.
* All Rights Reserved.
* This program is free software; you can redistribute it and/or modify it
* under the GNU General Artistic License, with the following stipulations:
* Changes or modifications must retain these Copyright statements. Changes
* or modifications must be submitted to both AUTHORS.
* This program is released under the General Artistic License.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the Artistic
* License for more details. This software is distributed AS-IS.
*
* Voituk Vadim voituk@asg.kiev.ua
* -- Fixed bug on calculation conditon to removing all unassigned template
* variables and added support for accessing object fields in template
* (using get() method of the object) like {Object.field}
*
* Alex Tonkov alex.tonkov@gmail.com
* -- Added define blocs which parsed in condition with defining some specified parse variable
* -- Added include block which include another template by statement (like SSI do)
* <!--#include file="include2.html"-->
* It is usefull if you have several different templates for different
* parts of page and you dont need to write any php code to gather all "blocks" of the page
* Also is very helpfull from designer point of view, he will see in a visual editor te result.
*
* AiK artvs@clubpro.spb.ru
*  -- more strict dynamic templates handling, including "silently removing"
* of unassigned  dynamic blocks
*  -- showDebugInfo() method that print into html conole some debug info
*
* Allyson Francisco de Paula Reis ragen@oquerola.com
*  -- Cache functions added
*
* Wilfried Trinkl - wisl@gmx.at
*  -- added Fast Write function
*
* GraFX webmaster@grafxsoftware.com
*  -- used str_replace instead of ereg_replace, this latest is not installed
* on a lot of servers and give out error.
*  -- added Pattern Assign - when variables or constants are the same as the
* template keys, these functions may be used as they are. Using these functions,
* can help you reduce the number of the assign functions in the php files, very
* useful for language files
*  -- get_magic_quotes_gpc() problem on some servers are fixed. Seems that some
* servers not read magic quotes correcttly and template files are messed up.
*/




/**
 * The <code>FastTemplate</code> class provides easy and quite fast
 * template handling functionality.
 * @author Jason Moore jmoore@sober.com.
 * @author CDI cdi@thewebmasters.net
 * @author Artyem V. Shkondin aka AiK artvs@clubpro.spb.ru
 * @author Allyson Francisco de Paula Reis ragen@oquerola.com
 * @author GraFX Software Solutions webmaster@grafxsoftware.com
 * @author Wilfried Trinkl wisl@gmx.at
 * projects based on Fast Templates at www.grafxsoftware.com
*/


function rewrite_link_href_callback($matches)
{
	$insideRegex = "/href\s*=(.*?)[\\\"']?([^\\\"' >]+)[\\\"'> ]/is";
	return preg_replace($insideRegex, 'href="'.$GLOBALS['REWRITE_SRC_PATH'].'\\2"', implode(' ', $matches));
}


if (!class_exists('FastTemplate')) {

class FastTemplate {
/**
 *@access private
 */
        var $FILELIST 		=        array();       //        Holds the array of filehandles
                                                 	//        FILELIST[HANDLE] == "fileName"
/**
 *@access private
 */
        var $DYNAMIC  		=        array();       //        Holds the array of dynamic
                                                 	//        blocks, and the fileHandles they
                                                 	//        live in.
/**
 *@access private
 */
	var $PHP_IN_HTML	= 	false;		// 	  If the php code is executed inside html templates

/**
 *@access private
 */
        var $PARSEVARS  	=        array();       //        Holds the array of Variable
                                                 	//        handles.
                                                 	//        PARSEVARS[HANDLE] == "value"
/**
 *@access private
 */
        var $LOADED  		=        array();       //        We only want to load a template
                                                 	//        once - when it's used.
                                                 	//        LOADED[FILEHANDLE] == 1 if loaded
                                                 	//        undefined if not loaded yet.
/**
 *@access private
 */
        var $HANDLE  		=        array();       //        Holds the handle names assigned

/**
 *@access private
 */
        var $WARNINGS  		=        array(); 		//        Holds the warnings
                                                 	//        by a call to parse()
/**
 *@access private
 */
        var $ROOT    		=        "";            //        Holds path-to-templates
/**
 *@access private
 */
        var $WIN32   		=        false;         //        Set to true if this is a WIN32 server
/**
 *@access private
 */
        var $ERROR   		=        "";            //        Holds the last error message
/**
 *@access private
 */
        var $LAST    		=        "";            //        Holds the HANDLE to the last
                                                 	//        template parsed by parse()
/**
 *@access private
 */
        var $STRICT_DEBUG	=        false;          //        Strict template checking.
                                                 	//        Unresolved vars in templates will
                                                 	//        generate a warning when found.
                                                 	//				used for debug.
/**
 *@access private
 */
        var $STRICT  		=        false;          //        Strict template checking.
                                                 	//        Unresolved vars in templates will
                                                 	//        generate a warning when found.

/**
 *@access public
 */
	var $REWRITE_SRC_PATH   = "";           // Rewrite js, css, and img src from template to a custom path

//NEW by AiK
/**
 *@access private
 */
		    var $start;       						// holds time of start generation

//NEW by Allyson Francisco de Paula Reis
/**
 *@access private
 */
			var $USE_CACHE  =   false;      		//  Enable caching mode.
	                                				//  Default: false
/**
 *@access private
 */
	    var $DELETE_CACHE  =   false;      		//  Enable caching mode.
	                                				//  Default: false
/**
 *@access private
 */
			var $UPDT_TIME  =   '60';       		//  Time in seconds to expire cache
	                                				//  files
/**
 *@access private
 */
			var $CACHE_PATH =   '/tmp';  			//  Dir for save cached files
/**
 *@access private
 */
			var $CACHING = "";						// filename for caching

//NEW by Voituk Vadim
/**
 *@access private
 */
			var $STRIP_COMMENTS 	= true;			// Do comments deletion on template loading
/**
 *@access private
 */
			var $COMMENTS_START 	= "{*";			// Start of template comments
/**
 *@access private
 */
			var $COMMENTS_END 		= "*}";			// Start of template comments

/**
 *@access private
 */

 			var $PATTERN_VARS_VARIABLE=array();	// Patterns are the ones which are used by the multiple assigned functions (this is for variable oriented language, config etc files)
/**
 *@access private
 */

			var $PATTERN_VARS_DEFINE=array();				// Patterns are the ones which are used by the multiple assigned functions (this is for define oriented language files)

/**
 * Constructor.
 * @param template root.
 * @return FastTemplate FastTemplate object
 */
    function FastTemplate ($pathToTemplates = "")
      {
          //if the track_errors configuration option is turned on (it defaults to off).
		  global $php_errormsg;
             if(!empty($pathToTemplates))
                {
                    $this->set_root($pathToTemplates);
                }
			//NEW by AiK
        	$this->start = $this->utime();
        }        // end (new) FastTemplate ()

		/**
		 * Parse template & return it
		 * @param $tpl_name - name of template to parse
		 * @return string
		 Added by Voituk Vadim
		 */
		 function parse_and_return($tpl_name) {
			 $HREF = 'TPL';
			 $this->parse($HREF, $tpl_name);
			 $result = trim($this->fetch($HREF));
			 $this->clear_href($HREF);
			 return $result;
		 }

    /**
    * Sets template root
    * All templates will be loaded from this "root" directory
    * Can be changed in mid-process by re-calling with a new
    * value.
    * @param $root path to templates dir
    * @return void
    */

    function set_root ($root)
    {
        $trailer = substr($root,-1);

        if(!$this->WIN32){
            if( (ord($trailer)) != 47 )
            {
                $root = "$root". chr(47);
            }
            if(is_dir($root)){
                $this->ROOT = $root;
            }else{
                $this->ROOT = "";
                $this->error("Specified ROOT dir [$root] is not a directory");
            }
        }else{
            // WIN32 box - no testing
            if( (ord($trailer)) != 92 ){
                $root = "$root" . chr(92);
            }
            $this->ROOT = $root;
        }

    }   // End set_root()
		/**
		* Return value of ROOT templates directory
		* @return root dir value with trailing slash
		* by Voituk Vadim
		*/
		function get_root() {
			return $this->ROOT;
		}// End get_root()

//        ************************************************************
		/**
		* Calculates current microtime
		* I throw this into all my classes for benchmarking purposes
		* It's not used by anything in this class and can be removed
		* if you don't need it.
		* @return void
		* @access private
		*/
    function utime ()
	{
        $time = explode( " ", microtime());
        $usec = (double)$time[0];
        $sec = (double)$time[1];
        return $sec + $usec;
    } // End utime ()

   /**
    * @access private
    */
	function getmicrotime()
	{
	return $this->utime();
    } // End utime ()
//  **************************************************************
/**
 * Enable strict templeate checking
 *
 * When strict() is on (it is on by default) all variables found during template parsing that are unresolved have a warning printed to STDERR;
 *
 * <kbd>
 * [FastTemplate] Warning: no value found for variable: SOME_VAR
 * </kbd>
 *
 * Also, the variables will be left in the output document. This was done for two reasons: to allow for parsing to be done in stages (i.e. multiple passes), and to make it easier to identify undefined variables since they appear in the parsed output.
 *
 * Note: STDERR output should be captured and logged by the webserver. With apache (and unix!) you can tail the error log during development to see the results as in:
 *
 * <kbd>
 * tail -f /var/log/httpd/error_log
 * </kbd>
 *
 * @return void
 */
    function strict ()
        {
                $this->STRICT = true;
        }
//        ************************************************************
/**
 * Turns off warning messages about unresolved template variables.
 *
 * A call to no_strict() is required to replace unknown variables with an empty string. By default, all instances of FastTemplate behave as is strict() was called. Also, no_strict() must be set for each instance of FastTemplate.
 *
 * <code>
 * $tpl = new FastTemplate("/path/to/templates");
 * $tpl->no_strict();
 * </code>
 * @return void
*/
    function no_strict ()
        {
                $this->STRICT = false;
        }
//        ************************************************************
		/**
    * A quick check of the template file before reading it.
    * This is -not- a reliable check, mostly due to inconsistencies
    * in the way PHP determines if a file is readable.
    * @return boolean
    */
    function is_safe ($filename)
    {
        if(!file_exists($filename))
        {
            $this->error("[$filename] does not exist",0);
            return false;
        }
        return true;
    }
//        ************************************************************


   /**
    * Set PHP_IN_HTML to true/false
    * @param bool PHP_IN_HTML
    * @return void
    */
    function php_in_html($value){
	$this->PHP_IN_HTML=$value;
    }

/**
 * Rewrite js, css, and img src from template to a custom path
 * @param string contents
 * @return string
 */
    function rewrite_src_path($contents){
    // Rewrite src path regex using Heredoc
	 $regexPattern[] = "/src\s*=(.*?)[\\\"']?([^\\\"' >]+)[\\\"'> ]/is";
	 //$regexPattern[] = "/<\s*link\s+[^>]*href\s*=\s*[\\\"']?([^\\\"' >]+)[\\\"' >]/is"; //BUG in 1.5.1
	 $regexPattern[] = "/<\s*link\s+[^>]*href\s*=\s*[\\\"']?[^\\\"' >]+[\\\"' >]/is";

        if(sizeof($this->REWRITE_SRC_PATH)>0) {
    	    $contents=preg_replace($regexPattern[0], 'src="'.$this->REWRITE_SRC_PATH.'\\2"', $contents);
	    // preg_reclace_callback return his result to a function outside class body
	    $GLOBALS['REWRITE_SRC_PATH'] = $this->REWRITE_SRC_PATH;
	    $contents=preg_replace_callback($regexPattern[1], 'rewrite_link_href_callback', $contents);
	    unset($GLOBALS['REWRITE_SRC_PATH']);
	}
	return $contents;
    }

	 /**
	  * Rewrite all src="template_path/file" found in the document
	  * to src="$path/file"
	  * @param string $path
	  * @return void
	  * @author Allyson Francisco de Paula Reis
	  * @since 1.3.9
	  * @very helpfull when you want to edit the work template in a
	  * visual editor without any relationship with the script path
	  * that will summon the work template output content.
	  */
	 function set_output_rewrite_src_path($path)
	 {
	 	$this->REWRITE_SRC_PATH = $path;
	 }


/**  Modified By: MarianoL (Mariano Luna from Buenos Aires, Argentina)
*   This Mod allows you to use nested templates and multimple templates in a single file
*   The format in the define is:
$used_tpl = array(
	'footer' => 'template.tpl#FOOTER',
	'header' => 'template.tpl#HEADER',
	'main' => 'template.tpl#MAIN'
	);
*   Template File Format (template.tpl):
<!-- START TEMPLATE HEADER -->
<html>
<title>{PAGE_TITLE}</title>
</head><body>
<!-- END TEMPLATE HEADER -->
<!-- START TEMPLATE MAIN -->
<p>Here goes the Main content {VAR} </p>
<!-- END TEMPLATE MAIN -->
<!-- START TEMPLATE FOOTER -->
<p>This is the footer text. {FOOTVAR}</P>
</body></html>
<!-- END TEMPLATE FOOTER -->
*	Still on Beta testing!!!!!
*
*   Based on the modification by Donatien in the originaly class
*   (Sebastian Grignoli, from Buenos Aires, Argentina)
*   in order to support nested templates in a single file.
*/
   /**
    * Grabs a template from the root dir and
    * reads it into a (potentially REALLY) big string
    * @param $template template name
    * @return string
    */
    function get_template ($template)
     {
	    //if the track_errors configuration option is turned on (it defaults to off).
		global $php_errormsg;
	    if (empty($this->ROOT))
        {
            $this->error("Cannot open template. Root not valid.",1);
            return false;
        }
        $filename   =   "$this->ROOT"."$template";

	if ($this->PHP_IN_HTML){
	    //execute any php code from the template
	    ob_start();
	    include($filename);
	    $contents = ob_get_contents();
	    ob_end_clean();
	} else {
        $subtemplate=strpos($template,"#");

        if ($subtemplate)
        {
                $templ = explode("#",$template);
                $template = $templ[0];
                $section = $templ[1];
                $templ = "";
                $filename = "$this->ROOT"."$template";
                $levl = 0;
                $contemp = implode('',file($filename));
                $filename = "" ;

                // strlen($contemp)-strlen(stristr($contemp,"<!-- START TEMPLATE "))
                // equivale a:    strpos($contemp,"<!-- START TEMPLATE ")
                // pero es case-insensitive, excepto cuando no lo encuentra, que
                // devuelve el largo de $contemp en vez de FALSE

                // econtrar el primer "<!-- START TEMPLATE ".$section." -->" y cortar inicio ahi
                // econtrar el primer "<!-- END TEMPLATE ".$section." -->" y cortar final ahi
                if(($contemp2 = stristr($contemp,"<!-- START TEMPLATE ".$section." -->")) === false){
                        //no encuentra el subtemplate, devuelve ""
            $contemp = "";
            $this->error("Could not find template: {$template}#{$section}",0);
                } else {
                        //le quitamos la cadena del principio
                        $contemp = substr($contemp2,strlen("<!-- START TEMPLATE ".$section." -->"));
                        //encontro el inicio del subtemplate, buscamos el final
                        if(!stristr($contemp,"<!-- END TEMPLATE ".$section." -->")){
                        //strpos($contemp,"<!-- END TEMPLATE ".$section." -->")
                                //no se encuentra el final del subtemplate,
                                //a falta de algo mejor lo usamos hasta el final del archivo
                                //es decir, lo dejamos como est?, pero tiramos un error
                    $this->error("Could not find template closing tag: {$template}#{$section}",0);
                        } else {
                                //encontramos el final del subtemplate
                                //cortamos final ahi
                                //y a la vez le quitamos la cadena del final
                                $contemp = substr($contemp, 0, strlen($contemp)-strlen(stristr($contemp,"<!-- END TEMPLATE ".$section." -->")));
                        }
                }

                // encontrar todos los "<!-- START TEMPLATE cualquiercosa -->" y luego los
                // "<!-- END TEMPLATE mismacosa -->" y extraerlos
                while(stristr($contemp,"<!-- START TEMPLATE ")){
                        //mientras haya otro template dentro del nuestro
                        $tagotro = strlen($contemp)-strlen(stristr($contemp,"<!-- START TEMPLATE "));
                        if(!($tagotro2 = strpos($contemp,"-->",$tagotro))){
                                //error, elimino todo el resto del texto
                                        //debug_print("contemp",$contemp,215);
                                        //debug_print("tagotro",$tagotro,215);
                       $this->error("Malformed tag inside {$template}#{$section} near <i>". substr(stristr($contemp,"<!-- START TEMPLATE "),0,25)."</i>" ,0);
                    $contemp = substr($contemp,0,strlen($contemp) - strlen(stristr($contemp,"<!-- START TEMPLATE ")));
                        } else {
                                //el texto que hay entre $tagotro y $tagotro2
                                //es el nombre del template que encontro ($templatefound)
                                $templatefound = substr($contemp,$tagotro + strlen("<!-- START TEMPLATE "));
                                $templatefound = substr($templatefound, 0, strpos($templatefound,"-->"));//mplatefound,"-->")-1);
                                $templatefound = trim($templatefound);
                                //busco el closing tag para este template

                                if(!stristr($contemp,"<!-- END TEMPLATE ".$templatefound." -->")){
                                        //no se encuentra el final del subtemplate,
                                        //(seran subtemplates entrecruzados?)
                                        //eliminamos todo el resto del texto
                            $this->error("Could not find tag &lt;!-- END TEMPLATE ".$templatefound." --&gt;"." inside subtemplate: {$template}#{$section} (Entangled templates, maybe?)",0);
                                        
                            $contemp = substr($contemp,0,strlen($contemp) - strlen(stristr($contemp,"<!-- START TEMPLATE ")));
                                } else {
                                        // extraigo todo lo que hay entre
                                        // "<!-- START TEMPLATE ".$templatefound." -->"
                                        // y "<!-- END TEMPLATE ".$templatefound." -->"
                                        $fintag = strlen($contemp)-strlen(stristr($contemp,"<!-- END TEMPLATE ".$templatefound." -->"));
                                        $parte1 = substr($contemp,0,$tagotro);// desde el principio hasta $tagotro
                                        $parte2 = substr($contemp,$fintag + strlen("<!-- END TEMPLATE ".$templatefound." -->"));// de $fintag en adelante
                                        $contemp = $parte1 . $parte2;
                                }
                        }
                }
                $contents = $contemp;
        } else {
        
		$contents = ((function_exists('file_get_contents'))) ? file_get_contents($filename) : implode("\n", file($filename));

	}
}
	$contents=$this->rewrite_src_path($contents);

	if( (!$contents) or (empty($contents)) )
        {
            $this->error("get_template() failure: [$filename] $php_errormsg",1);
			return false;
        }
		else
		{
    	/** Strip template comments */
			if ($this->STRIP_COMMENTS)
			{
				$pattern = "/".preg_quote($this->COMMENTS_START). "\s.*" .preg_quote($this->COMMENTS_END)."/sU";
				$contents = preg_replace($pattern, '', $contents);
			}

			$block=array("/<!--\s(BEGIN|END)\sDYNAMIC\sBLOCK:\s([a-zA-Z\_0-9]*)\s-->/");
			$corrected=array("\r\n <!-- \\1 DYNAMIC BLOCK: \\2 --> \r\n");
			$contents=preg_replace($block,$corrected,$contents);
			return trim($contents);
		}

	} // end get_template
//        ************************************************************
   /**
    * Prints the warnings for unresolved variable references
    * in template files. Used if STRICT is true
    * @param $Line string for variable references checking
    * @return void
    */
    function show_unknowns ($Line){
        $unknown = array();
        if (ereg("({[A-Z0-9_]+})",$Line,$unknown))
        {
            $UnkVar = $unknown[1];
            if(!(empty($UnkVar))){
            	if($this->STRICT_DEBUG)
            	$this->WARNINGS[]="[FastTemplate] Warning: no value found for variable: $UnkVar \n";

            	if($this->STRICT)
                @error_log("[FastTemplate] Warning: no value found for variable: $UnkVar ",0);
            }
        }
    }   // end show_unknowns()
//        ************************************************************


	/**
	 * Parse param string and replace simple variable
	 *
	 * @param string
	 * @return string
	 */
	function parseParamString($string) {
		if (preg_match_all('/\{([a-z0-9_]+)\}/i', $string, $matches)) {
			for ($i = 0; $i < count($matches[0]); $i++) {
				$string = str_replace($matches[0][$i], $this->PARSEVARS[$matches[1][$i]], $string);
			}
		}
		return $string;
	}


/**
 * @access private
*/
	function value_defined($value, $field = '', $params = '') {
		$var = $this->PARSEVARS[$value];
		if ($field{0}=='.') $field = substr($field, 1);
//		echo "$value, $field, $params <BR>";
		if (is_object($var)) {
			if (method_exists($var, $field)) {
				eval('$return = $var->' . $field . '(' . $this->parseParamString($params) . ');');
				return ((!empty($return)) || ($return === true));

			}	else if ((strcasecmp($field, 'id')!=0) && method_exists($var, 'get')) {
				$result = $var->get($field);
				return (!empty($result) || $result===true);
			} else if ( (strcasecmp($field, 'id')==0) && method_exists($var, 'getId') ) {
				$result = $var->getId();
				return (!empty($result) || $result===true);
			}
		}
		else return (!empty($var) || $var===true);
	}

   	/**
	* This routine get's called by parse_template() and does the actual
	* It remove defined blocs
	* @param $template strinng to be parsed
	* @return string
	* @access private
	author Alex Tonkov
	*/
	function parse_defined($template) 	{
		$lines = split("\n", $template);

		$newTemplate = "";
		$ifdefs = false;
		$depth = 0;
		$needparsedef[$depth]["defs"] = false;
		$needparsedef[$depth]["parse"] = true;

		while (list ($num,$line) = each($lines) ){
			//Added "necessary" lines to new string
			if (((!$needparsedef[$depth]["defs"]) || ($needparsedef[$depth]["parse"])) &&
				(strpos($line, "IFDEF:") === false) &&
				(strpos($line, "IFNDEF:") === false) &&
				(strpos($line, "ELSE") === false) &&
				(strpos($line, "ENDIF") === false))
			$newTemplate .= trim($line) . "\n";

				//by Alex Tonkov: Parse the start of define block and check the condition
				if (preg_match("/<!--\s*IFDEF:\s*([a-zA-Z_][a-zA-Z0-9_]+)(\.|\-\>)?([a-zA-Z_][a-zA-Z0-9_]+)?\(?(\s*\,?\".*\"\s*\,?|\s*\,?[a-z0-9\_]*\s*\,?)\)?\s*-->/i", $line, $regs)){
					$depth++;
					$needparsedef[$depth]["defs"] = true;
					if ($this->value_defined($regs[1], $regs[3], $regs[4])) $needparsedef[$depth]["parse"] = $needparsedef[$depth - 1]["parse"];
					else $needparsedef[$depth]["parse"] = false;
				}
				//by Alex Tonkov: IFNDEF block
				if (preg_match("/<!--\s*IFNDEF:\s*([a-zA-Z_][a-zA-Z0-9_]+)(\.|\-\>)?([a-zA-Z_][a-zA-Z0-9_]+)?\(?(\s*\,?\".*\"\s*\,?|\s*\,?[a-z0-9\_]*\s*\,?)\)?\s*-->/i", $line, $regs)){
					$depth++;
					$needparsedef[$depth]["defs"] = true;
					if (!$this->value_defined($regs[1], $regs[3], $regs[4])) $needparsedef[$depth]["parse"] = $needparsedef[$depth - 1]["parse"];
					else $needparsedef[$depth]["parse"] = false;
				}
				//by Alex Tonkov: ELSE block
				if (preg_match("/<!--\s*ELSE\s*-->/i", $line)) {
					if ($needparsedef[$depth]["defs"]) $needparsedef[$depth]["parse"] = (!($needparsedef[$depth]["parse"]) & $needparsedef[$depth - 1]["parse"]);
				}
				//by Alex Tonkov: End of the define block
				if (preg_match("/<!--\s*ENDIF\s*-->/i", $line)){
					$needparsedef[$depth]["defs"] = false;
					$depth--;
				}
			}
			if ($depth)
				$this->error('Some nonclosed IDEFS blocks', 0);
			return $newTemplate;
		}
//        ************************************************************
    /**
    * This routine get's called by parse() and does the actual
    * {VAR} to VALUE conversion within the template.
    * @param $template string to be parsed
    * @param $ft_array array of variables
    * @return string
    * @author CDI cdi@thewebmasters.net
    * @author Artyem V. Shkondin artvs@clubpro.spb.ru
    * @version 1.1.1
    * Comments by GRAFX
    * @access private
    */
    function parse_template ($template, $ft_array)
        {
		/* Parsing and replacing object statements {Object.field} */
		if (preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]+)(\.|\-\>)([a-zA-Z_][a-zA-Z0-9_]+)\(?(\s*\,?\".*?\"\s*\,?|\s*\,?[a-z0-9\_]*\s*\,?)\)?\}/i', $template, $matches)) {
			for ($i=0; $i<count($matches[0]); ++$i) {
				$obj = $ft_array[$matches[1][$i]];
				if ((is_object($obj) && method_exists($obj, $matches[3][$i]))) {
					eval('$return = $obj->' . $matches[3][$i] . '(' . $this->parseParamString($matches[4][$i]) . ');');
					$template = str_replace($matches[0][$i], $return, $template);
				}
				else
				if (is_object($obj) && ($matches[3][$i]=='id') && method_exists($obj, 'getId')) $template = str_replace($matches[0][$i], $obj->getId(), $template);
				else
				if (is_object($obj) && method_exists($obj, 'get')) $template = str_replace($matches[0][$i], $obj->get($matches[3][$i]), $template);
				else
				if (!is_object($obj)) $template = str_replace($matches[0][$i], '', $template);
			}//for
		}
//		echo $template;
		/* Parse Include blocks (like SSI) */
		if (preg_match_all('/<\!\-\-\s*#include\s+file="([\{\}a-zA-Z0-9_\.\-\/]+)"\s*\\-\->/i', $template, $matches)) {
			for ($i = 0; $i < count($matches[0]); $i++) {
				$file_path = $matches[1][$i];

				foreach ($ft_array as $key=>$value) {
					if (!empty($key)) {
						$key = '{'."$key".'}';
						$file_path = str_replace("$key","$value","$file_path");
					}
				} //foreach
				$content = '';

				if (!isset($ft_array[$file_path])) {
					if (!file_exists($file_path))
						$file_path = $this->ROOT . $file_path;

					if (!file_exists($file_path))
						$file_path = $this->ROOT . basename($file_path);

					if (file_exists($file_path)) {
					$contents = ((function_exists('file_get_contents'))) ? file_get_contents($filename) : implode("\n", file($filename));

					} else $content = '';
				}
				else $content = $ft_array[$file_path];

				$template = str_replace($matches[0][$i], $content, $template);
			} //for
		} //preg_match_all

		reset($ft_array);
       while ( list ($key,$val) = each ($ft_array) )
                {
                        if (!(empty($key)))
                        {
                                if(gettype($val) != "string")
                                {
                                        settype($val,"string");
                                }
									//php4 doesn't like '{$' combinations.
									$key = '{'."$key".'}';
									$template = str_replace("$key","$val","$template"); //Correct using str_replace insted ereg_replace
            					}
        				}
        //if(!$this->STRICT && ($this->STRICT && !$this->STRICT_DEBUG))
		if(!$this->STRICT || ($this->STRICT && !$this->STRICT_DEBUG)) //Fixed error ^^ // by Voituk Vadim
        {
            // Silently remove anything not already found
          $template = ereg_replace("{([A-Z0-9_\.]+)}","",$template); // by Voituk Vadim correct using str_replace insted ereg_replace
            //$template = str_replace("{([A-Z0-9_]+)}","",$template); // GRAFX
				// by Alex Tonkov: paste each define block in one line
				$template = preg_replace("/(<!--\s*IFDEF:\s*([a-zA-Z_][a-zA-Z0-9_]+)(\.|\-\>)?([a-zA-Z_][a-zA-Z0-9_]+)?\(?(\s*\,?\".*?\"\s*\,?|\s*\,?[a-z0-9\_]*\s*\,?)\)?\s*-->)/i", "\n$0\n", $template);
				$template = preg_replace("/(<!--\s*IFNDEF:\s*([a-zA-Z_][a-zA-Z0-9_]+)(\.|\-\>)?([a-zA-Z_][a-zA-Z0-9_]+)?\(?(\s*\,?\".*?\"\s*\,?|\s*\,?[a-z0-9\_]*\s*\,?)\)?\s*-->)/i", "\n$0\n", $template);
				$template = preg_replace("/(<!--\s*ELSE\s*-->)/i", "\n\\0\n", $template);
				$template = preg_replace("/(<!--\s*ENDIF\s*-->)/i", "\n\\0\n", $template);

				//Correct using str_replace insted ereg_replace
				// Removed because it deletes newline in textareas.
				// TX to Martin Fasani
				//$template = ereg_replace("([\n]+)", "\n", $template);
            	//by AiK: remove dynamic blocks
						$lines = split("\n",$template);
                $inside_block = false;
				// by Voituk Vadim
				$ifdefs = false;
				$needparsedef = false;
				// end by Voituk Vadim
                $template="";

            while (list ($num,$line) = each($lines) ){
                if (substr_count($line, "<!-- BEGIN DYNAMIC BLOCK:")>0 ) // -->
                {
                    $inside_block = true;
                }
                if (!$inside_block){
                    $template .= "$line\n";
                }
                if (substr_count($line, "<!-- END DYNAMIC BLOCK:")>0 ) // -->
                {
                    $inside_block = false;
                }
            }
			$template = $this->parse_defined($template);
        }else
        		{
            // Warn about unresolved template variables
            if (ereg("({[A-Z0-9_]+})",$template)){
                $unknown = split("\n",$template);
                while (list ($Element,$Line) = each($unknown) )
                {
                    $UnkVar = $Line;
                    if(!(empty($UnkVar)))
                    {
                        $this->show_unknowns($UnkVar);
                    }
                }
            }
        }
                return $template;
     } // end parse_template();


//        ************************************************************
/**
 * This is the main function in FastTemplate
 *
 * It accepts a new key value pair where the key is the TARGET and the values are the SOURCE templates. There are three forms this can be in:
 *
 * <code>
 *$tpl->parse(MAIN, "main");                     // regular
 *$tpl->parse(MAIN, array ( "table", "main") );  // compound
 *$tpl->parse(MAIN, ".row");                     // append
 * </code>
 *
 * In the regular version, the template named ``main'' is loaded if it hasn't been already, all the variables are interpolated, and the result is then stored in FastTemplate as the value MAIN. If the variable '{MAIN}' shows up in a later template, it will be interpolated to be the value of the parsed ``main'' template. This allows you to easily nest templates, which brings us to the compound style.
 * The compound style is designed to make it easier to nest templates. The following are equivalent:
 *
 * <code>
 *$tpl->parse(MAIN, "table");
 *$tpl->parse(MAIN, ".main");
 *</code>
 *is the same as:
 * <code>
 * $tpl->parse(MAIN, array("table", "main"));
 * </code>
 * this form saves function calls and makes your code cleaner.
 *
 * It is important to note that when you are using the compound form, each template after the first, must contain the variable that you are parsing the results into. In the above example, 'main' must contain the variable '{MAIN}', as that is where the parsed results of 'table' is stored. If 'main' does not contain the variable '{MAIN}' then the parsed results of 'table' will be lost.
 *
 * The append style allows you to append the parsed results to the target variable. Placing a leading dot . before a defined file handle tells FastTemplate to append the parsed results of this template to the returned results. This is most useful when building tables that have an dynamic number of rows - such as data from a database query.
 *
 * @return void
 */
     function parse ( $ReturnVar, $FileTags ){
		// add multiple parse section

		// these are the define assigns
		foreach( $this->PATTERN_VARS_DEFINE as $value)
		 $this->multiple_assign_define( "$value" );
		// these are the variable assigns
		foreach( $this->PATTERN_VARS_VARIABLE as $value)
		 $this->multiple_assign( "$value" );

		// end multiple parse section



		$append = false;
        $this->LAST = $ReturnVar;
        $this->HANDLE[$ReturnVar] = 1;
        //echo "startparse $ReturnVar";
        if (gettype($FileTags) == "array"){
            unset($this->$ReturnVar);   // Clear any previous data
            while ( list ( $key , $val ) = each ( $FileTags ) ) {
                if ( (!isset($this->$val)) || (empty($this->$val)) ) {
                    $this->LOADED["$val"] = 1;
                    if(isset($this->DYNAMIC["$val"])){
                        $this->parse_dynamic($val,$ReturnVar);
                    }else{
                        $fileName = $this->FILELIST["$val"];
                        $this->$val = $this->get_template($fileName);
                    }
                }
                //  Array context implies overwrite
                $this->$ReturnVar = $this->parse_template($this->$val,$this->PARSEVARS);
                //  For recursive calls.
                $this->assign( array( $ReturnVar => $this->$ReturnVar ) );




            }
        }   // end if FileTags is array()
        else{
            // FileTags is not an array

            $val = $FileTags;

            if( (substr($val,0,1)) == '.' ){
                // Append this template to a previous ReturnVar

                $append = true;
                $val = substr($val,1);
            }
            if ( (!isset($this->$val)) || (empty($this->$val)) ){
                    $this->LOADED["$val"] = 1;
                    if(isset($this->DYNAMIC["$val"])) {
                        $this->parse_dynamic($val,$ReturnVar);
                    }else {
                        $fileName = $this->FILELIST["$val"];
                        $this->$val = $this->get_template($fileName);
                    }
            }
            if($append){
               // changed by AiK
                if (isset($this->$ReturnVar)){
                    $this->$ReturnVar .= $this->parse_template($this->$val,$this->PARSEVARS);
                }else{
                    $this->$ReturnVar = $this->parse_template($this->$val,$this->PARSEVARS);
                }
            }else{
                   $this->$ReturnVar = $this->parse_template($this->$val,$this->PARSEVARS);

            }

            //  For recursive calls.
            $this->assign(array( $ReturnVar => $this->$ReturnVar) );




        }
        return;
    }   //  End parse()



//////////////////////////////////////////////
/**
 * Returns the parsed template
 *
 * This method is called by FastWrite and FastPrint
 * @access private
 * @param string template
 * @return string parsed template
 */
	function getfast ( $template = "")
	{
		if(empty($template))
		{
			$template = $this->LAST;
		}

		if( (!(isset($this->$template))) || (empty($this->$template)) )
		{
			$this->error("Nothing parsed, nothing printed",0);
			return;
		}
		else
		{

		  if (!get_magic_quotes_gpc())
             $this->$template=stripslashes($this->$template);


					if ($this->USE_CACHE)
					{
						$this->cache_file($this->$template);
					}
					else {
						return $this->$template;
					}
					return;
		}
	} // end getfast()
//////////////////////////////////////////////



//        ************************************************************
/**
 * Output the HTML-Code to a file.
 by Wilfried Trinkl - wisl@gmx.at
 * The method FastWrite() write the contents of the named variable into a file.
 *
 * <code>
 *$tpl->FastWrite("output.html"); // continuing from the last example, would
 *$tpl->FastWrite("MAIN","output.html"); // print the value of MAIN
 * </code>
 *
 * This method is provided for convenience. If you need to print somewhere else (a socket, file handle) you would want to fetch() a reference to the data first:
 *
 * <code>
 * $data = $tpl->fetch("MAIN");
 * fwrite($fd, $data);     // save to a file
 * </code>
 * To write into a folder, depend on server configuration.
 *
 * @param input template
 * @param outfile
 */
      function FastWrite ($template = "" , $outputfile)
		   {
		     $fp=fopen($outputfile,'w');
		     if ($fp){
		        fwrite($fp, $this->getfast($template));
		        fclose($fp);
		     };
		     return;
		   }


//        ************************************************************
/**
 * Prints parsed template
 *
 * The method FastPrint() prints the contents of the named variable. If no variable is given, then it prints the last variable that was used in a call to parse() which I find is a reasonable default.
 *
 *<code>
 * $tpl->FastPrint();
 * // print the value of MAIN
 * $tpl->FastPrint("MAIN"); // ditto
 *</code>
 *
 * This method is provided for convenience. If you need to print somewhere else (a socket, file handle) you would want to fetch() a reference to the data first:
 *
 * <code>
 * $data = $tpl->fetch("MAIN");
 * fwrite($fd, $data);     // save to a file
 * </code>
 * @param $template template handler
 * @return void
 */
	function FastPrint ( $template = "", $return="" )
	{
	    if (!$return)
		print $this->getfast($template);
	    else
		return $this->getfast($template);

	} // end FastPrint()







     /**
     * Prints parsed template
     * @param $template template handler
     * @return parsed template
     * @see FastTemplate#FastPrint()
     */
//	************************************************************
	/**  Try to use cached files.
	ragen@oquerola.com
	* @param string forldername
	*/
    function USE_CACHE ( $fname="" )
	{
	    $this->USE_CACHE = true;
        if ($fname) {
            $this->CACHING = $this->cache_path($fname);
        }
        $this->verify_cached_files($fname);
	}

/**
 * @access private
*/
	function setCacheTime($time){

	 $this->UPDT_TIME=$time;

	}

//	************************************************************
	/**  Try to delete used cached files.
	GraFX Software Solutions
	*/
    function DELETE_CACHE ()
	{
		$this->DELETE_CACHE = true;
		$expired = time() - $this->UPDT_TIME;
		$dir = $this->CACHE_PATH;
		$dirlisting = opendir($dir);
		while ($fname = readdir($dirlisting))
		{
		  $ext=explode(".",$fname);
		  if (filemtime($dir."/".$fname) < $expired && $fname != "." && $fname != ".." && $ext[1]=="ft") {
		  @unlink($dir."/".$fname);
	  	}
	}
		closedir($dirlisting);
	}

//	************************************************************
	/** Verify if cache files are updated (
	*   in function of $UPDT_TIME)
	*   then return cached page and exit - ragen@oquerola.com
	* @access private
	*/
	function verify_cached_files ()
	{
		if (($this->USE_CACHE) && ($this->cache_file_is_updated())) {
			if (!$this->CACHING) {
	     		// self_script() - return script as called Fast Template class
				include $this->self_script();
			} else {
				include $this->CACHING.".ft";
			}
			exit(0);
		}
	}
//	************************************************************
	/**	Return script as called Fast Template class
	*   by ragen@oquerola.com
	*	improved by P. Pavlovic: ppavlovic@mail.ru
	*	changed in 1.1.9 $fname var from SCRIPT_NAME into REQUEST_URI
	* @access private
	*/

	// modified in 1.4.0
  function self_script ($relativePath="")
  {
      $fname = $_SERVER['REQUEST_URI'];
      //$fname = getenv('SCRIPT_NAME');
      if (count($_SERVER['argv'])) {
         foreach ($_SERVER['argv'] as $val) {
		     $q[] = $val;
         }
         $fname .= join("_and_", $q);
      }
      $fname = md5($fname);

	  if ($relativePath) {
		  // Used by include to reclain cache
		  return $this->CACHE_PATH.'/'.$fname;
	  } else {
		  // Used to write and check/revalidate cache lifetime
		  return $this->cache_path($fname);
	  }
  }

//	************************************************************
	/**	Return the real path for write cache files
	*   by ragen@oquerola.com
	* @access private
	*/
	function cache_path ( $fname )
	{
		// out from 1.4.0
		/*
		$fname = explode("/",$fname);
		$fname = $fname[count($fname) - 1];
		return $this->CACHE_PATH."/".$fname;
		*/
		// into in 1.4.0
		return $_SERVER['DOCUMENT_ROOT'].$this->CACHE_PATH.'/'.basename($fname);
	}
//	************************************************************
	/**	Return the script as called Fast Template in cache dir
	*   by ragen@oquerola.com
	* @access private
	*/
	function self_script_in_cache_path ()
	{
		// out from 1.4.0
		/*
		$fname = explode("/",$this->self_script());
		$fname = $fname[count($fname) - 1];
		return $this->CACHE_PATH."/".$fname;
		*/
		// into in 1.4.0
		return $this->cache_path(basename($this->self_script()));
	}
//	************************************************************
	/**	Verify if cache file is updated or expired
	   by ragen@oquerola.com
	* @access private
	*/
	function cache_file_is_updated()
	{
		// Verification of cache expiration
		// filemtime() -> return unix time of last modification in file
		// time() -> return unix time
		if (!$this->CACHING) {
			$fname = $this->self_script_in_cache_path();
		} else {
			$fname = $this->CACHING;
		}
		if (!file_exists($fname.".ft")) {
			return false;
		}
		$expire_time = time() - filemtime($fname.".ft");

		if ($expire_time >= $this->UPDT_TIME) {
			return false;
		} else {
			return true;
		}
	}
//	************************************************************
	/**	The meat of the whole class. The magic happens here.
	by ragen@oquerola.com
	* @access private
	*/
	function cache_file ( $content = "" )
	{
		if (($this->USE_CACHE) && (!$this->cache_file_is_updated())) {
			if (!$this->CACHING) {
				$fname = $this->self_script_in_cache_path();
			} else {
				$fname = $this->CACHING;
			}
			$fname = $fname.".ft";
			// Tendo certeza que o arquivo existe e que há permiss&#259;o de escrita primeiro.
			//if (is_writable($fname)) {
			// Opening $fname in writing only mode
				if (!$fp = fopen($fname, 'w')) {
					$this->error("Error while opening cache file ($fname)",0);
					return;
				}
				// Writing $content to open file.
				if (!fwrite($fp, $content)) {
					$this->error("Error while writing cache file ($fname)",0);
					return;
				}
				else {
					fclose($fp);
					include $fname;
					return;
				}
				fclose($fp);
			//} else {
			//	$this->error("The cache file $fname is not writable",0);
			//	return;
			//}
		}
	} // end cache_file()

//  ************************************************************
/**
 * Returns the raw data from a parsed handle.
 *
 * Example:
 *
 * <code>
 *$tpl->parse(CONTENT, "main");
 *$content = $tpl->fetch("CONTENT");
 *print $content;        // print to STDOUT
 *fwrite($fd, $content); // write to filehandle
 * </code>
 *
 * @return string rawdata
 */
    function fetch( $template = "" )
    {
        if(empty($template))
        {
            $template = $this->LAST;
        }
        if( (!(isset($this->$template))) || (empty($this->$template)) )
        {
            $this->error("Nothing parsed, nothing printed",0);
            return "";
        }

        return($this->$template);
    }
//  ************************************************************
/**
 *
 * You can define dynamic content within a static template.
 * Here's an example of define_dynamic();
 * <code>
 * $tpl = new FastTemplate("./templates");
 * $tpl->define(    array( main  =>  "main.html",table =>  "dynamic.html" ));
 * $tpl->define_dynamic( "row" , "table" );
 * </code>
 * This tells FastTemplate that buried in the ``table'' template is a dynamic block, named ``row''. In older verions of FastTemplate (pre 0.7) this ``row'' template would have been defined as it's own file. Here's how a dynamic block appears within a template file;
 *
 *
 * <code>
 * <!-- NAME: dynamic.html -->
 *  <table>
 *  <!-- BEGIN DYNAMIC BLOCK: row -->
 *  <tr>
 *   <td>{NUMBER}</td>
 *   <td>{BIG_NUMBER}</td>
 *  </tr>
 *  <!-- END DYNAMIC BLOCK: row -->
 * </table>
 * <!-- END: dynamic.html -->
 *
 * </code>
 *
 * The syntax of your BEGIN and END lines needs to be VERY exact. It is case sensitive. The code block begins on a new line all by itself. There cannot be ANY OTHER TEXT on the line with the BEGIN or END statement. (although you can have any amount of whitespace before or after) It must be in the format shown: <br>
 * <b><!-- BEGIN DYNAMIC BLOCK: handle_name --></b> <br>
 * The line must be exact, right down to the spacing of the characters. The same is true for your END line. The BEGIN and END lines cannot span multiple lines. Now when you call the parse() method, FastTemplate will automatically spot the dynamic block, strip it out, and use it exactly as if you had defined it as a stand-alone template. No additional work is required on your part to make it work - just define it, and FastTemplate will do the rest. Included with this archive should have been a file named define_dynamic.phtml which shows a working example of a dynamic block.<br>
 * There are a few rules when using dynamic blocks - dynamic blocks should not be nested inside other dynamic blocks - strange things WILL occur. You -can- have more than one nested block of code in a page, but of course, no two blocks can share the same defined handle. The error checking for define_dynamic() is miniscule at best. If you define a dynamic block and FastTemplate fails to find it, no errors will be generated, just really weird output. (FastTemplate will not append the dynamic data to the retured output) Since the BEGIN and END lines are stripped out of the parsed results, if you ever see your BEGIN or END line in the parsed output, that means that FastTemplate failed to find that dynamic block.
 *
 * @param input Macro Name
 * @param input Parent Name
*/
    function define_dynamic ($Macro, $ParentName)
    {
        //  A dynamic block lives inside another template file.
        //  It will be stripped from the template when parsed
        //  and replaced with the {$Tag}.

        $this->DYNAMIC["$Macro"] = $ParentName;
        return true;
    }
//  ************************************************************

/**
 *
 * @access private
*/
    function parse_dynamic ($Macro,$MacroName)
    {
        // The file must already be in memory.
        //echo "parse_dynamic $Macro::$MacroName";
        $ParentTag = $this->DYNAMIC["$Macro"];
        if( (!isset($this->$ParentTag)) or (empty($this->$ParentTag)) )
        {
            $fileName = $this->FILELIST[$ParentTag];
            $this->$ParentTag = $this->get_template($fileName);
            $this->LOADED[$ParentTag] = 1;
        }
        if($this->$ParentTag)
        {
            $template = $this->$ParentTag;
            $DataArray = split("\n",$template);
            $newMacro = "";
            $newParent = "";
            $outside = true;
            $start = false;
            $end = false;
            while ( list ($lineNum,$lineData) = each ($DataArray) )
            {
                $lineTest = trim($lineData);
                if("<!-- BEGIN DYNAMIC BLOCK: $Macro -->" == "$lineTest" )
                {
                    $start = true;
                    $end = false;
                    $outside = false;
                }
                if("<!-- END DYNAMIC BLOCK: $Macro -->" == "$lineTest" )
                {
                    $start = false;
                    $end = true;
                    $outside = true;
                }
                if( (!$outside) and (!$start) and (!$end) )
                {
                    $newMacro .= "$lineData\n"; // Restore linebreaks
                }
                if( ($outside) and (!$start) and (!$end) )
                {
                    $newParent .= "$lineData\n"; // end Restore linebreaks
                }
                if($end)
                {
                    $newParent .= '{'."$MacroName}\n";
                }
                // Next line please
                if($end) { $end = false; }
                if($start) { $start = false; }
            }   // end While

            $this->$Macro = $newMacro;
            $this->$ParentTag = $newParent;
            return true;

        }   // $ParentTag NOT loaded - MAJOR oopsie
        else
        {
            @error_log("ParentTag: [$ParentTag] not loaded!",0);
            $this->error("ParentTag: [$ParentTag] not loaded!",0);
        }
        return false;
    }

//  ************************************************************
/**
 * Strips a dynamic block from a template.
 *
 * This provides a method to remove the dynamic block definition from the parent macro provided that you haven't already parsed the template. Using our example above:
 *
 * <code>
 * $tpl->clear_dynamic("row");
 * </code>
 *
 * Would completely strip all of the unparsed dynamic blocks named ``row'' from the parent template. This method won't do a thing if the template has already been parsed! (Because the required BEGIN and END lines have been removed through the parsing) This method works well when you are accessing a database, and your ``rows'' may or may not return anything to print to the template. If your database query doesn't return anything, you can now strip out the rows you've set up for the results.
 *
 * @param input Macro name
 */
    function clear_dynamic ($Macro="")
    {
        if(empty($Macro)) { return false; }

        // The file must already be in memory.

        $ParentTag = $this->DYNAMIC["$Macro"];

        if( (!$this->$ParentTag) or (empty($this->$ParentTag)) )
        {
            $fileName = $this->FILELIST[$ParentTag];
            $this->$ParentTag = $this->get_template($fileName);
            $this->LOADED[$ParentTag] = 1;
        }

        if($this->$ParentTag)
        {
            $template = $this->$ParentTag;
            $DataArray = split("\n",$template);
            $newParent = "";
            $outside = true;
            $start = false;
            $end = false;
            while ( list ($lineNum,$lineData) = each ($DataArray) )
            {
                $lineTest = trim($lineData);
                if("<!-- BEGIN DYNAMIC BLOCK: $Macro -->" == "$lineTest" )
                {
                    $start = true;
                    $end = false;
                    $outside = false;
                }
                if("<!-- END DYNAMIC BLOCK: $Macro -->" == "$lineTest" )
                {
                    $start = false;
                    $end = true;
                    $outside = true;
                }
                if( ($outside) and (!$start) and (!$end) )
                {
                    $newParent .= "$lineData\n"; // Restore linebreaks
                }
                // Next line please
                if($end) { $end = false; }
                if($start) { $start = false; }
            }   // end While

            $this->$ParentTag = $newParent;
            return true;

        }   // $ParentTag NOT loaded - MAJOR oopsie
        else
        {
            @error_log("ParentTag: [$ParentTag] not loaded!",0);
            $this->error("ParentTag: [$ParentTag] not loaded!",0);
        }
        return false;
    }
//  *******************************************************
/**
 *
 *The method define() maps a template filename to a (usually shorter) name;
 *   <code>
 *   $tpl = new FastTemplate("/path/to/templates");
 *   $tpl->define( array(    main    => "main.html", footer  => "footer.html" ));
 *   </code>
 * This new name is the name that you will use to refer to the templates. Filenames should not appear in any place other than a define().<br>
 * (Note: This is a required step! This may seem like an annoying extra step when you are dealing with a trivial example like the one above, but when you are dealing with dozens of templates, it is very handy to refer to templates with names that are indepandant of filenames.)<br>
 * TIP: Since define() does not actually load the templates, it is faster and more legible to define all the templates with one call to define(). <br>
 *
*/
    function define ($fileList, $value=null)
    {
		if ((gettype($fileList)!="array") && !is_null($value)) $fileList = array($fileList => $value); //added by Voituk Vadim
        while ( list ($FileTag,$FileName) = each ($fileList) )
        {
            $this->FILELIST["$FileTag"] = $FileName;
        }
        return true;
    }

//  ************************************************************
/**
 * Does the same thing as the clear() function
 * @see clear()
 */
    function clear_parse ( $ReturnVar = "")
    {
        $this->clear($ReturnVar);
    }
//  ************************************************************
/**
 * Clears the internal references that store data passed to parse().
 *
 * clear() accepts individual references, or array references as arguments.
 *
 * Note: All of the clear() functions are for use anywhere where your scripts are persistant. They generally aren't needed if you are writing CGI scripts.
 *
 * Often clear() is at the end of a script:
 *
 *<code>
 *$tpl->FastPrint("MAIN");
 *$tpl->clear("MAIN");
 *</code>
 *or
 *<code>
 *$tpl->FastPrint("MAIN");
 *$tpl->FastPrint("CONTENT");
 *$tpl->clear(array("MAIN","CONTENT"));
 *</code>
 *If called with no arguments, removes ALL references that have been set via parse().

 */
    function clear ( $ReturnVar = "" )
    {


        if(!empty($ReturnVar))
        {
            if( (gettype($ReturnVar)) != "array")
            {
                unset($this->$ReturnVar);
                return;
            }
            else
            {
                while ( list ($key,$val) = each ($ReturnVar) )
                {
                    unset($this->$val);
                }
                return;
            }
        }

        // Empty - clear all of them

        while ( list ( $key,$val) = each ($this->HANDLE) )
        {
            $KEY = $key;
            unset($this->$KEY);
        }
        return;

    }   //  end clear()

//  ************************************************************
/**
 *Cleans the module of any data, except for the ROOT directory.
 *
 *This is equivalent to:
 *
 *<code>
 *$tpl->clear_define();
 *$tpl->clear_href();
 *$tpl->clear_tpl();
 *$tpl->clear_parse();
 *</code>
 */
    function clear_all ()
    {
        $this->clear();
        $this->clear_assign();
        $this->clear_define();
        $this->clear_tpl();

        return;

    }   //  end clear_all
//  ************************************************************
/**
 *Clears the internal array that stores the contents of the templates (if they have been loaded)
 *
 *If you are having problems with template changes not being reflected, try adding this method to your script.
 *<code>
 *$tpl->define(MAIN,"main.html" );
 * ( assign(), parse() etc etc...)
 *$tpl->clear_tpl(MAIN);    // Loaded template now unloaded.
 *</code>
 */
    function clear_tpl ($fileHandle = "")
    {
        if(empty($this->LOADED))
        {
            // Nothing loaded, nothing to clear

            return true;
        }
        if(empty($fileHandle))
        {
            // Clear ALL fileHandles

            while ( list ($key, $val) = each ($this->LOADED) )
            {
                unset($this->$key);
            }
            unset($this->LOADED);

            return true;
        }
        else
        {
            if( (gettype($fileHandle)) != "array")
            {
                if( (isset($this->$fileHandle)) || (!empty($this->$fileHandle)) )
                {
                    unset($this->LOADED[$fileHandle]);
                    unset($this->$fileHandle);
                    return true;
                }
            }
            else
            {
                while ( list ($Key, $Val) = each ($fileHandle) )
                {
                    unset($this->LOADED[$Key]);
                    unset($this->$Key);
                }
                return true;
            }
        }

        return false;

    }   // end clear_tpl
//  ************************************************************
/**
 *Clears the internal list that stores data passed to:
 *<kbd>
 *$tpl->define();
 *</kbd>
 *
 *Note: The hash that holds the loaded templates is not touched with this method. ( See: clear_tpl() ) Accepts a single file handle, an array of file handles, or nothing as arguments. If no argument is given, it clears ALL file handles.
 *
 *<code>
 *$tpl->define( array( MAIN => "main.html",
 *BODY => "body.html", FOOT => "foot.html"  ));
 *(some code here)
 *$tpl->clear_define("MAIN");
 *</code>
 *
 */
    function clear_define ( $FileTag = "" )
    {
        if(empty($FileTag))
        {
            unset($this->FILELIST);
            return;
        }

        if( (gettype($Files)) != "array")
        {
            unset($this->FILELIST[$FileTag]);
            return;
        }
        else
        {
            while ( list ( $Tag, $Val) = each ($FileTag) )
            {
                unset($this->FILELIST[$Tag]);
            }
            return;
        }
    }
//        ************************************************************
/*        Aliased function - used for compatibility with CGI::FastTemplate
//GRAFX		REMOVED because a lot of problem was caused on different servers.
        function clear_parse ()
        {
                $this->clear_assign();
        }
*/
//  ************************************************************
/**
 * Clears all variables set by assign()
 */
    function clear_assign ()
    {
        if(!(empty($this->PARSEVARS)))
        {
            while(list($Ref,$Val) = each ($this->PARSEVARS) )
            {
                unset($this->PARSEVARS["$Ref"]);
            }
        }
    }
//  ************************************************************
/**
 * Removes a given reference from the list of refs that is built using:
 * <kbd>
 *$tpl->assign(KEY = val);
 *</kbd>
 *
 *If it's called with no arguments, it removes all references from the array.
 *
 *<code>
 *$tpl->assign(    array(    MOVIE  =>  "The Avengers", RATE   =>  "BadMovie"    ));
 *$tpl->clear_href("MOVIE"); // Now only {RATE} exists in the assign() array
 *</code>
 *
 */
    function clear_href ($href)
    {
        if(!empty($href))
        {
            if( (gettype($href)) != "array")
            {
                unset($this->PARSEVARS[$href]);
                return;
            }
            else
            {

				foreach ($href as $value)
					unset($this->PARSEVARS[$value]);

				return;
            }
        }
        else
        {
            // Empty - clear them all
            $this->clear_assign();
        }
        return;
    }
//  ************************************************************
		/**/

	/**
	* assign template variables with the same names from array by specfied keys
	* NOTE: template variables will be in upper case
	 by Voituk Vadim
	*/
	function assign_from_array($Arr, $Keys) {
			if (gettype($Arr) == "array") {
				foreach ($Keys as $k)
					if (!empty($k))
						$this->PARSEVARS[strtoupper($k)] =  str_replace('&amp;#', '&#', $Arr[$k]);
			}
		}
/**
 * Assign variables
 *
 * This method assigns values for variables. In order for a variable in a template to be interpolated it must be assigned. There are two forms which have some important differences. The simple form, is to accept an array and copy all the key/value pairs into an array in FastTemplate. There is only one array in FastTemplate, so assigning a value for the same key will overwrite that key.
 *
 * <code>
 * $tpl->assign(TITLE    => "king kong");
 * $tpl->assign(TITLE    => "gozilla");    // overwrites "king kong"
 * </code>
 *
 */
    function assign ($ft_array, $trailer="")
    {
        if(gettype($ft_array) == "array")
        {
            while ( list ($key,$val) = each ($ft_array) )
            {
                if (!(empty($key)))
                {
                    //  Empty values are allowed
                    //  Empty Keys are NOT

                    // ORIG $this->PARSEVARS["$key"] = $val;
					if (!is_object($val)) $this->PARSEVARS["$key"] =  str_replace('&amp;#', '&#', $val);  //GRAFX && Voituk
						else $this->PARSEVARS["$key"] =  $val;  //GRAFX && Voituk
                }
            }
        }
        else
        {
            // Empty values are allowed in non-array context now.
            if (!empty($ft_array))
            {
                // ORIG $this->PARSEVARS["$ft_array"] = $trailer;
                if (!is_object($trailer)) $this->PARSEVARS["$ft_array"] = str_replace('&amp;#', '&#', $trailer); //GRAFX
					else $this->PARSEVARS["$ft_array"] =  $trailer;  //GRAFX && Voituk
            }
        }
    }
//  ************************************************************
//  Christian Brandel cbrandel@gmx.de
/**
 *Return the value of an assigned variable.
 *
 *This method will return the value of a variable that has been set via assign(). This allows you to easily pass variables around within functions by using the FastTemplate class to handle ``globalization'' of the variables.
 *
 *For example:
 *
 * <code>
 * $tpl->assign(  array(  TITLE    =>    $title,
 * BGCOLOR  =>    $bgColor, TEXT     =>    $textColor ));
 * $bgColor = $tpl->get_assigned(BGCOLOR);
 * </code>
 */
    function get_assigned($ft_name = "")
    {
        if(empty($ft_name)) { return false; }
        if(isset($this->PARSEVARS["$ft_name"]))
        {
            return ($this->PARSEVARS["$ft_name"]);
        }
        else
        {
            return false;
        }
    }
//  ************************************************************
/**
 * Put an error message and stop (if requested)
 *
 *@param string
 *@param bool
*/
    function error ($errorMsg, $die = 0)
    {
        $this->ERROR = $errorMsg;
        echo "ERROR: $this->ERROR <BR> \n";
        if ($die == 1)
        {
            exit;
        }

        return;

    } // end error()

//  ************************************************************
    /**
     author GRAFX - www.grafxsoftware.com,since 1.1.3
     * Pattern Assign
     *
     * Pattern Assign - when variables or constants are the same as the
     * template keys, these functions may be used as they are. Using
     * these functions, can help you reduce the number of
     * the assign functions in the php files
     *
     * Useful for language files where all variables or constants have
     * the same prefix.i.e. <i>$LANG_SOME_VAR</i> or <i>LANG_SOME_CONST</i><br>
     * The $pattern is <i>LANG</i> in this case.
     */
			 function multiple_assign($pattern)
				{
						while(list($key,$value) = each($GLOBALS))
						{
							if (substr($key,0,strlen($pattern))==$pattern)
							{
								$this->assign(strtoupper($key),$value);
							}
						}
						reset($GLOBALS);
				} // multiple_assign
    /**
     * Same as multiple_assign(), but for constants (defines)
     */
			function multiple_assign_define($pattern)
				{
					$ar=get_defined_constants();
					foreach ($ar as $key => $def)
					  if (substr($key,0,strlen($pattern))==$pattern)
							$this->assign(strtoupper($key),$def);
				}	 // multiple_assign_define

//  ************************************************************

  /**
     * @author GRAFX - www.grafxsoftware.com
     * @very helpful when we want to run some filter bofore the tamplate is parsed
     */
			 function pre_filter($pattern,$replace)
				{
						$this->PRE_FILTER[0]=$pattern;
						$this->PRE_FILTER[1]=$replace;
				} // pre_filter


//  ************************************************************
    /**
     *  Prints debug info into console
     * @return void
     * @author AiK
     * @since 1.1.1
     * modified by GRAFX, added 2 Levels of debugging.
     * Level 1 is showing all info + added WARNINGS
     * Level 2 will popup the window only if WARNINGS are present,
     * very helpfull only when you want to see BUGS on your page
     */

    function showDebugInfo($Debug_type=null){
        $tm =  $this->utime()  - $this->start;
		if($Debug_type != null )
        if($Debug_type==1)
        {
        			// print time
			        print "
			        <SCRIPT language=javascript>
			        _debug_console = window.open(\"\",\"console\",\"width=500,height=420,resizable,scrollbars=yes, top=0 left=130\");
			        _debug_console.document.write('<html><title>Debug Console</title><body bgcolor=#ffffff>');
			        _debug_console.document.write('<h3>Debugging info: generated during $tm seconds</h3>');
			        ";


			        if($this->STRICT_DEBUG)
			       	$this->printarray($this->WARNINGS, "Warnings");

							$this->printarray($this->FILELIST, "Templates");
			        $this->printarray($this->DYNAMIC, "Dynamic bloks");
			        $this->printarray($this->PARSEVARS, "Parsed variables");

			        print " _debug_console.document.close();
			       </SCRIPT> ";
        }
        else
        if($Debug_type==2)
                     {
		        		if($this->STRICT_DEBUG && sizeof($this->WARNINGS)!=0)
		        		{
                     	// print time
					        print "
					        <SCRIPT language=javascript>
					        _debug_console = window.open(\"\",\"console\",\"width=500,height=420,resizable,scrollbars=yes, top=0 left=130\");
					        _debug_console.document.write('<html><title>Debug Console</title><body bgcolor=#ffffff>');
					        _debug_console.document.write('<h3>Debugging info: generated during $tm seconds</h3>');
					        ";


					        	  $this->printarray($this->WARNINGS, "Warnings");

					       print " _debug_console.document.close();
					       </SCRIPT> ";
		        		}
                     }
    }//end of showDebugInfo()

    /**
     *
     */
     function printarray($arr,$caption){
     if (count($arr)!=0){
        print "
        _debug_console.document.write('<font face=Tahoma color=#0000FF size=2><b>$caption</b> </font>');\n
        _debug_console.document.write('<table border=0 width=100%  cellspacing=1 cellpadding=2>');
        _debug_console.document.write('<tr bgcolor=#CCCCCC><th width=175>key</th><th>value</th></tr>');\n ";

        $flag=true;
         while ( list ($key,$val) = each ($arr) ){
         $flag=!$flag;
             $val=htmlspecialchars(mysql_escape_string ($val));
         if (!$flag) {
            $color ="#EEFFEE";
         }else{
            $color ="#EFEFEF";
         }
            print "_debug_console.document.write('<tr bgcolor=$color><td> $key</td><td valign=bottom><pre>$val</pre></td></tr>');\n ";
         }
        print "_debug_console.document.write(\"</table>\");";
        }
     } //
//  ************************************************************

 /**
     * Pattern Assign
     *
     * Pattern Assign - this is an extension of the earlier pattern assign.
	 * The main advantage is that this way we can deal with pattern assign in dinamic
	 * parts too.
     * The old version is still ok keeping the backward compatibility but the new strategy
	 * is a global assignement of the patterns.
     *
     * First we should initialize the pattern arrays with setPattern
	 * @param $pattern can be array or just a simple string which contains the patterns
	 * @param $type $type = 1 we have defines, $type = 0(or any other number) we have vars; default is 1(defines)
	 * @author Zoltan Elteto
	 * @very helpful to initialize all the patterns.initialization can be made for defines or 		variables,
	 * but not both on the same time.
	 * Ex: $this->setPattern("LANG_"); would apply for all defines begining with LANG_
	 *	   $this->setPattern(array("LANG_","CONF_")); it is the same only we use 2 patterns now LANG_ and CONF_
	 *	   The variable part is simple, too.
	 *	   $this->setPattern("conf_",0);
     **/
     function setPattern($pattern,$type=1){

	/*
	  $type = 1 we have defines
	  $type = 0(or any other number) we have vars

	*/
      if(is_array($pattern))
	    {
		  foreach($pattern as $value)
		    if($type)
		      $this->PATTERN_VARS_DEFINE["$value"]="$value";
			else
			  $this->PATTERN_VARS_VARIABLE["$value"]="$value";

		}
		else
		  if($type)
		      $this->PATTERN_VARS_DEFINE["$pattern"]="$pattern";
			else
			  $this->PATTERN_VARS_VARIABLE["$pattern"]="$pattern";

	 }
	/**
 	 * @author Zoltan Elteto
	 * @very just clean up all patterns
	 **/
     function emptyPattern(){

     $this->PATTERN_VARS_DEFINE[]=array();
	 $this->PATTERN_VARS_VARIABLE[]=array();

	 }

	/**
 	 * @author Zoltan Elteto
	 * @param $pattern can be array or just a simple string which contains the patterns
	 * @param $type $type = 1 we have defines, $type = 0(or any other number) we have vars; default is 1(defines)
	 * @very delete the specified pattern
	 **/
     function deletePattern($pattern,$type=1){

	  if($type)
     	unset($this->PATTERN_VARS_DEFINE["$pattern"]);
	  else
	    unset($this->PATTERN_VARS_VARIABLE["$pattern"]);


	 }


//  ************************************************************
} // End cls_fast_template.php
}// end of if defined



// End cls_fast_template.php
?>