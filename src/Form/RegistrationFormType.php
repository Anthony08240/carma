<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        // ajoute un champ de type text pour l'etablissement

            ->add('etablissement', TextType::class,[
                'label' => 'Etablissement',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer le nom de vôtre établissement',
                    ]),
                ],
                'required' => true,
                ])

        // ajoute un champ de type text pour le nom de l'utilisateur

            ->add('name', TextType::class,[
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer votre Nom',
                    ]),
                ],
                'required' => true,
            ])

        // ajoute un champ de type text pour le prenom de l'utilisateur

            ->add('firstname', TextType::class,[
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer votre Prénom',
                    ]),
                ],
                'required' => true,
            ])

        // ajoute un champ de type text pour le code postal de l'utilisateur

            ->add('codepostal', TextType::class,[
                'label' => 'Code postal',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer votre votre code postal',
                    ]),
                ],
                'required' => true,
            ])

        // ajoute un champ de type text pour la ville de l'utilisateur

            ->add('ville', TextType::class,[
                'label' => 'Ville',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer votre Ville',
                    ]),
                ],
                'required' => true,
            ])

        // ajoute un champ de type text pour l'adresse de l'utilisateur

            ->add('adresse', TextType::class,[
                'label' => 'adresse',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer votre Adresse',
                    ]),
                ],
                'required' => true,
            ])

        // ajoute un champ de type date de naissance pour la date de naissance de l'utilisateur

            ->add('dnaissance', BirthdayType::class,[
                'label' => 'Date de Naissance',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer votre Date de Naissance',
                    ]),
                ],
                'required' => true,
            ])

        // ajoute un champ de type email pour l'email de l'utilisateur

            ->add('email', EmailType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer un e-mail',
                    ]),
                ],
                'required' => true,
            ])

        // ajoute un champ de type téléphone pour le numero de téléphone de l'utilisateur

            ->add('tel', TelType::class,[
                'label' => 'Téléphone',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer votre Numerot de téléphone',
                    ]),
                ],
                'required' => true,
            ])

        // ajoute 2 champs de type password pour le mot de passe de l'utilisateur

            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identique',
                'options' => ['attr' => ['class' => 'input w-75 mx-auto']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Répéter le mot de passe'],
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de pass doit contenir au moin {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
