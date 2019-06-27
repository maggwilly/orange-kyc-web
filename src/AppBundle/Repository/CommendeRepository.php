<?php

namespace AppBundle\Repository;
use AppBundle\Entity\PointVente; 
use AppBundle\Entity\User; 
use Doctrine\ORM\NoResultException;
/**
 * CommendeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommendeRepository extends \Doctrine\ORM\EntityRepository
{
	  	 public function findByPointVente(PointVente $pointVente){
           $qb = $this->createQueryBuilder('c')
           ->where('c.pointVente=:pointVente')
           ->andWhere('c.date is null or c.date>=:startDate')
           ->setParameter('startDate', new \DateTime('first day of this month'))
           ->setParameter('pointVente', $pointVente)
           ->orderby('c.date','asc');
         return $qb->getQuery()->getResult();  
  }

  	  	public function findList(User $user=null, PointVente $pointVente=null,$startDate=null, $endDate=null){
           $qb = $this->createQueryBuilder('c')->join('c.pointVente','p');
           if($pointVente!=null){
           $qb ->andWhere('c.pointVente=:pointVente')->setParameter('pointVente', $pointVente);
            }
           if($user!=null){
           $qb ->andWhere('p.user=:user')->setParameter('user', $user);
            }            
             if($startDate!=null){
           $qb->andWhere('c.date is null or c.date>=:startDate')->setParameter('startDate', new \DateTime($startDate));
          }
          if($endDate!=null){
           $qb->andWhere('c.date is null or c.date<=:endDate')->setParameter('endDate',new \DateTime($endDate));
          }
         return $qb->getQuery()->getResult();  
  }


    public function findByInsidentList($insident,$startDate=null, $endDate=null){
           $qb = $this->createQueryBuilder('c');
           if($insident!=null){
           $qb ->andWhere('c.typeInsident=:typeInsident')->setParameter('typeInsident', $insident);
            }
             if($startDate!=null){
           $qb->andWhere('c.date is null or c.date>=:startDate')->setParameter('startDate', new \DateTime($startDate));
          }
          if($endDate!=null){
           $qb->andWhere('c.date is null or c.date<=:endDate')->setParameter('endDate',new \DateTime($endDate));
          }
         return $qb->getQuery()->getResult();  
  }


      public  function rapports($startDate=null, $endDate=null){
        $qb = $this->createQueryBuilder('c');
         if($startDate!=null){
              $qb->andWhere('c.date is null or c.date>=:startDate')->setParameter('startDate', new \DateTime($startDate));
          }
          if($endDate!=null){
             $qb->andWhere('c.date is null or c.date<=:endDate')->setParameter('endDate',new \DateTime($endDate));
          }     
         $qb->select('c.typeInsident')
         ->addSelect('count(c.id) as nombre')
         ->addGroupBy('c.typeInsident');
           return $qb->getQuery()->getArrayResult(); 
  } 

 


    public   function workedDays($startDate=null, $endDate=null,$all=false){

        $qb = $this->createQueryBuilder('c')
        ->join('c.pointVente','p')
        ->join('p.user','u')
        ->leftJoin('c.lignes','l');
         if($startDate!=null){
              $qb->andWhere('c.date is null or c.date>=:startDate')->setParameter('startDate', new \DateTime($startDate));
          }
          if($endDate!=null){
             $qb->andWhere('c.date is null or c.date<=:endDate')->setParameter('endDate',new \DateTime($endDate));
          }     
         $qb->select('p.id')
         ->addSelect('p.nom')
         ->addSelect('p.telephone')
         ->addSelect('u.id as idsup')
         ->addSelect('u.nom as superviseur')
         ->addSelect('sum(l.quantite) as nombre')
         ->addSelect('count(DISTINCT c.date) as nombrejours')
         ->addGroupBy('p.id')
         ->addGroupBy('p.nom')
         ->addGroupBy('p.telephone')
         ->addGroupBy('u.nom')
         ->addGroupBy('u.id');
          if (!$all) 
           return $qb->getQuery()->setMaxResults(11)->getArrayResult();
        return $qb->getQuery()->getArrayResult(); 
  } 

    public   function totalWorkedDays($startDate=null, $endDate=null){

        $qb = $this->createQueryBuilder('c');
         if($startDate!=null){
              $qb->andWhere('c.date is null or c.date>=:startDate')->setParameter('startDate', new \DateTime($startDate));
          }
          if($endDate!=null){
             $qb->andWhere('c.date is null or c.date<=:endDate')->setParameter('endDate',new \DateTime($endDate));
          }     
   try {
    $qb->select('count(DISTINCT c.date) as nombrejours');
         return $qb->getQuery()->getSingleScalarResult();  
   } catch (NoResultException $e) {
        return 0;
     }
  } 
  
  public   function isThere($id, $startDate){
        $qb = $this->createQueryBuilder('c')->join('c.pointVente','p')
        ->andWhere('p.id=:id')->setParameter('id', $id)
        ->andWhere('c.date=:startDate')->setParameter('startDate', new \DateTime($startDate));     
   try {
    $qb->select('count(DISTINCT c.date) as nombre');
         return $qb->getQuery()->getSingleScalarResult();  
   } catch (NoResultException $e) {
        return 0;
     }
  } 

}
