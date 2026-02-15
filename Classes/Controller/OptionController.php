<?php
namespace Ws\WsForms\Controller;

use Ws\WsForms\Domain\Repository\OptionRepository;
use Ws\WsForms\Domain\Model\Option;

class OptionController extends BaseController {

    protected OptionRepository $optionRepository;

    public function __construct() {
        parent::__construct();
        $this->optionRepository = new OptionRepository();
    }

    public function initAction($atts = []) {
        return $this->editAction();
    }

    public function editAction() {
        $option = $this->optionRepository->get(); // $option (singular)
        $roles = $this->optionRepository->getAvailableRoles();
        $message = $_GET['message'] ?? '';

        $this->assign('option', $option); // Template Variable 'option'
        $this->assign('roles', $roles);
        $this->assign('message', $message);
        $this->assign('headline', 'Plugin Option');
        // URL Slug auf ws_forms_option angepasst
        $this->assign('formAction', admin_url('admin.php?page=ws_forms_option&action=update'));

        // Template Pfad angepasst
        echo $this->renderView('Option/Edit');
    }

    public function updateAction(\WP_REST_Request $request): \WP_REST_Response
    {
        $params = $this->getParams($request);
        // POST Datenfeld umbenannt zu wsf_option (singular)
        $postData = $params['wsf_option'] ?? [];

        $option = new Option();
        $option->userBackendRole = $postData['backend_role'] ?? '';
        $option->userFrontendRole = $postData['frontend_role'] ?? '';

        $this->optionRepository->update($option);

        return new \WP_REST_Response([
            'message' => 'Option saved!',
            'redirect' => admin_url('admin.php?page=ws_forms_options&action=edit&message=updated')
        ], 200);

    }

    public function listAction()
    {
        // TODO: Implement listAction() method.
    }

    public function newAction()
    {
        // TODO: Implement newAction() method.
    }
}