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
        $produits=$em->getRepository('AppBundle:Produit')->countByProduit(null, $startDate,$endDate,$region);
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
         $pointVente = $em->getRepository('AppBundle:PointVente')->find($request->query->get('id'));
        $commendes = $em->getRepository('AppBundle:Commende')->findByPointVente( $pointVente);

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
        $form->submit($request->request->all());
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
        $editForm->submit($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return  $commende;
        }

        return $editForm;
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
