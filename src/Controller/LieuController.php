<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use App\Service\LieuService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lieu")
 */
class LieuController extends Controller
{
    /**
     * @Route("/", name="lieu_index", methods={"GET"})
     */
    public function index(LieuRepository $lieuRepository): Response
    {
        return $this->render('lieu/index.html.twig', [
            'lieus' => $lieuRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="lieu_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, LieuService $lieuService): Response
    {
        $lieu = new Lieu();

        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);


        /*
         * Ne pas effacer, tentative de simplifier en passant par le form symfony
        if ($request->isMethod('POST')){
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted()){
                $entityManager->persist($lieu);
                $entityManager->flush();

                $infosLieu = ["idLieu"=>$lieu->getId(),"nomLieu"=>$lieu->getNom()];

                return new JsonResponse(["data" => json_encode($lieu)]);
            }
            else{
                return new JsonResponse(["data"=>json_encode("coucou")]);
            }
        }
        */

        //la méthode post est réservée à la requête ajax permettant d'ajouter un lieu dans le form sortie
        if($request->isMethod('POST')){
            //On récupère un objet lieu construit en JS
            $lieuJS = $request->get('lieu');
            //On reconstruit une entité lieu dans symfony
            $lieu->setNom($lieuJS['nom']);
            $lieu->setRue($lieuJS['rue']);
            if ($lieuJS['latitude'] !== ''){
                $lieu->setLatitude(intval($lieuJS['latitude']));
            }
            if($lieuJS['longitude'] !== ''){
                $lieu->setLongitude(intval($lieuJS['longitude']));
            }
            $lieu->setVille($entityManager->getRepository("App:Ville")->find($lieuJS['idVille']));

            //On checke si les données sont ok, si ko envoi d'un message d'erreur en ajax, sinon on insère le lieu en bdd
            $message = $lieuService->validerLieu($lieu);
            $tabInfos = ["erreur"=>$message];
            if($message !== ""){
                return new JsonResponse(["data" => json_encode($tabInfos)]);
            }
            else{
                $entityManager->persist($lieu);
                $entityManager->flush();
                $infosLieu = ["idLieu"=>$lieu->getId(),"nomLieu"=>$lieu->getNom()];

                return new JsonResponse(["data" => json_encode($infosLieu)]);
            }


        }
        //comportement si appel de la page en get
        else{
            return $this->render('lieu/new.html.twig', [
                'sortie' => $lieu,
                'form' => $form->createView(),
            ]);
        }



        /*
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lieu);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_new');
        }

        return $this->render('lieu/new.html.twig', [
            'lieu' => $lieu,
            'form' => $form->createView(),
        ]);
        */
    }

    /**
     * @Route("/{id}", name="lieu_show", methods={"GET"})
     */
    public function show(Lieu $lieu): Response
    {
        return $this->render('lieu/show.html.twig', [
            'lieu' => $lieu,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="lieu_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Lieu $lieu): Response
    {
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lieu_index');
        }

        return $this->render('lieu/edit.html.twig', [
            'lieu' => $lieu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lieu_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Lieu $lieu): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lieu->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($lieu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('lieu_index');
    }

    /**
     * @Route("/infos", name="lieu_infos")
     *
     * Méthode permettant d'envoyer les infos principales d'un lieu à partir de son id,
     * via une requête ajax
     */
    public function infoLieu(Request $request, EntityManagerInterface $entityManager){

        $idLieu = $request->get('id');

        $lieu = $entityManager->getRepository("App:Lieu")->find($idLieu);
        $tablieu = [
            'rue' => $lieu->getRue(),
            'ville'=>$lieu->getVille()->getNom(),
            'codeP'=>$lieu->getVille()->getCodePostal(),
            'lat'=>$lieu->getLatitude(),
            'lon'=>$lieu->getLongitude(),
        ];
        return new JsonResponse(["data" => json_encode($tablieu)]);
    }
}
