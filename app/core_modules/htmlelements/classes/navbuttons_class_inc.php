<?php

/**
 * A set of button objects to create interface buttons
 * 
 * PHP version 5
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// Include the HTML interface class

/**
 * Description for require_once
 */
require_once("ifhtml_class_inc.php");


abstract class navbuttons extends object implements ifhtml
{

    /**
     * 
     * @var  object $objLanguage
     * @access public
     */
     public $objLanguage;

    /**
     *
     * @var    object $objConfig
     * @access public 
     */
     public $objConfig;

    /**
    * Init method for the buttons class, used to
    * instantiate a local access to the language object
    */
    public function init() 
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('config', 'config');
    }

    /**
    * Return an edit button as string
    * @param string $type: the type of icon to display, translates to the filename
    * @param string $filename: the filename if it is different from $type
    */
    public function button($type, $filename=NULL, $ext="gif",$alt=NULL) 
    {
        //global $objLanguage, $objConfig;
        $key='word_'.$type;
        if (!$filename) {
            $filename=$type; //.".".$ext;
        }
        if (!$alt) {
            $alt=$this->objLanguage->languageText($key);
        }
        $icon=$this->newObject('geticon','htmlelements');
        $icon->setIcon($filename);
        $icon->alt=$alt;
        $icon->title=$alt;
        return $icon->show();
        /*
        $ret='<img src="'.$this->objConfig->defaultIconFolder().$filename.'" 
            alt="'.$alt.'" border="0" 
            align="absmiddle" valign="middle" />';
        return $ret;
        */
    }
    
    /**
    * Print an edit button
    */
    public function putEditButton() 
    {
        return $this->button("edit");
    }
    
    /**
    * Print a delete button
    */
    public function putDeleteButton() 
    {
        return $this->button("delete");
    }
    
    /**
    * Print a info button
    */
    public function putInfoButton() 
    {
        return $this->button("info");
    }
    
    /**
    * Print a home button
    */
    public function putHomeButton() 
    {
        return $this->button("home");
    }
    
    /**
    * Print an edit button
    */
    public function linkedButton($type, $link, $target="_top",$alt=Null) 
    {
        $strout='<a href="'.$link.'" target="'.$target.'">'.$this->button($type,Null,'gif',$alt).'</a>';
        return $strout;
    }
    
    
    /**
    * Method to put a form field button
    */
    public function formButton($type, $label) 
    {
        global $objLanguage;
        $ret='<input type="'.$type
            .'" name="'.$label.'" value="'
            .$this->objLanguage->languageText("word_$label")
            .'" class="button" />';
        return $ret;
    }

    
    /** 
    * Method to put a save button
    */
    public function putSaveButton() 
    {
        return $this->formButton("submit", "save");
    }
    
    /** 
    * Method to put a search button
    */
    public function putSearchButton() 
    {
        return $this->formButton("submit", "search");
    }
    
    /**
    * Print a GO button
    */
    public function putGoButton() {
        return $this->formButton("submit", "go");
    }
    
    /**
    * Method to print a KEWL.NextGen button 
    * style link. Make sure that $linktext is translated
    * before passing it.
    * @param string $link: the URL to which the button links
    * @param string $linktext: the text to appear on the button
    * @param string $space: @values:TRUE|FALSE, whether to print a space after the button
    */
    public function pseudoButton($link, $linktext, $space=FALSE)
    {
        if (!$space==FALSE) {
            $space="&nbsp;";
        }
        return $space."<a href='".$link."' class='pseudobutton'>".$linktext.'</a>'.$space."\n";
    }
}
?>
