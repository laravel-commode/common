<?php

namespace LaravelCommode\Common\Controllers;

class CommodeControllerTestSubject extends CommodeController
{
    public function getSomeMethod($id)
    {
        return func_get_args();
    }

    public function getSomeMethodResolve($id, CommodeControllerTest $test)
    {
        return func_get_args();
    }

    public function ajaxgetSomeMethodResolve($id, CommodeControllerTest $test)
    {
        return func_get_args();
    }
}
