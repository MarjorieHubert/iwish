<?php

namespace App\Controller;

use App\Entity\Wishes;
use App\Form\WishesFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class WishesController extends AbstractController
{
    //LISTING (READ)
    #[Route('/wishes', name: 'app_wishes')]
    public function index(ManagerRegistry $doctrine): Response
    {
        //Requête pour aller chercher tout ce qu'il y a dans la base de données
        $wishes = $doctrine->getRepository(Wishes::class)->findBy(['user' => $this->getUser()],[]);

        return $this->render('wishes/index.html.twig', [
            'wishes' => $wishes
        ]);
    }

    //CREATE
    #[Route('wishes/add', name: 'app_add')]
    public function add(Request $request,ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        //On crée un nouveau souhait
        $createWish = new Wishes();

        //On crée le formulaire
        $wishesForm = $this->createForm(WishesFormType::class, $createWish);

        //On traite la requête du formulaire
        $wishesForm->handleRequest($request);

        //On vérifie si le formulaire est soumis ET valide
        if($wishesForm->isSubmitted() && $wishesForm->isValid()) {
            //On récupère toutes les données du formulaire
            $wish = $wishesForm->getData();
            //et les images
            $images = $wishesForm->get('image')->getData();
            //On  récupère l'utilisateur qui crée le souhait
            $wish->setUser($this->getUser());

            if ($images) {
                $originalFilename = pathinfo($images->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$images->guessExtension();

                // Move the file to the directory where avatars are stored
                try {
                    $images->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $createWish->setImage($newFilename);
            }

            //On envoie en BDD
            $entityManager = $doctrine->getManager();
            $entityManager->persist($wish);
			$entityManager->flush();

            return $this->redirectToRoute('app_wishes');
        }


        return $this->render('wishes/add.html.twig', [
            'wishesForm' => $wishesForm->createView()
        ]);
    }

    //UPDATE
    #[Route('wishes/edit/{id}', name: 'app_edit')]
    public function edit(Wishes $createWish, Request $request,ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        //On crée le formulaire
        $wishesForm = $this->createForm(WishesFormType::class, $createWish);

        //On traite la requête du formulaire
        $wishesForm->handleRequest($request);

        //On vérifie si le formulaire est soumis ET valide
        if($wishesForm->isSubmitted() && $wishesForm->isValid()) {
            //On récupère toutes les données du formulaire
            $wish = $wishesForm->getData();
            $images = $wishesForm->get('image')->getData();
            //On  récupère l'utilisateur qui crée le souhait
            $wish->setUser($this->getUser());

            if ($images) {
                $originalFilename = pathinfo($images->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$images->guessExtension();

                // Move the file to the directory where avatars are stored
                try {
                    $images->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $createWish->setImage($newFilename);
            }

            //On envoie en BDD
            $entityManager = $doctrine->getManager();
            $entityManager->persist($wish);
			$entityManager->flush();

            return $this->redirectToRoute('app_wishes');
        }

        return $this->render('wishes/edit.html.twig', [
            'wishesForm' => $wishesForm->createView()
        ]);  
    }

    //DELETE
    #[Route('wishes/delete/{id}', name: 'app_delete')]
    public function delete(Wishes $createWish, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($createWish);
        $entityManager->flush();

        return $this->redirectToRoute('app_wishes'); 
    }
}
