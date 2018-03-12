<?php

namespace Pwm\AdminBundle\Repository;
use Pwm\AdminBundle\Entity\Ressource;
use Pwm\AdminBundle\Entity\Info;
use AppBundle\Entity\Session;
/**
 * CommandeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommandeRepository extends \Doctrine\ORM\EntityRepository
{
 public function findOneByUserRessource(Info $info,Ressource $ressource){
 $qb =$this->createQueryBuilder('a')
       ->where('a.info=:info') ->setParameter('info', $info)->andWhere('a.ressource=:ressource') ->setParameter('ressource', $ressource);
        return   $qb->getQuery()->setMaxResults(1)->getOneOrNullResult();
 }	

  public function findOneByUserSession(Info $info,Session $session){
 $qb =$this->createQueryBuilder('a')
       ->where('a.info=:info') ->setParameter('info', $info)->andWhere('a.session=:session') ->setParameter('session', $session);
        return   $qb->getQuery()->setMaxResults(1)->getOneOrNullResult();
 }
}
