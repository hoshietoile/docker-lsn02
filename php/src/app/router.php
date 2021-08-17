<?php
class Router
{
  private $request_method = null;
  private $request_url = null;

  public function __construct($method, $url)
  {
    $this->request_method = $method;
    $this->request_url = $url;
  }

  public function resolve($method, $url, $cb)
  {
    if ($method === $this->request_method && $url === $this->request_url) {
      $cb();
      exit;
    } else {
      return;
    }
  }
}
