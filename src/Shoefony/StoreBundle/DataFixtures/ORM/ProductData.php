<?php

namespace Shoefony\StoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Shoefony\StoreBundle\Entity\Product;
use Shoefony\StoreBundle\Entity\Image;
use Shoefony\StoreBundle\Entity\Brand;
use Shoefony\StoreBundle\Entity\Opinion;


class LoadProductData implements FixtureInterface{

	public function load(ObjectManager $manager){

		$images = [];
		for($i=1;$i<=14;$i++){
			$im = new Image();
			$im->setUrl("bundles/shoefonycms/img/products/shoe-".$i.".jpg");
			$im->setAlt("shoe-".$i.".jpg");
			$images[] = $im;
			$manager->persist($im);
		}


		$marques = ['Adidas','Asics','Nike','Puma'];
		$brands = [];
		foreach($marques as $m){
			$brand = new Brand();
			$brand->setTitle($m);
			$brands[] = $brand;
			$manager->persist($brand);
		}


		$products = array();

		for($i=0;$i<14;$i++){
			$product = new Product();
			$product->setTitle("Produit ".$i);
			$product->setDescription("Lorem ipsum dolor sit amet, <strong>consectetur adipiscing elit.</strong> Nulla placerat lobortis dui. Suspendisse consequat venenatis semper. Proin scelerisque velit eu sem interdum blandit. ");
			$product->setPrice($i * rand(1,50));

			$b = rand(0,count($brands)-1);
			$brand = $brands[$b];
			$brand->addProduct($product);
			$product->setBrand($brand);

			$product->setImage($images[$i]);

			$products[] = $product;
			$manager->persist($product);
		}
		


		$manager->flush();



		// Generer des commentaires

		$prenoms = array("pierre","paul","jack","andrée","aymeric","fabien","simon", "hanz");
		$noms = array("Dupont","Martin","Zimmer");
		$commentaires = array("un des meilleurs produit !","C'est telement un plagiat !!");



		$max_commentaire = 20;
		for($i=0;$i<$max_commentaire;$i++){
			$opinion = new Opinion();
			$opinion->setNom($noms[rand(0, count($noms)-1)]);
			$opinion->setPrenom($prenoms[rand(0, count($prenoms)-1)]);
			$opinion->setCommentaire($commentaires[rand(0, count($commentaires)-1)]);

			// ajout du com sur un produit aléatoire
			$prod = $products[rand(0, count($products)-1)];
			$prod->addOpinions($opinion);
			$manager->persist($opinion);
			$manager->persist($prod);
		}


		$manager->flush();










	} 
}