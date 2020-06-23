<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


class ArticleFixtures extends Fixture 
{
    public function load(ObjectManager $manager) //$manager est un objet de la class d'objectManager(Objet de dépendance)
    {  
        // On déclare une boucle qui tourne 10 fois afin de créer 10 articles
       for($i = 1; $i <= 10; $i++)
        {
       $article = new Article; // Pour pouvoir créer des articles et les insérer en BDD, nous devons instancier la classe/Entité Article qui permet de renseigner les titres, contenu, image et date de l'article

       //On fait appel à tous les setteurs de l'objet $article pour ajouter un titre, une image, une date à nos articles
       $article->setTitle("Titre de l'article n° $i")
               ->setContent("<p>Contenu de l'article n° $i</p>")
               ->setImage("https://picsum.photos/id/1037/200") 
               ->setCreatedAt(new \DateTime());

               $manager->persist($article); 
               //la classe ObjectManager est une classe prédéfinie en Symfony qui permet de manipuler les lignes de la BDD (INSERT,UPDATE,DELETE)
               // persist() est une méthode issue de la classe ObjectManager qui permet de préparer les insertions et de les garder en mém
        }
        $manager->flush();  // Flush est une méthode de la class ObjectManager libère la requête d'insertion
    }
}
