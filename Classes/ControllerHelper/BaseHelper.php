<?php
namespace Ws\WsForms\ControllerHelper;

class BaseHelper {

    /**
     * @var \Ws\WsForms\Domain\Model\Base
     */
    protected $base = null;

    public function __construct() {
        $this->base = new \Ws\WsForms\Domain\Model\Base();
    }

    /**
     * @return bool
     */
    public function getIsAdmin(): bool {
        return $this->base->getIsAdmin();
    }

}
