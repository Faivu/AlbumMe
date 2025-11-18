<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Genre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AlbumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // artist and genres not ordered, I can do that later since UX is not a priority rn
        $builder
            ->add('title')
            // Changed from json to textarea for now, I need to make it work firt the assignment 1
            ->add('trackList', TextareaType::class, [
                'help' => 'Enter each track on a new line',
                'attr' => ['rows' => 10],
                'required' => false,
            ])
            ->add('artist', EntityType::class, [
                'class' => Artist::class,
                'choice_label' => 'name',
            ])
            ->add('genres', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'name',
                'multiple' => true,
                // Temporaty for this first assignment 
                'expanded' => true
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Create Album'
            ])
        ;
        // A data transformer since trackList has an array and can't process strings directly, I just don't have time for js that won't my mark better in A1.
        $builder->get('trackList')
            ->addModelTransformer(new CallbackTransformer(
                function ($tracksAsArray): string {
                    if (empty($tracksAsArray)) {
                        return '';
                    }
                    return implode("\n", $tracksAsArray);
                },

                // From string to array, addModelTransformer needs to functions for the two ways, so it knows which one to use and when
                function ($tracksAsString): array {

                    if (empty($tracksAsString)) {
                        return [];
                    }
                    $tracks = explode("\n", str_replace("\r\n", "\n", $tracksAsString));

                    // Trim 
                    return array_filter(array_map('trim', $tracks));
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Album::class,
        ]);
    }
}
