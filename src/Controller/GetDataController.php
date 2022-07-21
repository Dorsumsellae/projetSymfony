<?php

namespace App\Controller;

use App\Entity\Pays;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetDataController extends AbstractController
{

    /**
     * @Route(
     *        "/read_table/{table}/{column}={value}",
     *        name="read_table",
     *        defaults={
     *                 "table": "",
     *                 "column": "",
     *                 "value": "",
     *        },
     *        requirements={
     *                      "table": "[0-9a-z_]+",
     *                      "column": "[0-9a-z_]+",
     *        }
     * )
     */

    public function readColumn(ManagerRegistry $doctrine, string $table, string $column, $value): Response
    {
        $alert = '';

        try {

            $table = $doctrine->getRepository(Pays::class);

            $results = $table->findAll();

            $fields = ['id', 'nom', 'code'];
        } catch (\Exception $e) {
            $alert = $e->getMessage();
        }
        return $this->render('Pays/displaytable.html.twig', ['results' => $results, 'errors' => $alert, 'table' => $table, 'fields' => $fields]);
    }
}
