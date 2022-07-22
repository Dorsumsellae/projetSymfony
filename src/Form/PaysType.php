<?php
 
namespace App\Form;
 
use App\Entity\Pays;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
 
class PaysType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['attr' => ['placeholder' => 'nom du pays']])
            ->add('code', TextType::class, ['attr' => ['placeholder' => 'code du pays']])
            ->add('flag', FileType::class, ['label' => 'Fichier du drapeau',
					    'constraints' => [
					      new File([
						'maxSize' => '2048k',
						'mimeTypes' => [
						  'image/jpeg',
						  'image/png',
						  'image/svg+xml',
						  'image/svg',
						],
						'mimeTypesMessage' => 'Chargez un fichier valide (image)',
					      ])
					    ],
            ])
            ->add('send', SubmitType::class, ['label' => 'Envoyer']);
    }
 
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pays::class,
        ]);
    }
}