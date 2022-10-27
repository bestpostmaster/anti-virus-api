<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221027203053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE action (id INT AUTO_INCREMENT NOT NULL, action_name VARCHAR(250) NOT NULL, command_to_run VARCHAR(250) NOT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE action_requested ADD action_id INT DEFAULT NULL, DROP action_name, DROP command_to_execute');
        $this->addSql('ALTER TABLE action_requested ADD CONSTRAINT FK_3C5408B29D32F035 FOREIGN KEY (action_id) REFERENCES action (id)');
        $this->addSql('CREATE INDEX IDX_3C5408B29D32F035 ON action_requested (action_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action_requested DROP FOREIGN KEY FK_3C5408B29D32F035');
        $this->addSql('DROP TABLE action');
        $this->addSql('DROP INDEX IDX_3C5408B29D32F035 ON action_requested');
        $this->addSql('ALTER TABLE action_requested ADD action_name VARCHAR(255) NOT NULL, ADD command_to_execute VARCHAR(255) DEFAULT NULL, DROP action_id');
    }
}
