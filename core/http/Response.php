<?php
namespace Core\http;

class Response  {
    protected $swooleRespose;
    protected $body;

    /**
     * Response constructor.
     * @param $swooleRespose
     */
    public function __construct(\Swoole\Http\Response $swooleRespose)
    {
        $this->swooleRespose = $swooleRespose;
    }

    public static function init(\Swoole\Http\Response $respose)
    {
        return new self($respose);
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body): void
    {
        $this->body = $body;
    }

    public function end()
    {
        $json_conver = ['array'];
        $body = $this->getBody();
        if (in_array(gettype($body), $json_conver)){
            $this->swooleRespose->header('Content-type','application/json');
            $this->setBody(json_encode($body));
        }
        $this->swooleRespose->write($this->getBody());
        $this->swooleRespose->end();
    }

}