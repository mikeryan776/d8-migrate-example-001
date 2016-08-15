<?php

namespace Drupal\migrate_carlyle\Plugin\migrate\process;

use Drupal\file\Plugin\migrate\process\d6\CckFile;
use Drupal\migrate\Plugin\MigrationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @MigrateProcessPlugin(
 *   id = "carlyle_cck_file"
 * )
 */
class CarlyleCckFile extends CckFile {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    // Configure the migration process plugin to look up migrated IDs from
    // the (Carlyle) file migration (overriding the default d6_file).
    $migration_plugin_configuration = [
      'source' => ['fid'],
      'migration' => 'file',
    ];

    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('plugin.manager.migrate.process')->createInstance('migration', $migration_plugin_configuration, $migration)
    );
  }

}
