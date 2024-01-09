<?php

namespace App\Service;
use App\Entity\Article;

class ReadingTimeService {
    /**
     * Function to calculate the estimated reading time of the given text.
     * 
     * @param string $text The text to calculate the reading time for.
     * @param string $wpm The rate of words per minute to use.
     * @return Float
     */
    public function estimateReadingTime(Article $article, $wpm = 238) {
        
        
        $title = $article->getTitle();
        $description = $article->getDescription();
        $content = $article->getContent();
        
        $totalWords = str_word_count($title) + str_word_count($description) + str_word_count($content);
        
        $minutes = ceil($totalWords / $wpm);
        return $minutes;
    }
}
