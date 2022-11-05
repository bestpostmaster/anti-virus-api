<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221105132744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action ADD description VARCHAR(250) NOT NULL, ADD provider VARCHAR(250) NOT NULL, ADD type VARCHAR(250) NOT NULL, ADD hidden TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE action_requested DROP FOREIGN KEY FK_3C5408B2EE73CD22');
        $this->addSql('DROP INDEX IDX_3C5408B2EE73CD22 ON action_requested');
        $this->addSql('ALTER TABLE action_requested DROP hosted_file_id, CHANGE action_parameters action_parameters TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE hosted_file CHANGE authorized_users authorized_users LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action DROP description, DROP provider, DROP type, DROP hidden');
        $this->addSql('ALTER TABLE action_requested ADD hosted_file_id INT DEFAULT NULL, CHANGE action_parameters action_parameters VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE action_requested ADD CONSTRAINT FK_3C5408B2EE73CD22 FOREIGN KEY (hosted_file_id) REFERENCES hosted_file (id)');
        $this->addSql('CREATE INDEX IDX_3C5408B2EE73CD22 ON action_requested (hosted_file_id)');
        $this->addSql('ALTER TABLE hosted_file CHANGE authorized_users authorized_users VARCHAR(255) DEFAULT NULL');
    }
}
