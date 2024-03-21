<?php

namespace Gerencianet\Magento2\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class GerencianetRedirectUrl implements SchemaPatchInterface {

  /** @var ModuleDataSetupInterface */
  private $moduleDataSetup;

  public function __construct(ModuleDataSetupInterface $moduleDataSetup) {
    $this->moduleDataSetup = $moduleDataSetup;
  }

  public static function getDependencies() {
    return [];
  }

  public function getAliases() {
    return [];
  }

  public function apply() {
    $this->moduleDataSetup->startSetup();

    
    $this->moduleDataSetup->getConnection()->addColumn(
      $this->moduleDataSetup->getTable('sales_order'),
      'gerencianet_redirect_url',
      [
        'type' => Table::TYPE_TEXT,
        'length' => 5000,
        'nullable' => true,
        'comment'  => 'Url de redirecionamento Open Finance',
      ]
    );




    $this->moduleDataSetup->endSetup();
  }
}
