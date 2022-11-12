<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221112154911 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action_requested CHANGE user_is_notified_by_email user_is_notified_by_email VARCHAR(45) NOT NULL, CHANGE user_is_notified_by_post_query user_is_notified_by_post_query VARCHAR(45) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action_requested CHANGE user_is_notified_by_email user_is_notified_by_email VARCHAR(255) DEFAULT NULL, CHANGE user_is_notified_by_post_query user_is_notified_by_post_query VARCHAR(255) DEFAULT NULL');
    }
}
