<?php
/**
 * Represents a position in the Crossword, be it a part of a word or not.
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

abstract class Square {
    /**
     * Y position in the Crossword.
     * @var integer
     */
    private $vertical_position = -1;

    /**
     * X position in the Crossword.
     * @var integer
     */
    private $horizontal_position = -1;

    /**
     * True means the Y position is the smallest of the Crossword.
     * @var boolean
     */
    private $is_top_border_square = false;

    /**
     * True means the X position is the smallest of the Crossword.
     * @var boolean
     */
    private $is_left_border_square = false;

    /**
     * Use it to avoid rearranging twice a Square that belongs to two answers.
     * @var integer
     */
    private $times_this_square_was_rearranged = 0;



    /**
     * Initializes the position of any subclass.
	 *
	 * @param integer $vertical_position Y position in the Crossword.
	 * @param integer $horizontal_position X position in the Crossword.
	 * @return self
     */
    public function __construct($vertical_position, $horizontal_position) {
        $this->vertical_position = $vertical_position;
        $this->horizontal_position = $horizontal_position;
    }

    

    /**
     * Get vertical_position. {@see $vertical_position}
     * @return integer
     */
    public function getVerticalPosition() { return $this->vertical_position; }

    /**
     * Get horizontal_position. {@see $horizontal_position}
     * @return integer
     */
    public function getHorizontalPosition() { return $this->horizontal_position; }



    /**
     * Identifies the Square as beying on the top border after the Crossword is ready.
     * @return void
     */
    public function setAsTopBorderSquare() { $this->is_top_border_square = true; }

    /**
     * Identifies the Square as beying on the left border after the Crossword is ready.
     * @return void
     */
    public function setAsLeftBorderSquare() { $this->is_left_border_square = true; }

    /**
     * Identifies the Square's HTML as beying on the top border after the Crossword is ready.
     * @return string A class to add to the HTML object, or empty.
     */
    private function getTopBorderSquareHtmlClass() {
        return $this->is_top_border_square ?
            "top-border-square" :
            "";
    }

    /**
     * Identifies the Square's HTML as beying on the left border after the Crossword is ready.
     * @return string A class to add to the HTML object, or empty.
     */
    private function getLeftBorderSquareHtmlClass() {
        return $this->is_left_border_square ?
            "left-border-square" :
            "";
    }



    /**
     * Moves the Square together with the Crossword.
     *
     * @param [type] $vertical_position_to_add Y amound to move.
     * @param [type] $horizontal_position_to_add X amount to move.
     * @param [type] $times_all_squares_will_have_been_rearranged_after_this_one The Square should moved only if it was moved less times than this.
     * @return void
     */
    public function addToPosition($vertical_position_to_add, $horizontal_position_to_add, $times_all_squares_will_have_been_rearranged_after_this_one) {
        if ($this->times_this_square_was_rearranged < $times_all_squares_will_have_been_rearranged_after_this_one) {
            $this->vertical_position += $vertical_position_to_add;
            $this->horizontal_position += $horizontal_position_to_add;

            $this->times_this_square_was_rearranged++;
        }
    }


    
	/**
	 * A display defined by the subclass.
	 * @return string HTML ready to be displayed.
	 */
    public function getHtml() {
        $html = $this->getHtmlFile();
		$html_variables = $this->getHtmlVariables();

		$replaced_html = $this->replaceHtmlVariables($html, $html_variables);

        return $replaced_html;
    }

    /**
     * The view corresponding to the subclass.
     * @return string HTML with variables to replace.
     */
    abstract protected function getHtmlFile();



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
    protected function getHtmlVariables() {
        $variables = [
            "{{vertical-position}}" => $this->getVerticalPosition(),
            "{{horizontal-position}}" => $this->getHorizontalPosition(),
            "{{top-border-square-class}}" => $this->getTopBorderSquareHtmlClass(),
            "{{left-border-square-class}}" => $this->getLeftBorderSquareHtmlClass()
        ];
        
        return $variables;
    }
}
?>