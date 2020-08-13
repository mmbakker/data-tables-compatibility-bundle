<?php

namespace Apipa169\DataTablesCompatibilityBundle\Handler;

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
}