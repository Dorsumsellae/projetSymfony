<?php
    namespace App\Controller;

use App\Entity\Pays;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


    class SaveDatabaseController extends AbstractController
    {
        /**
         * @Route(
         *        "/save_database/",
         *        name="save_database"
         * )
         */

        public function saveData(ManagerRegistry $doctrine){

            $alert = '';
            $transfert = [];
            $str = '';

            try {
                $doc_db = $doctrine->getRepository(Pays::class);

                $transfert = $doc_db->findAll(Pays::class);

                foreach ($transfert as $value) {
                    $str .= sprintf("%s,%s,%s\n", $value->getId(), $value->getNom(), $value->getCode());
                }

            } catch (\Exception $e) {
            $alert = $e->getMessage();
            }
            return new Response($str, 200, ['content-type'=> 'text/csv',
                                            'content-disposition' => 'filename="pays.csv"']);
        }
    }