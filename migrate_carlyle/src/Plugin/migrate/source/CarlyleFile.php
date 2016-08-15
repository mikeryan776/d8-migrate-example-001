<?php

namespace Drupal\migrate_carlyle\Plugin\migrate\source;

use Drupal\file\Plugin\migrate\source\d6\File;
use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate\Row;

/**
 * Filtered list of files to migrate.
 *
 * @MigrateSource(
 *   id = "carlyle_file"
 * )
 */
class CarlyleFile extends File {

  /**
   * Tables and columns in the D6 database containing the fids we want to
   * migrate.
   *
   * @var array
   *   For each element, the table name and column name for the file field we
   *   want to migrate.
   */
  protected $fileFields = [
    ['table' => 'content_field_image', 'column' => 'field_image_fid'],
    ['table' => 'content_field_images', 'column' => 'field_images_fid'],
    ['table' => 'content_type_market_commentary', 'column' => 'field_market_commentary_file_fid'],
    ['table' => 'content_type_market_commentary', 'column' => 'field_market_commentary_image_fid'],
    ['table' => 'content_type_podcast', 'column' => 'field_mp3_fid'],
    ['table' => 'content_type_podcast', 'column' => 'field_podcast_image_fid'],
    ['table' => 'content_type_portfolio', 'column' => 'field_header_image_fid'],
    ['table' => 'content_type_portfolio', 'column' => 'field_logo_fid'],
    ['table' => 'content_field_pdf', 'column' => 'field_pdf_fid'],
  ];

  /**
   * A cache of the file IDs whitelisted for migration.
   *
   * @var array
   *   List of file IDs.
   */
  protected $fidsToMigrate = [];

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // Ignore any fids not referenced by our automatically-migrated nodes.
    if (empty($this->fidsToMigrate)) {
      foreach ($this->fileFields as $field_info) {
        $fids = $this->select($field_info['table'], 't')
          ->fields('t', [$field_info['column']])
          ->isNotNull($field_info['column'])
          ->distinct()
          ->execute()
          ->fetchCol();
        $this->fidsToMigrate = array_merge($this->fidsToMigrate, $fids);
      }
    }

    if (in_array($row->getSourceProperty('fid'), $this->fidsToMigrate)) {
      return parent::prepareRow($row);
    }
    else {
      $this->idMap->saveIdMapping($row, [], MigrateIdMapInterface::STATUS_IGNORED);
      return FALSE;
    }
  }

}
