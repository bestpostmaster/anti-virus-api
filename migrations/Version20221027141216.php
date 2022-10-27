<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221027141216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE action_requested (id INT AUTO_INCREMENT NOT NULL, hosted_file_id INT DEFAULT NULL, action_name VARCHAR(255) NOT NULL, action_parameters VARCHAR(255) NOT NULL, date_of_demand DATETIME NOT NULL, start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, accomplished TINYINT(1) NOT NULL, action_results TINYINT(1) NOT NULL, INDEX IDX_3C5408B2EE73CD22 (hosted_file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE action_requested ADD CONSTRAINT FK_3C5408B2EE73CD22 FOREIGN KEY (hosted_file_id) REFERENCES hosted_file (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action_requested DROP FOREIGN KEY FK_3C5408B2EE73CD22');
        $this->addSql('DROP TABLE action_requested');
    }
}
