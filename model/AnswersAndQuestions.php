<?php
/**
 * Randomly suplies an array of answeres and another with its questions in the same order.
 * 
 * Example of usage:
 * $answersAndQuestions = new AnswersAndQuestions();
 * [$answers, $questions] = $answersAndQuestions->getAnswersAndQuestions(["wonderland"]);
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

class AnswersAndQuestions {
	public function __construct() {}

    /**
     * Array with two arrays: answers and questions.
     * @param string[] $array_of_themes Answers for each theme wil be randomly included.
     * @return string[][] Array with an array of answers and another with their questions.
     */
    public function getAnswersAndQuestions($array_of_themes = ["wonderland", "looking-glass"]) {
        $answers = [];
        $questions = [];

        foreach ($array_of_themes as $theme) {
            [$new_answers, $new_possible_questions_for_each_answer] = $this->getAnswersAndPossibleQuestionsForEachAnswerByTheme($theme);
            
            $new_questions = $this->getSelectedQuestionForEachAnswer($new_possible_questions_for_each_answer);
            
            $answers = array_merge($answers, $new_answers);
            $questions = array_merge($questions, $new_questions);
        }

        [$reordered_answers, $reordered_questions] = $this->getRandomizedAnswersAndQuestions($answers, $questions);

        return [$reordered_answers, $reordered_questions];
    }

    /**
     * Array with two arrays: answers, and possible questions for each answer (array of arrays).
     * @param string[][] $theme Defines the method used to generate the questions and answers.
     * @return string[][] Array with array of answers and possible questions for each answer.
     */
    private function getAnswersAndPossibleQuestionsForEachAnswerByTheme($theme) {
        switch($theme) {
            default:
            case "aristotle": return $this->getAnswersAndPossibleQuestionsForEachAnswerAboutAliceInWonderland();

            case "looking-glass": return $this->getAnswersAndPossibleQuestionsForEachAnswerAboutThroughTheLookingGlass();
        }
    }

    /**
     * Array with two arrays: answers, and possible questions for each answer (array of arrays).
     * @return string[][]
     */
    private function getAnswersAndPossibleQuestionsForEachAnswerAboutAliceInWonderland() {
        $answers = []; // strings
        $questions = []; // arrays of strings
        $row = 0;
    
        $answers[$row] = "alice";
        $questions[$row] = [];
        array_push($questions[$row], "The dream-child.");
        array_push($questions[$row], "Is a lovely little girl!");
        array_push($questions[$row], "Goes through a lot of changes.");
    
        $row++;
        $answers[$row] = "white rabbit";
        $questions[$row] = [];
        array_push($questions[$row], "Was in a hurry!");
        array_push($questions[$row], "Showed the way to Wonderland.");
    
        $row++;
        $answers[$row] = "caterpillar";
        $questions[$row] = [];
        array_push($questions[$row], "Smokes a lot!");
        array_push($questions[$row], "Is exactly three inches high.");
    
        $row++;
        $answers[$row] = "duchess";
        $questions[$row] = [];
        array_push($questions[$row], "Is a very ugly woman!");
        array_push($questions[$row], "Has a very sharp chin.");
    
        $row++;
        $answers[$row] = "cheshire cat";
        $questions[$row] = [];
        array_push($questions[$row], "Can disappear.");
        array_push($questions[$row], "Has a very distinctive grin.");
    
        $row++;
        $answers[$row] = "hatter";
        $questions[$row] = [];
        array_push($questions[$row], "Was murdering the time.");
        array_push($questions[$row], "Is always drinking tea.");
    
        $row++;
        $answers[$row] = "march hare";
        $questions[$row] = [];
        array_push($questions[$row], "Goes mad in march.");
        array_push($questions[$row], "Is always drinking tea.");
    
        $row++;
        $answers[$row] = "dormouse";
        $questions[$row] = [];
        array_push($questions[$row], "Has difficulty telling a story to the end.");
        array_push($questions[$row], "Is always sleepy.");
    
        $row++;
        $answers[$row] = "queen of hearts";
        $questions[$row] = [];
        array_push($questions[$row], "Has ordered many executions.");
        array_push($questions[$row], "Likes to play croquet.");
    
        $row++;
        $answers[$row] = "king of hearts";
        $questions[$row] = [];
        array_push($questions[$row], "Forgives many people his wife condemns.");
        array_push($questions[$row], "Is the judge of Wonderland.");
    
        $row++;
        $answers[$row] = "knave of hearts";
        $questions[$row] = [];
        array_push($questions[$row], "Was accused of stealing some tarts.");
        array_push($questions[$row], "Was the defendant of a great criminal trial.");
    
        $row++;
        $answers[$row] = "gryphon";
        $questions[$row] = [];
        array_push($questions[$row], "Is a flying mythical creature.");
        array_push($questions[$row], "Has wings and feathers, but is not a bird.");
        
        $row++;
        $answers[$row] = "mock turtle";
        $questions[$row] = [];
        array_push($questions[$row], "Is a very melancholic character.");
        array_push($questions[$row], "Went to school in the sea.");
    
        return [$answers, $questions];
    }

    /**
     * Array with two arrays: answers, and possible questions for each answer (array of arrays).
     * @return string[][]
     */
    private function getAnswersAndPossibleQuestionsForEachAnswerAboutThroughTheLookingGlass() {
        $answers = []; // strings
        $questions = []; // arrays of strings
        $row = 0;
  
        $answers[$row] = "alice";
        $questions[$row] = [];
        array_push($questions[$row], "Becomes a queen.");
        array_push($questions[$row], "Travels a great distance to complete an objective.");
      
        $row++;
        $answers[$row] = "humpty dumpty";
        $questions[$row] = [];
        array_push($questions[$row], "Sat on a wall, then had a great fall.");
        array_push($questions[$row], "When he uses a word, it means just what he chooses it to mean.");
        array_push($questions[$row], "Looks like an egg.");
      
        $row++;
        $answers[$row] = "red king";
        $questions[$row] = [];
        array_push($questions[$row], "Spends all the story sleeping.");
      
        $row++;
        $answers[$row] = "red knight";
        $questions[$row] = [];
        array_push($questions[$row], "Loses a duel.");
      
        $row++;
        $answers[$row] = "red queen";
        $questions[$row] = [];
        array_push($questions[$row], "Runs extremely fast.");
        array_push($questions[$row], "Explains the rules of Chess to Alice.");
      
        $row++;
        $answers[$row] = "tweedledum";
        $questions[$row] = [];
        array_push($questions[$row], "His name rhymes with drum.");
      
        $row++;
        $answers[$row] = "tweedledee";
        $questions[$row] = [];
        array_push($questions[$row], "His name rhymes with bee.");
      
        $row++;
        $answers[$row] = "white king";
        $questions[$row] = [];
        array_push($questions[$row], "Whished he was capable of seeing Nobody.");
        array_push($questions[$row], "His wife is too fast for him to follow her.");
        
        $row++;
        $answers[$row] = "white knight";
        $questions[$row] = [];
        array_push($questions[$row], "Wins a duel.");
        array_push($questions[$row], "Is an inventor.");
        array_push($questions[$row], "Escorts Alice to her destination.");
        array_push($questions[$row], "Is a representation of the author himself.");
      
        $row++;
        $answers[$row] = "white queen";
        $questions[$row] = [];
        array_push($questions[$row], "When young, could believe six impossible things before breakfast.");
        array_push($questions[$row], "Pricks her finger with the pin of her brooch after it starts bleeding, which explains the bleeding.");
      
        $row++;
        $answers[$row] = "lion";
        $questions[$row] = [];
        array_push($questions[$row], "Has a mane.");
        array_push($questions[$row], "Beat his enemy all round the town.");
      
        $row++;
        $answers[$row] = "unicorn";
        $questions[$row] = [];
        array_push($questions[$row], "Is a horned creature.");
        array_push($questions[$row], "Is a land mythical animal.");
        array_push($questions[$row], "Always thought children were fabulous monsters.");
      
        return [$answers, $questions];
    }



    /**
     * Randomly selects one question per answer from an array of multiple questions for each.
     * @param string[][] $possible_questions_for_each_answer Each row contains multiple questions for the same answer.
     * @return string[] A single question for each answer.
     */
    private function getSelectedQuestionForEachAnswer($possible_questions_for_each_answer) {
        $selected_questions = [];

        foreach ($possible_questions_for_each_answer as $questions_for_an_answer) {
            $selected_question = $this->getRandomQuestionForAnAnswer($questions_for_an_answer);
            array_push($selected_questions, $selected_question);
        }

        return $selected_questions;
    }

    /**
     * Randomly selects one question from an array of questions.
     * @param string[] $questions_for_answer Multiple questions for the same answer.
     * @return string A single question.
     */
    private function getRandomQuestionForAnAnswer($questions_for_answer) {
        $random_row = rand(0, count($questions_for_answer)-1);
        $random_question = $questions_for_answer[$random_row];

        return $random_question;
    }

    /**
     * Randomly reorders the given pair of answers and questions.
     * @param string[] $answers_to_randomize Answers for the Crossword.
     * @param string[] $questions_to_randomize Each answer's question.
     * @return string[][] Array with two arrays: answers and questions.
     */
    private function getRandomizedAnswersAndQuestions($answers_to_randomize, $questions_to_randomize) {
        $randomized_answers = [];
        $randomized_questions = [];

        while (count($answers_to_randomize) > 0) {
            $random_index = rand(0, count($answers_to_randomize)-1);

            $selected_answer = $answers_to_randomize[$random_index];
            $selected_question = $questions_to_randomize[$random_index];

            array_splice($answers_to_randomize, $random_index, 1);
            array_splice($questions_to_randomize, $random_index, 1);

            array_push($randomized_answers, $selected_answer);
            array_push($randomized_questions, $selected_question);
        }

        return [$randomized_answers, $randomized_questions];
    }
}
?>