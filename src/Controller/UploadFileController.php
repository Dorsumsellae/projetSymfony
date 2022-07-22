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
use App\Entity\Flag;

class UploadFileController extends AbstractController
{
    #[Route('/admin/upload_file', name: 'app_upload_file')]
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
                    $alert .= "Fichier non uploadé";
                }
                $fileCsv = $form->get('fichierCsv')->getData();
                try {
                    $doc_db = $doctrine->getManager();
                    $handle = fopen($fileCsv, "r");
                    $paysRep = $doctrine->getRepository(Pays::class);
                    if ($paysRep->findAll()) throw new \Exception("La table est déjà remplie !");

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
                $alert .= "Formulaire Invalide";
            }
        } catch (FileException $e) {
            $err = 'Erreur de chargement du fichier : ' . $e->getMessage();
        }
        try {
            $doc_db = $doctrine->getManager();
            $paysRep = $doctrine->getRepository(Pays::class);
            $flagFiles = glob($this->getParameter('upload_directory') . '/flags/*.{svg,png,jpeg,jpg}', GLOB_BRACE);
            foreach ($flagFiles as $flagFile) {
                $fileName = basename($flagFile);
                $dataFlag = explode('.', $fileName);
                $codeFlag = $dataFlag[0];
                $countryFlag = $paysRep->findOneBy(["code" => $codeFlag]);
                if ($countryFlag) {
                    $flag = new Flag();
                    $flag->setPays($countryFlag);
                    $flag->setFileType($dataFlag[1]);
                    $flag->setPath($this->getParameter('upload_directory') . "/flags/" . $fileName);
                    $doc_db->persist($flag);
                } else {
                    $alert .= $codeFlag . " : pays non trouvé --";
                }
            }
            $doc_db->flush();
        } catch (\PDOException $e) {
            $alert .= $e->getTraceAsString();
        } catch (\Exception $e) {
            $alert .= $e->getMessage() . "l : " . $e->getLine();
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
