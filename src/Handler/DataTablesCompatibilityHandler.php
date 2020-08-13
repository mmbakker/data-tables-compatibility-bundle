<?php

namespace Apipa169\DataTablesCompatibilityBundle\Handler;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DataTablesCompatibilityHandler
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    // DataTables sends information about search and sort actions from the user. This is mapped here to a format we want to use.
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $config = $this->container->getParameter('data_tables_compatibility_config');

        // Only if Draw and columns is provided in the request and the method is GET, so we are sure it is a DataTables request
        if($request->get('draw') && $request->get('columns') && $request->getMethod() === 'GET') {
            $columns = $request->get('columns');
            $order = $request->get('order');
            $search = $request->get('search');

            // map the property names based on the configfile config/packages/data_tables_compatibility.yaml
            $mappings = $config['mapping'];
            for ($i = 0; $i < count($columns); $i++) {
                foreach ($mappings as $mapping) {
                    $columns[$i]['data'] = preg_replace('~' . $mapping['from'] . '~', $mapping['to'], $columns[$i]['data']);
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