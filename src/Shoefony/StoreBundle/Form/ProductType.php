<?php

namespace Shoefony\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductType extends AbstractType{

	
	public function buildForm(FormBuilderInterface $builder, array $options){
		$builder->add("title", 'text');
	}


	public function getName(){
		return 'product_form';
	}

}
?>