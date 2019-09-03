<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Service\ParticipantService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/participant")
 */
class ParticipantController extends Controller
{
    /**
     * @Route("/", name="participant_index", methods={"GET"})
     */
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('participant/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="participant_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder, ParticipantService $participantService): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        //si clic sur le bouton valider...
        if($form->isSubmitted()){
            //on checke que le pw respecte les contraintes
            //si ne respecte pas : ajoute message erreur à afficher dans la page du formulaire d'inscription

            if(!$participantService->validatePassword($form->get("motDePasseEnClair")->getData())){
                $this->addFlash('erreur',"Mot de passe incorrect : il doit contenir au moins 8 caractères dont une minuscule,
                     une majuscule, un chiffre et un caractère spécial");
            }
            //si le pw est correct, on vérifie que les autres éléments du form sont OK, si oui, on flush
            else{
                if ($form->isValid()){

                    try{
                        $participant->setMotDePasse($encoder->encodePassword($participant,$form->get("motDePasseEnClair")->getData()));

                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($participant);
                        $entityManager->flush();

                        return $this->redirectToRoute('sortie_index');
                    }
                    catch (UniqueConstraintViolationException $exception){
                        $this->addFlash("erreurUnique","Le pseudo ou l'email est déjà utilisé par un autre participant");
                    }



                }
            }
        }
        return $this->render('participant/new.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="participant_show", methods={"GET"})
     */
    public function show(Participant $participant): Response
    {
        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="participant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Participant $participant, ParticipantService $participantService, UserPasswordEncoderInterface $encoder): Response
    {
        if($this->getUser() !== $participant){
            $this->addFlash("erreur","Vous n'avez pas le droit d'accéder à cette page");
            return $this->redirectToRoute("sortie_index");
        }
        else {
            $form = $this->createForm(ParticipantType::class, $participant);
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                //on checke que le pw respecte les contraintes
                //si ne respecte pas : ajoute message erreur à afficher dans la page de la modif du profil
                if (!$participantService->validatePassword($form->get("motDePasseEnClair")->getData())) {
                    $this->addFlash('erreur', "Mot de passe incorrect : il doit contenir au moins 8 caractères dont une minuscule,
                     une majuscule, un chiffre et un caractère spécial");
                } //si le pw est correct, on vérifie que les autres éléments du form sont OK, si oui, on flush
                else {
                    if ($form->isValid()) {

                        try {
                            $participant->setMotDePasse($encoder->encodePassword($participant, $form->get("motDePasseEnClair")->getData()));

                            $entityManager = $this->getDoctrine()->getManager();
                            $entityManager->flush();

                            return $this->redirectToRoute('participant_index');
                        } //si le pw ou l'email est déjà présent en BDD, cela renverra cette exception
                        catch (UniqueConstraintViolationException $exception) {
                            $this->addFlash("erreurUnique", "Le pseudo ou l'email est déjà utilisé par un autre participant");
                        }

                    }
                }
            }

            return $this->render('participant/edit.html.twig', [
                'participant' => $participant,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/{id}", name="participant_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Participant $participant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($participant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('participant_index');
    }

}
