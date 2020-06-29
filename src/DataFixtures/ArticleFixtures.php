<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;



class ArticleFixtures extends Fixture 
{
    public function load(ObjectManager $manager) //$manager est un objet de la class d'objectManager(Objet de dépendance)
    {  
        // La librairie FAKER permet d'insérer en BDD des fausses données (fixtures), il génère via différentes méthodes, des données aléatoires (paragraphes aléatoires, nom et prénoms aléatoires etc...)
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 1; $i <=3; $i++)

        {   // Création de 3 catégories
            //On appelle les setteurs de l'objet $category
            // sentence(): méthode issue de l'objey $faker qui permet de créer des phases aléatoires
            // paragraph() : Méthode issue de l'objet $faker qui permet de créer des paragraphes aléatoires
            $category = new Category;

            $category->setTitle($faker->sentence())
                    ->setDescription($faker->paragraph());

            $manager->persist($category);

            // Création de 4 à 6 articles par catégorie

            for ($j = 1; $j<= mt_rand(4,6); $j++)

            {
                $article = new Article;

                $content = '<p>'   .join($faker->paragraphs(5), '<p></p>') .  '<p>';
                // On appelle les setteurs de l'objet $article
                $article->setTitle($faker->sentence()) // titre aléatoire
                ->setContent($content) //paragraphes aléatoires
                ->setImage($faker->imageUrl()) // Génère des URL d'images 
                ->setCreatedAt($faker->dateTimeBetween('-6 months')) // Création de date de commentaire d'il y a 6 mois à aujour'dhui
                ->setCategory($category);

                $manager->persist($article);

                // Création entre 4 et 10 commentaires par article
                for($k =1; $k <= mt_rand(4,10); $k++)

                {   // On instancie l'entité comment afin d'insérer des commentaires dans la BDD
                    $comment = new Comment;

                    $content = '<p>'   .join($faker->paragraphs(2), '<p></p>') .  '<p>';
                      
                    $now = new \DateTime;
                    $interval = $now->diff($article->getCreatedAt()); //Représente le temps en timestamps entre la date de création de l'article et maintenant
                    $days = $interval->days; //nombre de jour entre la date de création de l'article et maintenant
                    $minimum = '-' .$days . ' days';

                    $comment->setAuthor($faker->name)
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween($minimum))
                            ->setArticle($article); // On relie nos commentaires aux articles crées ci-dessus ( clé étrangère)

                    $manager->persist($comment); // On prépare l'insertion des commentaires



                }


            }


        }
        $manager->flush(); // On libère et execute les requetes d'insertion
    }
}
