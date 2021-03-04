<?php

namespace App\Form;

use App\Entity\Link;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class LinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url',TextType::class,[
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Url([
                        'message' => 'Please enter a valid URL.'
                    ]),
                    new Assert\Length([
                        'max' => 2083
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'data-rule-url' => 'true',
                    'maxlength' => 2083,
                    'data-rule-maxlength' => '2083',
                    'data-msg-maxlength' => 'This value is too long. It should have 2083 characters or less.',
                    'placeholder' => 'Url To Shorten'
                ],
                'label' => 'Enter URL to be shortened'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Link::class,
        ]);
    }
}
