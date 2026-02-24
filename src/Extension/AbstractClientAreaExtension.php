<?php

namespace WHMCS\Cloud4Africa\Extension;

use Carbon\Carbon;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;
use WHMCS\Cloud4Africa\Translation\TranslatorInterface;
use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\View\Menu\Item as MenuItem;

abstract class AbstractClientAreaExtension implements ClientAreaExtensionInterface
{
    /** @var Translator $translator **/
    private $translator;
    
    /** @var WhmcsRepositoryInterface $whmcsRepository **/
    private WhmcsRepositoryInterface $whmcsRepository;

    /** @var array $params **/
    private array $params;

    public function __construct(WhmcsRepositoryInterface $whmcsRepository, TranslatorInterface $translator, array $params)
    {
        $this->params = $params;
        $this->whmcsRepository = $whmcsRepository;
        $this->translator = $translator;
    }

    public function renderSidebarItem(string $currentLink): array
    {}

    public function renderDashboardMetricItem(): array
    {}

    public function renderSidebarItems(string $currentLink): array
    {
        $this->initializeModuleAddonTable();

        $sidebarItems = [];

        foreach (Capsule::table('mod_c4a_addons')->orderBy('position', 'asc')->get() as $module) {
            $hostings = null;

            if ($module->name != 'c4a_console' && $module->name != 'c4a_account_manager' && $module->name != 'c4a_domain_manager') {
                $constantsClass = 'WHMCS\Module\Addon\\' . $module->name . '\Constants';
                $productSlug = $constantsClass::PROVISIONING_ALGORITHM_NAME;
                $hostings = Capsule::select('select * from tblhosting as hosting join tblproducts as product on hosting.packageid = product.id where hosting.userid = ? and product.slug = ?', [$this->params['uid'], $productSlug]);
            }

            if ($module->name == 'c4a_domain_manager') {
                $hostings = Capsule::table('tbldomains')->where('userid', $this->params['uid'])->get();
            }

            if ($hostings || $module->name === 'c4a_console' || $module->name === 'c4a_account_manager') {
                $moduleAddonExtensionClass = 'WHMCS\Module\Addon\\' . $module->name . '\Client\Extension\ClientAreaExtension';
                $moduleAddonTranslatorClass = 'WHMCS\Module\Addon\\' . $module->name . '\Client\Util\Translator';
                $moduleAddonTranslator = new $moduleAddonTranslatorClass($this->params['language']);
                $moduleAddonExtension = new $moduleAddonExtensionClass($this->whmcsRepository, $moduleAddonTranslator, []);

                if (is_callable([$moduleAddonExtension, 'renderSidebarItem'])) {
                    if ($sidebarItem = $moduleAddonExtension->renderSidebarItem($currentLink)) {
                        $sidebarItems[] = $sidebarItem;
                    }
                }
            }
        }

        return $sidebarItems;
    }

    public function renderDashboardMetrics(): array
    {
        $this->initializeModuleAddonTable();

        $metrics = [];

        foreach (Capsule::table('mod_c4a_addons')->orderBy('position', 'asc')->get() as $module) {
            if ($module->name != 'c4a_console' && $module->name != 'c4a_account_manager') {
                if ($module->name == 'c4a_domain_manager') {
                    $hostings = Capsule::table('tbldomains')->where('userid', $this->params['uid'])->get();
                } else {
                    $constantsClass = 'WHMCS\Module\Addon\\' . $module->name . '\Constants';
                    $productSlug = $constantsClass::PROVISIONING_ALGORITHM_NAME;
                    $hostings = Capsule::select('select * from tblhosting as hosting join tblproducts as product on hosting.packageid = product.id where hosting.userid = ? and product.slug = ?', [$this->params['uid'], $productSlug]);

                    if ($hostings) {
                        $moduleAddonExtensionClass = 'WHMCS\Module\Addon\\' . $module->name . '\Client\Extension\ClientAreaExtension';
                        $moduleAddonTranslatorClass = 'WHMCS\Module\Addon\\' . $module->name . '\Client\Util\Translator';
                        $moduleAddonTranslator = new $moduleAddonTranslatorClass($this->params['language']);
                        $moduleAddonExtension = new $moduleAddonExtensionClass($this->whmcsRepository, $moduleAddonTranslator, []);

                        if (is_callable([$moduleAddonExtension, 'renderDashboardMetric'])) {
                            if ($metric = $moduleAddonExtension->renderDashboardMetric()) {
                                $metrics[] = $metric;
                            }
                        }
                    }
                }
            }
        }

        return $metrics;
    }

