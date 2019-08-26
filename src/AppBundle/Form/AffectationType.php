<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
class AffectationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user')
        ->add('pointVente', EntityType::class, array(
            'choice_label' => 'nom',
            'class' => 'AppBundle:PointVente',
            ,'label'=>'PDV'))
        ->add('ressource', EntityType::class, array(
            'choice_label' => 'nom',
            'class' => 'AppBundle:Ressource',
            ,'label'=>'BA'));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Affectation',
           'csrf_protection' => false,
            'allow_extra_fields' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_affectation';
    }


}
