<?php
namespace noogic\router;


class Router{
    protected $methods = [];

    public function register($method, $fn){
        $this->methods[$method] = $fn;
    }

    public function execute($method, $params = null){
        return $this->methods[$method]($params);
    }
}
