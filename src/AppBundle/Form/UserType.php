<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom', 'text', array('required' => true,'label'=>'Nom'))
        ->add('ville', 'choice', array('required' => true,
          'choices'=>array(
            'Douala'=>'Douala',
            'Yaounde'=>'Yaounde',
            'Bafoussam'=>'Bafoussam',
            'Dschang'=>'Dschang',
            'Garoua'=>'Garoua',
            'Maroua'=>'Maroua')))
        ->add('username', 'text', array('required' => true,'label'=>'Identifiant'))
        ->add('email', 'text', array('required' => true,'label'=>'Email'))
        ->add('type', ChoiceType::class, array(
                                  'choices'  => array(
                                  'superviseur' => 'superviseur',
                                   'administrateur' => 'administrateur'
                                   ), 
                                  'multiple'=>false,
                                  'expanded'=>false,
                                  'attr'=>array('data-rel'=>'chosen'),
                                   ));
    }
    
        public function getParent()
        {
            return 'fos_user_registration';
        }

        public function getName()
        {
            return 'app_user_registration';
        }

}
