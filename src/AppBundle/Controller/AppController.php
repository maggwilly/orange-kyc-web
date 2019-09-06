<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Form\CredentialsType;
use AppBundle\Entity\AuthToken;
use AppBundle\Entity\Credentials;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use FOS\RestBundle\View\View; 
use AppBundle\Entity\PointVente;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Etape controller.
 *
 */
class AppController extends Controller
{
    /**
     * Lists all etape entities.
     *
     */
    public function indexAction()
    {   
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager();
        $region=$session->get('region');
        $startDate=$session->get('startDate','first day of this month');
        $endDate=$session->get('endDate', 'last day of this month');
        $produits=$em->getRepository('AppBundle:Produit')->countByProduit(null, $startDate,$endDate,$region);

        $performances=(new ArrayCollection($em->getRepository('AppBundle:Affectation')->findPerformances($startDate,$endDate,$region)))->map(function ($affectation) use ($em,$region,$startDate,$endDate){
                 $affectation['ventes']=$em->getRepository('AppBundle:Produit')->countByProduit($affectation['id'], $startDate,$endDate,$region);
                 if(empty($affectation['ventes']))   
                 $affectation['ventes']=$em->getRepository('AppBundle:Produit')->findOrderedList();

             return $affectation;
        });
        $workedSuperviseur=$em->getRepository('AppBundle:User')->workedSuperviseur($startDate,$endDate,$region);
        $colors=array("#FF6384","#36A2EB","#FFCE56","#F7464A","#FF5A5E","#46BFBD", "#5AD3D1","#FDB45C");
        $rapports=$em->getRepository('AppBundle:Commende')->rapports($startDate,$endDate,$region);
        return $this->render('AppBundle::index.html.twig', 
          array(
            'colors'=>$colors,
            'performances'=>$performances,
            'produits'=>$produits,
            'rapports'=>$rapports,
            'workedSuperviseur'=>$workedSuperviseur
          ));
    }
    
    /**
     * @Rest\View()
     */
    public function testJsonAction()
    {   
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager();
        $region=$session->get('region');
        $startDate=$session->get('startDate','first day of this month');
        $endDate=$session->get('endDate', 'last day of this month');
        $performances=(new ArrayCollection($em->getRepository('AppBundle:Affectation')->findPerformances($startDate,$endDate,$region)))->map(function ($affectation) use ($em,$region,$startDate,$endDate){
                 $affectation['ventes']=$em->getRepository('AppBundle:Produit')->countByProduit($affectation['id'], $startDate,$endDate,$region);
                 if(empty($affectation['ventes']))   
                 $affectation['ventes']=$em->getRepository('AppBundle:Produit')->findOrderedList();

             return $affectation;
        });
        return  $performances;
    }


    public function kpiAction()
    {   
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager();
        $region=$session->get('region');
        $startDate=$session->get('startDate','first day of this month');
        $endDate=$session->get('endDate', 'last day of this month');
        $countByProduit= $em->getRepository('AppBundle:Produit')->countByProduit(null,$startDate,$endDate,$region);
        return $this->render('AppBundle::part/kpi.html.twig', 
          array(
            'produits'=>$countByProduit,
          ));
    }

    public function setRegionAction(Request $request)
    {
        $region=$request->query->get('region');
         $session = $this->getRequest()->getSession();
        $session->set('region',$region);
       $referer = $this->getRequest()->headers->get('referer');   
         return new RedirectResponse($referer);
    }
    
