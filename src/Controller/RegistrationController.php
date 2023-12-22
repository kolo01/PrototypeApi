<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


#[Route('/api', name: 'api_')]
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'api_register')]
    public function register(Request $request,UserRepository $userRep, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $email = $request->request->get("email");
      
        $checker = $userRep->findOneBy(["email"=> $email]);
           if ($checker) {
            return $this->json(["Utilisateur déjà enregistré"]);
           }      
        if (!$checker) {
            $user->setEmail($request->request->get("email"));
            $user->setFirstName($request->request->get("fname"));
            $user->setLastName($request->request->get("lname"));
          
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $request->request->get("plainPassword")
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            return $this->json(['enregistrer avec succés ' . $user->getEmail()]);

            
        }
          
       
    }
}
