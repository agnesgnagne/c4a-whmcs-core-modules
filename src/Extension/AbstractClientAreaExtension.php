<?php

namespace WHMCS\Cloud4Africa\Extension;

use Carbon\Carbon;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;
use WHMCS\Cloud4Africa\Translation\TranslatorInterface;
use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\View\Menu\Item as MenuItem;

abstract class AbstractClientAreaExtension implements ClientAreaExtensionInterface
{
    /** @var TranslatorInterface $translator **/
    protected $translator;
    
    /** @var WhmcsRepositoryInterface $whmcsRepository **/
    protected WhmcsRepositoryInterface $whmcsRepository;
    
    /** @var array $moduleAddonItems **/
    protected array $moduleAddonItems;

    public function __construct(WhmcsRepositoryInterface $whmcsRepository, TranslatorInterface $translator, array $moduleAddonItems)
    {
        $this->whmcsRepository = $whmcsRepository;
        $this->translator = $translator;
        $this->moduleAddonItems = $moduleAddonItems;
    }

    public function renderSidebarItem(int $userId, string $currentLink, string $language = 'french'): array
    {}

    public function renderDashboardMetricItem(int $userId, string $currentLink, string $language = 'french'): array
    {}

    public function renderSidebarItems(int $userId, string $currentLink, string $language = 'french'): array
    {
        $sidebarItems = [];
        
        foreach ($this->moduleAddonItems as $moduleAddonItem) {
            $hasHostingsorDomains = true;
            
            if ($moduleAddonItem['slug'] != 'c4a_console' && $moduleAddonItem['slug'] != 'c4a_account_manager' && $moduleAddonItem['slug'] != 'c4a_domain_manager') {
                $constantsClass = 'WHMCS\Module\Addon\Cloud4Africa\\' . $moduleAddonItem['name'] . '\Constants';
                $hasHostingsorDomains = $this->whmcsRepository->exists(
                    "SELECT COUNT(*) as count
                     FROM tblhosting AS hosting
                     JOIN tblproducts AS product
                       ON product.id = hosting.packageid
                     WHERE product.slug = ?
                       AND hosting.userid = ?",
                    [$constantsClass::PROVISIONING_ALGORITHM_NAME, $userId]
                );
            }

            if ($moduleAddonItem['name'] == 'c4a_domain_manager') {
                $hasHostingsorDomains = $this->whmcsRepository->exists(
                    "SELECT 1
                     FROM tbldomains
                     WHERE userid = ?
                     LIMIT 1",
                    [$userId]
                );
            }

            if ($hasHostingsorDomains || $moduleAddonItem['slug'] === 'c4a_console' || $moduleAddonItem['slug'] === 'c4a_account_manager') {
                $moduleAddonExtensionClass = 'WHMCS\Module\Addon\Cloud4Africa\\' . $moduleAddonItem['name'] . '\Extension\ClientAreaExtension';
                $moduleAddonTranslatorClass = 'WHMCS\Module\Addon\Cloud4Africa\\' . $moduleAddonItem['name'] . '\Translation\Translator';
                $moduleAddonTranslator = new $moduleAddonTranslatorClass($constantsClass::DEFAULT_TRANSLATION_DIR);
                $moduleAddonExtension = new $moduleAddonExtensionClass($this->whmcsRepository, $moduleAddonTranslator, []);

                if (is_callable([$moduleAddonExtension, 'renderSidebarItem'])) {
                    if ($sidebarItem = $moduleAddonExtension->renderSidebarItem($userId, $currentLink, $language)) {
                        $sidebarItems[] = $sidebarItem;
                    }
                }
            }
        }

        return $sidebarItems;
    }

