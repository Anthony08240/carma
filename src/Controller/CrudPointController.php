<?php

namespace App\Controller;

use App\Entity\Point;
use App\Form\Point1Type;
use App\Repository\PointRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/crud/point")
 */
class CrudPointController extends AbstractController
{
    /**
     * @Route("/", name="crud_point_index", methods={"GET"})
     */
    public function index(PointRepository $pointRepository): Response
    {
        return $this->render('crud_point/index.html.twig', [
            'points' => $pointRepository->findBy(['id_user' => $this->getUser()]),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="crud_point_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Point $point, EntityManagerInterface $entityManager, SluggerInterface $slugger, HttpClientInterface $httpClient): Response
    {
        $form = $this->createForm(Point1Type::class, $point);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();

            // api adresse datagouv

            $rue = $user->getAdresse();

            $cp = $user->getCodepostal();

            $ville = $user->getVille();

            $response = $httpClient->request('GET', 'https://api-adresse.data.gouv.fr/search/?q='. $rue . '+' . $cp . '+' . $ville, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'query' => [
                    'format' => 'json',
                    'inc' => 'geometry',
                    'limit' => '1'
                ]
            ]);

            $data = $response->toArray();

            $features = $data['features'];

            $filter = $features[0];

            $geometry = $filter['geometry'];

            $coordinates = $geometry['coordinates'];

            $latitude = $coordinates[1];

            $longitude = $coordinates[0];

            $arr = array(
                'latitude' => $latitude, 
                'longitude' => $longitude
            );

            $point->setPoint($arr);

            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('img')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('img_upload'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents

                $point->setImg($newFilename);
            }

            $entityManager->persist($point);
            $entityManager->flush();

            return $this->redirectToRoute('crud_point_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('crud_point/edit.html.twig', [
            'point' => $point,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="crud_point_delete", methods={"POST"})
     */
    public function delete(Request $request, Point $point, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $point->getId(), $request->request->get('_token'))) {
            $entityManager->remove($point);
            $entityManager->flush();
        }

        return $this->redirectToRoute('crud_point_index', [], Response::HTTP_SEE_OTHER);
    }
}
