<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class SlugifyService
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function slugify(string $title): string
    {
        return $this->slugger->slug($title, '_')->lower();
    }
}
