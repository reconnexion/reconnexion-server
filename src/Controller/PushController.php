<?php

namespace App\Controller;

use App\Service\PushService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PushController extends BaseController
{
    /**
     * @Route("/device", name="add_device", methods={"POST"})
     */
    public function addDeviceAction(Request $request, PushService $pushService)
    {
        $user = $this->getUser();
        $params = $this->parseBodyAsJson($request);

        $pushService->subscribe($user, $params['deviceToken']);

        return new Response(null, Response::HTTP_CREATED );
    }
}