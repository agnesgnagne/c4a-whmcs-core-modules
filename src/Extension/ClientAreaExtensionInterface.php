<?php

namespace WHMCS\Cloud4Africa\Extension;

interface ClientAreaExtensionInterface
{
    public function renderSidebarItem(int $userId, string $currentLink, string $language = 'french'): array;
    
    public function renderSidebarItems(int $userId, string $currentLink, string $language = 'french'): array;
    
    public function renderDashboardMetrics(int $userId, string $currentLink, string $language = 'french'): array;
    
    public function renderDashboardMetricItem(int $userId, string $currentLink, string $language = 'french'): array;
    
    public function buildPrimaryNavbar(int $userId, string $currentLink, string $language = 'french'): void;
    
    public function buildSidebar(int $userId, string $currentLink, string $language = 'french'): void;
}
