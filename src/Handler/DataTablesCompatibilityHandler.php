<?php

namespace Apipa169\DataTablesCompatibility\Handler;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class DataTablesCompatibilityHandler
{
    // DataTables sends information about search and sort actions from the user. This is mapped here to a format we want to use.
    public function onKernelRequest(RequestEvent $event)
    {
        // Array of names to be replaced, needs to be in yaml file for example to make re-use easier as this is service specific
        $nameReplace = [
            // 'name_in_datatables' => 'new_name'
            '_id' => 'id',
            'management' => 'management_ip',
            'equipment' => 'device_type_name',
            'equipment_id' => 'device_type_id',
            'manufacturer' => 'vendor_name',
        ];

        $request = $event->getRequest();

        // Only if Draw and columns is provided in the request and the method is GET, so we are sure it is a DataTables request
        if($request->get('draw') && $request->get('columns') && $request->getMethod() === 'GET') {
            $columns = $request->get('columns');
            $order = $request->get('order');
            $search = $request->get('search');

            // map names of fields in "data"
            for ($i = 0; $i < count($columns); $i++) {
                if (isset($nameReplace[$columns[$i]['data']])) {
                    $columns[$i]["data"] = $nameReplace[$columns[$i]['data']];
                }
            }

            $oderByName = $columns[$order[0]['column']]['data'] ?? '';
            $orderDirection = $order[0]['dir'] ?? 'asc';
            $searchValue = $search['value'] ?? '';

            // set path params
            $request->query->set('order_by', $oderByName);
            $request->query->set('order_direction', $orderDirection);
            $request->query->set('quick_search', $searchValue);
        }
    }

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