<?php
    namespace App\Controller;

    use App\Entity\Pays;
    use Doctrine\Persistence\ManagerRegistry;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    class PaysController extends AbstractController
    {
        /**
         * @Route(
         *          "/fill_pays/{file}",
         *          name="fill_pays",
         *          requirements={"file": "[a-z0-9A-Z_.]+"},
         * )
         */
        public function fill(ManagerRegistry $doctrine, string $file): Response
        {
            $alert = '';
            try {
                $doc_db = $doctrine->getManager();
           
                $rfile = __DIR__."/../../assets/data/".$file;
                $handle = fopen($rfile, "r");
                $pays_get = $doctrine->getRepository(Pays::class);
                if ($pays_get->findAll()) throw new \Exception("La table est déjà remplie !");
                
                while (($tab = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    //création du pays
                    if (preg_match("/^[a-zA-Z]{2}$/",$tab[2])) {
                        $pays = new Pays();
                        $pays->setCode($tab[2]);
                        $pays->setNom($tab[1]);
                        $doc_db->persist($pays);
                    }
                }
                $doc_db->flush();
            } catch (\PDOException $e) {
                $alert = $e->getTraceAsString();
            } catch (\Exception $e) {
                $alert = $e->getMessage();
            }
            return $this->render('Pays/fill.html.twig', ['errors' => $alert]);
        }
    }
