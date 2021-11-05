<?php
/**
 * Creates a Crossword, sets it ready to use and provides its HTML.
 * 
 * Requires the following files:
 * 1. crossword-article.html;
 * 2. Crossword.php; and
 * 2. AnswersAndQuestions.php.
 * 
 * Example of usage:
 * $crossword_creator = new CrosswordCreator();
 * $crossword_creator->createCrossword(["alice_in_wonderland"]);
 * 
 * setSessionVariablesForResultDotPhp();
 * 
 * $content = $crossword_creator->getHtml();
 * echo $content;
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

require_once(__DIR__ . '/../../model/AnswersAndQuestions.php');
require_once('Crossword.php');

class CrosswordCreator {
	/**
	 * Board with squares to be filled by the user with the correct letters.
	 * @var Crossword
	 */
	private $crossword = null;

	/**
	 * Object that supplies an array of answers and another with their questions.
	 * @var AnswersAndQuestions
	 */
	private $answersAndQuestions = null;



	/**
	 * The crossword generated will have this number of words or more.
	 * @var integer
	 */
	private $min_entries = 7;

	/**
	 * The horizontal squares will be this number or less.
	 * @var integer
	 */
	private $max_horizontal_squares = 10;

	/**
	 * The vertical squares will be this number or less, except for the influence of a longer word.
	 * @var integer
	 */
	private $initial_max_vertical_squares = 10;






	/**
	 * Initializes the properties necessary to create the Crossword.
	 * @return self
	 */
	public function __construct($min_entries = 7, $initial_max_vertical_squares = 10, $max_horizontal_squares = 10) {
		$this->min_entries = $min_entries;
		$this->initial_max_vertical_squares = $initial_max_vertical_squares;
		$this->max_horizontal_squares = $max_horizontal_squares;

		$this->answersAndQuestions = new AnswersAndQuestions();
	}

	/**
	 * Creates the Crossword using the properties set in the constructor.
	 *
	 * @param string[] $array_of_themes Themes recognized by AnswersAndQuestions.
	 * @return void
	 */
	public function createCrossword($array_of_themes) {
		// Creates multiple Crosswords until one of them meets the requirements.
		do {
			[$answers, $questions] = $this->answersAndQuestions->getAnswersAndQuestions($array_of_themes);
			$this->crossword = new Crossword($this->max_horizontal_squares, $this->initial_max_vertical_squares, $answers, $questions);
		} while (!$this->validateCrossword());

		$this->crossword->setReadyToUse();
	}



	/**
	 * Checks if the Crossword created is satisfactory.
	 * @return boolean
	 */
	private function validateCrossword() { return $this->crossword->getNumberOfEntries() >= $this-> min_entries; }

	/**
	 * Characters expected for each square of the Crossword.
	 * @return string[][] One letter in each valid [y, x] position.
	 */
	public function getMapOfCharacters() { return $this->crossword->getMapOfCharacters(); }

	/**
	 * Article with the Crossword and all its surrounding interface.
	 * @return string HTML ready to be displayed.
	 */
    public function getHtml() {
        $html = $this->getHtmlFile();
		$html_variables = $this->getHtmlVariables();

        $html = $this->replaceHtmlVariables($html, $html_variables);

        return $html;
    }

	/**
	 * Crossword's article's HTML whose variables must be replaced by their values.
	 * @return string Raw HTML from file.
	 */
    private function getHtmlFile() { return file_get_contents(__DIR__ . "/../../view/crossword-article.html"); }

    /**
	 * Variables to replace and their respective values.
	 * @return string[] "{{key}}" => "value"
	 */
	private function getHtmlVariables() {
        $html_variables = [
            "{{crossword}}" => $this->crossword->getHtml(),
			"{{questions}}" => $this->crossword->getQuestionsHtml()
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