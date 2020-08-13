<?php

namespace Apipa169\DataTablesCompatibility\Handler;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class DataTablesDrawHandler
{
    // DataTables sends a ‘draw’ field that it uses to check ordering of requests,
    // this value is return as is and is specific to DataTables.
    public function onKernelResponse(ResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        // Only if Draw is provided in the request and the status code is 200
        if($request->get('draw') && $request->getMethod() === 'GET' && $response->getStatusCode() === Response::HTTP_OK) {
            // decode the json response in order to add draw to the 'new' response
            $content = json_decode($response->getContent(), true);

            // Add draw via a cast to int to prevent Cross Site Scripting (XSS) attacks as advised in the Datatable docs
            $content['draw'] = (int) $request->get('draw');

            // Set the response content with the new data
            $response->setContent(json_encode($content));
        }
    }
}