<?php

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfileType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ProfileType constructor.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, ['required' => true, 'label' => $this->translator->trans('Profile.LastName')])
            ->add('firstName', TextType::class, ['required' => true, 'label' => $this->translator->trans('Profile.FirstName')])
            ->add('patronymic', TextType::class, ['required' => false, 'label' => $this->translator->trans('Profile.Patronymic')])
            ->add('phone', TextType::class, [
                'required' => true,
                'label' => $this->translator->trans('Profile.Phone'),
                'attr' => ['data-mask' => '+7(000)000-00-00'], ])
            ->add('sex', ChoiceType::class, [
                'choices' => [
                    $this->translator->trans('Profile.Sex.man') => 1,
                    $this->translator->trans('Profile.Sex.woman') => 2,
                ],
                'label' => $this->translator->trans('Profile.Sex.label'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
