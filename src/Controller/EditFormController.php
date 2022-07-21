<?php
    namespace App\Controller;

    use App\Entity\Pays;
    use Doctrine\Persistence\ManagerRegistry;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Routing\Annotation\Route;

    class EditFormController extends AbstractController {

        /**
        * @Route(
        *        "/edit_pays/{id}",
        *        name="edit_pays",
        *        defaults={"id": "1"}
        * )
        */
        
        public function displayInfo(ManagerRegistry $doctrine, $id) {
            $alert ='';
            $result = [];

        //Verifying if the id exists
        if (!($id >=1 && $id <= 237)) {
            
            //If the id does not exist, the edit page will show the first country in the list (here, France),
            //and return an error message
            $id = 1;
            $alert = "Erreur: l'id est innexistant, affichage de la valeur par défaut";
        }
            try {

                $doc_db = $doctrine->getRepository(Pays::class);

                $result = $doc_db->find($id);

            } catch (\Exception $e) {
            $alert = $e->getMessage();
        } return $this->render('edit/edit.html.twig', ['result' => $result, 'errors' => $alert]);
    }
}