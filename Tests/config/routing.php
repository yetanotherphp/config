<?php

$adminRoutes = $this->import('routes/admin');
$siteRoutes = $this->import('routes/site');
$routes = array_merge($adminRoutes, $siteRoutes);

return array(
    'namespace' => 'My\\Namespace',
    'routers' => $routes
);
