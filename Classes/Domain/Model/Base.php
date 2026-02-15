<?php
namespace Ws\WsForms\Domain\Model;

class Base {

    /**
     * Admin area status
     *
     * @var bool
     */
    protected $isAdmin = false;

    /**
     * @var string
     */
    protected $pageUrl = '';

    public function __construct() {
        $this->setIsAdmin();
        $this->setPageUrl();
    }

    /**
     * Returns if admin area status
     *
     */
    public function getIsAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * Sets if is Admin area status
     * Berücksichtigt auch AJAX/REST-Requests über den Referer
     *
     * @return void
     */
    public function setIsAdmin(): void
    {
        // 1. Standard WordPress Prüfung für reguläre Seitenaufrufe
        if (!(defined('REST_REQUEST') && REST_REQUEST) && is_admin()) {
            $this->isAdmin = true;
            return;
        }

        // 2. Prüfung für REST-Anfragen
        if (defined('REST_REQUEST') && REST_REQUEST) {
            // Wir prüfen den Referer-Header
            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            if (strpos($referer, 'wp-admin') !== false) {
                $this->isAdmin = true;
                return;
            }
        }

        $this->isAdmin = false;
    }

    /**
     * Returns the page url
     *
     * @return string
     */
    public function getPageUrl(): string
    {
        return $this->pageUrl;
    }

    /**
     * Sets if is Admin area
     *
     */
    public function setPageUrl(): void
    {
        global $wp;
        $this->pageUrl = ($wp) ? home_url( $wp->request ) : '';// check required because might not exist when RestAPI is called
    }


}