    public function renderDashboardMetrics(int $userId, string $currentLink, string $language = 'french'): array
    {
        $metrics = [];

        foreach ($this->moduleAddonItems as $moduleAddonItem) {
            if ($moduleAddonItem['slug'] != 'c4a_console' && $moduleAddonItem['slug'] != 'c4a_account_manager') {
                $hasHostingsorDomains = true;
                
                if ($moduleAddonItem['name'] == 'c4a_domain_manager') {
                    $hasHostingsorDomains = $this->whmcsRepository->exists(
                        "SELECT 1
                         FROM tbldomains
                         WHERE userid = ?
                         LIMIT 1",
                        [$userId]
                    );
                } else {
                    $constantsClass = 'WHMCS\Module\Addon\Cloud4Africa\\' . $moduleAddonItem['name'] . '\Constants';
                    $hasHostingsorDomains = $this->whmcsRepository->exists(
                        "SELECT COUNT(*) as count
                         FROM tblhosting AS hosting
                         JOIN tblproducts AS product
                           ON product.id = hosting.packageid
                         WHERE product.slug = ?
                           AND hosting.userid = ?",
                        [$constantsClass::PROVISIONING_ALGORITHM_NAME, $userId]
                    );
                    
                    if ($hasHostingsorDomains) {
                        $moduleAddonExtensionClass = 'WHMCS\Module\Addon\Cloud4Africa\\' . $moduleAddonItem['name'] . '\Extension\ClientAreaExtension';
                        $moduleAddonTranslatorClass = 'WHMCS\Module\Addon\Cloud4Africa\\' . $moduleAddonItem['name'] . '\Translation\Translator';
                        $moduleAddonTranslator = new $moduleAddonTranslatorClass($constantsClass::DEFAULT_TRANSLATION_DIR);
                        $moduleAddonExtension = new $moduleAddonExtensionClass($this->whmcsRepository, $moduleAddonTranslator, []);

                        if (is_callable([$moduleAddonExtension, 'renderDashboardMetricItem'])) {
                            if ($metric = $moduleAddonExtension->renderDashboardMetricItem($userId, $currentLink, $language)) {
                                $metrics[] = $metric;
                            }
                        }
                    }
                }
            }
        }

        return $metrics;
    }

    public function buildPrimaryNavbar(int $userId, string $currentLink, string $language = 'french'): void
    {
        // Get the current navigation bars.
        $primaryNavbar = Menu::primaryNavbar();
        $primaryNavbar->addChild('mod_c4a_addons')
                    ->setLabel($this->translator->trans('console'))
                    ->setUri('/index.php?m=c4a_console')
                    ->setOrder(25);

        $c4aAddonModulesMenuItem = $primaryNavbar->getChild('mod_c4a_addons');
        
        foreach ($this->moduleAddonItems as $moduleAddonItem) {
            $hasHostingsorDomains = true;

            if ($moduleAddonItem['slug'] != 'c4a_console' && $moduleAddonItem['slug'] != 'c4a_account_manager' && $moduleAddonItem['slug'] != 'c4a_domain_manager') {
                $constantsClass = 'WHMCS\Module\Addon\Cloud4Africa\\'.$moduleAddonItem['name'].'\Constants';
                $hasHostingsorDomains = $this->whmcsRepository->exists(
                    "SELECT COUNT(*) as count
                     FROM tblhosting AS hosting
                     JOIN tblproducts AS product
                       ON product.id = hosting.packageid
                     WHERE product.slug = ?
                       AND hosting.userid = ?",
                    [$constantsClass::PROVISIONING_ALGORITHM_NAME, $userId]
                );
            }
            
            if ($moduleAddonItem['name'] == 'c4a_account_manager') {
                $hasHostingsorDomains = $this->whmcsRepository->exists(
                    "SELECT COUNT(*) as count
                     FROM tblhosting AS hosting
                     JOIN tblproducts AS product
                       ON product.id = hosting.packageid
                     WHERE hosting.userid = ?",
                    [$userId]
                );
            }

            if ($moduleAddonItem['name'] == 'c4a_domain_manager') {
                $hasHostingsorDomains = $this->whmcsRepository->exists(
                    "SELECT 1
                     FROM tbldomains
                     WHERE userid = ?
                     LIMIT 1",
                    [$userId]
                );
            }

            if ($hasHostingsorDomains || $moduleAddonItem['slug'] == 'c4a_console') {
                $moduleAddonExtensionClass = 'WHMCS\Module\Addon\Cloud4Africa\\' . $moduleAddonItem['name'] . '\Extension\ClientAreaExtension';
                $moduleAddonTranslatorClass = 'WHMCS\Module\Addon\Cloud4Africa\\' . $moduleAddonItem['name'] . '\Translation\Translator';
                $moduleAddonTranslator = new $moduleAddonTranslatorClass($constantsClass::DEFAULT_TRANSLATION_DIR);
                $moduleAddonExtension = new $moduleAddonExtensionClass($this->whmcsRepository, $moduleAddonTranslator, []);
            
                if (is_callable(array($moduleAddonExtension, 'renderSidebarItem'))) {
                    if ($menuItem = $moduleAddonExtension->renderSidebarItem($userId, $currentLink, $language)) {
                        $c4aAddonModulesMenuItem->addChild($menuItem['link'], array(
                            'label' => $menuItem['name'],
                            'uri' => '/index.php?m='.$menuItem['link'],
                            'order' => $moduleAddonItem['position'],
                            'icon' => $menuItem['icon'],
                        ));
                    }
                }
            }
        }

        return;
    }

