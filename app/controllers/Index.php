<?php
use Yaf\Controller_Abstract;

class IndexController extends Controller_Abstract
{

    function indexAction()
    {
        $this->redirect('/doc');
        return false;
    }
}