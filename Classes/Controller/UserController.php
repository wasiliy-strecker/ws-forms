<?php
namespace Ws\WsForms\Controller;

class UserController extends BaseController {

    /**
     * @var \Ws\WsForms\Domain\Model\User
     */
    protected $user = null;

    /**
     * @var \Ws\WsForms\ControllerHelper\UserHelper
     */
    protected $userHelper = null;

    public function __construct() {
        parent::__construct();
        $this->user = new \Ws\WsForms\Domain\Model\User();
        $this->userHelper = new \Ws\WsForms\ControllerHelper\UserHelper();
    }

    public function listBeAction($request = null) {
        $params = $request instanceof \WP_REST_Request ? $this->getParams($request) : $_GET;
        $data = $this->userHelper->getPreparedUserListData($params);
        foreach ($data as $key => $value) {
            $this->assign($key, $value);
        }

        if ($request instanceof \WP_REST_Request) {
            $html = $this->renderPartial('User/TableRows');
            return new \WP_REST_Response(['html' => $html, 'pagination' => ''], 200);
        }

        return $this->renderView('User/List');
    }

    public function listFeAction($request = null) {
        $params = $request instanceof \WP_REST_Request ? $this->getParams($request) : $_GET;
        $data = $this->userHelper->getPreparedUserListData($params);
        foreach ($data as $key => $value) {
            $this->assign($key, $value);
        }

        if ($request instanceof \WP_REST_Request) {
            $html = $this->renderPartial('User/ListRows');
            $pagination = $this->renderPartial('User/Pagination');
            return new \WP_REST_Response(['html' => $html, 'pagination' => $pagination], 200);
        }

        return $this->renderView('User/List');
    }

    public function newBeAction(): string
    {
        $user = new \stdClass();
        $user->ID = 0;
        $user->user_email = '';
        $user->wsf_first_name = '';
        $user->wsf_last_name = '';
        $this->assign('user', $user);
        $this->assign('headline', 'Neuen Benutzer anlegen');

        return $this->renderView('User/New');
    }

    public function newFeAction(): string
    {
        $user = new \stdClass();
        $user->ID = 0;
        $user->user_email = '';
        $user->wsf_first_name = '';
        $user->wsf_last_name = '';
        $this->assign('user', $user);
        $this->assign('headline', 'Registrieren');

        return $this->renderView('User/New');
    }

    public function createBeAction(\WP_REST_Request $request): \WP_REST_Response
    {
        return $this->createAction($request);
    }

    public function createFeAction(\WP_REST_Request $request): \WP_REST_Response
    {
        return $this->createAction($request);
    }

	public function createAction(\WP_REST_Request $request): \WP_REST_Response
    {
		$params = $this->getParams($request);

		if (empty($params['user']['wsf_email']) || !is_email($params['user']['wsf_email'])) {
			return new \WP_REST_Response(['message' => 'Gültige E-Mail fehlt!'], 400);
		}

		if (email_exists($params['user']['wsf_email'])) {
			return new \WP_REST_Response(['message' => 'E-Mail existiert bereits!'], 400);
		}

		$userRepository = new \Ws\WsForms\Domain\Repository\UserRepository();
		$userId = $userRepository->add($params);

		if (is_wp_error($userId)) {
			$result = $userId;
			return new \WP_REST_Response(['message' => $result->get_error_message()], 500);
		}

		if (!empty($params['newAddress']) && is_array($params['newAddress'])) {
			$addressRepository = new \Ws\WsForms\Domain\Repository\AddressRepository();

			foreach ($params['newAddress'] as $addressData) {
				$addressRepository->add($userId, $addressData);
			}
		}

        if ($this->base->getIsAdmin()) {
            $redirectUrl = admin_url('admin.php?page=ws_forms_users&message=created');
        }else{
            $requestedRedirect = $params['current_url'] ?? '';
            $safeUrl = wp_validate_redirect($requestedRedirect, home_url());
            $redirectUrl = add_query_arg([
                'wsf_action'  => 'list',
                'message' => 'created'
            ], $safeUrl);
        }

		return new \WP_REST_Response([
			'message' => 'Benutzer erfolgreich angelegt!',
			'redirect' => $redirectUrl
		], 200);
	}

    public function editBeAction(): string
    {
        return $this->editAction();
    }

    public function editFeAction(): string
    {
        return $this->editAction();
    }

    public function editAction(): string
    {
		$userId = get_query_var('wsf_id') ?: (isset($_GET['wsf_id']) ? $_GET['wsf_id'] : 0);
		$userId = intval($userId);

		// Prüfen, ob der User existiert und ob er über dein Plugin erstellt wurde
		$user = get_user_by('id', $userId);
		$isWsfUser = get_user_meta($userId, 'wsf_created', true);
		if (!$user || !$isWsfUser) {
			wp_die(__('Benutzer nicht gefunden oder keine Berechtigung.', 'ws-forms'));
		}

// Meta-Daten für die View an das Objekt hängen
		$user->wsf_first_name = get_user_meta($userId, 'wsf_first_name', true);
		$user->wsf_last_name  = get_user_meta($userId, 'wsf_last_name', true);

		// NEU: Adressen aus der Datenbank holen
		$addressRepository = new \Ws\WsForms\Domain\Repository\AddressRepository();
		$addresses = $addressRepository->findAllByUserId($userId);

		$this->assign('isEdit', true);
		$this->assign('headline', 'Benutzer bearbeiten');
		$this->assign('user', $user);
		$this->assign('addresses', $addresses); // Adressen an die View übergeben

        return $this->renderView('User/Edit');
	}

