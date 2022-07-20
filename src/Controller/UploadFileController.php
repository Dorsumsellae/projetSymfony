<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\UploadFileType;
use App\Entity\Pays;
use App\Entity\File;

class UploadFileController extends AbstractController
{
    #[Route('/upload_file', name: 'app_upload_file')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $err = "";
        $alert = "";
        $status = "";

        try {
            $form = $this->createForm(UploadFileType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $fileZip = $form->get('fichierZip')->getData();
                if ($fileZip) {
                    $status = File::unzip($fileZip, $this->getParameter('upload_directory'));
                } else {
                    $alert = "Fichier non uploadé";
                }
                $fileCsv = $form->get('fichierCsv')->getData();
                try {
                    $doc_db = $doctrine->getManager();
                    $handle = fopen($fileCsv, "r");
                    $pays_get = $doctrine->getRepository(Pays::class);
                    if ($pays_get->findAll()) throw new \Exception("La table est déjà remplie !");

                    while (($tab = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        //création du pays
                        if (preg_match("/^[a-zA-Z]{2}$/", $tab[2])) {
                            $pays = new Pays();
                            $pays->setCode($tab[2]);
                            $pays->setName($tab[1]);
                            $doc_db->persist($pays);
                        }
                    }
                    $doc_db->flush();
                } catch (\PDOException $e) {
                    $alert = $e->getTraceAsString();
                } catch (\Exception $e) {
                    $alert = $e->getMessage();
                }
            } else {
                $alert = "Formulaire Invalide";
            }
        } catch (FileException $e) {
            $err = 'Erreur de chargement du fichier : ' . $e->getMessage();
        }

        return $this->renderForm('upload_file/index.html.twig', [
            'form' => $form,
            'alert' => $alert,
            'err' => $err,
            'status' => $status,
            'submittedForm' => $form->isSubmitted(),
        ]);
    }
}
