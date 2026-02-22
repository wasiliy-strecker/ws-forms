<?php
namespace Ws\WsForms\ControllerHelper;

use Ws\WsForms\Domain\Repository\ProductRepository;

class ProductHelper extends BaseHelper {

    /**
     * @var \Ws\WsForms\Domain\Model\Product
     */
    protected $productModel = null;

    public function __construct() {
        parent::__construct();
        $this->productModel = new \Ws\WsForms\Domain\Model\Product();
    }

    /**
     * @param array $params
     * @return array
     */
    public function getPreparedProductListData($params): array {
        $searchQuery = !empty($params['wsf_search']) ? sanitize_text_field($params['wsf_search']) : '';
        $currentPage = isset($params['wsf_page']) ? max(1, intval($params['wsf_page'])) : 1;
        $limit = $this->productModel->getLimitToShow();
        $offset = ($currentPage - 1) * $limit;

        $repository = new ProductRepository();

        if (!empty($searchQuery)) {
            $products = $repository->searchProducts($searchQuery, $limit, $offset);
            $totalProducts = $repository->countSearchProducts($searchQuery);
        } else {
            $products = $repository->findAllProducts($limit, $offset);
            $totalProducts = $repository->countAllProducts();
        }

        $totalPages = ceil($totalProducts / $limit);
        $startEntry = ($totalProducts > 0) ? $offset + 1 : 0;
        $endEntry   = min($offset + $limit, $totalProducts);

        return [
            'products' => $products,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalUsers' => $totalProducts, // Keep consistent with user pagination or rename in templates
            'startEntry' => $startEntry,
            'endEntry' => $endEntry,
            'limit' => $limit,
            'isAdmin' => $this->getIsAdmin()
        ];
    }
}
