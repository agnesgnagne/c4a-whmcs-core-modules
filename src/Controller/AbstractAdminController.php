<?php

namespace WHMCS\Cloud4Africa\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;
use WHMCS\Cloud4Africa\Translation\TranslatorInterface;
use WHMCS\Cloud4Africa\Traits\ControllerTrait;
use WHMCS\Cloud4Africa\Traits\ValidatorTrait;
use WHMCS\Cloud4Africa\Service\KarajanManagerInterface;
use Smarty\Smarty;

abstract class AbstractAdminController implements ControllerInterface
{
    use ControllerTrait;
    use ValidatorTrait;
    
    /** @var KarajanManagerInterface $karajanManager **/
    protected KarajanManagerInterface $karajanManager;
    
    /**
     * @param TranslatorInterface $translator
     * @param WhmcsRepositoryInterface $whmcsRepository
     * @param KarajanManagerInterface $karajanManager
     */
    public function __construct(TranslatorInterface $translator, WhmcsRepositoryInterface $whmcsRepository, KarajanManagerInterface $karajanManager)
    {
        $this->translator = $translator;
        $this->whmcsRepository = $whmcsRepository;
        $this->karajanManager = $karajanManager;
    }
    
    /**
     * @param array<string, mixed> $vars
     * @return Response
     */
    public function listConfigs($vars): Response
    {
        try {
            $configs = $this->whmcsRepository->findAll();
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
        
        return $this->getResponse(__DIR__ . '/../../templates/adminarea/pages/list_configs.tpl', [
            'translator' => $this->translator,
            'configs' => $configs
        ]);
    }
    
    /**
     * @param array<string, mixed> $vars
     * @return Response
     */
    public function addConfig($vars): Response
    {
        $postData = $vars['postData'];
        
        try {
            $this->validate($postData, $this->addConfigRules(), $this->addConfigMessages());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return new Response($e->validator->errors()->first(), 400);
        }
        
        try {
            $this->whmcsRepository->insert([
                'key' => $vars['postData']['config_key'],
                'value' => $vars['postData']['config_value'],
                'description' => $vars['postData']['config_description']
            ]);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
        
        return $this->redirect([
            'module' => $vars['moduleName']
        ]);
    }
    
    /**
     * @param array<string, mixed> $vars
     * @return Response
     */
    public function updateConfigs(array $vars): Response
    {
        $postData = $vars['postData'];
        $properties = array_keys($postData);
        
        try {
            $this->validate($postData, $this->updateConfigsRules($properties), $this->updateConfigsMessages($properties));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return new Response($e->validator->errors()->first(), 400);
        }
        
        foreach ($vars['postData'] as $key => $value) {
            try {
                $this->whmcsRepository->updateBy(['value' => $value], ['key' => $key]);
            } catch (\Exception $e) {
                return new Response($e->getMessage(), 500);
            }
        }
        
        return $this->redirect([
            'module' => $vars['moduleName']
        ]);
    }
    
    /**
     * @param array<string, mixed> $vars
     * @return Response
     */
    public function deleteConfig($vars): Response
    {
        if (! isset($vars['queryParams']['id'])) {
            return new Response($this->translator->trans($vars['moduleName'].'.admin.config.error.bad_request'), 404);
        }
        
        try {
            $this->whmcsRepository->delete($vars['queryParams']['id']);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
        
        return $this->redirect([
            'module' => $vars['moduleName']
        ]);
    }
    
    /**
     * Redirect response
     *
     * @param array<string, mixed> $queryParams
     * @return Response
     */
    protected function redirect(array $queryParams): Response
    {
        if (false === isset($queryParams['module'])){
            return $this->getExceptionResponse(new \Exception($this->translator->trans('error.not_found'), 404));
        }
        
        return new RedirectResponse('/admin/addonmodules.php?'.http_build_query($queryParams));
    }
    
    /**
     * @return array<string, mixed>
     */
    protected function addConfigRules(): array
    {
        return [
            'config_key' => [
                'required',
                'string',
            ],
            'config_value' => [
                'required',
                'string',
            ],
            'config_description' => [
                'nullable',
                'string',
            ],
        ];
    }
    
    /**
     *
     * @param array<int, mixed> $properties
     * @return array<string, mixed>
     */
    protected function updateConfigsRules(?array $properties = []): array
    {
        $rules = [];
        
        foreach ($properties as $property) {
            $rules[$property] = [
                'required',
                'string',
            ];
        }
        
        return $rules;
    }
    
    /**
     * @return array<string, mixed>
     */
    protected function addConfigMessages(): array
    {
        return [
            'config_key.required' => $this->translator->trans('admin.config.error.key.blank'),
            'config_value.required' => $this->translator->trans('admin.config.error.value.blank'),
            'config_description.string' => $this->translator->trans('admin.config.error.description.blank'),
        ];
    }
    
    /**
     * @param array<int, mixed> $properties
     * @return array<string, mixed>
     */
    protected function updateConfigsMessages(?array $properties = []): array
    {
        $messages = [];
        
        foreach ($properties as $property) {
            $messages[$property . '.required'] = $this->translator->trans('default.admin.config.error.value.blank');
        }
        
        return $messages;
    }
}
