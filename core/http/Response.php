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
        $this->setHeader('Content-Type','text/plan;charset=utf8');
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

    public function setHeader($key,$val)
    {
        $this->swooleRespose->header($key,$val);
    }

    public function writeHttpStatus($code)
    {
        $this->swooleRespose->status($code);
    }
    public function writeHtml($html)
    {
        $this->swooleRespose->write($html);
    }

    public function redirect($url,$code=301)
    {
        $this->writeHttpStatus($code);
        $this->setHeader('Location ',$url);
    }

    public function end()
    {
        $json_convert=['array',"object"];
        $body = $this->getBody();
        if (in_array(gettype($body), $json_convert)){
            $this->setHeader('Content-type','application/json');
            $this->swooleRespose->write(json_encode($body));
        }else{
            if ($body) {
                $this->swooleRespose->write($body);
            }
        }
        $this->swooleRespose->end();
    }

}