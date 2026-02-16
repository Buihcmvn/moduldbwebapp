<?php

namespace App\Form;

use App\Entity\Software;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SoftwareType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('name', TextType::class,[
                'label'         => 'Name',
                'attr'          => ['class' => 'name', 'placeholder' => 'Name'],
                'required'      =>false
            ])
            ->add('bezeichnen', TextType::class,[
                'label'         => 'Bezeichnen',
                'attr'          => ['class' => 'bezeichnen', 'placeholder' => 'bezeichnen'],
                'required'      =>false
            ])
            ->add('beschreibung', TextType::class,[
                'label'         => 'Beschreibung',
                'attr'          => ['class' => 'beschreibung', 'placeholder' => 'Beschreibung'],
                'required'      =>false,
            ])
            ->add('kommentar', TextType::class,[
                'label'         => 'Kommentar',
                'attr'          => ['class' => 'kommentar', 'placeholder' => 'Kommentar'],
                'required'      =>false,
            ])
            ->add('file', FileType::class,[
                'attr'      => ['class' => 'file', 'placeholder' => 'File'],
                'required'  =>false,
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // unmapped fields can't define their validation using attributes
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ]);

            if ($options['edit']) {
                $builder->add('speichern', SubmitType::class,['label'=>'Speichern']);
            }
            else {
                $builder->add('hinzufuegen', SubmitType::class,['label'=>'Hinzufuegen']);
            }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'edit' => false,
            'data_class' => Software::class, // Hier keine data_class angeben, da wir keine Entity validieren
            'csrf_protection' => true, // Standardmäßig true, aber zur Sicherheit hier hinzufügen
            'csrf_field_name' => '_token', // Der Name des CSRF-Feldes
            'csrf_token_id' => 'new_software', // Ein eindeutiger Bezeichner für das Token
        ]);
        $resolver->setAllowedTypes('edit', 'bool');
    }
}