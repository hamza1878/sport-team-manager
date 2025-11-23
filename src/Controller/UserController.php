<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User1Type;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
final class UserController extends AbstractController
{
#[Route('/user', name: 'user_index')]

public function index(UserRepository $UserRepository): Response{
return $this->render('user/index.html.twig',[
    'users'=>$UserRepository->findAll(),
]);

}}


?>