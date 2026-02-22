<?php
namespace Ws\WsForms\Controller;

abstract class BaseController{

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
        $action = get_query_var('wsf_action') ?: (isset($_GET['wsf_action']) ? $_GET['wsf_action'] : 'list');
        $action = sanitize_text_field($action);

        $isAdmin = $this->base->getIsAdmin();
        $suffix = $isAdmin ? 'BeAction' : 'FeAction';
        $methodName = $action . $suffix;

        if (method_exists($this, $methodName)) {
            return $this->$methodName($atts);
        }

        // Fallback or generic routing if specific Be/Fe doesn't exist
        $genericMethod = $action . 'Action';
        if (method_exists($this, $genericMethod)) {
            return $this->$genericMethod($atts);
        }

        return $isAdmin ? $this->listBeAction($atts) : $this->listFeAction($atts);
    }

    protected function assignGeneralVariables(): void
    {
        $this->assign('pageUrl', $this->base->getPageUrl());
    }

    protected function renderView($templateName): bool|string
    {
        $this->assignGeneralVariables();
        extract($this->variables);

        $context = $this->base->getIsAdmin() ? 'Be' : 'Fe';
        // Path adjusted: Resources/Private/Templates/[Fe|Be]/[Template][Fe|Be].php
        $path = plugin_dir_path(__FILE__) . "../../Resources/Private/Templates/{$context}/{$templateName}{$context}.php";

        ob_start();
        if (file_exists($path)) {
            include $path;
        } else {
            echo "Template nicht gefunden: $path";
        }
        return ob_get_clean();
    }

    protected function renderPartial($partialName): bool|string
    {
        extract($this->variables);

        $context = $this->base->getIsAdmin() ? 'Be' : 'Fe';
        // Path adjusted: Resources/Private/Partials/[Fe|Be]/[Partial][Fe|Be].php
        $path = plugin_dir_path(__FILE__) . "../../Resources/Private/Partials/{$context}/{$partialName}{$context}.php";

        ob_start();
        if (file_exists($path)) {
            include $path;
        } else {
            echo "Partial nicht gefunden: $path";
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