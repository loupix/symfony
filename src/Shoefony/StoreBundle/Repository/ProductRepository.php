<?php

namespace Shoefony\StoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends EntityRepository
{
	public function lastProduct($max=4){
		$products = $this->createQuerybuilder("p")
			->orderBy("p.id", "DESC")
			->setMaxResults($max);
		return $products->getQuery()->getResult();
	}


	public function findByTitle($titleProduct){
		$db = $this->createQuerybuilder("p");
		$db->join("p.brand","b");
		$db->where($db->expr()->like('p.title',':title'))
			->orWhere($db->expr()->like('b.title',':title'))
			->orWhere($db->expr()->like('p.description',':title'))
			->setParameter("title","%".$titleProduct."%");
		return $db->getQuery()->getResult();
	}


	public function getMostCommentaire($max=4){
		$products = $this->getEntityManager()->createQuerybuilder()
			->select("p, count(o.id) AS HIDDEN nbCom")
			->from("ShoefonyStoreBundle:Product", "p")
			->innerJoin("p.opinions", "o")
			->groupBy("p.id")
			->orderBy("nbCom", "DESC")
			->setMaxResults($max);
		return $products->getQuery()->getResult();
	}



	public function getList($page=1, $maxperpage=4){
		$db = $this->createQuerybuilder("p");
		$db->setFirstResult(($page-1) * $maxperpage)
			->setMaxResults($maxperpage);
		return new Paginator($db);
	}



	public function getListBrand($page=1, $maxperpage=4, $brand){
		$db = $this->createQuerybuilder("p")
				->select("p")
				->where("p.brand=:brand")
				->setParameter("brand", $brand);
		$db->setFirstResult(($page-1) * $maxperpage)
			->setMaxResults($maxperpage);
		return new Paginator($db);
	}
}
