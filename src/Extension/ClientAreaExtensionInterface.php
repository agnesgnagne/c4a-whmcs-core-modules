<?php

namespace WHMCS\Cloud4Africa\Extension;


interface ClientAreaExtensionInterface
{
    public function renderSidebarItem(string $currentLink): array;

    public function renderSidebarItems(string $currentLink): array;

    public function renderDashboardMetrics(): array;
    
    public function renderDashboardMetricItem(): array;

    public function buildPrimaryNavbar(): void;

    public function buildSidebar(): void;
}
