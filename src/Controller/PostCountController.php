<?php


namespace App\Controller;


use App\Repository\PostRepository;

class PostCountController
{
    public function __invoke($data): int
    {
        return count($data);
    }
}