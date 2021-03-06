<?php

namespace AppBundle\Repository;
use Doctrine\ORM\NoResultException;
/**
 * LigneRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LigneRepository extends \Doctrine\ORM\EntityRepository
{

   public function kpiByWeek( $startDate=null, $endDate=null,$region=null, $produit=null){
        $qb = $this->createQueryBuilder('l')->join('l.commende', 'c')->leftJoin('c.user', 'u');
          if($region!=null){
              $qb->andWhere('u.ville=:ville or u.ville is NULL')->setParameter('ville', $region);
          }
         if($startDate!=null){
           $qb->andWhere('c.date is null or c.date>=:startDate')->setParameter('startDate', new \DateTime($startDate));
          }
          if($endDate!=null){
           $qb->andWhere('c.date is null or c.date<=:endDate')->setParameter('endDate',new \DateTime($endDate));
          } 
        if($produit!=null){
           $qb->andWhere('l.produit=:produit')->setParameter('produit',$produit);
          }           
       $qb->addOrderBy('c.week','asc')
       ->select('c.weekText')
       ->addSelect('sum(l.quantite) as nombre')
       ->addGroupBy('c.week')
       ->addGroupBy('c.weekText');
         return $qb->getQuery()->getArrayResult();  
  }

}
