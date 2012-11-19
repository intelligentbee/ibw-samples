<?php

namespace IBW\WebsiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type for activity location
 */
class StairsActivityLocationType extends StairsActivityType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->remove('amount')
            ->remove('created_at')
            ->remove('user')
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
