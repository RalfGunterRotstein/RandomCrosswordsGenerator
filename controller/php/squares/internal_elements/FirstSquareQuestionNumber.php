<?php
/**
 * Stores a direction and the number of the first Square of an answer written in this direction.
 * 
 * Requires the following files:
 * 1. first-square-question-number.html.
 * 
 * Example of usage:
 * $first_vertical_square_question_number = new FirstSquareQuestionNumber("vertical");
 * $first_vertical_square_question_number->setQuestionNumber($question_number);
 * $html = $first_vertical_square_question_number->getHtml();
 * echo $html;
 *
 * @author Ralf Gunter Rotstein <ralfrotstein@gmail.com>
 * @copyright Copyright (c) 2021, Ralf Gunter Rotstein
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License
 * 
 * @category RandomCrosswordsGenerator
 * @package RandomCrosswordsGenerator_Squares_InternalElements
 * @version 1.0.0
 */

namespace random_crosswords_generator;

class FirstSquareQuestionNumber {
	/**
	 * 0 means the Square is not the first of an answer in this direction.
	 * @var integer
	 */
	private $question_number = 0;

	/**
	 * Direction for which this number would be valid.
	 * @var string "vertical" or "horizontal"
	 */
	private $direction = "";

	/**
	 * Sets the properties.
	 * 
	 * @param string $direction "vertical" or "horizontal".
	 * @return self
	 */
	public function __construct($direction) { $this->direction = $direction; }

	public function setQuestionNumber($question_number) { $this->question_number = $question_number; }
	


	/**
	 * A span containing a number.
	 * @return string HTML ready to be displayed.
	 */
	public function getHtml() {
		if ($this->question_number != 0) {
			$html = $this->getHtmlFile();
			$html_variables = $this->getHtmlVariables();

			$replaced_html = $this->replaceHtmlVariables($html, $html_variables);

			return $replaced_html;
		}
		else
        	return "";
	}

	/**
	 * FirstSquareQuestionNumber's HTML whose variables must be replaced by their values.
	 * @return string Raw HTML from file.
	 */
    private function getHtmlFile() { return file_get_contents(__DIR__ . "/../../../../view/squares/internal-elements/first-square-question-number.html"); }



	/**
	 * Replaces expected "{{variables}}" for their "values".
	 *
	 * @param string $html HTML with {{variables}} to be replaced.
	 * @param string[] $html_variables Dictionary: {{variable}} => value.
	 * @return string HTML with variables replaced for their values.
	 */
	private function replaceHtmlVariables($html, $html_variables) {
		$html_keys = array_keys($html_variables);
		$html_values = array_values($html_variables);

		$replaced_html = str_replace($html_keys, $html_values, $html);

		return $replaced_html;
	}


	
	/**
	 * Variables to replace and their respective values.
	 * @return string[] "{{key}}" => "value"
	 */
	private function getHtmlVariables() {
		$html_variables = [
			"{{direction}}" => $this->direction,
			"{{question-number}}" => $this->question_number
		];
		
		return $html_variables;
	}
}
?>