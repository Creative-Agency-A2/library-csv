<?php

namespace application\providers;

class csvProvider extends \regasen\engine\provider
{
  public function register($di)
  {
    $di->set('csv', function (&$obj, $params) {
      return new \regasen\libraries\csv\csv();
    });
  }

  public function boot($di)
  {
  }
}
