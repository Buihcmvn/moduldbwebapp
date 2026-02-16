<?php

namespace App\Form;

use App\Entity\Area;
use App\Entity\Hardware;
use App\Entity\Projekte;
use App\Entity\Software;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ProjektType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'name', 'placeholder' => 'Name'],
                'required' => false
            ])

            ->add('area', EntityType::class, [
                'class' => Area::class,
                'choice_label' => 'name', // choose property to show in options
                'multiple' => true,       // "true" if more option to choose in Areas
                'expanded' => false,      // "true" for checkboxes, false for  select
                'label' => 'Areas',       // label
            ])
            ->add('software', EntityType::class, [
                'class' => Software::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'label' => 'Software',
            ])
            ->add('hardware', EntityType::class, [
                'class' => Hardware::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'label' => 'Hardware',
            ])
            ->add('beschreibung', TextType::class, [
                'attr' => ['class' => 'beschreibung', 'placeholder' => 'Beschreibung'],
                'required' => false,
            ])
            ->add('kommentar', TextType::class, [
                'attr' => ['class' => 'kommentar', 'placeholder' => 'Kommentar'],
                'required' => false,]);
        if( $options['edit'] === true ){
            $builder->add('speichern', SubmitType::class, ['label' => 'Speichern']);
        }
        else   {
            $builder->add('hinzufuegen', SubmitType::class, ['label' => 'Hinzufügen']);
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'edit' => false,
            'data_class' => Projekte::class, // Hier keine data_class angeben, da wir keine Entity validieren
            'csrf_protection' => true, // Standardmäßig true, aber zur Sicherheit hier hinzufügen
            'csrf_field_name' => '_token', // Der Name des CSRF-Feldes
            'csrf_token_id' => 'new_hardware', // Ein eindeutiger Bezeichner für das Token
        ]);

        $resolver->setAllowedTypes('edit', 'bool');
    }
}
