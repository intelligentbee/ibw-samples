<?php

namespace IBW\WebsiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type for activity
 */
class StairsActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('amount', 'text')
            ->add('created_at')
            ->add('user')
            ->add('lng',null,array(
                    'label'  => 'Longitude'))
            ->add('lat',null,array(
                    'label'  => 'Latitude'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IBW\WebsiteBundle\Entity\StairsActivity'
        ));
    }

    public function getName()
    {
        return 'ibw_websitebundle_stairsactivitytype';
    }
}
