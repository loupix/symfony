<?php

namespace Shoefony\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class OpinionType extends AbstractType{

	
	public function buildForm(FormBuilderInterface $builder, array $options){
		$builder->add("nom", 'text', array('label'=>'Votre nom'))
				->add("prenom", 'text', array('label'=>'Votre prenom'))
				->add("commentaire", 'textarea', array('label'=>'Votre avis'));
	}


	public function getName(){
		return 'opinion_form';
	}

}
?>