<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PersonneController extends AbstractController
{
    /**
     * @Route("/personne", name="personne_index")
     */
    public function index(PersonneRepository $personneRepo): Response
    {

        $personnes = $personneRepo->findAll();
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
        ]);
    }

    /**
     * @Route("/personne/show/{id}", name="personne_show")
     */
    public function show(PersonneRepository $personneRepo,$id): Response
    {
        $personne= $personneRepo->find($id);

        $personnes = $personneRepo->findAll();
        return $this->render('personne/show.html.twig', [
            'personnes' => $personnes,
        ]);
    }


    /**
     * @Route("/personne/creation/new", name="personne_create")
     * @Route("/personne/edit/{id}", name="personne_edit")
     */
    public function ajourEtModif(Personne $personne = null, Request $request, EntityManagerInterface $em)
    {

        if (!$personne) {
            $personne = new Personne();
        }

        $form = $this->createForm(PersonneType::class, $personne);
        $form->handlerequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            // ici on calcule l'age
            $naissance = $personne->getNaissance();
            $now = new \DateTime(); // date actuelle
        
            $age = $now->diff($naissance, true)->y;
            $personne->setAge($age);

            if($age < 150 )
            {
                // on enregistre le vol dans la base de données 
                $em->persist($personne);
                $em->flush();
            }
     
        
      

            return $this->redirectToRoute('personne_index');
        }

        return $this->render('personne/ajoutEtModif.html.twig', [

            'form' => $form->createView(),
            'editMode' => $personne->getId() !== null
        ]);
    }



}
