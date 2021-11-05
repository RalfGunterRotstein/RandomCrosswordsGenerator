<?php
/**
 * Represents a position in the Crossword corresponding to a " " character in an answer.
 * 
 * Requires the following files:
 * 1. space-marker-square.html; and
 * 2. Square.php.
 * 
 * Example of usage:
 * $square = new SpaceMarkerSquare(0, 0);
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

class SpaceMarkerSquare extends Square {
	/**
	 * True means this Square is part of a vertical answer.
	 * @var boolean
	 */
	private $contains_vertical_direction = false;

	/**
	 * True means this Square is part of a horizontal answer.
	 * @var boolean
	 */
	private $contains_horizontal_direction = false;
	
	/**
	 * Calls the parent's constructor, then initializes the subclass properties.
	 *
	 * @param integer $vertical_position Y position in the Crossword.
	 * @param integer $horizontal_position X position in the Crossword.
	 * @param boolean $direction_is_horizontal False means the answer is vertical.
	 * @return self
	 */
	public function __construct($vertical_position, $horizontal_position, $direction_is_horizontal) {
		parent::__construct($vertical_position, $horizontal_position);

		$direction_is_horizontal ?
			$this->addHorizontalDirection() :
			$this->addVerticalDirection();
	}





	/**
	 * Marks the Square as part of a vertical answer.
	 * @return void
	 */
	public function addVerticalDirection() { $this->contains_vertical_direction = true; }

	/**
	 * Marks the Square as part of a horizontal answer.
	 * @return void
	 */
	public function addHorizontalDirection() { $this->contains_horizontal_direction = true; }

    /**
     * Get contains_vertical_direction. {@see $contains_vertical_direction}
     * @return boolean
     */
	public function containsVerticalDirection() { return $this->contains_vertical_direction; }

    /**
     * Get contains_horizontal_direction. {@see $contains_horizontal_direction}
     * @return boolean
     */
	public function containsHorizontalDirection() { return $this->contains_horizontal_direction; }



	/**
	 * Answer's character corresponding to this Square (always " ").
	 * @return string
	 */
	public function getCharacter() { return " "; }



	/**
	 * Square's HTML whose variables must be replaced by their values.
	 * @return string Raw HTML from file.
	 */
    protected function getHtmlFile() { return file_get_contents(__DIR__ . "/../../../view/squares/space-marker-square.html"); }
}
?>