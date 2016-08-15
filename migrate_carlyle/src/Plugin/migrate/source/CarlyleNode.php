<?php

namespace Drupal\migrate_carlyle\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\node\Plugin\migrate\source\d6\Node;

/**
 * Common support for Carlyle node migrations.
 */
abstract class CarlyleNode extends Node {
  
  /**
   * Holds mappings for term names which changed between D6 and D8.
   *
   * @var array
   *   Keyed by vocabulary name, values are arrays keyed by Drupal 6 term names
   *   with values of the desired Drupal 8 name.
   */
  protected $termNameMappings = [
    'Position' => [
      'Carlyle GMS Finance Inc' => 'Carlyle GMS Finance Inc.',
      'Claren Road- Head of Research' => 'Claren Road - Head of Research',
      'COO/CCO' => 'Chief Operating Officer & Chief Compliance Officer',
      'Managing Partner & Portfolio Manager -Agricultural Commodities' => 'Managing Partner & Portfolio Manager - Agricultural Commodities',
    ],
    'Type' => [
      'Testimony' => 'Testimonies',
      'Video' => 'Videos',
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    if (parent::prepareRow($row) === FALSE) {
      return FALSE;
    }

    // Populate the row with all terms assigned to the node.
    $query = $this->select('vocabulary_node_types', 'vnt');
    $query->innerJoin('vocabulary', 'v', 'vnt.vid=v.vid');
    $vocabularies = $query->fields('v', ['vid', 'name'])
      ->condition('vnt.type', $this->configuration['node_type'])
      ->execute()
      ->fetchAllKeyed();
    foreach ($vocabularies as $vid => $vocab_name) {
      $query = $this->select('term_node', 'tn')
        ->condition('tn.vid', $row->getSourceProperty('vid'));
      $query->innerJoin('term_data', 'td', 'tn.tid=td.tid');
      $term_names = $query->fields('td', ['name'])
        ->condition('td.vid', $vid)
        ->execute()
        ->fetchCol();
      // Translate any changed names.
      foreach ($term_names as $key => $term_name) {
        if (isset($this->termNameMappings[$vocab_name][$term_name])) {
          $term_names[$key] = $this->termNameMappings[$vocab_name][$term_name];
        }
      }
      $row->setSourceProperty($vocab_name, $term_names);
    }

    return TRUE;
  }

  /**
   * Converts a node reference source property from nids to titles.
   *
   * @param \Drupal\migrate\Row $row
   *   Row containing the source data.
   *
   * @param $field_name
   *   Name of the nodereference field to convert.
   *
   * @throws \Exception
   */
  protected function getTargetTitles(Row $row, $field_name) {
    if ($row->hasSourceProperty($field_name)) {
      $property = $row->getSourceProperty($field_name);
      $nids = [];
      foreach ($property as $instance) {
        $nids[] = $instance['nid'];
      }
      if (!empty($nids)) {
        $titles = $this->select('node', 'n')
          ->fields('n', ['title'])
          ->condition('nid', $nids, 'IN')
          ->execute()
          ->fetchCol();

        // Special case - Energy industry renamed to 'Energy & Power'.
        if ($field_name == 'field_industry') {
          foreach ($titles as $key => $title) {
            if ($title == 'Energy') {
              $titles[$key] = 'Energy & Power';
            }
          }
        }

        $row->setSourceProperty($field_name, $titles);
      }
    }
  }

  /**
   * Month-only timestamps come in as YYYY-MM-00T00:00:00, but in the D8 date
   * field need to be YYYY-MM-01.
   *
   * @param array $value_list
   *   List of timestamps in the form YYYY-MM-00T00:00:00;
   *
   * @return array
   *   List of timestamps modified to the form YYYY-MM-01.
   */
  public function fixMonthDates(array $value_list) {
    foreach ($value_list as $key => $value) {
      $value_list[$key] = str_replace('00T00:00:00', '01', $value);
    }
    return $value_list;
  }

}
