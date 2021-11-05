<?php
/**
 * Stores a question from a Crossword and provides its HTML.
 * 
 * Requires the following files:
 * 1. question.html.
 * 
 * Example of usage:
 * $question = new Question($question_text);
 * $question->setNumber($question_number);
 * $html = $question->getHtml();
 * echo $html;
 *
 * @author Ralf Gunter Rotstein <ralfrotstein@gmail.com>
 * @copyright Copyright (c) 2021, Ralf Gunter Rotstein
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License
 * 
 * @category RandomCrosswordsGenerator
 * @package RandomCrosswordsGenerator
 * @version 1.0.0
 */

namespace random_crosswords_generator;

class Question {
    /**
     * Text to be displayed in a list of questions.
     * @var string
     */
    private $text = "";
    /**
     * Number to match the question with its answer.
     * @var integer
     */
    private $number = 0;
    
    /**
     * Defines the question's text.
     * @param [type] $text The question's text
     * @return self
     */
    public function __construct($text) {
        $this->text = $text;
    }



    /**
     * Defines the question's number.
     * @param [type] $number The question's number.
     * @return void
     */
    public function setNumber($number) { $this->number = $number; }

    /**
     * Question's number.
     * @return integer
     */
    public function getNumber() { return $this->number; }

	/**
	 * A div containing a DT and a DD. Should go inside a DL.
	 * @return string HTML ready to be displayed.
	 */
    public function getHtml() {
        $html = file_get_contents(__DIR__ . "/../../view/question.html");
		$html_variables = $this->getHtmlVariables();

        $html = $this->replaceHtmlVariables($html, $html_variables);

        return $html;
    }

    /**
	 * Variables to replace and their respective values.
	 * @return string[] "{{key}}" => "value"
	 */
    private function getHtmlVariables() {
        $html_variables = [
            "{{question-number}}" => $this->number,
            "{{question-text}}" => $this->text
        ];
        
        return $html_variables;
    }

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
}
?>