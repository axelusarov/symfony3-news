<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AddArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('fullText', TextareaType::class)
            ->add('title_ru', null, ['mapped' => false, 'required' => false])
            ->add('fullText_ru', TextareaType::class, ['mapped' => false, 'required' => false])
            ->add('add', SubmitType::class, array('label' => 'articles.submit'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Article',
            'attr'=>array('novalidate'=>'novalidate')
        ));
    }
}