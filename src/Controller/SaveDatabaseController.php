<?php

namespace App\Controller;

use App\Entity\Pays;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ZipArchive;


class SaveDatabaseController extends AbstractController
{
    /**
     * @Route(
     *        "admin/save_database/",
     *        name="save_database"
     * )
     */
    public function saveData(ManagerRegistry $doctrine): Response
    {

        $alert = '';
        $transfert = [];
        $str = '';

        try {
            $doc_db = $doctrine->getRepository(Pays::class);

            $transfert = $doc_db->findAll(Pays::class);

            foreach ($transfert as $value) {
                $str .= sprintf("%s,%s,%s\n", $value->getId(), $value->getName(), $value->getCode());
            }

        } catch (\Exception $e) {
            $alert = $e->getMessage();
        }
        return new Response($str, 200, ['content-type' => 'text/csv',
            'content-disposition' => 'filename="pays.csv"']);
    }

    /**
     * @Route(
     *        "admin/save_flags",
     *        name="save_flags"
     * )
     */
    public function saveFlags(): Response
    {
        $pathDir = $this->getParameter('upload_directory') . "/flags/";

        //The full path to where we want to save the zip file.
        $zipFilePath = $this->getParameter('upload_directory') . "flags.zip";

        $zip = new ZipArchive;

        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            $dir = opendir($pathDir);
            while ($file = readdir($dir)) {
                if (is_file($pathDir . $file)) {
                    $zip->addFile($pathDir . $file, $file);
                }
            }
            $zip->close();
        }

        $response = new Response();
        $response->setContent(file_get_contents($zipFilePath));
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="flags.zip"');
        $response->headers->set('Content-length', filesize($zipFilePath));

        @unlink($zipFilePath);
        return $response;
    }
}
