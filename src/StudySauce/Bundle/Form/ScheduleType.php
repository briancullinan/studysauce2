<?php

namespace StudySauce\Bundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ScheduleType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('university')
            ->add('grades')
            ->add('weekends')
            ->add('sharp6am11am')
            ->add('sharp11am4pm')
            ->add('sharp4pm9pm')
            ->add('sharp9pm2am')
            ->add('uid')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'StudySauce\Bundle\Entity\Schedule',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'intention'       => 'schedule_intention',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'studysauce_bundle_schedule';
    }
}
