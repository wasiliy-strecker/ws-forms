<?php
namespace Ws\WsForms\Controller;

abstract class BaseController{

    // Tells PHP that this functions exists in the child classes
    abstract public function listAction();
    abstract public function newAction();
    abstract public function editAction();

    protected $variables = [];

    /**
     * @var \Ws\WsForms\Domain\Model\Base
     */
    protected $base = null;

    public function __construct() {
        $this->base = new \Ws\WsForms\Domain\Model\Base();
    }

    protected function assign($key, $value): void
    {
        $this->variables[$key] = $value;
    }

    public function initAction($atts = []) {

        $action = isset($_GET['action']) ? $_GET['action'] : 'list';

        if ($action === 'new') {
            return $this->newAction();
        }else if ($action === 'edit') {
            return $this->editAction();
        }else{
            return $this->listAction();
        }

    }

    protected function assignGeneralVariables(): void
    {
        $this->assign('pageUrl', $this->base->getPageUrl());
    }

    protected function renderView($templateName): bool|string
    {
        $this->assignGeneralVariables();
        extract($this->variables);
        $path = plugin_dir_path(__FILE__) . '../../Resources/Private/Templates/' . $templateName . '.php';
        ob_start();
        if (file_exists($path)) {
            include $path;
        } else {
            echo "Template nicht gefunden: $path";
        }
        return ob_get_clean();
    }

	/**
	 * Holt die Parameter aus dem Request und bereinigt sie rekursiv.
	 */
	protected function getParams(\WP_REST_Request $request): array {
		$params = $request->get_params();
		return $this->sanitizeRecursive($params);
	}

	/**
	 * Rekursive Sanitisierung für Arrays und Einzelwerte.
	 */
	private function sanitizeRecursive($value): array|string
    {
		if (is_array($value)) {
			foreach ($value as $key => $val) {
				$value[$key] = $this->sanitizeRecursive($val);
			}
			return $value;
		}

		if (is_email($value)) {
			return sanitize_email($value);
		}

		// Standard-Reinigung (entfernt HTML-Tags, etc.)
		return sanitize_text_field($value);
	}

}