<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController


/*
Symfony fonctionne toujours avec un système de routage. Une méthode d'un controller sera executée en fonction de la route transmise sur l'Url
ex: Si nous envoyons la route '/blog' dans l'url(http:localhost:8000/blog), cela fait appel au controller 'Blogcontroller" et execute la 
méthode 'index()'.  Cette méthode renvoit un templare sur le navigateur (méthode render())
Symfony se sert des annotations (@route())
Les annotations doivent toujours contenir 4 astérix

*/
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }

    /**
     *  @Route("/", name="home")
     */

     public function home()

     {
         return $this->render('blog/home.html.twig' ,[
             'title' => 'Bienvenue sur le Blog Symfony',
             'age' => 25
         ]);
     }
}