    public function buildPrimaryNavbar(): void
    {
        $this->initializeModuleAddonTable();

        // Get the current navigation bars.
        $primaryNavbar = Menu::primaryNavbar();
        $primaryNavbar->addChild('mod_c4a_addons')
                    ->setLabel($this->translator->trans('console'))
                    ->setUri('/index.php?m=c4a_console')
                    ->setOrder(25);

        $c4aAddonModulesMenuItem = $primaryNavbar->getChild('mod_c4a_addons');
        
        foreach (Capsule::table('mod_c4a_addons')->orderBy('position', 'asc')->get() as $module) {
            $hostings = null;

            if ($module->name != 'c4a_console' && $module->name != 'c4a_account_manager' && $module->name != 'c4a_domain_manager') {
                $constantsClass = 'WHMCS\Module\Addon\\'.$module->name.'\Constants';
                $productSlug = $constantsClass::PROVISIONING_ALGORITHM_NAME;
                $hostings = Capsule::select('select * from tblhosting as hosting join tblproducts as product on hosting.packageid = product.id where hosting.userid = ? and product.slug = ?', [$this->params['uid'], $productSlug]);
            }
            
            if ($module->name == 'c4a_account_manager') {
                $hostings = Capsule::select('select * from tblhosting as hosting join tblproducts as product on hosting.packageid = product.id where hosting.userid = ?', [$this->params['uid']]);
            }

            if ($module->name == 'c4a_domain_manager') {
                $hostings = Capsule::table('tbldomains')->where('userid', $this->params['uid'])->get();
            }

            if ($hostings || $module->name == 'c4a_console') {
                $moduleAddonExtensionClass = 'WHMCS\Module\Addon\\' . $module->name . '\Client\Extension\ClientAreaExtension';
                $moduleAddonTranslatorClass = 'WHMCS\Module\Addon\\' . $module->name . '\Client\Util\Translator';
                $moduleAddonTranslator = new $moduleAddonTranslatorClass($this->params['language']);
                $moduleAddonExtension = new $moduleAddonExtensionClass($this->whmcsRepository, $moduleAddonTranslator, []);
            
                if (is_callable(array($moduleAddonExtension, 'renderSidebarItem'))) {
                    if ($menuItem = $moduleAddonExtension->renderSidebarItem()) {
                        $c4aAddonModulesMenuItem->addChild($menuItem['link'], array(
                            'label' => $menuItem['name'],
                            'uri' => '/index.php?m='.$menuItem['link'],
                            'order' => $module->position,
                            'icon' => $menuItem['icon'],
                        ));
                    }
                }
            }
        }

        return;
    }

    public function buildSidebar(): void
    {
        $this->initializeModuleAddonTable();

        $secondarySidebar->addChild('mod_c4a_addons', array(
            'label' => $this->translator->trans('console'),
            'uri' => '#',
            // 'icon' => 'fas fa-thumbs-up',
        ));
        
        // Retrieve the panel we just created.
        $c4aAddonModulesPanel = $secondarySidebar->getChild('mod_c4a_addons');
        $c4aAddonModulesPanel->moveToBack();
        
        $modules = Capsule::table('mod_c4a_addons')->orderBy('position', 'asc')->get();
            
        foreach ($modules as $module) {
            // Get module configurations
            $opts = array();
            $moduleoptions = select_query('tbladdonmodules', '*', array('module' => $module->name));
            
            while($m = mysql_fetch_assoc($moduleoptions)){
                $opts[$m['setting']] = $m['value'];
            }

            $hostings = null;

            if ($module->name != 'c4a_console' && $module->name != 'c4a_account_manager' && $module->name != 'c4a_domain_manager') {
                $constantsClass = 'WHMCS\Module\Addon\\'.$module->name.'\Constants';
                $productSlug = $constantsClass::PROVISIONING_ALGORITHM_NAME;
                $hostings = Capsule::select('select * from tblhosting as hosting join tblproducts as product on hosting.packageid = product.id where hosting.userid = ? and product.slug = ?', [$this->params['uid'], $productSlug]);
            } 
            
            if ($module->name == 'c4a_account_manager') {
                $hostings = Capsule::select('select * from tblhosting as hosting join tblproducts as product on hosting.packageid = product.id where hosting.userid = ?', [$_SESSION['uid']]);
            }

            if ($module->name == 'c4a_domain_manager') {
                $hostings = Capsule::table('tbldomains')->where('userid', $this->params['uid'])->get();
            }

            if ($hostings || $module->name == 'c4a_console') {
                $moduleAddonExtensionClass = 'WHMCS\Module\Addon\\' . $module->name . '\Client\Extension\ClientAreaExtension';
                $moduleAddonTranslatorClass = 'WHMCS\Module\Addon\\' . $module->name . '\Client\Util\Translator';
                $moduleAddonTranslator = new $moduleAddonTranslatorClass($this->params['language']);
                $moduleAddonExtension = new $moduleAddonExtensionClass($this->whmcsRepository, $moduleAddonTranslator, $opts);
                
                
                if (is_callable(array($moduleAddonExtension, 'renderSidebarItem'))) {
                    if ($menuItem = $moduleAddonExtension->renderSidebarItem()) {
                        $c4aAddonModulesPanel->addChild($menuItem['link'], array(
                            'label' => $menuItem['name'],
                            'uri' => '/index.php?m='.$menuItem['link'],
                            'order' => $module->position,
                            'icon' => $menuItem['icon'],
                        ));
                        
                    }
                }
            }
        }

        return;
    }

    private function initializeModuleAddonTable()
    {
        if (!Capsule::schema()->hasTable('mod_c4a_addons')) {
            Capsule::schema()->create('mod_c4a_addons', function ($table) {
                /** @var \Illuminate\Database\Schema\Blueprint $table */
                $table->increments('id');
                $table->text('name');
                $table->integer('position');
                $table->json('options')->nullable();
            });
        }

        return;
    }
}
