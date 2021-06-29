<?php

namespace Lib;

class HTTPResponse extends ApplicationComponent
{
    protected $response;

    public function addHeader($header)
    {
        header($header);
    }

    // public function redirect($location)
    // {
    //     header('Location: ' . $location);
    //     exit;
    // }

    public function redirect404($error)
    {
        $this->response = get_class($error) . ' : ' . $error->getMessage() . ' in file ' . $error->getFile() . ' : ' . $error->getLine();

        $this->addHeader('HTTP/1.0 404 Not Found');
        $this->send();
    }

    public function setResponseBody($response)
    {
        $this->addHeader("Content-type: JSON");

        // $this->response = json_encode($response, JSON_FORCE_OBJECT);
        $this->response = json_encode($response, JSON_PRETTY_PRINT);
    }

    public function send()
    {
        // Actuellement, cette ligne a peu de sens dans votre esprit.
        // Promis, vous saurez vraiment ce qu'elle fait d'ici la fin du chapitre
        // (bien que je suis sûr que les noms choisis sont assez explicites !).
        // var_dump($this->response);
        // exit;

        exit($this->response);
    }


    // Changement par rapport à la fonction setcookie() : le dernier argument est par défaut à true
    public function setCookie($name, $value = '', $expire = 0, $httpOnly,  $path = '/', $domain = null, $secure = true)
    {
        //for the defference between client side and server side
        $oneDay = 60 * 60 * 24;

        setcookie($name, $value, $expire + $oneDay, $path, $domain, $secure, $httpOnly);
    }

    public function setResponseCookies(array $cookies)
    {

        foreach ($cookies as $cookie) {
            // echo $cookie['name'] . ' : ';
            // echo $cookie['expire'];
            // // echo date('Y/m/d H:i:s', $cookie['expire']);
            // echo ' _____ ';
            // $cookie['expire'] = 1640006563;
            $this->setCookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['httpOnly']);
        }
    }
}