<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\AnnulSortieType;
use App\Form\RechercheType;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Router;

/**
 * @Route("/sortie")
 */
class SortieController extends Controller
{
    /**
     * @Route("/", name="sortie_index", methods={"GET","POST"})
     */
    public function index(SortieRepository $sortieRepository, Request $request): Response
    {
        $sorties = [];
        $sorties2 = [];
        $sorties3 = [];
        $user = $this->getUser();


        $formRecherche = $this->createForm(RechercheType::class);
        $formRecherche->handleRequest($request);

        if ($formRecherche->isSubmitted() && $formRecherche->isValid()) {
           if(!$formRecherche->get('sortiesOrga')->getData()
           && !$formRecherche->get('sortiesInsc')->getData()
           && !$formRecherche->get('sortiesPasInsc')->getData()
           && !$formRecherche->get('sortiesPass')->getData()){
               $sorties = $sortieRepository->findBySeveralFields(
                   $formRecherche->get('site')->getData(),
                   $user,
                   $formRecherche->get('nom')->getData(),
                   $formRecherche->get('date1')->getData(),
                   $formRecherche->get('date2')->getData()
               );
           }

            if ($formRecherche->get('sortiesOrga')->getData())  {
                $sorties2 = $sortieRepository->findOrga(
                    $formRecherche->get('site')->getData(),
                    $formRecherche->get('date1')->getData(),
                    $formRecherche->get('date2')->getData(),
                    $user,
                    $formRecherche->get('nom')->getData());
                $sorties3=$sorties;
                $sorties = array_merge ($sorties2, $sorties3);
			}
            if ($formRecherche->get('sortiesInsc')->getData()){
                $sorties3 = $sortieRepository->findInsc(
                    $formRecherche->get('site')->getData(),
                    $formRecherche->get('date1')->getData(),
                    $formRecherche->get('date2')->getData(),
                    $user,
                    $formRecherche->get('nom')->getData());
                $sorties = array_merge ($sorties2, $sorties3);

			}
            if ($formRecherche->get('sortiesPasInsc')->getData()){
                $sortiesI = [];
                $sortiesO = [];
                $sortiesS = [];
                $sortiesS = $sortieRepository->findBySeveralFields(
                    $formRecherche->get('site')->getData(),
                    $user,
                    $formRecherche->get('nom')->getData(),
                    $formRecherche->get('date1')->getData(),
                    $formRecherche->get('date2')->getData());

                $sortiesO = $sortieRepository->findInsc(
                    $formRecherche->get('site')->getData(),
                    $formRecherche->get('date1')->getData(),
                    $formRecherche->get('date2')->getData(),
                    $user,
                    $formRecherche->get('nom')->getData());
                $sortiesI = $sortieRepository->findOrga(
                    $formRecherche->get('site')->getData(),
                    $formRecherche->get('date1')->getData(),
                    $formRecherche->get('date2')->getData(),
                    $user,
                    $formRecherche->get('nom')->getData());

                $sorties2 = array_udiff($sortiesS, $sortiesO,  function ($obj_a, $obj_b) {
                    return $obj_a->getId() - $obj_b->getId();});
                $sortiesS = array_udiff($sorties2, $sortiesI,  function ($obj_a, $obj_b) {
                    return $obj_a->getId() - $obj_b->getId();});
                $sorties3 = $sorties;
                $sorties = array_merge ($sortiesS, $sorties3);
			}
            if ($formRecherche->get('sortiesPass')->getData()){
                $sorties2 = $sortieRepository->findPass(
                    $formRecherche->get('site')->getData(),
                    $formRecherche->get('date1')->getData(),
                    $formRecherche->get('date2')->getData(),
                    $user,
                    $formRecherche->get('nom')->getData());
                $sorties3 = $sorties;
                $sorties = array_merge ($sorties2, $sorties3);
			}


            return $this->render("sortie/index.html.twig",
                ["form" => $formRecherche->createView(), "sorties" => $sorties]);
        }
        $sorties = $sortieRepository->findOrder();

        return $this->render("sortie/index.html.twig",
            ["form" => $formRecherche->createView(), "sorties" => $sorties]);
    }

