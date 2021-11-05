<?php
/**
 * Represents a position with a character different than " " in the Crossword.
 * 
 * Requires the following files:
 * 3. filled-square.html.
 * 2. Square.php;
 * 3. Input.php; and
 * 4. FirstSquareQuestionNumber.php;
 * 
 * Example of usage:
 * $square = new FilledSquare(0, 0, false, 'c');
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
require_once('internal_elements/Input.php');
require_once('internal_elements/FirstSquareQuestionNumber.php');

class FilledSquare extends Square {
	/**
	 * Answer's character to which this Square corresponds.
	 * @var string
	 */
	private $character = "";

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
	 * Object that will receive the user's input.
	 * @var Input
	 */
	private $input = null;

	/**
	 * When not null, identifies the Square as the first of a vertical answer.
	 * @var FirstSquareQuestionNumber
	 */
	private $first_vertical_square_question_number = null;

	/**
	 * When not null, identifies the Square as the first of a horizontal answer.
	 * @var FirstSquareQuestionNumber
	 */
	private $first_horizontal_square_question_number = null;
	
	/**
	 * Calls the parent's constructor, then initializes the subclass properties.
	 *
	 * @param integer $vertical_position Y position in the Crossword.
	 * @param integer $horizontal_position X position in the Crossword.
	 * @param boolean $direction_is_horizontal False means the answer is vertical.
	 * @param string $character Multibyte character of the answer.
	 * @return self
	 */
	public function __construct($vertical_position, $horizontal_position, $direction_is_horizontal, $character) {
		parent::__construct($vertical_position, $horizontal_position);

		// It starts with one direction. The other will be added in case of crossing.
		$direction_is_horizontal ?
			$this->addHorizontalDirection() :
			$this->addVerticalDirection();

		$this->character = $character;

		$this->input = new Input($this);

		// Will become valid when it receives a number.
		$this->first_vertical_square_question_number = new FirstSquareQuestionNumber("vertical");
		// Will become valid when it receives a number.
		$this->first_horizontal_square_question_number = new FirstSquareQuestionNumber("horizontal");
	}

    /**
     * Marks the Square as the beginning of an answer after the Crossword is ready.
     *
     * @param integer $question_number Answer's position in the list of answers.
     * @param boolean $direction_is_horizontal Answer's direction.
     * @return void
     */
	public function setAsFirstSquareOfAnswer($question_number, $direction_is_horizontal) {
		$direction_is_horizontal ?
			$this->first_horizontal_square_question_number->setQuestionNumber($question_number) :
			$this->first_vertical_square_question_number->setQuestionNumber($question_number);
	}



    /**
     * Get character. {@see $character}
     * @return string A multibyte character.
     */
	public function getCharacter() { return $this->character; }



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
     * Identifies the Square's HTML as beying part of a vertical answer.
     * @return string A class to add to the HTML object, or empty.
     */
    private function getVerticalHtmlClass() {
        return $this->containsVerticalDirection() ?
            "vertical" :
            "";
    }

    /**
     * Identifies the Square's HTML as beying part of a horizontal answer.
     * @return string A class to add to the HTML object, or empty.
     */
    private function getHorizontalHtmlClass() {
        return $this->containsHorizontalDirection() ?
            "horizontal" :
            "";
    }





	/**
	 * Square's HTML whose variables must be replaced by their values.
	 * @return string Raw HTML from file.
	 */
    protected function getHtmlFile() { return file_get_contents(__DIR__ . "/../../../view/squares/filled-square.html"); }

	/**
	 * Variables to replace and their respective values.
	 * @return string[] "{{key}}" => "value"
	 */
	protected function getHtmlVariables() {
		$html_variables = parent::getHtmlVariables();

		$specific_html_variables = [
				"{{vertical-first-square-question-number}}" => $this->first_vertical_square_question_number->getHtml(),
				"{{horizontal-first-square-question-number}}" => $this->first_horizontal_square_question_number->getHtml(),
				"{{vertical-class}}" => $this->getVerticalHtmlClass(),
				"{{horizontal-class}}" => $this->getHorizontalHtmlClass(),
				"{{input}}" => $this->input->getHtml()
		];

		$all_variables = array_merge($html_variables, $specific_html_variables);
		
		return $all_variables;
	}
}
?>