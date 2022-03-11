<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if($builder->getData()->getImg()) {
            $img = new File($_SERVER['DOCUMENT_ROOT'] . "/images/article/" . $builder->getData()->getImg());
        }
        else {
            $img = new File($_SERVER['DOCUMENT_ROOT'] . "/images/article/placeholder.png");
        }
        $builder
            ->add('title', TextType::class, [
                'data' => $builder->getData()->getTitle()
            ])
            ->add('content', TextareaType::class, [
                'data' => $builder->getData()->getcontent()
            ])
            ->add('visibility', CheckboxType::class, [
                'label' => "published",
                'data' => $builder->getData()->getVisibility(),
                "required" => false
            ])
            ->add('img', FileType::class, [
                'label' => "Image",
                "data" => $img,
                "required" => false,
                "mapped" => false
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
