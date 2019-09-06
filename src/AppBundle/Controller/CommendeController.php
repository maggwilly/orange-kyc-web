<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Commende;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use FOS\RestBundle\View\View;
use AppBundle\Entity\PointVente; 
use AppBundle\Entity\Ressource; 
use AppBundle\Entity\User; 
use \Doctrine\Common\Collections\ArrayCollection;
/**
 * Commende controller.
 *
 */
class CommendeController extends Controller
{
    /**
     * Lists all commende entities.
     *
     */
    public function indexAction()
    {
 $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
        $region=$session->get('region');
        $startDate=$session->get('startDate','first day of this month');
        $endDate=$session->get('endDate', 'last day of this month');
        $commendes = $em->getRepository('AppBundle:Commende')->findList(null,null,null,null,$startDate,$endDate,$region);
        return $this->render('commende/index.html.twig', array(
            'commendes' => $commendes ));
    }



    public function performancesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
        $region=$session->get('region');
        $startDate=$session->get('startDate','first day of this month');
        $endDate=$session->get('endDate', 'last day of this month');
        $produits=$em->getRepository('AppBundle:Produit')->findOrderedList();
        $performances=(new ArrayCollection($em->getRepository('AppBundle:Affectation')->findPerformances($startDate,$endDate,$region)))->map(function ($affectation) use ($em,$region,$startDate,$endDate){
                 $affectation['ventes']=$em->getRepository('AppBundle:Produit')->countByProduit($affectation['id'], $startDate,$endDate,$region);
                 if(empty($affectation['ventes']))   
                 $affectation['ventes']=$em->getRepository('AppBundle:Produit')->findOrderedList();

             return $affectation;
        });        
        return $this->render('AppBundle::performances.html.twig',
         array(
            'performances'=>$performances,
            'produits'=>$produits,
         ));
    }

    public function listByInsidentAction(Request $request,$insident)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
        $region=$session->get('region');
        $startDate=$session->get('startDate','first day of this month');
        $endDate=$session->get('endDate', 'last day of this month');
        $commendes=$em->getRepository('AppBundle:Commende')->findList(null,null,null,$insident,$startDate,$endDate,$region);

        return $this->render('commende/index.html.twig',
         array('commendes' => $commendes));
    }

    public function performancesExcelAction()
    {
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
           ->setTitle("PERFORMANCES  ".$periode)
           ->setSubject("PERFORMANCES  de ".$periode)
           ->setDescription("PERFORMANCES ".$periode)
           ->setKeywords("PERFORMANCE".$periode)
           ->setCategory("Rapports Orange");
           $ativeshiet=0;
        foreach ($days as $shiet => $day) {

                $phpExcelObject->createSheet($ativeshiet);
                $phpExcelObject->setActiveSheetIndex($ativeshiet)
               ->setCellValue('A1', 'SUPERVISEURS')
               ->setCellValue('B1', 'NOM & PRENOM')
               ->setCellValue('C1', 'TELEPHONE')
               ->setCellValue('D1', 'PDV')
               ->setCellValue('E1', 'TYPE');
               $ofset=0;
                foreach ($days as $shiet => $day) {
                     $ofset+=5;
                  foreach ($produits as $key => $produit) {
                    $column= $phpExcelObject->getActiveSheet()
                     ->getCellByColumnAndRow($key+$ofset,1)
                     ->setValue($produit['nom'])
                     ->getColumn();
                      $phpExcelObject->getActiveSheet()->getColumnDimension($column)->setAutoSize(false);
                      $phpExcelObject->getActiveSheet()->getColumnDimension($column)->setWidth(7.2);  
                      $phpExcelObject->getActiveSheet()->getStyle($column.'1')->getAlignment()->setTextRotation(90);
                 }
               }

         foreach ($days as $shiet => $day) {
         $produits=$em->getRepository('AppBundle:Produit')->findOrderedList();
         $performances=(new ArrayCollection($em->getRepository('AppBundle:Affectation')->findPerformances($day,$day,$region)))->map(function ($affectation) use ($em,$region,$startDate,$endDate){
            $affectation['ventes']=$em->getRepository('AppBundle:Produit')->countByProduit($affectation['id'], $day,$day,$region);
                 if(empty($affectation['ventes']))   
                 $affectation['ventes']=$em->getRepository('AppBundle:Produit')->findOrderedList();
             return $affectation;
            }); 
            }
            
             foreach ($ventes as $key => $value) {
               $phpExcelObject->getActiveSheet()//->setActiveSheetIndex($shiet)
               ->setCellValue('A'.($key+2), $value['supernom'])
               ->setCellValue('B'.($key+2), $value['fsnom'])
               ->setCellValue('C'.($key+2), NULL)
               ->setCellValue('D'.($key+2),  $value['fsserietablette'])
               ->setCellValue('E'.($key+2), $value['fstelephone'])
               ->setCellValue('F'.($key+2), $value['fsorange'])
               ->setCellValue('G'.($key+2), $value['souscription'])
               ->setCellValue('H'.($key+2), $value['renouvellement'])
               ->setCellValue('I'.($key+2), $value['snom'].' '.$value['snom'])
               ->setCellValue('J'.($key+2), $value['stelephone'])
               ->setCellValue('K'.($key+2), $value['contrat']) 
               ->setCellValue('L'.($key+2), $value['montant'])
               ->setCellValue('M'.($key+2), $value['mode']);              
           };
        $phpExcelObject->getActiveSheet()->setTitle('perf '.$day);
       // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        // create the writer
         $ativeshiet++;
        }
         
        {    // $workedDays=$em->getRepository('AppBundle:Commende')->workedDays($startDate,$endDate,true);
               $workedDays = $em->getRepository('AppBundle:PointVente')->recapPeriode($startDate,$endDate);
               $phpExcelObject->createSheet($ativeshiet);
                $phpExcelObject->setActiveSheetIndex($ativeshiet)
               ->setCellValue('A1', 'SUPERVISEURS')
               ->setCellValue('B1', 'NOM & PRENOM')
               ->setCellValue('C1', 'TELE PERSONNEL')
               ->setCellValue('D1', 'N° TABLETTE')
               ->setCellValue('E1', 'SOUSCRIPTION')
               ->setCellValue('F1', 'RENOUVELLEMENT')
               ->setCellValue('G1', 'TOTAL VENTE')
               ->setCellValue('H1', 'NOMBRE DE JOURS');
             foreach ($workedDays as $key => $value) {
               $phpExcelObject->getActiveSheet()
               ->setCellValue('A'.($key+2), $value['supernom'])
               ->setCellValue('B'.($key+2), $value['fsnom'])
               ->setCellValue('C'.($key+2), $value['fstelephone'])
               ->setCellValue('D'.($key+2), $value['fsserietablette'])
               ->setCellValue('E'.($key+2), $value['souscription'])
               ->setCellValue('F'.($key+2), $value['renouvellement'])
               ->setCellValue('G'.($key+2), $value['total'])
               ->setCellValue('H'.($key+2), $value['nbjours']);              
           };
        $phpExcelObject->getActiveSheet()->setTitle('RECAP');   
        }
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $startDate=new \DateTime($startDate);
        $endDate= new \DateTime($endDate);
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'Perf. '.$startDate->format('d M Y').' au '.$endDate->format('d M Y').'.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        return $response;        
    }




    public function listAction(Request $request,User $user=null, Pointvente $pointVente=null,Ressource $ressource=null)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
        $region=$session->get('region');
        $startDate=$session->get('startDate','first day of this month');
        $endDate=$session->get('endDate', 'last day of this month');
    $commendes = $em->getRepository('AppBundle:Commende')->findList($user,$pointVente,$ressource,null,$startDate,$endDate,$region);
        return $this->render('commende/index.html.twig', array('commendes' => $commendes  ));
    }

    /**
     * @Rest\View(serializerGroups={"commende"})
     * 
     */
    public function indexJsonAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
         $affectation = $em->getRepository('AppBundle:Affectation')->find($request->query->get('id'));
        $commendes = $em->getRepository('AppBundle:Commende')->findByAffectaion($affectation);

        return $commendes;
    }
    /**
     * Creates a new commende entity.
     *
     */
    public function newAction(Request $request)
    {
        $commende = new Commende();
        $form = $this->createForm('AppBundle\Form\CommendeType', $commende);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($commende);
            $em->flush();
            return $this->redirectToRoute('commende_show', array('id' => $commende->getId()));
        }

        return $this->render('commende/new.html.twig', array(
            'commende' => $commende,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Rest\View(serializerGroups={"commende"})
     * 
     */
    public function newJsonAction(Request $request)
    {
        $commende = new Commende();
        $form = $this->createForm('AppBundle\Form\CommendeType', $commende);
        $form->submit($this->makeUp($request),false);
        if ($form->isValid()) {
             $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle:User')->findOneById($request->headers->get('X-User-Id'));
            $commende->setUser($user);
            $em->persist($commende);
            $em->flush();
            return $commende;
        }

        return  array(
            'status' => 'error');
    }

    /**
     * @Rest\View(serializerGroups={"commende"})
     * 
     */
    public function editJsonAction(Request $request, Commende $commende)
    {
        $editForm = $this->createForm('AppBundle\Form\CommendeType', $commende);
        $editForm->submit($this->makeUp($request),false);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return  $commende;
        }

        return $editForm;
    }

public function makeUp(Request $request){
      $commende= $request->request->all();
     if(array_key_exists('affectation', $commende)&&is_array($commende['affectation']))
       $commende['affectation']=$commende['affectation']['id'];   
        
    return $commende;
}
    /**
     * Finds and displays a commende entity.
     *
     */
    public function showAction(Commende $commende)
    {
        $deleteForm = $this->createDeleteForm($commende);
        return $this->render('commende/show.html.twig', array(
            'commende' => $commende,
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * @Rest\View(serializerGroups={"commende"})
     * 
     */
    public function showJsonAction(Commende $commende)
    {
        return $commende;
    }

    /**
     * Displays a form to edit an existing commende entity.
     *
     */
    public function editAction(Request $request, Commende $commende)
    {
        $deleteForm = $this->createDeleteForm($commende);
        $editForm = $this->createForm('AppBundle\Form\CommendeType', $commende);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('commende_edit', array('id' => $commende->getId()));
        }

        return $this->render('commende/edit.html.twig', array(
            'commende' => $commende,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a commende entity.
     *
     */
    public function deleteAction(Request $request, Commende $commende)
    {
        $form = $this->createDeleteForm($commende);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($commende);
            $em->flush();
        }

        return $this->redirectToRoute('commende_index');
    }


    /**
     * @Rest\View()
     * 
     */
    public function deleteJsonAction(Request $request, Commende $commende)
    {
            $em = $this->getDoctrine()->getManager();
          $em->remove($commende);
            $em->flush();
   try {
    return array('status' => "ok" );
   }   catch (Exception $e) {
        return array('status' => $e );
     }
  }          
        


    /**
     * Creates a form to delete a commende entity.
     *
     * @param Commende $commende The commende entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Commende $commende)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('commende_delete', array('id' => $commende->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
