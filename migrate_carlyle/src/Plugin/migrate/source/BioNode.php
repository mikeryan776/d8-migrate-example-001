<?php

namespace Drupal\migrate_carlyle\Plugin\migrate\source;
use Drupal\migrate\Row;

/**
 * Drupal 6 node source from database.
 *
 * @MigrateSource(
 *   id = "bio_node"
 * )
 */
class BioNode extends CarlyleNode {

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    if (parent::prepareRow($row) === FALSE) {
      return FALSE;
    }

    $this->getTargetTitles($row, 'field_asset_class');
    $this->getTargetTitles($row, 'field_fund_segment');
    $this->getTargetTitles($row, 'field_industry');
    $this->getTargetTitles($row, 'field_location');

    return TRUE;
  }

}
