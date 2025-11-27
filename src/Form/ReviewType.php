<?php

namespace App\Form;


use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;



class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('rating', ChoiceType::class, [
                'label' => 'Rating',
                'choices' => [
                    '5 Stars' => 5,
                    '4 Stars' => 4,
                    '3 Stars' => 3,
                    '2 Stars' => 2,
                    '1 Star' => 1,
                ],
                'placeholder' => 'Select a rating',
                'constraints' => [
                    new NotBlank(['message' => 'Please select a rating.']),
                ],
            ])

            ->add('comment')


            ->add('save', SubmitType::class, [
                'label' => 'Add review',
                'attr' => [
                    'class' => 'btn btn-info text-white me-3',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
