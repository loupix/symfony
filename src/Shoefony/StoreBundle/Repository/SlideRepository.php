<?php

namespace Shoefony\StoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * SlideRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SlideRepository extends EntityRepository
{
	public function getLast($max=2){
		$slides = $this->createQuerybuilder("s")
			->orderBy("s.createdAt", "DESC")
			->setMaxResults($max);
		return $slides->getQuery()->getResult();
	}
}
