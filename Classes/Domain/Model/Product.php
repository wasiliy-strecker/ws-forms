<?php
namespace Ws\WsForms\Domain\Model;

class Product {

    /**
     * Gibt an, wie viele Produkte pro Seite standardmäßig
     * im Frontend/Backend angezeigt werden sollen.
     * * @return int
     */
    public function getLimitToShow(): int {
        return 3;
    }
}