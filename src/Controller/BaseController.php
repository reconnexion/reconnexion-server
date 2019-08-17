<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BaseController extends AbstractController
{
    protected function parseBodyAsJson(Request $request): ?array
    {
        $content = $request->getContent();

        return !empty($content)
            ? json_decode($content, true)
            : [];
    }
}