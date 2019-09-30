<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PointVente;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use FOS\RestBundle\View\View;
use AppBundle\Entity\User; 
/**
 * Pointvente controller.
 *
 */
class PointVenteController extends Controller
{
    /**
     * Lists all pointVente entities.
     */
    public function indexAction( )
    {
        $em = $this->getDoctrine()->getManager();
        $pointVentes=$em->getRepository('AppBundle:PointVente')->findAll();
        return $this->render('pointvente/index.html.twig', array(
            'pointVentes' => $pointVentes,
        ));
    }

    /**
     * @Rest\View(serializerGroups={"pointvente"})
     */
    public function indexJsonAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneById($request->headers->get('X-User-Id'));
        $pointVentes = $em->getRepository('AppBundle:PointVente')->findByUser($user);
        return  $pointVentes ;
    }

    /**
     * @Rest\View(serializerGroups={"pointvente"})
     */
    public function newJsonAction(Request $request)
    {
        $pointVente = new Pointvente();
        $form = $this->createForm('AppBundle\Form\PointVenteType', $pointVente);
        $form->submit($request->request->all(),false);
        if ($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle:User')->findOneById($request->headers->get('X-User-Id'));
            $pointVente->setUser($user);
            $em->persist($pointVente);
            $em->flush();
            return $pointVente;
        }
        return  array('status' => 'error');
    }


    /**
     * @Rest\View(serializerGroups={"pointvente"})
     */
    public function editJsonAction(Request $request, PointVente $pointVente)
    {
        $editForm = $this->createForm('AppBundle\Form\PointVenteType', $pointVente);
        $editForm->submit($request->request->all(),false);
        if ($editForm->isValid()){
            $this->getDoctrine()->getManager()->flush();
             return $pointVente;
        }
        return array('status' => 'error');
    }


    /**
     * Creates a new pointVente entity.
     */
    public function newAction(Request $request)
    {
        $pointVente = new Pointvente();
        $form = $this->createForm('AppBundle\Form\PointVenteType', $pointVente);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
              $em = $this->getDoctrine()->getManager();
              $em->persist($pointVente);
              $em->flush();
            return $this->newForm($pointVente);
         }

        return $this->render('pointvente/new.html.twig',array(
            'pointVente' => $pointVente,
            'form' => $form->createView(),
        ));
    }


    public function newForm(Pointvente $pointVente)
    {
        $pointVente = new Pointvente($pointVente->getUser());
        $form = $this->createForm('AppBundle\Form\PointVenteType', $pointVente);
        return $this->render('pointvente/new.html.twig', array(
            'pointVente' => $pointVente,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a pointVente entity.
     */
    public function showAction(PointVente $pointVente)
    {
        $deleteForm = $this->createDeleteForm($pointVente);
        return $this->render('pointvente/show.html.twig', array(
            'pointVente' => $pointVente,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing pointVente entity.
     */
    public function editAction(Request $request, PointVente $pointVente)
    {
        $deleteForm = $this->createDeleteForm($pointVente);
        $editForm = $this->createForm('AppBundle\Form\PointVenteType', $pointVente);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('pointvente_edit', array('id' => $pointVente->getId()));
        }
        return $this->render('pointvente/edit.html.twig', array(
            'pointVente' => $pointVente,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a pointVente entity
     */
    public function deleteAction(Request $request, PointVente $pointVente)
    {
        $form = $this->createDeleteForm($pointVente);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($pointVente);
            $em->flush();
        }

        return $this->redirectToRoute('pointvente_index');
    }

    /**
     * @Rest\View()
     * 
     */
    public function deleteJsonAction(Request $request, PointVente $pointVente)
    {
   $em = $this->getDoctrine()->getManager();
   try {
    $em->remove($pointVente);
    $em->flush();  
    return array('status' => "ok" );
   }   catch (Exception $e) {
     $pointVente->setDeleted(true);
      $em->flush();  
        return array('status' => "no" );
  } finally {
     return array('status' => "ok" );
  }
  } 
    /**
     * @param PointVente $pointVente The pointVente entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PointVente $pointVente)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pointvente_delete', array('id' => $pointVente->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
