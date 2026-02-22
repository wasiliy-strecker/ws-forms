<?php
namespace Ws\WsForms\ControllerHelper;

use Ws\WsForms\Domain\Repository\UserRepository;
use Ws\WsForms\Domain\Repository\AddressRepository;

class UserHelper extends BaseHelper {

    /**
     * @var \Ws\WsForms\Domain\Model\User
     */
    protected $userModel = null;

    public function __construct() {
        parent::__construct();
        $this->userModel = new \Ws\WsForms\Domain\Model\User();
    }

    /**
     * @param array $params
     * @return array
     */
    public function getPreparedUserListData($params): array {
        $searchQuery = !empty($params['wsf_search']) ? sanitize_text_field($params['wsf_search']) : '';
        $currentPage = isset($params['wsf_page']) ? max(1, intval($params['wsf_page'])) : 1;
        $limit = $this->userModel->getLimitToShow();
        $offset = ($currentPage - 1) * $limit;

        $repository = new UserRepository();

        if (!empty($searchQuery)) {
            $users = $repository->search($searchQuery, $limit, $offset);
            $totalUsers = $repository->countSearch($searchQuery);
        } else {
            $users = $repository->findAll($limit, $offset);
            $totalUsers = $repository->countAll();
        }

        $addressRepository = new AddressRepository();
        $userIds = array_map(function($u) { return $u->ID; }, $users);
        $addressCounts = !empty($userIds) ? $addressRepository->getCountByUserIDs($userIds) : [];

        foreach ($users as $user) {
            $user->address_count = $addressCounts[$user->ID] ?? 0;
            $user->wsf_first_name = get_user_meta($user->ID, 'wsf_first_name', true);
            $user->wsf_last_name  = get_user_meta($user->ID, 'wsf_last_name', true);
        }

        $totalPages = ceil($totalUsers / $limit);
        $startEntry = ($totalUsers > 0) ? $offset + 1 : 0;
        $endEntry   = min($offset + $limit, $totalUsers);

        return [
            'users' => $users,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers,
            'startEntry' => $startEntry,
            'endEntry' => $endEntry,
            'limit' => $limit,
            'isAdmin' => $this->getIsAdmin()
        ];
    }
}
