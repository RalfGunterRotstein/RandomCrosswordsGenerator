<?php
/**
 * Manages an answer divided in squares along with its question.
 * 
 * Requires the following files:
 * 1. FilledSquare.php;
 * 2. SpaceMarkerSquare.php; and
 * 3. Question.php.
 * 
 * Example of usage:
 * $horizontal_entry = new Entry(["a", "n", "s", "w", "e", "r"], "Question?", true, 0, 0);
 * tryToInsertEntry($horizontal_entry);
 * 
 * $vertical_entry = new Entry(["a", "r", "c"], "It is curve.", false, 0, 0);
 * tryToInsertEntry($vertical_entry);
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

require_once('Question.php');
require_once('squares/FilledSquare.php');
require_once('squares/SpaceMarkerSquare.php');

class Entry {
    /**
     * Expected input for each square. (One multibyte character per row.)
     * @var string[]
     */
    private $answer_letters_array = [];

    /**
     * The question related to the answer.
     * @var Question
     */
    private $question = null;
    
    /**
     * True: the answer fits horizontally in the crossword. False: it fits vertically.
     * @var boolean
     */
    private $direction_is_horizontal = false;

    /**
     * The squares for each character of the answer.
     * @var Square[]
     */
    private $answer_squares = [];
    

    /**
     * Initializes the properties related to the answer and its question.
     *
     * @param string $answer_letters_array One multibyte character per row.
     * @param string $question_text The text of the question for the answer.
     * @param boolean $direction_is_horizontal False means the answer is vertical.
     * @param integer $firstSquare_vertical_position Y position of the first character.
     * @param integer $firstSquare_horizontal_position X position of the first character.
	 * @return self
     */
    public function __construct($answer_letters_array, $question_text, $direction_is_horizontal, $firstSquare_vertical_position, $firstSquare_horizontal_position) {
        $this->answer_letters_array = $answer_letters_array;

        $this->question = new Question($question_text);

        $this->direction_is_horizontal = $direction_is_horizontal;

        $this->createAnswerSquares($firstSquare_vertical_position, $firstSquare_horizontal_position);
    }



    /**
     * Creates one Square per character of the answer, with its corresponding character and position, and stores them in the property $answer_squares.
     * 
     * @param integer $firstSquare_vertical_position Y position of the first character.
     * @param integer $firstSquare_horizontal_position X position of the first character.
     * @return void
     */
    private function createAnswerSquares($firstSquare_vertical_position, $firstSquare_horizontal_position) {
        for ($i=0; $i < count($this->answer_letters_array); $i++) {
            $letter = $this->answer_letters_array[$i];

            // The v. pos. increases only if the direction is vertical.
            $vertical_position = $firstSquare_vertical_position + $i * !$this->direction_is_horizontal;
            
            // The h. pos. increases only if the direction is horizontal.
            $horizontal_position = $firstSquare_horizontal_position + $i * $this->direction_is_horizontal;

            $square = $this->createSquare($vertical_position, $horizontal_position, $letter);

            array_push($this->answer_squares, $square);
        }
    }

    /**
     * Returns the Square to be put in the position passed.
     * 
     * @param integer $vertical_position Y position of the character.
     * @param integer $horizontal_position X position of the character.
     * @param string $character Multibyte character in the [Y, X] position.
     * @return FilledSquare|SpaceMarkerSquare
     */
    private function createSquare($vertical_position, $horizontal_position, $letter) {
        if ($letter == " ")
            return new SpaceMarkerSquare($vertical_position, $horizontal_position, $this->direction_is_horizontal);
        else
            return  new FilledSquare($vertical_position, $horizontal_position, $this->direction_is_horizontal, $letter);
    }


    
    /**
     * Number of characters in the answer.
     * @return integer
     */
    public function getAnswerLength() { return count($this->answer_letters_array); }

    /**
     * Get answer_squares. {@see $answer_squares}
     * @return Square[]
     */
    public function getAnswerSquares() { return $this->answer_squares; }

    /**
     * Square corresponding to the first character of the answer.
     * @return Square
     */
    public function getAnswerFirstSquare() { return $this->answer_squares[0]; }

    /**
     * Square corresponding to the last character of the answer.
     * @return Square
     */
    public function getAnswerLastSquare() { return $this->answer_squares[$this->getAnswerLength() - 1]; }

    /**
     * Get question. {@see $question}
     * @return Question
     */
    public function getQuestion() { return $this->question; }

    /**
     * Get direction_is_horizontal. {@see $direction_is_horizontal}
     * @return boolean
     */
    public function getDirectionIsHorizontal() { return $this->direction_is_horizontal; }


    
    /**
     * Y position of the first character.
     * @return integer
     */
    public function getMinVerticalPosition() { return $this->answer_squares[0]->getVerticalPosition(); }

    /**
     * Y position of the last character.
     * @return integer
     */
    public function getMaxVerticalPosition() { return $this->answer_squares[count($this->answer_squares)-1]->getVerticalPosition(); }

    /**
     * X position of the first character.
     * @return integer
     */
    public function getMinHorizontalPosition() { return $this->answer_squares[0]->getHorizontalPosition(); }

    /**
     * X position of the last character.
     * @return integer
     */
    public function getMaxHorizontalPosition() { return $this->answer_squares[count($this->answer_squares)-1]->getHorizontalPosition(); }



    /**
     * Moves all Squares together with the Crossword.
     *
     * @param [type] $vertical_position_to_add Y amound to move.
     * @param [type] $horizontal_position_to_add X amount to move.
     * @param [type] $times_all_squares_will_have_been_rearranged_after_this_one Only the squares moved less times than this should be moved.
     * @return void
     */
    public function addToPosition($vertical_position_to_add, $horizontal_position_to_add, $times_all_squares_will_have_been_rearranged_after_this_one) {
        foreach ($this->answer_squares as $answerSquare)
            $answerSquare->addToPosition($vertical_position_to_add, $horizontal_position_to_add, $times_all_squares_will_have_been_rearranged_after_this_one);
    }



    /**
     * Marks the first Square as the beginning of the answer after the Crossword is ready.
     *
     * @param integer $question_number Answer's position in the list of answers.
     * @return void
     */
    public function setFirstSquareAndQuestionNumber($question_number) {
        $this->answer_squares[0]->setAsFirstSquareOfAnswer($question_number, $this->direction_is_horizontal);

        $this->question->setNumber($question_number);
    }

	/**
	 * A display defined by the class Question.
	 * @return string HTML ready to be displayed.
	 */
    public function getQuestionHtml() { return $this->question->getHtml(); }

    

    /**
     * Adds inexistent squares to the crossword and the answer's direction to the others.
     * 
     * If the square already exists in the crossword in the other direction, the Entry will drop the new square, and use the one already existent in the crossword, just adding a new direction to it, making it vertical and horizontal at the same time. It means two answers cross each other at this point.
     *
     * @param Crossword $crossword The board to which the answer belongs.
     * @return void
     */
    public function storeSquaresOnCrossword($crossword) {
        foreach ($this->answer_squares as &$answer_square) {
            $vertical_position = $answer_square->getVerticalPosition();
            $horizontal_position = $answer_square->getHorizontalPosition();

            if (!$crossword->squareExists($vertical_position, $horizontal_position))
                $crossword->insertSquare($answer_square);
            else {
                // Adopts the old Square of the Crossword instead of its own.
                $answer_square = $crossword->getSquare($vertical_position, $horizontal_position);

                // Adds the missing direction to the old Square that was crossed.
                $this->direction_is_horizontal ?
                    $answer_square->addHorizontalDirection() :
                    $answer_square->addVerticalDirection();
            }
        }
    }



    /**
     * False means I should come after the given Entry.
     *
     * @param Entry $given_entry Instance before which the called one could be positioned.
     * @return boolean
     */
    public function IShouldComeBeforeTheGivenEntry($given_entry) {
        $only_i_am_horizontal = $this->getDirectionIsHorizontal() && !$given_entry->getDirectionIsHorizontal();

        if ($only_i_am_horizontal)
            return true;



        $we_have_the_same_direction = $this->getDirectionIsHorizontal() == $given_entry->getDirectionIsHorizontal();

        $i_am_leftwards = $this->getAnswerFirstSquare()->getHorizontalPosition() < $given_entry->getAnswerFirstSquare()->getHorizontalPosition();
        
        if ($we_have_the_same_direction && $i_am_leftwards)
            return true;



        $we_have_the_same_horizontal_position = $this->getAnswerFirstSquare()->getHorizontalPosition() == $given_entry->getAnswerFirstSquare()->getHorizontalPosition();

        $i_am_upwards = $this->getAnswerFirstSquare()->getVerticalPosition() < $given_entry->getAnswerFirstSquare()->getVerticalPosition();

        if ($we_have_the_same_direction && $we_have_the_same_horizontal_position && $i_am_upwards)
            return true;
        

            
        return false;
    }
}
?>