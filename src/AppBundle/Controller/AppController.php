<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Form\CredentialsType;
use AppBundle\Entity\AuthToken;
use AppBundle\Entity\Credentials;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use FOS\RestBundle\View\View; 
/**
 * Etape controller.
 *
 */
class AppController extends Controller
{
    /**
     * Lists all etape entities.
     *
     */
    public function indexAction()
    {   
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager();
        $region=$session->get('region');
        $startDate=$session->get('startDate',date('Y').'-01-01');
        $endDate=$session->get('endDate', date('Y').'-12-31');

        $countAndCashByWeek= $em->getRepository('AppBundle:Ligne')->countAndCashByWeek($startDate,$endDate);
        $countAndCashByMonth= $em->getRepository('AppBundle:Ligne')->countAndCashByMonth($startDate,$endDate);
        $countAndCash= $em->getRepository('AppBundle:Ligne')->countAndCash($startDate,$endDate);
        $workedDays=$em->getRepository('AppBundle:Commende')->workedDays($startDate,$endDate,true);
        $fiedSoldiersCount=$em->getRepository('AppBundle:PointVente')->fiedSoldiersCount($startDate,$endDate);
        $produits=$em->getRepository('AppBundle:Produit')->produits($startDate,$endDate);
        $totalWorkedDays=$em->getRepository('AppBundle:Commende')->totalWorkedDays($startDate,$endDate);
        $workedSuperviseur=$em->getRepository('AppBundle:User')->workedSuperviseur($startDate,$endDate);
        $colors=array("#FF6384","#36A2EB","#FFCE56","#F7464A","#FF5A5E","#46BFBD", "#5AD3D1","#FDB45C");

        return $this->render('AppBundle::index.html.twig', 
          array(
            'colors'=>$colors,
            'countAndCash'=>$countAndCash[0],
            'countAndCashByWeek'=>$countAndCashByWeek,
            'countAndCashByMonth'=>$countAndCashByMonth,
            'workedDays'=>$workedDays,
            'fiedSoldiersCount'=>$fiedSoldiersCount,
            'produits'=>$produits,
            'totalWorkedDays'=>$totalWorkedDays,
            'workedSuperviseur'=>$workedSuperviseur,

          ));
    }

public function getWorkingDays($startDate, $endDate)
{
    $begin = strtotime($startDate);
    $end   = strtotime($endDate);
    if ($begin > $end) {

        return 0;
    } else {
        $no_days  = 0;
        while ($begin <= $end) {
            $what_day = date("N", $begin);
            if (!in_array($what_day, [6,7]) ) // 6 and 7 are weekend
                $no_days++;
            $begin += 86400; // +1 day
        };

        return $no_days;
    }
}

    public function setPeriodeAction(Request $request)
    {
  
        $region=$request->request->get('region');
        $periode= $request->request->get('periode');
        $dates = explode(" - ", $periode);
        $startDate=$dates[0];
        $endDate=$dates[1];
        $format = 'd/m/Y';
        $startDate= \DateTime::createFromFormat($format, $dates[0]);
        $endDate= \DateTime::createFromFormat($format, $dates[1]);
        $session = $this->getRequest()->getSession();
        $session->set('region',$region);
        $session->set('startDate',$startDate->format('Y-m-d'));
        $session->set('endDate',$endDate->format('Y-m-d'));
        $session->set('periode',$periode);
        $session->set('end_date_formated',$endDate->format('d/m/Y'));
        $session->set('start_date_formated',$startDate->format('d/m/Y'));
       $referer = $this->getRequest()->headers->get('referer');   
         return new RedirectResponse($referer);
    }





    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"user"})
     */
    public function postAuthTokensAction(Request $request)
    {
        $credentials = new Credentials();
        $form = $this->createForm( CredentialsType::class, $credentials);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
         $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneByUsername($credentials->getLogin());

        if (!$user) { // L'utilisateur n'existe pas
            return $this->invalidCredentials();
        }
       /** $encoder = $this->get('security.password_encoder');
        $isPasswordValid = $encoder->isPasswordValid($user, $credentials->getPassword());

        if (!$isPasswordValid) { // Le mot de passe n'est pas correct
            return $this->invalidCredentials();
        }*/
        $authToken=AuthToken::create($user);
        $em->persist($authToken);
        $em->flush();
        return $authToken->getUser();
    }


}