<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
  /**
   * @Route("/inscription", name="security_registration")
   */

public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
// Pour insérer dans la table SQL User, nous devons instancier un objet issu de l'entity User
// L'entity User reflète la table SQL User

{
    $user = new User;

    //On appelle la classe RegistrationType afin de créer le formulaire d'inscription
    $form =$this->createForm(RegistrationType::class, $user);

    dump($request);

    // handleRequest récupère toutes les données saisies dans le formulaire et les envoit directment dans les setteurs de l'objet $user
    $form->handleRequest($request);

    //Si le formulair a bien été validé et que les setteurs sont bien remplis alors on entre dans le IF
    if($form->isSubmitted() && $form->isValid())
    {   
        // On transmet à la méthode encodePassord() de l'interface UserPasswordEncoderInterface le mot de passe du formulaire à encoder
        // $hash contient le mot de passe encodé
        $hash = $encoder->encodePassword($user, $user->getPassword());// Il y a deux paramètres, le 1er qui correspond 
        // On transmet le MDP encodé au setteur de l'objet user
        $user->setPassword($hash);

        $manager->persist($user); //On prépare l'insertion
        $manager->flush(); //On exécute la requete d'insertion

        $this->addFlash('success', 'Felicitations!! Vous êtes maintenant inscrit, vous pouvez maintenant vous connecter.');

        // On redirige après inscription vers la page de connexion
        return $this->redirectToRoute('security_login');
    }

    return $this->render('security/registration.html.twig', [
        'form' => $form->createView()
    ]);
}

/**
 * @Route("/connexion", name="security_login")
 * 
 */

public function login()

{
    return $this->render('security/login.html.twig');
}

/**
 * @Route("/deconnexion", name="security_logout")
 */

 public function logout()

 {
     //Cette méthode ne retourne rien, il nous suffit d'avoir une route pour la déconnexion
 }



}
