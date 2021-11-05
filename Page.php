<?php
namespace random_crosswords_generator;

require_once('controller/php/CrosswordCreator.php');

class Page {
    public function __construct($page_name, $themes_array) {
        $this->page_name = $page_name;
        $this->themes_array = $themes_array;
        
        $this->html_file_location = __DIR__ . "/view/crossword-article.html";
    }

    protected function getMainContent() {
        $content = "";
        
        $this->crossword_creator = new CrosswordCreator();
        $this->crossword_creator->createCrossword($this->themes_array);
        
        $this->setSessionVariables();

        $content .= $this->crossword_creator->getHtml();

        return $content;
    }

    protected function setSessionVariables() {
        $_SESSION["sectionNameForDisplay"] = $this->page_name;
        $_SESSION["bidimensionalArrayOfCorrectCharactersForCurrentCrosswords"] = $this->crossword_creator->getMapOfCharacters();
    }





    protected function getImage($image_name, $image_extension) { return file_get_contents(__DIR__ . "/view/images/" . $image_name . "." . $image_extension); }




    
    /**
     * Retorna página solicitada
     * @return string
    */
    private function getView() {
        $content = "";

        $content .= $this->getMainContent();



        $variables = $this->getHtmlVariables();



        $keys = array_keys($variables);
        $keys = array_map(function($item) { return "{{" . $item . "}}"; }, $keys);

        $values = array_values($variables);


        $content = str_replace($keys, $values, $content);
        return $content;
    }

    /**
     * Imprime página na tela.
    */
    public function display() {
        $content = $this->getView();
        echo $content;
    }





    protected function getHtmlVariables() {
        $html_variables = [
            "svg-direction-icon" => $this->getImage("direction-selector", "svg"),
            "space-marker-image" => $this->getImage("space-marker", "svg"),
        ];
        
        return $html_variables;
    }
}