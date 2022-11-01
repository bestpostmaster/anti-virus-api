<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221031195702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE action_requested_hosted_file (action_requested_id INT NOT NULL, hosted_file_id INT NOT NULL, INDEX IDX_196AE73A444F097 (action_requested_id), INDEX IDX_196AE73AEE73CD22 (hosted_file_id), PRIMARY KEY(action_requested_id, hosted_file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE action_requested_hosted_file ADD CONSTRAINT FK_196AE73A444F097 FOREIGN KEY (action_requested_id) REFERENCES action_requested (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE action_requested_hosted_file ADD CONSTRAINT FK_196AE73AEE73CD22 FOREIGN KEY (hosted_file_id) REFERENCES hosted_file (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action_requested_hosted_file DROP FOREIGN KEY FK_196AE73A444F097');
        $this->addSql('ALTER TABLE action_requested_hosted_file DROP FOREIGN KEY FK_196AE73AEE73CD22');
        $this->addSql('DROP TABLE action_requested_hosted_file');
    }
}