    public function setPeriodeAction(Request $request)
    {
        $region=$request->request->get('region');
        $periode= $request->request->get('periode');
        $dates = explode(" - ", $periode);
        $startDate=$dates[0];
        $endDate=$dates[1];
        $format = 'd/m/Y';
        $startDate= \DateTime::createFromFormat($format, $dates[0]);
        $endDate= \DateTime::createFromFormat($format, $dates[1]);
        $session = $this->getRequest()->getSession();
        $session->set('region',$region);
        $session->set('startDate',$startDate->format('Y-m-d'));
        $session->set('endDate',$endDate->format('Y-m-d'));
        $session->set('periode',$periode);
        $session->set('end_date_formated',$endDate->format('d/m/Y'));
        $session->set('start_date_formated',$startDate->format('d/m/Y'));
        $referer = $this->getRequest()->headers->get('referer');   
         return new RedirectResponse($referer);
    }


    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"user"})
     */
    public function postAuthTokensAction(Request $request)
    {
        $credentials = new Credentials();
        $form = $this->createForm( CredentialsType::class, $credentials);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
         $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneByUsername($credentials->getLogin());

        if (!$user) { // L'utilisateur n'existe pas
            return $this->invalidCredentials();
        }
        $authToken=AuthToken::create($user);
        $em->persist($authToken);
        $em->flush();
        return $authToken->getUser();
    }


    


    public function pointagesPeriodeExcelAction()
    {
      $styleGreen = array(
       'fill'  => array(
         'type'  => 'solid',
         'color' => array('rgb' => '088E1C'),
       ));
      $styleRed = array(
       'fill'  => array(
         'type'  => 'solid',
         'color' => array('rgb' => 'F53B12'),
       ));
      $em = $this->getDoctrine()->getManager();
      $session = $this->getRequest()->getSession();
      $region=$session->get('region','Douala');
        $startDate=$session->get('startDate','first day of this month');
        $endDate=$session->get('endDate', 'last day of this month');
      $periode= $session->get('periode',' 01/01 - 31/12/'.date('Y'));
      $days=$this->getWorkingDays($startDate, $endDate);
      $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
      $phpExcelObject->getProperties()->setCreator("LPM C")
           ->setLastModifiedBy("LPM C")
           ->setTitle("POINTAGEs  ".$periode)
           ->setSubject("POINTAGEs  de ".$periode)
           ->setDescription("POINTAGEs ".$periode)
           ->setKeywords("POINTAGEs".$periode)
           ->setCategory("POINTAGEs DBS");
            $workedDays=$em->getRepository('AppBundle:Commende')->workedDays($startDate,$endDate,true);
           // $phpExcelObject->createSheet(0);
            $phpExcelObject->setActiveSheetIndex(0)
               ->setCellValue('A1', 'SUPERVISEURS')
               ->setCellValue('B1', 'NOM & PRENOM')
               ->setCellValue('C1', 'NUMERO PERSONNEL')
               ->setCellValue('D1', 'TOTAL VENTE')
               ->setCellValue('E1', 'TOTAL JOURS');
                foreach ($days as $key => $day) {
                   $date=new \DateTime($day);
                   $column= $phpExcelObject->getActiveSheet()
                     ->getCellByColumnAndRow($key+5,1)
                     ->setValue($date->format('d M'))
                     ->getColumn();  
                 $phpExcelObject->getActiveSheet()->getStyle($column.'1')->getAlignment()->setTextRotation(90);
                }
             foreach ($workedDays as $key => $value){
               $phpExcelObject->getActiveSheet()
               ->setCellValue('A'.($key+2), $value['superviseur'])
               ->setCellValue('B'.($key+2), $value['nom']) 
               ->setCellValue('C'.($key+2), $value['telephone']) 
               ->setCellValue('D'.($key+2), $value['nombre'])
               ->setCellValue('E'.($key+2), $value['nombrejours']);
                  foreach ($days as $shiet => $day) {
                    $isThere=$em->getRepository('AppBundle:Commende')->isThere($value['id'],$day);
                  $cell= $phpExcelObject->getActiveSheet()
                     ->getCellByColumnAndRow($shiet+5,($key+2))->setValue($isThere)->getStyle();
                     if($isThere>0)
                        $cell->applyFromArray($styleGreen);
                      else
                         $cell->applyFromArray($styleRed);
                 }            
           };
        $phpExcelObject->getActiveSheet()->setTitle('POINTAGES FS');   
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $startDate=new \DateTime($startDate);
        $endDate= new \DateTime($endDate);
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'Pointages. '.$startDate->format('d M Y').' au '.$endDate->format('d M Y').'.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        return $response;        
    }



public function getWorkingDays($startDate, $endDate)
{
    $date1 = new \DateTime($startDate);
    $date2 = new \DateTime($endDate);
    if ($date1 >= $date2) {
        return [];
    } else {
        $no_days  = [];
        while ($date1 <= $date2) {
           // $what_day = date("N", $begin);
           // if (!in_array($what_day, [6,7]) ) // 6 and 7 are weekend
             $no_days[]=$date1->format('Y-m-d');
             $date1->modify('+1 day');
        };

        return $no_days;
    }
}

   /*load secteurs from excel*/
  public function loadrhAction()
    {
     $manager = $this->getDoctrine()->getManager();
     $path = $this->get('kernel')->getRootDir(). "/../web/import/rhs.xlsx";
     $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject($path);
     $rhs= $objPHPExcel->getSheet(1);
     $highestRow  = $rhs->getHighestRow(); // e.g. 10
    for ($row = 5; $row <= $highestRow; ++ $row) {
            $secteur = $rhs->getCellByColumnAndRow(0, $row)->getValue();
            $nomsecteur = $rhs->getCellByColumnAndRow(1, $row)->getValue();
             $nom = $rhs->getCellByColumnAndRow(2, $row)->getValue();
            $telephone = $rhs->getCellByColumnAndRow(4, $row)->getValue();
            $username= $rhs->getCellByColumnAndRow(5, $row)->getValue();
             $user = $manager->getRepository('AppBundle:User')->findOneByUsername($username);
             if ($nom==null || $nom=='') 
                     continue;
            $pointVente=new PointVente();
            $pointVente
            ->setSecteur( $secteur)
            ->setNomSecteur( $nomsecteur)
            ->setNom($nom)
            ->setTelephone($telephone)
            ->setUser($user);
            $manager->persist($pointVente);
    }
     $manager->flush();
    return $this->redirectToRoute('homepage');      
    }

}