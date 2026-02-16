<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Hardware;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;

class HardwareType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('name', TextType::class,[
                'attr'      => ['class' => 'name', 'placeholder' => 'Name'],
                'required'  =>false
            ])
            ->add('bezeichnen', TextType::class,[
                'attr'      => ['class' => 'bezeichnen', 'placeholder' => 'bezeichnen'],
                'required'  =>false
            ])
            ->add('beschreibung', TextType::class,[
                'attr'      => ['class' => 'beschreibung', 'placeholder' => 'Beschreibung'],
                'required'  =>false,
            ])
            ->add('kommentar', TextType::class,[
                'attr'      => ['class' => 'kommentar', 'placeholder' => 'Kommentar'],
                'required'  =>false,
            ]);
            if(!$options['api']){
                if (!$options['image']) {
                    $builder->add('image', FileType::class, [
                        'attr' => ['class' => 'image', 'accept' => 'image/*'],
                        'required' => false,
                        'mapped' => false,
                        'constraints' => [
                            new File([
                                'maxSize' => '5024k',
                                'mimeTypes' => [
                                    'image/*',
                                ],
                                'mimeTypesMessage' => 'Please upload a valid image file.',
                            ])
                        ]
                    ]);
                }
                $builder->add('file', FileType::class,[
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
            }
        if ($options['edit']) {
            $builder->add('speichern', SubmitType::class,['label'=>'Speichern']);
        }
        else  {
            $builder->add('hinzufuegen', SubmitType::class,['label'=>'Hinzufügen']);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'edit'              => false,
            'image'             => false,
            'api'               => false,
            'data_class'        => Hardware::class, // Hier keine data_class angeben, da wir keine Entity validieren
            'csrf_protection'   => true, // Standardmäßig true, aber zur Sicherheit hier hinzufügen
            'csrf_field_name'   => '_token', // Der Name des CSRF-Feldes
            'csrf_token_id'     => 'new_hardware', // Ein eindeutiger Bezeichner für das Token
        ]);
        $resolver->setAllowedTypes('edit','bool');
        $resolver->setAllowedTypes('image','bool');
        $resolver->setAllowedTypes('api','bool');
    }
}