<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function index(ArticleRepository $repo)
    {    /* 

        Un des principes de Symfony est l'injection de dépendances.
        Par exemple, içi dans le cas de la méthode index(), celle ci a besoin de la classe ArticleRepository pour 
        fonctionner correctement. La méthode index() dépend de la classe ArticleRepository
        On injecte une dépendance en argument de la méthode index(), on impose un objet issu de la classe ArticleRepository
        On n'a plus besoin de faire appel à Doctrine(getDoctrine);
        $repo est un objet issu de la classe ArticleRepository et nous avons accès à toute les méthodes issues de cette classe
        Les méthodes sont moins chargé et c'est plus simple d'utilisation



         Pour sélectionner des données en BDD, nous avons besoin de la classe Repository de la classe Article
        Une classe Repository permet uniquement de sélectionner des données en BDD (requête SQL SELECT)
        On a besoin de l'ORM DOCTRINE pour faire la relation entre la BDD et le controller(getDoctrine())
        getRepository(): méthode issue de l'objet DOCTRINE qui permet d'importer une classe repository (SELECT)

        $repo est un objet issu de la classe ArticleRepository, cette classe contient des méthodes prédéfinies par SYMFONY permettant
        de sélectionner des données en BDD(find(), findBy(), findOneBy(), findAll())

        findAll()est une méthode issue de la classe ArticleRepository permettanr de sélectionner l'ensemble de la table SQL donc içi la table 
        Article

        */

        //$repo =$this->getDoctrine()->getRepository(Article::class);

        $articles = $repo->findAll();

        dump($articles);// equivalent de var_dump();

        return $this->render('blog/index.html.twig', [ // On envoit sur le navigateur le template à afficher
            'controller_name' => 'BlogController',
            'articles'=> $articles // On envoit sur le template 'index.html.twig' les articles sélectionnées en BDD $articles que nous allons traiter avec le langage TWIG sur le template
        ]);
    }

    // home() : méthode

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

     // Create() : methode permettant d'insérer un nouvel article en BDD

    /**
     *  @Route ("/blog/new", name="blog_create")
     *  @Route ("/blog/{id}/edit", name="blog_edit")
     */

    public function create(Article $article = null, Request $request, EntityManagerInterface $manager)
    {

        /*     La classe Request est une classe prédéfinie en Symfony qui stocke toutes les données véhiculées par les superglobales($_POST,$_COOKIE,$_SERVER etc...)
                Nous avons avons accès aux données saisies dans le formulaire via l'objet $request
                La propriété 'request->request' représente la superglobale $_POST, les données saisies dans le formulaire sont 
                accessibles via cette propriété.
                Pour insérer un nouvel article, nous devons instancier la classe/Entité Article pour avoir un objet article vide, afin de 
                renseigner tous les setteurs de l'objet $article

                EntityManagerInterface est une interface prédéfinie en symphony qui permet de manipuler les lignes de la BDD (INSERT, UPDATE, DELETE);
                Elle possède des méthodes permettant de préparer et d'exécuter les requêtes SQL persist(), flush()

                persist() est un eméthode issue de l'interface EntityManagerInterface qui permet de préparer et stocker la requete SQL
                Flush() est une méthode issue de l'interfaceEntityManager qui permet de libérer et d'exécuter la requête SQL

                redirectToRoute() est une méthode prédéfinie en symfony qui permet de rediriger vers une route spécifique
                , dans notre cas on redirige après l'insertion la oute 'blog_show' (détail de l'article que l'on vient d'insérer) et on transmet
                à la méthode l'id de l'article à envoyer dans l'URL

                get() : méthode de l'objet $request qui permet de récupérer les données saisies aux différents indices 'name' du formulaire


        */
        // dump($request);

        //if($request->request->count() > 0) // je pointe dans mon dump et vérifie qu'il ya du contenu
//{
  //  $article = new Article;
    //$article->SetTitle($request->request->get('title'))
      //      ->SetContent($request->request->get('content')) 
       //     ->SetImage($request->request->get('image')) 
         //   ->SetCreatedAt(new DateTime());

    // $manager->persist($article);
    // $manager->flush();   
    
    // dump($article);

   // return $this->redirectToRoute('blog_show', [ // 2 arguments l'url et l'id sous tableau array qu'on précise à la méthode "redirectToRoute'
   //     'id' => $article->getId()
  //      ]);

//}       
        if(!$article)  // Si ce n'est pas une modification
        {

        $article = new Article;
        }

        

        $form = $this->createFormBuilder($article)
                      ->add('title')
                      ->add('content')
                      ->add('image')
                      ->getForm(); 
                      
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $article->setCreatedAt(new Datetime);
            
            $manager->persist($article);
            $manager->flush();

            dump($article);

            return $this->redirectToRoute('blog_show', [ // 2 arguments l'url et l'id sous tableau array qu'on précise à la méthode "redirectToRoute'
                   'id' => $article->getId()
                     ]);


        }

        return $this->render('/blog/create.html.twig', [
            'formArticle' => $form->createView()
        ]);

    }



     /**
      * @Route("/blog/{id}", name="blog_show")
      */

    public function show(ArticleRepository $repo, $id) // id 2
    {
        /*   

        show (ArticleRepository $repo) --> $repo c'est une variable de reception que nous nommons à souhait et qui receptionne
        un objet issu de la class ArticleRepository

        Pour sélectionner 1 article en BDD, c'est à dire voir le détail d'1 article, nous utilisons le principe de route paramétrée
        ("/blog/{id}"), notre route attends un paramètre de type {id}, donc l'id d'1 article qui est stockée
        en BDD. Lorsque nous transmettons dans l'Url une route par exple "/blog/9", on envoie un id connu dans l'url,Symfony va automatiquement récupérer 
        ce paramètre pour le transmettre en argument de la méthode show().
        Le but est de sélectionner les données en BDD de l'{id} récupéré en paramètre.
        Nous avons besoin pour cela de la classe ArticleRepository afin de pouvoir sélectionner en BDD.
        La méthode find() est issue de la classe ArticleRepository et permet de sélectionner des données en BDD avec un 
        argument de type {id}
        DOCTRINE fait après tout le travail pour nous, c'est à dire qu'elle récupère la requête de sélection pour l'exécuter
        en BDD et elle retourne le résultat au controller

        $repo est un objet issu de la class ArticleRepository, cette classe contienr des méthodes prédéfinies par SYMFONY
        permettant de sélectionner des données en BDD (find(), findBy(), )


        */
        //$repo = $this->getDoctrine()->getRepository(Article::class);

        $article = $repo->find($id); //id 2 en argument de la méthode

        dump($article);
        
        return $this->render('blog/show.html.twig' , [
            'article' => $article // on envoit avec le template show.html.twig l'article 1 selectionné en BDD
            ]);
            //Arguments render ('template_a_envoyer', 'ARRAY options')
    }

  


}
