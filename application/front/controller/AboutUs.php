<?php

namespace app\front\controller;

class AboutUs extends Base
{


    public function initialize()
    {
        parent::initialize();
    }

    public function index()
    {
        return $this->fetch();
    }
}
