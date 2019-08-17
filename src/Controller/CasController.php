<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\CasService;
use AV\ActivityPubBundle\DbType\ActorType;
use AV\ActivityPubBundle\Entity\Actor;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CasController extends AbstractController
{
    /**
     * @Route("/cas/login", name="cas_login")
     */
    public function loginAction(Request $request, CasService $casService, JWTTokenManagerInterface $JWTTokenManager)
    {
        // Warning! $redirectUrl must be **double-encoded**, otherwise the $casService->forceAuthentication() method
        // will decode it and we will lose it on page reload (this route is called twice because of forceAuthentication)
        $redirectUrl = $request->query->get('redirectUrl');
        if( !$redirectUrl ) throw new \Exception('No redirectUrl found');

        // on vide les cookie pour la premiere connexion
        if (!isset($_GET['ticket'])) {
            setcookie("PHPSESSID", "", time()-3600, "/"); // delete session cookie
        }

        $isAuthenticated = $casService->forceAuthentication();

        if ($isAuthenticated) {
            $attr = $casService->getUserAttributes();

            $em = $this->getDoctrine()->getManager();

            // Check if user already logged in the application
            $userRepo = $em->getRepository(User::class);
            $user = $userRepo->findOneBy(['uuid' => $attr['uuid']]);

            // If user does not exist yet, create and persist it
            if( !$user ) {

                $actor = new Actor();
                $actor
                    ->setType(ActorType::PERSON)
                    ->setUsername($attr['name'])
                    ->setName($attr['field_first_name'] . " " . $attr['field_last_name']);

                $user = new User($actor);
                $user
                    ->setId($attr['uid'])
                    ->setUuid($attr['uuid'])
                    ->setEmail($attr['mail'])
                    ->setPassword($attr['pass']);

                // TODO persist other returned data
                //'langcode' => string 'fr' (length=2)
                //'preferred_langcode' => string 'fr' (length=2)
                //'preferred_admin_langcode' => string 'fr' (length=2)
                //'timezone' => string 'Europe/Berlin' (length=13)
                //'status' => string '1' (length=1)
                //'created' => string '1537521204' (length=10)
                //'changed' => string '1555532612' (length=10)
                //'access' => string '1555843095' (length=10)
                //'login' => string '1555532579' (length=10)
                //'init' => string 'srosset81@gmail.com' (length=19)
                //'roles' => string '' (length=0)
                //'default_langcode' => string '1' (length=1)
                //'path' => string '' (length=0)
                //'field_address' => string '{"langcode":null,"country_code":"FR","administrative_area":"","locality":"Chantilly","dependent_locality":"","postal_code":"60500","sorting_code":"","address_line1":"10 ter impasse Souchier","address_line2":"","organization":null,"given_name":null,"additional_name":null,"family_name":null}' (length=290)
                //'field_avatar' => string '{"target_id":"5","alt":"","title":"","width":"117","height":"128"}' (length=66)
                //'field_consent_gdpr' => string '{"target_id":"1","target_revision_id":"1","agreed":"1","user_id":"60534","date":"2019-04-17 22:23:32","user_id_accepted":"60534","notes":""}' (length=140)
                //'field_lat_lon' => string '{"value":"POINT(2.468857 49.195031)","geo_type":"Point","lat":49.195031,"lon":2.468857,"left":2.468857,"top":49.195031,"right":2.468857,"bottom":49.195031,"geohash":"u09zb5tvbd3n","latlon":"49.195031,2.468857"}' (length=210)
                //'field_learning360_id' => string '5ba4b635c8837d17198348cb' (length=24)
                //'field_newsletter_colibris' => string '0' (length=1)

                $em->persist($user);
                $em->flush();
            }

            // Manually generate JWT token
            // https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/7-manual-token-creation.md
            $token = $JWTTokenManager->create($user);

            // Since the redirectUrl was double-encoded, we must now decode it
            return $this->redirect(urldecode($redirectUrl) . '?token=' . $token . "&username=" . $user->getActor()->getUsername());
        }
    }
}