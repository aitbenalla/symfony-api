<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AbstractController
{
//    public function __invoke(Post $post): Post
//    {
//        $post->setOnline(true);
//        return $post;
//    }

    public function __invoke(PostRepository $postRepository, Request $request): int
    {
        $onlineQuery = $request->get('online');
        $condition = [];

        if ($onlineQuery !== null)
        {
            $condition = ['online' => $onlineQuery === '1' ? true : false];
        }

        return $postRepository->count($condition);
    }

}