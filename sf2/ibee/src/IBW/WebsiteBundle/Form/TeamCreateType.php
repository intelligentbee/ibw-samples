<?php

namespace IBW\WebsiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type for team name
 */
class TeamCreateType extends AbstractType
{
     public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('name', 'text')
        ;
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IBW\WebsiteBundle\Entity\Team'
        ));
    }

    public function getName()
    {
        return 'ibw_websitebundle_teamcreatetype';
    }
}

?>
