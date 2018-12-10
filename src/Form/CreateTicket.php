<?php
namespace App\Form;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Entity\Tickets;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class CreateTicket{

    public function buildForm(FormBuilderInterface $builder, array $options){

    $builder
    ->add('Name')
    ->add('ProjectId')
    ->add('UserId')
    ->add('Description')
    ->add('AssigneeId')
    ->add('Status')
    ->add('Submit', SubmitType::class);

    }

}
