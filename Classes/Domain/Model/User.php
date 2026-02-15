<?php
namespace Ws\WsForms\Domain\Model;

class User {

    /**
     * returns limit to show frontend and backend
     *
     * @var int
     */
    protected $limitToShow = 3;

    /**
     * @var string
     */
    protected $userName = '';// example

    /**
     *
     * @return void
     */
    public function setUserName()
    {
        $this->userName = '';// example
    }

    /**
     *
     */
    public function getLimitToShow():int
    {
        return $this->limitToShow;
    }

}