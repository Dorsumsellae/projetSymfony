<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Form\EditFormPaysType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EditFormController extends AbstractController
{

    /**
     * @Route(
     *        "/edit_pays/{id}",
     *        name="edit_pays",
     *        defaults={"id": "1"}
     * )
     */

    public function displayInfo(ManagerRegistry $doctrine, $id, Request $request)
    {
        $alert = '';
        $result = [];
        $info = '';

        //Verifying if the id exists
        if (!($id >= 1 && $id <= 237)) {

            //If the id does not exist, the edit page will show the first country in the list (here, France),
            //and return an error message
            $id = 1;
            $alert = "Erreur: l'id est innexistant, affichage de la valeur par défaut";
        }
        try {

            $paysRep = $doctrine->getRepository(Pays::class);

            $result = $paysRep->find($id);
        } catch (\Exception $e) {
            $alert = $e->getMessage();
        }

            $doc_man = $doctrine->getManager();

            $form = $this->createForm(EditFormPaysType::class, $result);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $doc_man->persist($result);
                $doc_man->flush();
                $info = 'Pays mis à jour';
            }

        return $this->renderForm('edit/edit.html.twig', ['form' => $form, 'errors' => $alert, 'info' => $info, "result"=>$result, "formSubmitted"=>$form->isSubmitted()]);
    }
}
