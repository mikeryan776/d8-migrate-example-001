<?php

namespace Drupal\migrate_carlyle\Plugin\migrate\source;
use Drupal\migrate\Row;

/**
 * Drupal 6 node source from database.
 *
 * @MigrateSource(
 *   id = "press_release_node"
 * )
 */
class PressReleaseNode extends CarlyleNode {

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    if (parent::prepareRow($row) === FALSE) {
      return FALSE;
    }

    $this->getTargetTitles($row, 'field_fund');
    $this->getTargetTitles($row, 'field_industry');
    $this->getTargetTitles($row, 'field_fund_segment');

    // Fix field_portfolio to a scalar.
    $field_portfolio = $row->getSourceProperty('field_portfolio');
    if (isset($field_portfolio[0]['nid'])) {
      $row->setSourceProperty('field_portfolio', $field_portfolio[0]['nid']);
    }

    return TRUE;
  }

}
