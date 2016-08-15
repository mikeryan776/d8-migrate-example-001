Migration of Drupal 6 content from www.carlyle.com to a new Drupal 8 instance.

To test locally:

1. Copy the legacy (Drupal 6) database to a MySQL server accessible to the web
server on which the Drupal 8 instance is running.

2. Set up a database connection named 'carlyled6' pointing to the D6 database,
e.g. with typical values for a Dev Desktop installation:

$databases['carlyled6']['default'] = array(
  'driver' => 'mysql',
  'database' => 'CarlyleD6',
  'username' => 'root',
  'password' => '',
  'host' => '127.0.0.1',
  'port' => 33067 );

3. Enable the migrate_caryle module.

To manage the migration process:

# migrate-status command
docroot$ drush ms
 Group: carlyle   Status  Total  Imported  Unprocessed  Last imported
 user             Idle    64     64        0            2016-04-25 12:07:01
 node_investment  Idle    797    797       0            2016-04-25 12:10:19

# migrate-import command - run all migrations
docroot$ drush mi --all
Processed 64 items (64 created, 0 updated, 0 failed, 0 ignored) - done with 'user'
Processed 797 items (797 created, 0 updated, 0 failed, 0 ignored) - done with 'node_investment'

# Run one migration
docroot$ drush mi node_investment

# Run a small sample for testing
docroot$ drush mi node_investment --limit=10

# Migrate a single item for testing (with source nid 4)
docroot$ drush mi node_investment --idlist=4

# migrate-rollback command
drush mr --all
drush mr node_investment

# Check for error messages
docroot$ drush mmsg user
No messages for this migration
