<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class PointVenteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom', 'text', array('required' => true,'label'=>'Nom'))
        ->add('telephone', 'text', array('required' => true,'label'=>'Numero de telephone'))
        ->add('description', 'text', array('required' => true,'label'=>'ID de tablette DBS'))       
        ->add('user', EntityType::class, array(
            'choice_label' => 'nom',
            'class' => 'AppBundle:User'
            ,'label'=>'Sperviseur'));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PointVente',
           'csrf_protection' => false,
            'allow_extra_fields' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_pointvente';
    }


}
