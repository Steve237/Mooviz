<?php

namespace App\Entity;

class SearchMovies {

    private $category;
    
    public function getCategory() {

        return $this->category; 
    }

    public function setCategory(string $category) {

        $this->category = $category;
        return $this;

    }



}
