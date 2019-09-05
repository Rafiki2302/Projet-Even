<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\AnnulSortieType;
use App\Form\RechercheType;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use App\Service\UtilService;
use DateInterval;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Router;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route("/sortie")
 *
 * @IsGranted("ROLE_USER")
 */
class SortieController extends Controller
{
    /**
     * @Route("/", name="sortie_index", methods={"GET","POST"})
     */
    public function index(SortieRepository $sortieRepository, Request $request,EntityManagerInterface $entityManager): Response
    {
        $sorties = [];
        $sorties2 = [];
        $sorties3 = [];
        $user = $this->getUser();

        $formRecherche = $this->createForm(RechercheType::class);
        $formRecherche->handleRequest($request);

        /* données des filtres du formulaire de recherche à passer en paramètres*/
        $p1 = $formRecherche->get('site')->getData();
        $p2 = $formRecherche->get('date1')->getData();
        $p3 = $formRecherche->get('date2')->getData();
        $p4 = $formRecherche->get('nom')->getData();
        $orga = $formRecherche->get('sortiesOrga')->getData();
        $insc = $formRecherche->get('sortiesInsc')->getData();
        $pasInsc =$formRecherche->get('sortiesPasInsc')->getData();
        $pass =  $formRecherche->get('sortiesPass')->getData();

        /*filtres cumulatifs : l'array $sorties est alimentée au fur et à mesure des filtres
            les filtres sélectifs sont traités dans le repository*/
        if ($formRecherche->isSubmitted() && $formRecherche->isValid()) {
           if(!$orga && !$insc && !$pasInsc && !$pass){
               $sorties = $sortieRepository->findBySeveralFields($p1, $user, $p4, $p2, $p3);
           }

            if ($orga)  {
                $sorties2 = $sortieRepository->findOrga($p1,$p2,$p3, $user,$p4);
                $sorties3=$sorties;
                $sorties = array_merge ($sorties2, $sorties3);
			}
            if ($insc){
                $sorties3 = $sortieRepository->findInsc($p1,$p2,$p3, $user,$p4);
                $sorties = array_merge ($sorties2, $sorties3);

			}
            if ($pasInsc){
                $sortiesI = [];
                $sortiesO = [];
                $sortiesS = [];
                $sortiesS = $sortieRepository->findBySeveralFields($p1,$user,$p4,$p2,$p3);

                $sortiesO = $sortieRepository->findInsc($p1,$p2,$p3, $user,$p4);
                $sortiesI = $sortieRepository->findOrga($p1,$p2,$p3, $user,$p4);

                $sorties2 = array_udiff($sortiesS, $sortiesO,  function ($obj_a, $obj_b) {
                    return $obj_a->getId() - $obj_b->getId();});
                $sortiesS = array_udiff($sorties2, $sortiesI,  function ($obj_a, $obj_b) {
                    return $obj_a->getId() - $obj_b->getId();});
                $sorties3 = $sorties;
                $sorties = array_merge ($sortiesS, $sorties3);
			}
            if ($pass){
                $sorties2 = $sortieRepository->findPass($p1,$p2,$p3, $user,$p4);
                $sorties3 = $sorties;
                $sorties = array_merge ($sorties2, $sorties3);
			}

            return $this->render("sortie/index.html.twig",
                ["form" => $formRecherche->createView(), "sorties" => $sorties]);
        }
            $sorties = $sortieRepository->findOrder();

           /* mise à jour des états des sorties au premier affichage */

            foreach ($sorties as $sortie){

                date_timezone_set($sortie->getDatecloture(), timezone_open('Europe/Paris'));
                date_timezone_set($sortie->getDatedebut(), timezone_open('Europe/Paris'));
                $now = new \Datetime('now', new \DateTimeZone('Europe/Paris'));
                $dateFin = $sortie->getDatedebut()->add( new DateInterval('PT'.$sortie->getDuree().'S'));

                if($sortie->getDatecloture()<= new \DateTime('now')) {
                    $etat = $entityManager->getRepository('App:Etat')->findBy(['libelle' => 'Clôturée'])[0];
                    $sortie->setEtat($etat);
                }

                if ($sortie->getDatedebut() <= $now
                    && $now <= $dateFin) {

                    $etat = $entityManager->getRepository('App:Etat')->findBy(['libelle' => 'Activité en cours'])[0];
                    $sortie->setEtat($etat);
                }
                if($sortie->getDatedebut()->add( new DateInterval('PT'.$sortie->getDuree().'S'))<= $now) {
                    $etat = $entityManager->getRepository('App:Etat')->findBy(['libelle' => 'Passée'])[0];
                    $sortie->setEtat($etat);
                }
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($sortie);
                    $em->flush();
            }

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
                    $etat = $entityManager->getRepository('App:Etat')->findBy(["libelle"=>"Créée"])[0];
                    $sortie->setEtat($etat);
                    $this->addFlash('info',"La sortie a été ajoutée à vos brouillons !");

                }
                else if($form->get('publi')->isClicked()){
                    //On va chercher l'état "Ouverte"
                    $etat = $entityManager->getRepository('App:Etat')->findBy(["libelle"=>"Ouverte"])[0];
                    $sortie->setEtat($etat);
                    $this->addFlash('info',"La sortie a bien été publiée !");
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
    public function show(Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        $dateActuelle = new \DateTime('now');

        //n'affiche pas la sortie s'il s'agit d'un brouillon d'un autre utilisateur
        if($this->getUser() !== $sortie->getOrganisateur()
            && $sortie->getEtat() === $entityManager->getRepository("App:Etat")->findBy(["libelle"=>"Créée"])[0]){

            return $this->redirecToAccueil();
        }
        //n'affiche pas la page si la date de la sortie est passée de plus de 30 jours
        elseif($sortie->getDatedebut() < ($dateActuelle->add(new \DateInterval('P30D')))){

            return $this->redirecToAccueil();
        }
        else{

            return $this->render('sortie/show.html.twig', [
                'sortie' => $sortie,
            ]);
        }
    }

    /**
     * @Route("/{id}/edit", name="sortie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        //Si la personne voulant modifier n'est pas l'organisateur ou si la sortie n'est pas en
        //brouillon, on renvoie l'internaute sur la page d'accueil
        if($this->getUser() !== $sortie->getOrganisateur()
            || $sortie->getEtat() !== $entityManager->getRepository("App:Etat")->findBy(["libelle"=>"Créée"])[0]){
            return $this->redirecToAccueil();
        }
        else {
            $form = $this->createForm(SortieType::class, $sortie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                //on définit l'état de la sortie suivant le bouton cliqué (enregistrer ou publier)
                if ($form->get('enreg')->isClicked()) {
                    //On va chercher l'état "créée" et on l'insert dans la sortie
                    $etat = $entityManager->getRepository('App:Etat')->findBy(["libelle"=>"Créée"])[0];
                    $sortie->setEtat($etat);
                    $this->addFlash('info', "La sortie a été ajoutée à vos brouillons !");

                } else if ($form->get('publi')->isClicked()) {
                    //On va chercher l'état "Ouverte"
                    $etat = $entityManager->getRepository('App:Etat')->findBy(["libelle"=>"Ouverte"])[0];
                    $sortie->setEtat($etat);
                    $this->addFlash('info', "La sortie a bien été publiée !");
                }

                $this->getDoctrine()->getManager()->flush();


                return $this->redirectToRoute('sortie_index');
            }

            return $this->render('sortie/edit.html.twig', [
                'sortie' => $sortie,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/{id}", name="sortie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() !== $sortie->getOrganisateur()
        || $sortie->getEtat() !== $entityManager->getRepository("App:Etat")->findBy(["libelle"=>"Créée"])[0]){
            return $this->redirecToAccueil();
        }
        else{
            if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($sortie);
                $entityManager->flush();

                $this->addFlash("info","Sortie supprimée de vos brouillons !");


            }

            return $this->redirectToRoute('sortie_index');
        }


    }

    /**
     * Inscrit l'utilisateur à une sortie s'il ne l'est pas encore, le désinscrit s'il est déjà inscrit
     * @param Sortie $sortie
     *
     * @Route("/{id}/inscription", name="sortie_insc")
     */
    public function inscription(Sortie $sortie, EntityManagerInterface $entityManager, Request $request, UtilService $utilService){

        if($this->getUser() === $sortie->getOrganisateur()
            || $sortie->getEtat() !== $entityManager->getRepository("App:Etat")->findBy(["libelle"=>"Ouverte"])[0]){
            return $this->redirecToAccueil();
        }
        else{


            //si le user en session fait déjà partie des participants de la sortie, on l'enlève. Sinon on l'ajoute
            if($sortie->getParticipants()->contains($this->getUser())){
                $sortie->getParticipants()->removeElement($this->getUser());
                $this->addFlash('erreur',"Votre désistement a bien été prise en compte !");

            }
            else{
                $sortie->getParticipants()->add($this->getUser());
                $this->addFlash('info',"Votre inscription a bien été prise en compte !");
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

            $router = $this->get('router');
            $route = $utilService->getPreviousRoute($request,$router);
            if($route !== null){
                return $this->redirectToRoute($route,['id'=>$sortie->getId()]);
            }
            else{
                return $this->redirectToRoute("sortie_index");
            }
        }
    }

    /**
     * @param Sortie $sortie
     *
     * @Route("/{id}/annuler", name="sortie_annul")
     */
    function annulerSortie(Sortie $sortie, EntityManagerInterface $entityManager, Request $request){

        if($this->getUser() !== $sortie->getOrganisateur()
            || $sortie->getEtat() !== $entityManager->getRepository("App:Etat")->findBy(["libelle"=>"Ouverte"])[0]){
            return $this->redirecToAccueil();
        }
        else{
            $form = $this->createForm(AnnulSortieType::class);
            $form->handleRequest($request);

            if($form->isValid() && $form->isSubmitted()){

                if($form->get('motif')->getData() === null){
                    $this->addFlash('erreur','Le motif doit être rempli');
                    return $this->redirectToRoute("sortie_annul",[
                        "id"=>$sortie->getId(),
                    ]);
                }
                elseif(strlen($form->get('motif')->getData()) > 500 ){
                    $this->addFlash('erreur','Le nombre de caractères ne doit pas dépasser 500');
                    return $this->redirectToRoute("sortie_annul",[
                        "id"=>$sortie->getId(),
                    ]);
                }
                $sortie->setEtat($entityManager->getRepository("App:Etat")->findBy(["libelle"=>"Annulée"])[0]);
                $sortie->setMotifAnnul($form->get('motif')->getData());
                $entityManager->flush();
                $this->addFlash('info','Sortie annulée');

                return $this->redirectToRoute("sortie_index");
            }

            return $this->render("sortie/annuler.html.twig",["form"=>$form->createView(),"sortie"=>$sortie]);
        }
    }


    /**
     *
     * @Route("/{id}/publier", name="sortie_publie")
     */
    /*
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
    */

    function redirecToAccueil(){
        $this->addFlash("erreur","Vous n'avez pas le droit d'accéder à cette page");
        return $this->redirectToRoute("sortie_index");
    }
}


