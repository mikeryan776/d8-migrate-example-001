<?php

namespace Drupal\migrate_carlyle\Plugin\migrate\source;
use Drupal\migrate\Row;

/**
 * Drupal 6 node source from database.
 *
 * @MigrateSource(
 *   id = "investment_node"
 * )
 */
class InvestmentNode extends CarlyleNode {

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    if (parent::prepareRow($row) === FALSE) {
      return FALSE;
    }

    $this->getTargetTitles($row, 'field_fund');
    $this->getTargetTitles($row, 'field_industry');

    return TRUE;
  }

}
