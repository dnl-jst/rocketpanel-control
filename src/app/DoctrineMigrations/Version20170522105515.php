<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170522105515 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE dns_records (id INT AUTO_INCREMENT NOT NULL, hosting_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(10) NOT NULL, content LONGTEXT NOT NULL, ttl INT DEFAULT 600 NOT NULL, prio INT DEFAULT NULL, created DATETIME NOT NULL, INDEX IDX_7DF9D7AE9044EA (hosting_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dns_records ADD CONSTRAINT FK_7DF9D7AE9044EA FOREIGN KEY (hosting_id) REFERENCES hostings (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE dns_records');
    }
}