    /**
     * @Route("/new", name="sortie_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($this->getUser()) {

                //on set l'utililsateur en session en tant qu'organisateur
                $sortie->setOrganisateur($this->getUser());
                $participant = $this->getUser();
                $sortie->setSite($participant->getSite());


                //on définit l'état de la sortie suivant le bouton cliqué (enregistrer ou publier)
                if($form->get('enreg')->isClicked()){
                    //On va chercher l'état "créée" et on l'insert dans la sortie
                    $etat = $entityManager->getRepository('App:Etat')->find(1);
                    $sortie->setEtat($etat);
                }
                else if($form->get('publi')->isClicked()){
                    //On va chercher l'état "Ouverte"
                    $etat = $entityManager->getRepository('App:Etat')->find(2);
                    $sortie->setEtat($etat);
                }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($sortie);
                $entityManager->flush();
            }

            return $this->redirectToRoute('sortie_index');
        }
        return $this->render('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_show", methods={"GET"})
     */
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sortie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sortie $sortie): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sortie_index');
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Sortie $sortie): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sortie_index');
    }

    /**
     * @param Sortie $sortie
     *
     * @Route("/{id}/inscription", name="sortie_insc")
     */
    public function inscription(Sortie $sortie, EntityManagerInterface $entityManager, Request $request){

        //si le user en session fait déjà partie des participants de la sortie, on l'enlève. Sinon on l'ajoute
        if($sortie->getParticipants()->contains($this->getUser())){
            $sortie->getParticipants()->removeElement($this->getUser());

            $this->addFlash('info',"Votre inscription a bien été prise en compte !");
        }
        else{
            $sortie->getParticipants()->add($this->getUser());
            $this->addFlash('info',"Votre désistement a bien été prise en compte !");

        }

        /*
         * Ne pas supprimer : ébauche pour renvoi vers la page précédente du site
        dump($request->headers->get('Host'));
        dump($request->headers->get('referer'));
        $router = $this->get('router');
        $route = $router->match("/sortie/8/inscription");
        dump($route);
        dump($route["_route"]);
        exit();
        */

        $entityManager->flush();
        return $this->redirectToRoute('sortie_show',['id'=>$sortie->getId()]);
    }

    /**
     * @param Sortie $sortie
     *
     * @Route("/{id}/annuler", name="sortie_annul")
     */
    function annulerSortie(Sortie $sortie, EntityManagerInterface $entityManager, Request $request){

        $form = $this->createForm(AnnulSortieType::class);
        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()){
            $sortie->setEtat($entityManager->getRepository("App:Etat")->findBy(["libelle"=>"Annulée"])[0]);
            $sortie->setMotifAnnul($form->get('motif')->getData());
            $entityManager->flush();
            $this->addFlash('info','Sortie annulée');

            return $this->redirectToRoute("sortie_index");
        }

        return $this->render("sortie/annuler.html.twig",["form"=>$form->createView(),"sortie"=>$sortie]);
    }

    /**
     *
     * @Route("/{id}/publier", name="sortie_publie")
     */
    function publierSortie(Sortie $sortie, EntityManagerInterface $entityManager, Request $request){

        $form = $this->createForm(AnnulSortieType::class);
        $form->handleRequest($request);
        if($form->isValid() && $form->isSubmitted()){
            $sortie->setEtat($entityManager->getRepository("App:Etat")->findBy(["libelle"=>"Annulée"])[0]);
            $sortie->setMotifAnnul($form->get('motif')->getData());
            $entityManager->flush();
            $this->addFlash('info','Sortie annulée');

            return $this->redirectToRoute("sortie_index");
        }

        return $this->render("sortie/annuler.html.twig",["form"=>$form->createView(),"sortie"=>$sortie]);
    }
}


