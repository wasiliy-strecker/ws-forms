<?php
namespace Ws\WsForms\Domain\Model;

class Order {

    /**
     * @return int
     */
    public function getLimitToShow(): int {
        return 10;
    }
}
