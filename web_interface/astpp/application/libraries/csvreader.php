<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* CSVReader Class
*
* $Id: csvreader.php 147 2007-07-09 23:12:45Z Pierre-Jean $
*
* Allows to retrieve a CSV file content as a two dimensional array.
* The first text line shall contains the column names.
*
* @author        Pierre-Jean Turpeau
* @link        http://www.codeigniter.com/wiki/CSVReader
*/
class CSVReader {

    var $fields;        /** columns names retrieved after parsing */
    var $separator = ',';    /** separator used to explode each line */

    /**
     * Parse a text containing CSV formatted data.
     *
     * @access    public
     * @param    string
     * @return    array
     */
    function parse_text($p_Text) {
        $lines = explode("\n", $p_Text);
        return $this->parse_lines($lines);
    }

    /**
     * Parse a file containing CSV formatted data.
     *
     * @access    public
     * @param    string
     * @return    array
     */
    function parse_file($p_Filepath) {
        $lines = file($p_Filepath);
        //Giving line numbers
        for($i=0;$i<sizeof($lines);$i++)
        {
            if(trim($lines[$i]) != "")
            {
                $columnname = explode($this->separator, $lines[$i]);
//   echo "<pre>"; print_r($columnname);
                for($i=0;$i<sizeof($columnname);$i++)
                {
                    $columnname[$i]=$columnname[$i];
                }
                 break;                    
            }
        }
//   echo "<pre>"; print_r($columnname);

        //echo $columnname;
        return $this->parse_lines($lines,$columnname);
    }
    /**
     * Parse an array of text lines containing CSV formatted data.
     *
     * @access    public
     * @param    array
     * @return    array
     */
    function parse_lines($p_CSVLines,$c_Names) {
        $content = FALSE;
        if( !is_array($content) ) 
        { // the first line contains fields numbers
            $this->fields = $c_Names;
            $content = array();
        }
                
        foreach( $p_CSVLines as $line_num => $line )
        {
            if( $line != '' ) 
            { // skip empty lines
                $elements = explode($this->separator, $line);
                $item = array();
                foreach( $this->fields as $id => $field ) 
                {
                    if( isset($elements[$id]) ) 
                    {
                        $item[str_replace('"','',trim($field))] = str_replace('"','',trim($elements[$id]));                        
//                        $item[trim($field)] = trim($elements[$id]);
                    }
                }
               $content[] = $item;
            }
        }
        return $content;
    }
}   
