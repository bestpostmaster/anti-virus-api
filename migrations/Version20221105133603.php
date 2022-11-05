<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221105133603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action_requested DROP FOREIGN KEY FK_3C5408B29D32F035');
        $this->addSql('DROP INDEX IDX_3C5408B29D32F035 ON action_requested');
        $this->addSql('ALTER TABLE action_requested CHANGE action_id actions_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE action_requested ADD CONSTRAINT FK_3C5408B2B15F4BF6 FOREIGN KEY (actions_id) REFERENCES action (id)');
        $this->addSql('CREATE INDEX IDX_3C5408B2B15F4BF6 ON action_requested (actions_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action_requested DROP FOREIGN KEY FK_3C5408B2B15F4BF6');
        $this->addSql('DROP INDEX IDX_3C5408B2B15F4BF6 ON action_requested');
        $this->addSql('ALTER TABLE action_requested CHANGE actions_id action_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE action_requested ADD CONSTRAINT FK_3C5408B29D32F035 FOREIGN KEY (action_id) REFERENCES action (id)');
        $this->addSql('CREATE INDEX IDX_3C5408B29D32F035 ON action_requested (action_id)');
    }
}
