<?php

namespace App\Controller;

use App\Entity\Main\SuperUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $encoder;
    private $entityManager;

    public function __construct(UserPasswordHasherInterface $encoder, EntityManagerInterface $entityManagerInterface)
    {
        $this->encoder = $encoder;
        $this->entityManager = $entityManagerInterface;
    }

    #[Route('/api/create/user', name: 'app_user_create', methods : ['POST'])]
    public function index(Request $request): Response
    {
        // Get the raw JSON content from the request
        $jsonData = $request->getContent();

        // Decode the JSON content into an associative array
        $data = json_decode($jsonData, true);

        $user = new SuperUser();
        
        $user->setEmail($data['email']);
        $hash = $this->encoder->hashPassword($user, $data['password']);
        $user->setPassword($hash);
        $user->setRoles($data['roles']);
      
        $date = new \DateTime($data['datecreation']);
      
        $user->setEstActif($data['estActif']);
      
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['status' => 200]);
    }

    #[Route('/api/edit/user/{id}', name: 'app_edit', methods : ["PUT"])]
    public function edit(Request $request, $id): Response
    {
        // Get the raw JSON content from the request
        $jsonData = $request->getContent();

        // Decode the JSON content into an associative array
        $data = json_decode($jsonData, true);

        $user = $this->entityManager->getRepository(SuperUser:: class)->find($id);

        $user->setEmail($data['email']);
        $hash = $this->encoder->hashPassword($user, $data['password']);
        $user->setPassword($hash);
        $user->setRoles($data['roles']);
      
        $user->setEstActif($data['estActif']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['status' => 200]);
    }
}