<?php
namespace Ws\WsForms\Domain\Repository;

class UserRepository {

    public function findAll(int $limit = -1, int $offset = 0) {
        $args = [
            'meta_query' => [
                [
                    'key'     => 'wsf_created',
                    'value'   => 1,
                    'compare' => '='
                ]
            ],
            'orderby' => 'ID',
            'order'   => 'DESC'
        ];

        // WICHTIG: Hier wird die Pagination angewendet
        if ($limit > 0) {
            $args['number'] = $limit; // 'number' ist das Limit bei get_users/WP_User_Query
            $args['offset'] = $offset;
        }

        $users = get_users($args);

        // Meta Daten anhängen
        foreach ($users as $user) {
            $user->wsf_first_name = get_user_meta($user->ID, 'wsf_first_name', true);
            $user->wsf_last_name  = get_user_meta($user->ID, 'wsf_last_name', true);
        }

        return $users;
    }

    /**
     * Zählt alle User, die das Flag 'wsf_created' = 1 haben.
     *
     * @return int
     */
    public function countAll(): int {
        $args = [
            'meta_query' => [
                [
                    'key'     => 'wsf_created',
                    'value'   => 1,
                    'compare' => '='
                ]
            ],
            'fields' => 'ID', // Performance: Wir brauchen keine kompletten User-Objekte
            'number' => 1,    // Performance: Wir laden nur 1 Resultat, WP berechnet trotzdem die Gesamtanzahl
        ];

        $userQuery = new \WP_User_Query($args);

        return (int) $userQuery->get_total();
    }

	public function add(array $data) {

		$userId = wp_insert_user([
			'user_email' => $data['user']['wsf_email'],
			'user_login' => $data['user']['wsf_email'],
			'user_pass'  => wp_generate_password(),
			'role'       => 'subscriber'
		]);

		if (is_wp_error($userId)) {
			return $userId;
		}

		update_user_meta($userId, 'wsf_created', 1);
		update_user_meta($userId, 'wsf_first_name', sanitize_text_field($data['user']['wsf_first_name']));
		update_user_meta($userId, 'wsf_last_name', sanitize_text_field($data['user']['wsf_last_name']));

		do_action('wsf_after_user_created', $userId, $data);

		return $userId;
	}

    /**
     * Sucht nach Usern.
     * Strategie: Erst Suche in Standard-Feldern (Email/Login),
     * wenn leer, dann Suche in Meta-Feldern (Namen).
     */
    public function search(string $query, int $limit = -1, int $offset = 0): array {

        // --- VERSUCH 1: Standard Suche (Email, Login) ---
        $args = [
            'search'         => '*' . esc_attr($query) . '*',
            'search_columns' => ['user_email', 'user_login', 'display_name'],
            'meta_query'     => [
                [
                    'key'     => 'wsf_created',
                    'value'   => 1,
                    'compare' => '='
                ]
            ],
            'orderby' => 'ID',
            'order'   => 'DESC'
        ];

        // Pagination anwenden
        if ($limit > 0) {
            $args['number'] = $limit;
            $args['offset'] = $offset;
        }

        $userQuery = new \WP_User_Query($args);
        $results = $userQuery->get_results();

        // --- VERSUCH 2: Fallback Meta Suche (Namen) ---
        if (empty($results)) {
            $args = [
                'meta_query' => [
                    'relation' => 'AND',
                    [
                        'key'     => 'wsf_created',
                        'value'   => 1,
                        'compare' => '='
                    ],
                    [
                        'relation' => 'OR',
                        [
                            'key'     => 'wsf_first_name',
                            'value'   => $query,
                            'compare' => 'LIKE'
                        ],
                        [
                            'key'     => 'wsf_last_name',
                            'value'   => $query,
                            'compare' => 'LIKE'
                        ]
                    ]
                ],
                'orderby' => 'ID',
                'order'   => 'DESC'
            ];

            // Auch hier: Pagination anwenden!
            if ($limit > 0) {
                $args['number'] = $limit;
                $args['offset'] = $offset;
            }

            $userQuery = new \WP_User_Query($args);
            $results = $userQuery->get_results();
        }

        // Meta Daten anhängen
        foreach ($results as $user) {
            $user->wsf_first_name = get_user_meta($user->ID, 'wsf_first_name', true);
            $user->wsf_last_name  = get_user_meta($user->ID, 'wsf_last_name', true);
        }

        return $results;
    }

    /**
     * Zählt die Ergebnisse einer Suche analog zur search()-Methode.
     * Nutzt dieselbe Fallback-Logik (Erst E-Mail/Login, dann Meta-Daten).
     */
    /**
     * Zählt die Ergebnisse einer Suche analog zur search()-Methode.
     * Wichtig: Muss exakt dieselbe Logik haben (Step 1: Standard, Step 2: Meta),
     * damit die Pagination stimmt.
     */
    public function countSearch(string $query): int {

        // ---------------------------------------------------------
        // VERSUCH 1: Zähle Treffer in Standard WP-Feldern
        // ---------------------------------------------------------
        $args = [
            'search'         => '*' . esc_attr($query) . '*',
            'search_columns' => ['user_email', 'user_login', 'display_name'],
            'meta_query'     => [
                [
                    'key'     => 'wsf_created',
                    'value'   => 1,
                    'compare' => '='
                ]
            ],
            'fields' => 'ID', // Performance: Nur IDs laden
            'number' => 1,    // Performance: Limit 1 reicht, WP berechnet total trotzdem
        ];

        $userQuery = new \WP_User_Query($args);
        $total = (int) $userQuery->get_total();

        // Wenn wir hier Ergebnisse finden (z.B. 5 E-Mails passen),
        // dann ist das unsere Gesamtanzahl. Die Meta-Suche wird ignoriert (wie bei search()).
        if ($total > 0) {
            return $total;
        }

        // ---------------------------------------------------------
        // VERSUCH 2: Fallback - Zähle Treffer in Namen (Meta)
        // ---------------------------------------------------------

        // Wir setzen die Query komplett neu auf, ohne 'search' Parameter
        $args = [
            'meta_query' => [
                'relation' => 'AND',
                // Bedingung 1: Muss wsf_created sein
                [
                    'key'     => 'wsf_created',
                    'value'   => 1,
                    'compare' => '='
                ],
                // Bedingung 2: Vorname ODER Nachname
                [
                    'relation' => 'OR',
                    [
                        'key'     => 'wsf_first_name',
                        'value'   => $query,
                        'compare' => 'LIKE'
                    ],
                    [
                        'key'     => 'wsf_last_name',
                        'value'   => $query,
                        'compare' => 'LIKE'
                    ]
                ]
            ],
            'fields' => 'ID',
            'number' => 1,
        ];

        $userQuery = new \WP_User_Query($args);

        return (int) $userQuery->get_total();
    }

}