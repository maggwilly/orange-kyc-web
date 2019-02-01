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
        ->add('secteur', ChoiceType::class, array(
                                  'choices'  => array(
                                   'SECTEUR 1' => 'SECTEUR 1',
                                   'SECTEUR 2' => 'SECTEUR 2',
                                   'SECTEUR 3' => 'SECTEUR 3',
                                   'SECTEUR 4' => 'SECTEUR 4',
                                   'SECTEUR 5' => 'SECTEUR 5',
                                   'SECTEUR 6' => 'SECTEUR 6',
                                   'SECTEUR 7' => 'SECTEUR 7',
                                   'SECTEUR 8' => 'SECTEUR 8',
                                   'SECTEUR 9' => 'SECTEUR 9',
                                   'SECTEUR 10' => 'SECTEUR 10',
                                   'SECTEUR 11' => 'SECTEUR 11',
                                   'SECTEUR 12' => 'SECTEUR 12',
                                   'SECTEUR 13' => 'SECTEUR 13',
                                   'SECTEUR 14' => 'SECTEUR 14',
                                   'SECTEUR 15' => 'SECTEUR 15',
                                   'SECTEUR 16' => 'SECTEUR 16',
                                   'SECTEUR 17' => 'SECTEUR 17',
                                   'SECTEUR 18' => 'SECTEUR 18',
                                   'SECTEUR 19' => 'SECTEUR 19',
                                   'SECTEUR 20' => 'SECTEUR 20',
                                   'SECTEUR 21' => 'SECTEUR 21',
                                   'SECTEUR 22' => 'SECTEUR 22',
                                   'SECTEUR 23' => 'SECTEUR 23',
                                   'SECTEUR 24' => 'SECTEUR 24',
                                   'SECTEUR 25' => 'SECTEUR 25'
                                   ), 
                                  'multiple'=>false,
                                  'expanded'=>false,
                                  'attr'=>array('data-rel'=>'chosen'),
                                   ))        
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
            'data_class' => 'AppBundle\Entity\PointVente'
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