    public function buildSidebar(int $userId, string $currentLink, string $language = 'french'): void
    {
        $secondarySidebar->addChild('mod_c4a_addons', array(
            'label' => $this->translator->trans('console'),
            'uri' => '#',
            // 'icon' => 'fas fa-thumbs-up',
        ));
        
        // Retrieve the panel we just created.
        $c4aAddonModulesPanel = $secondarySidebar->getChild('mod_c4a_addons');
        $c4aAddonModulesPanel->moveToBack();
        
        foreach ($this->moduleAddonItems as $moduleAddonItem) {
            $opts = array();
            $moduleOptions = json_decode($this->capsule->table('tbladdonmodules')->where('module', $moduleAddonItem['slug'])->get(), true);
            
            foreach ($moduleOptions as $moduleOption) {
                $opts[$moduleOption['setting']] = $moduleOption['value'];
            }

            $hasHostingsorDomains = true;

            if ($moduleAddonItem['slug'] != 'c4a_console' && $moduleAddonItem['slug'] != 'c4a_account_manager' && $moduleAddonItem['name'] != 'c4a_domain_manager') {
                $constantsClass = 'WHMCS\Module\Addon\Cloud4Africa\\'.$moduleAddonItem['name'].'\Constants';
                $hasHostingsorDomains = $this->whmcsRepository->exists(
                    "SELECT COUNT(*) as count
                     FROM tblhosting AS hosting
                     JOIN tblproducts AS product
                       ON product.id = hosting.packageid
                     WHERE product.slug = ?
                       AND hosting.userid = ?",
                    [$constantsClass::PROVISIONING_ALGORITHM_NAME, $userId]
                );
            } 
            
            if ($moduleAddonItem['name'] == 'c4a_account_manager') {
                $hasHostingsorDomains = $this->whmcsRepository->exists(
                    "SELECT COUNT(*) as count
                     FROM tblhosting AS hosting
                     JOIN tblproducts AS product
                       ON product.id = hosting.packageid
                     WHERE hosting.userid = ?",
                    [$userId]
                );
            }

            if ($moduleAddonItem['name'] == 'c4a_domain_manager') {
                $hasHostingsorDomains = $this->whmcsRepository->exists(
                    "SELECT 1
                     FROM tbldomains
                     WHERE userid = ?
                     LIMIT 1",
                    [$userId]
                );
            }

            if ($hasHostingsorDomains || $moduleAddonItem['slug'] == 'c4a_console') {
                $moduleAddonExtensionClass = 'WHMCS\Module\Addon\Cloud4Africa\\' . $moduleAddonItem['name'] . '\Extension\ClientAreaExtension';
                $moduleAddonTranslatorClass = 'WHMCS\Module\Addon\Cloud4Africa\\' . $moduleAddonItem['name'] . '\Translation\Translator';
                $moduleAddonTranslator = new $moduleAddonTranslatorClass($constantsClass::DEFAULT_TRANSLATION_DIR);
                $moduleAddonExtension = new $moduleAddonExtensionClass($this->whmcsRepository, $moduleAddonTranslator, $opts);
                
                
                if (is_callable(array($moduleAddonExtension, 'renderSidebarItem'))) {
                    if ($menuItem = $moduleAddonExtension->renderSidebarItem($userId, $currentLink, $language)) {
                        $c4aAddonModulesPanel->addChild($menuItem['link'], array(
                            'label' => $menuItem['name'],
                            'uri' => '/index.php?m='.$menuItem['link'],
                            'order' => $moduleAddonItem['position'],
                            'icon' => $menuItem['icon'],
                        ));
                        
                    }
                }
            }
        }

        return;
    }
}
