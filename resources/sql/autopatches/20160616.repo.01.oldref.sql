CREATE TABLE {$NAMESPACE}_repository.repository_oldref (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  repositoryPHID VARBINARY(64) NOT NULL,
  commitIdentifier VARCHAR(40) NOT NULL COLLATE {$COLLATE_TEXT},
  KEY `key_repository` (repositoryPHID)
) ENGINE=InnoDB, COLLATE {$COLLATE_TEXT};
