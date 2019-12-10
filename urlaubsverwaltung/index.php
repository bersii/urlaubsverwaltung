<?php
    use classes\request\HttpRequest;
    use classes\response\HttpResponse;
    use classes\commands\FrontController;
    use classes\filter\SessionAuthFilter;

    // Konfigurations-Datei einbinden:
    require_once './config/config.php';

    // Pfade für Linux anpassen:
    function autoload($className){
        $className = ltrim($className, '\\');
        $fileName  = '';
        $namespace = '';
        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            // Austausch des Backslash: Linux braucht / und Windows \
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        require $fileName;
    }
    spl_autoload_register('autoload');

    $request = new HttpRequest();
    $response = new HttpResponse();
    $controller = new FrontController('classes\commands', START_LOGGED_OUT);
    $filter = new SessionAuthFilter();

    $controller->addPreFilter($filter);
    $controller->handleRequest($request, $response);
?>
