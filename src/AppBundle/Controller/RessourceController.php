<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Ressource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Ressource controller.
 *
 */
class RessourceController extends Controller
{
    /**
     * Lists all ressource entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ressources = $em->getRepository('AppBundle:Ressource')->findAll();

        return $this->render('ressource/index.html.twig', array(
            'ressources' => $ressources,
        ));
    }

    /**
     * Creates a new ressource entity.
     *
     */
    public function newAction(Request $request)
    {
        $ressource = new Ressource();
        $form = $this->createForm('AppBundle\Form\RessourceType', $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ressource);
            $em->flush();

            return $this->redirectToRoute('ressource_show', array('id' => $ressource->getId()));
        }

        return $this->render('ressource/new.html.twig', array(
            'ressource' => $ressource,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ressource entity.
     *
     */
    public function showAction(Ressource $ressource)
    {
        $deleteForm = $this->createDeleteForm($ressource);

        return $this->render('ressource/show.html.twig', array(
            'ressource' => $ressource,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ressource entity.
     *
     */
    public function editAction(Request $request, Ressource $ressource)
    {
        $deleteForm = $this->createDeleteForm($ressource);
        $editForm = $this->createForm('AppBundle\Form\RessourceType', $ressource);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ressource_edit', array('id' => $ressource->getId()));
        }

        return $this->render('ressource/edit.html.twig', array(
            'ressource' => $ressource,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ressource entity.
     *
     */
    public function deleteAction(Request $request, Ressource $ressource)
    {
        $form = $this->createDeleteForm($ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ressource);
            $em->flush();
        }

        return $this->redirectToRoute('ressource_index');
    }

    /**
     * Creates a form to delete a ressource entity.
     *
     * @param Ressource $ressource The ressource entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Ressource $ressource)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ressource_delete', array('id' => $ressource->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
