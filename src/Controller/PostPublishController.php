<?php


namespace App\Controller;


use App\Entity\Post;
use App\Service\MailerService;

class PostPublishController
{
    public function __construct(private MailerService $mailer){}

    public function __invoke(Post $data): Post
    {
        $data->setOnline(true);

        $this->mailer->send(
            'dreidemyromain@gmail.com',
            'Publication de l\'article !',
            $data->getTitle() . ' vient d\'être publier ! C\'est super nan ?'
        );

        return $data;
    }
}