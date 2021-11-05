<?php
/**
 * Represents a position with no character in the Crossword, in an area surrounded by answers.
 * 
 * Requires the following files:
 * 1. inner-square.html; and
 * 2. Square.php.
 * 
 * Example of usage:
 * $square = new InnerSquare(0, 0);
 * $content = $square->getHtml();
 * echo $content;
 *
 * @author Ralf Gunter Rotstein <ralfrotstein@gmail.com>
 * @copyright Copyright (c) 2021, Ralf Gunter Rotstein
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License
 * 
 * @category RandomCrosswordsGenerator
 * @package RandomCrosswordsGenerator_Squares
 * @version 1.0.0
 */

namespace random_crosswords_generator;

require_once('Square.php');

class InnerSquare extends Square {
	/**
	 * Calls the parent's constructor.
	 *
	 * @param integer $vertical_position Y position in the Crossword.
	 * @param integer $horizontal_position X position in the Crossword.
	 * @return self
	 */
	public function __construct($vertical_position, $horizontal_position) {
		parent::__construct($vertical_position, $horizontal_position);
	}

	/**
	 * Square's HTML whose variables must be replaced by their values.
	 * @return string Raw HTML from file.
	 */
    protected function getHtmlFile() { return file_get_contents(__DIR__ . "/../../../view/squares/inner-square.html"); }
}
?>