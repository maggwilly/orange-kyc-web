<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Form\CredentialsType;
use AppBundle\Entity\AuthToken;
use AppBundle\Entity\Credentials;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use FOS\RestBundle\View\View; 
use AppBundle\Entity\PointVente;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class AppController extends Controller
{

  const AUTORISATION_HEADERS=array(
    "accept: application/json",
    "Authorization: Bearer IPJwGGcAJ33O34Lr53Eyh0MG1xxZ",
    "cache-control: no-cache",
    "content-type: application/json"
  );

  const ORANGE_SMS_URLBASE="https://api.orange.com/smsmessaging/v1/outbound/";
  const OM_TOKEN_URL = "https://api.orange.com/oauth/v2/token";

    public function indexAction()
    {   
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager();
        $region=$session->get('region');
        $startDate=$session->get('startDate');
        $endDate=$session->get('endDate');
        $produits=$em->getRepository('AppBundle:Produit')->countByProduit(null, $startDate,$endDate,$region);
        $performances=(new ArrayCollection($em->getRepository('AppBundle:PointVente')->findPerformances($startDate,$endDate,$region)))->map(function ($poinVente) use ($em,$region,$startDate,$endDate){
                 $poinVente['ventes']=$em->getRepository('AppBundle:Produit')->countByProduit($poinVente['pdvid'], $startDate,$endDate,$region);
                 if(empty($poinVente['ventes']))   
                 $poinVente['ventes']=$em->getRepository('AppBundle:Produit')->findOrderedList();
             return $poinVente;
        });
        $workedSuperviseur=$em->getRepository('AppBundle:User')->workedSuperviseur($startDate,$endDate,$region);
        $colors=array("#FF6384","#36A2EB","#FFCE56","#F7464A","#FF5A5E","#46BFBD", "#5AD3D1","#FDB45C");
        $rapports=$em->getRepository('AppBundle:Commende')->rapports($startDate,$endDate,$region);
        return $this->render('AppBundle::index.html.twig', 
          array(
            'colors'=>$colors,
            'performances'=>$performances,
            'produits'=>$produits,
            'rapports'=>$rapports,
            'workedSuperviseur'=>$workedSuperviseur
          ));
    }
    


    public function docsAction()
    {   
        return $this->render('commende/docs.html.twig');
    }


    public function courbesAction()
    {   
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager();
        $region=$session->get('region');
        $startDate=$session->get('startDate','first day of this month');
        $endDate=$session->get('endDate', 'last day of this month');
        $kpiByWork=$em->getRepository('AppBundle:Ligne')->kpiByWeek($startDate,$endDate,$region,1);
        return $this->render('commende/courbes.html.twig', array('kpiByWeek' =>$kpiByWork ));
    }


    public function kpiAction()
    {   
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager();
        $region=$session->get('region');
        $startDate=$session->get('startDate');
        $endDate=$session->get('endDate',);
        $countByProduit= $em->getRepository('AppBundle:Produit')->countByProduit(null,$startDate,$endDate,$region);
        return $this->render('AppBundle::part/kpi.html.twig', 
          array(
            'produits'=>$countByProduit,
          ));
    }

    public function setRegionAction(Request $request)
    {
        $region=$request->query->get('region');
         $session = $this->getRequest()->getSession();
        $session->set('region',$region);
       $referer = $this->getRequest()->headers->get('referer');   
         return new RedirectResponse($referer);
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
        $authToken=AuthToken::create($user);
        $em->persist($authToken);
        $em->flush();
        return $authToken->getUser();
    }


    /**
     * @Rest\View()
     */
  public function sendSmsAction(Request $request)
    {
     $messagepath = $this->get('kernel')->getRootDir(). "/../web/sms/message.txt";
     $path = $this->get('kernel')->getRootDir(). "/../web/sms/centre-vague-3.xlsx";
     $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject($path);
     $secteurs= $objPHPExcel->getSheet(0);
     $highestRow  = $secteurs->getHighestRow(); 
     $msg=file_get_contents($messagepath); 
     $logPath = $this->get('kernel')->getRootDir(). "/../web/sms/centre-vague-3.txt";
     $mode = (!file_exists($logPath)) ? 'w':'a';
    $logfile = fopen($logPath, $mode);

    for ($row = 0; $row <= $highestRow; ++$row) {
             $numeroCell = $secteurs->getCellByColumnAndRow(0, $row)->getFormattedValue();
             $numero='+237'.$numeroCell;
             //$contacts=urlencode($numero);
          $url='https://api.orange.com/smsmessaging/v1/outbound/tel%3A%2B2370000/requests';  
          $data = array('outboundSMSMessageRequest' => 
            array(
            'address' => "tel:".$numero, 
            'senderAddress' => "tel:+2370000",
            'outboundSMSTextMessage' => array('message' => $msg ),
            'senderName' => "NYA"
              ));
         $res = $this->sendOrGetData($url,$data,'POST',false, self::AUTORISATION_HEADERS); 
         $date = date("Y-m-d H:i:s");   
         fwrite($logfile, "\r\n". $date.' - '. $res); 
         if ($row%5==0) {
            sleep(5);  
         }       
     }
     fclose($logfile);
    $content = file_get_contents($logPath);
    $response = new Response();
    $response->headers->set('Content-Type', 'mime/type');
    $response->headers->set('Content-Disposition', 'attachment;filename="centre-vague-3.txt"');
    $response->setContent($content); 
       return $response;  
    }


 public function sendOrGetData($url,$data,$costum_method,$json_decode=true,$headers=array())
    {    $content ='';
        if(!is_null($data))
           $content = json_encode($data);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST , $costum_method);
        if(!is_null($data))
            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ( $status != 200 ) {}
        curl_close($curl);
        $response = json_decode($json_response, true);
        return $json_decode?$response:$json_response;
    }


    /**
     * @Rest\View()
     */
    public function getTokenAction()
    {
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt_array($curl, array(
  CURLOPT_URL => self::OM_TOKEN_URL,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 120,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "grant_type=client_credentials",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic ZHlaN0Zna29qN3YyTDRFZE1WeHdMRE82TzBqU3l1NHo6Sk14a3czMng3ZVdNbFd1Wg=="
  ),
));

$json_response = curl_exec($curl);
$response = json_decode($json_response, true);
  return $response;
}

}