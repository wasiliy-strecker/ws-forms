<?php
namespace Ws\WsForms\Domain\Repository;

class AddressRepository {

	public function findAllByUserId(int $userId): array {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wsf_addresses';

		$results = $wpdb->get_results(
			$wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d ORDER BY id DESC", $userId)
		);

		return $results ?: [];
	}

    /**
     * Holt die Anzahl der Adressen nur für eine spezifische Liste von User-IDs.
     * Performance-Optimiert für Pagination (lädt nur die Counts der sichtbaren User).
     *
     * @param array $userIds Array mit User IDs (int)
     * @return array [userId => count]
     */
    public function getCountByUserIDs(array $userIds): array {
        // Falls keine IDs übergeben wurden, leeres Array zurückgeben
        if (empty($userIds)) {
            return [];
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wsf_addresses';

        // 1. Platzhalter für die SQL IN(...) Klausel generieren (%d, %d, %d)
        $placeholders = implode(',', array_fill(0, count($userIds), '%d'));

        // 2. Query vorbereiten
        $sql = "SELECT user_id, COUNT(*) as count 
                FROM $table_name 
                WHERE user_id IN ($placeholders) 
                GROUP BY user_id";

        // 3. Ausführen: Wir nutzen den Spread-Operator (...), um das Array als einzelne Argumente zu übergeben
        $results = $wpdb->get_results(
            $wpdb->prepare($sql, ...$userIds),
            OBJECT_K // Indexiert das Ergebnis-Objekt direkt nach 'user_id'
        );

        // 4. Ergebnis normalisieren: Sicherstellen, dass auch User mit 0 Adressen enthalten sind
        $counts = [];
        foreach ($userIds as $id) {
            // Wenn im Resultat vorhanden, nimm den Count, sonst 0
            $counts[$id] = isset($results[$id]) ? (int) $results[$id]->count : 0;
        }

        return $counts;
    }

	public function add($userId, $data) {
		global $wpdb;
		return $wpdb->insert(
			$wpdb->prefix . 'wsf_addresses',
			[
				'user_id' => $userId,
				'street'  => sanitize_text_field($data['street'] ?? ''),
				'zip'     => sanitize_text_field($data['zip'] ?? ''),
				'city'    => sanitize_text_field($data['city'] ?? ''),
				'country' => sanitize_text_field($data['country'] ?? 'DE'),
			]
		);
	}

	/**
	 * Löscht eine Adresse, sofern sie dem richtigen User gehört.
	 */
	public function delete(int $addressId, int $userId) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wsf_addresses';

		return $wpdb->delete(
			$table_name,
			[
				'id'      => $addressId,
				'user_id' => $userId
			],
			['%d', '%d']
		);
	}

	/**
	 * Aktualisiert eine bestehende Adresse.
	 */
	public function update(int $addressId, int $userId, array $data) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wsf_addresses';

		$updateData = [
			'street'  => sanitize_text_field($data['street'] ?? ''),
			'zip'     => sanitize_text_field($data['zip'] ?? ''),
			'city'    => sanitize_text_field($data['city'] ?? ''),
			'country' => sanitize_text_field($data['country'] ?? 'DE'),
		];

		return $wpdb->update(
			$table_name,
			$updateData,
			[
				'id'      => $addressId,
				'user_id' => $userId
			],
			['%s', '%s', '%s', '%s'], // Formate der Daten
			['%d', '%d']              // Formate der Where-Klausel
		);
	}

	/**
	 * Holt die Anzahl der Adressen für alle User in einem Array [userId => count]
	 */
	public function getCountGroupedByUser(): array {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wsf_addresses';

		$results = $wpdb->get_results(
			"SELECT user_id, COUNT(*) as count FROM $table_name GROUP BY user_id",
			OBJECT_K
		);

		// Umwandeln in ein flaches Array [ID => Count]
		$counts = [];
		if ($results) {
			foreach ($results as $userId => $row) {
				$counts[$userId] = (int) $row->count;
			}
		}

		return $counts;
	}

}