<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Affectation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use FOS\RestBundle\View\View;
/**
 * Affectation controller.
 *
 */
class AffectationController extends Controller
{
    /**
     * Lists all affectation entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $affectations = $em->getRepository('AppBundle:Affectation')->findAll();
        return $this->render('affectation/index.html.twig', array(
            'affectations' => $affectations,
        ));
    }

    /**
     * @Rest\View(serializerGroups={"affectation"})
     */
    public function indexJsonAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneById($request->headers->get('X-User-Id'));
        $affectations = $em->getRepository('AppBundle:Affectation')->findByUser($user);
        return  $affectations ;
    }

    /**
     * @Rest\View(serializerGroups={"affectation"})
     * 
     */
    public function newJsonAction(Request $request)
    {
        $affectation = new Affectation();
        $form = $this->createForm('AppBundle\Form\AffectationType', $affectation);
        $form->submit($this->makeUp($request),false);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle:User')->findOneById($request->headers->get('X-User-Id'));
            $affectation->setUser($user);
            $em->persist($affectation);
            $em->flush();
            return $affectation;
        }
        return  array(
            'status' => 'error');
    }

    /**
     * @Rest\View(serializerGroups={"affectation"})
     * 
     */
    public function editJsonAction(Request $request, Affectation $affectation)
    {
        $editForm = $this->createForm('AppBundle\Form\AffectationType', $affectation);
        $editForm->submit($this->makeUp($request),false);
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
             return $affectation;
        }
        return array('status' => 'error');
    }

public function makeUp(Request $request, $setId=true){
      $affectation= $request->request->all();
     if(array_key_exists('pointVente', $affectation)&&is_array($affectation['pointVente']))
       $affectation['pointVente']=$affectation['pointVente']['id'];   
     if(array_key_exists('ressource', $affectation)&&is_array($affectation['ressource']))
       $affectation['ressource']=$affectation['ressource']['id'];          
    return $affectation;
}
    /**
     * Creates a new affectation entity.
     *
     */
    public function newAction(Request $request)
    {
        $affectation = new Affectation();
        $form = $this->createForm('AppBundle\Form\AffectationType', $affectation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($affectation);
            $em->flush();

            return $this->redirectToRoute('affectation_show', array('id' => $affectation->getId()));
        }

        return $this->render('affectation/new.html.twig', array(
            'affectation' => $affectation,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a affectation entity.
     *
     */
    public function showAction(Affectation $affectation)
    {
        $deleteForm = $this->createDeleteForm($affectation);

        return $this->render('affectation/show.html.twig', array(
            'affectation' => $affectation,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing affectation entity.
     *
     */
    public function editAction(Request $request, Affectation $affectation)
    {
        $deleteForm = $this->createDeleteForm($affectation);
        $editForm = $this->createForm('AppBundle\Form\AffectationType', $affectation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('affectation_edit', array('id' => $affectation->getId()));
        }

        return $this->render('affectation/edit.html.twig', array(
            'affectation' => $affectation,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a affectation entity.
     *
     */
    public function deleteAction(Request $request, Affectation $affectation)
    {
        $form = $this->createDeleteForm($affectation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($affectation);
            $em->flush();
        }

        return $this->redirectToRoute('affectation_index');
    }

    /**
     * Creates a form to delete a affectation entity.
     *
     * @param Affectation $affectation The affectation entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Affectation $affectation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('affectation_delete', array('id' => $affectation->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
