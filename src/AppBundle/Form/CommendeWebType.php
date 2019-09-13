<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CommendeWebType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('typeInsident', ChoiceType::class, array(
                                  'choices'  => array(
                                   'Rien à signaler' => 'Rien à signaler',
                                   'Problème avec le PDV' => 'Problème avec le PDV',
                                   'Problème avec le materiel' => 'Problème avec le materiel',
                                   'Autres insidents' => 'Autres insidents'
                                   ), 
                                  'multiple'=>false,
                                  'expanded'=>false,
                                  'attr'=>array('data-rel'=>'chosen'),
                                   ))
        ->add('lignes',CollectionType::class, array('entry_type'=> LigneType::class,'allow_add' => true));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Commende',
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_commende';
    }


}
