<?php

namespace App\Controller;

use App\Entity\IncomingWebhook;
use AV\ActivityPubBundle\Service\ActivityPubService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends BaseController
{
    protected $activityPubService;

    public function __construct(ActivityPubService $activityPubService)
    {
        $this->activityPubService = $activityPubService;
    }

    /**
     * @Route("/api/{apiKey}/activity", name="api_post_activity", methods={"POST"})
     */
    public function postActivity(Request $request)
    {
        /** @var IncomingWebhook $incomingWebhook */
        $incomingWebhook = $this->getUser();
        $json = $this->parseBodyAsJson($request);

        if( !$json ) {
            throw new BadRequestHttpException("You must POST a valid JSON object to this endpoint");
        }

        $activity = $this->activityPubService->handleActivity($json, $incomingWebhook->getActor());

        return new Response(null, Response::HTTP_CREATED, ['Location' => $this->activityPubService->getObjectUri($activity)]);
    }
}