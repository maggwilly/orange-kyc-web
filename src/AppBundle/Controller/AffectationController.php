<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Affectation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
