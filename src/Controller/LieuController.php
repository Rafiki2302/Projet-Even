<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
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
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();
        /*
        $test = ["a"=>1,"b"=>"test"];
        dump($test);
        dump(json_encode($test));
        exit();
        */
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        $lieuJS = $request->get('lieu');

        $lieu->setNom($lieuJS['nom']);
        $lieu->setRue($lieuJS['rue']);
        if ($lieuJS['latitude'] !== ''){
            $lieu->setLatitude(intval($lieuJS['latitude']));
        }
        if($lieuJS['longitude'] !== ''){
            $lieu->setLongitude(intval($lieuJS['longitude']));
        }
        $lieu->setVille($entityManager->getRepository("App:Ville")->find($lieuJS['idVille']));

        $entityManager->persist($lieu);
        $entityManager->flush();

        $infosLieu = ["idLieu"=>$lieu->getId(),"nomLieu"=>$lieu->getNom()];

        return new JsonResponse(["data" => json_encode($infosLieu)]);

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
