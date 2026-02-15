<?php
namespace Ws\WsForms\Domain\Repository;

use Ws\WsForms\Domain\Model\Option;

class OptionRepository {

    /**
     * Lädt die Optionen und mappt sie auf das Domain-Objekt
     */
    public function get(): Option {
        $options = new Option();
        // 2. Parameter ist der Default-Wert, falls Option noch nicht existiert
        $options->userBackendRole = get_option('wsf_option_user_backend_role', 'administrator');
        $options->userFrontendRole = get_option('wsf_option_user_frontend_role', 'subscriber');

        return $options;
    }

    /**
     * Speichert das Domain-Objekt in die WP Option Tabelle
     */
    public function update(Option $options): bool {
        $success1 = update_option('wsf_option_user_backend_role', $options->userBackendRole);
        $success2 = update_option('wsf_option_user_frontend_role', $options->userFrontendRole);

        // Gibt true zurück, wenn mindestens einer der Werte aktualisiert wurde
        return $success1 || $success2;
    }

    /**
     * Hilfsmethode um alle verfügbaren WP Rollen zu holen
     * (Für das Dropdown im Frontend)
     */
    public function getAvailableRoles(): array {
        if (!function_exists('wp_roles')) {
            return [];
        }
        return wp_roles()->get_names();
    }
}