 public function updateBeAction(\WP_REST_Request $request): \WP_REST_Response
 {
     return $this->updateAction($request);
 }

 public function updateFeAction(\WP_REST_Request $request): \WP_REST_Response
 {
     return $this->updateAction($request);
 }

 public function updateAction(\WP_REST_Request $request): \WP_REST_Response
    {
		$params = $this->getParams($request);
		$userId = $params['id'];
		$userData = $params['user'] ?? [];

		if (!current_user_can('edit_user', $userId) || !get_user_meta($userId, 'wsf_created', true)) {
			return new \WP_REST_Response(['message' => 'Nicht autorisiert oder User nicht gefunden.'], 403);
		}

		// optional if email will be sent
		/*
		if (empty($userData['wsf_email']) || !is_email($userData['wsf_email'])) {
			return new \WP_REST_Response(['message' => 'Gültige E-Mail erforderlich.'], 400);
		}

		$existingUser = get_user_by('email', $userData['wsf_email']);
		if ($existingUser && $existingUser->ID !== (int)$userId) {
			return new \WP_REST_Response(['message' => 'Diese E-Mail wird bereits von einem anderen Account verwendet.'], 400);
		}
		$updatedId = wp_update_user([
			'ID'         => $userId,
			'user_email' => sanitize_email($userData['wsf_email'])
		]);
		if (is_wp_error($updatedId)) {
			return new \WP_REST_Response(['message' => $updatedId->get_error_message()], 500);
		}*/

		update_user_meta($userId, 'wsf_first_name', sanitize_text_field($userData['wsf_first_name']));
		update_user_meta($userId, 'wsf_last_name', sanitize_text_field($userData['wsf_last_name']));

		// 1. Verarbeitung der gelöschten Adressen
		if (!empty($params['removedAddresses']) && is_array($params['removedAddresses'])) {
			$addressRepository = new \Ws\WsForms\Domain\Repository\AddressRepository();
			foreach ($params['removedAddresses'] as $addressId) {
				$addressId = intval($addressId);
				if ($addressId > 0) {
					$addressRepository->delete($addressId, $userId);
				}
			}
		}

		// 2. Verarbeitung von bestehenden Adressen (Updates)
		if (!empty($params['address']) && is_array($params['address'])) {
			$addressRepository = new \Ws\WsForms\Domain\Repository\AddressRepository();
			foreach ($params['address'] as $addressId => $addressData) {
				$addressId = intval($addressId);
				if ($addressId > 0) {
					$addressRepository->update($addressId, $userId, $addressData);
				}
			}
		}

		// 3. Verarbeitung von neuen Adressen (Inserts)
		if (!empty($params['newAddress']) && is_array($params['newAddress'])) {
			$addressRepository = new \Ws\WsForms\Domain\Repository\AddressRepository();
			foreach ($params['newAddress'] as $addressData) {
				// Nur hinzufügen, wenn mindestens ein Feld ausgefüllt ist
				if (!empty(array_filter($addressData))) {
					$addressRepository->add($userId, $addressData);
				}
			}
		}

		return new \WP_REST_Response([
			'message' => 'Benutzer erfolgreich aktualisiert!',
			'redirect' => admin_url('admin.php?page=ws_forms_users&message=updated')
		], 200);
	}

	public function checkEmailAction(\WP_REST_Request $request): \WP_REST_Response
    {
		$email = $request->get_param('email');

		if (empty($email) || !is_email($email)) {
			return new \WP_REST_Response(['valid' => false, 'message' => 'Ungültige E-Mail'], 400);
		}

		$exists = email_exists($email);

		return new \WP_REST_Response([
			'exists' => (bool)$exists,
			'message' => $exists ? 'E-Mail ist bereits vergeben.' : 'E-Mail ist frei.'
		], 200);
	}

    public function loginAction($atts = []): string
    {
        // Wir übergeben den Status und das User-Objekt an das Template
        $this->assign('is_logged_in', is_user_logged_in());
        $this->assign('current_user', wp_get_current_user());

        return $this->renderView('User/Login');
    }

    /**
     * Verarbeitet den Login-Versuch und erstellt die Session.
     */
    public function loginCreateAction(\WP_REST_Request $request) {
        $params = $this->getParams($request);
        $creds = [
            'user_login'    => $params['login'] ?? '',
            'user_password' => $params['password'] ?? '',
            'remember'      => true
        ];

        // 1. Authentifizierung via WordPress Core
        $user = wp_signon($creds, is_ssl());

        if (is_wp_error($user)) {
            return new \WP_Error('wsf_login_failed', 'Ungültige Anmeldedaten.', ['status' => 401]);
        }

        // 2. Security Check: Darf dieser User unser Plugin nutzen?
        $isWsfUser = get_user_meta($user->ID, 'wsf_created', true);

        if (!$isWsfUser) {
            wp_logout();
            return new \WP_Error('wsf_access_denied', 'Keine Berechtigung für diesen Bereich.', ['status' => 403]);
        }

        // 3. Sicherer Redirect
        $requestedUrl = $params['current_url'] ?? home_url();
        $safeUrl = wp_validate_redirect($requestedUrl, home_url());
        $redirectUrl = add_query_arg('login', 'success', $safeUrl);

        return new \WP_REST_Response([
            'message'  => 'Anmeldung erfolgreich. Sie werden weitergeleitet...',
            'redirect' => esc_url_raw($redirectUrl)
        ], 200);
    }


}