<?php
namespace Ws\WsForms\Controller;

class AddressController extends BaseController {

    public function listAction() {
        // Beispielhafte Adressdaten
        $addresses = [
            ['city' => 'Berlin', 'zip' => '10115', 'street' => 'Friedrichstraße 1'],
            ['city' => 'München', 'zip' => '80331', 'street' => 'Marienplatz 2']
        ];

        $this->assign('addresses', $addresses);
        $this->assign('headline', 'Adressverwaltung');

        // Dein gewünschter Template-Pfad
        echo $this->renderView('Address/List');
    }

    public function newAction() {
        echo '';
    }

    public function editAction() {
        echo '';
    }
}