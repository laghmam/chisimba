<?php
/**
 *
 * Canvas
 *
 * Chisimba canvas module.
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   canvas
 * @author    Derek Keats derek.keats@wits.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbcanvas.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* This class automates the selection of a canvas within
* the currently active skin.
*
* @author Derek Keats
* @package canvas
*
*/
class canvaschooser extends controller
{
    /**
    *
    * @var string $canvas The name of the canvas to load
    * @access private
    * 
    */
    private $canvas;

    /**
    *
    * Intialiser for the canvas chooser
    * @access public
    *
    */
    public function init()
    {
        $this->canvas = FALSE;
    }

    /**
     *
     * Identify and return the name of the canvas we should be using
     * to display our content.
     *
     * @param  string array $validCanvases An array of valid canvases sent by
     *  the skin that is being rendered. This is required for use of the
     *  canvas functionality.
     * @return string A valid canvas name
     * @access public
     *
     */
    public function getCanvas($validCanvases)
    {
        if (!$this->canvas) {
            // Look first in the parameters
            $canvas = $this->getParam('canvas', FALSE);
            if ($canvas) {
                $this->setCanvas($canvas);
            } else {
                $canvas = $this->guessCanvas();
            }
        } else {
            $canvas = $this->canvas;
        }
        if ($this->isValidCanvas($canvas, $validCanvases)) {
            return $canvas;
        } else {
            return "_default";
        }
    }

    /**
     *
     * Setter method for setting the canvas property of the class
     *
     * @param string $canvas The name of the canvas
     * @return boolean TRUE
     * @access public
     *
     */
    public function setCanvas($canvas)
    {
        $this->canvas = $canvas;
        return TRUE;
    }

    /**
     *
     * Guess what canvas should be presented based on circumstances.
     * Currently just returns default.
     *
     * @return string The guessed canvas name
     * @access private
     *
     */
    private function guessCanvas()
    {
        return "_default";
    }

    /**
     *
     * Determine if the passed canvas is valid for the current skin. This
     * information is supplied as an array by the skin author.
     *
     * @param string $canvas The name of the canvas
     * @param  string array $validCanvases An array of valid canvases sent by
     *  the skin that is being rendered. This is required for use of the
     *  canvas functionality.
     * @return boolean TRUE|FALSE
     * @access private
     */
    private function isValidCanvas($canvas, $validCanvases)
    {
        if (in_array($canvas, $validCanvases)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
?>