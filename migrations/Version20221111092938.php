<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221111092938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE action (id INT AUTO_INCREMENT NOT NULL, action_name VARCHAR(250) NOT NULL, command_to_run VARCHAR(250) NOT NULL, description VARCHAR(250) NOT NULL, provider VARCHAR(250) NOT NULL, type VARCHAR(250) NOT NULL, enabled TINYINT(1) NOT NULL, hidden TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE action_requested (id INT AUTO_INCREMENT NOT NULL, action_id INT DEFAULT NULL, user_id INT DEFAULT NULL, action_parameters TEXT DEFAULT NULL, date_of_demand DATETIME NOT NULL, start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, hosted_file_ids LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', accomplished TINYINT(1) NOT NULL, action_results LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_3C5408B29D32F035 (action_id), INDEX IDX_3C5408B2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE banned_email (id INT AUTO_INCREMENT NOT NULL, ip VARCHAR(250) NOT NULL, last_try INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flood (id INT AUTO_INCREMENT NOT NULL, ip VARCHAR(250) NOT NULL, last_try INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hosted_file (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, client_name VARCHAR(255) NOT NULL, upload_date DATETIME NOT NULL, expiration_date DATETIME DEFAULT NULL, virtual_directory VARCHAR(255) NOT NULL, size DOUBLE PRECISION NOT NULL, scaned TINYINT(1) NOT NULL, infected TINYINT(1) DEFAULT 0 NOT NULL, scan_result TEXT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, download_counter BIGINT NOT NULL, url VARCHAR(255) NOT NULL, upload_localisation VARCHAR(255) DEFAULT NULL, copyright_issue TINYINT(1) NOT NULL, conversions_available VARCHAR(255) DEFAULT NULL, file_password VARCHAR(255) DEFAULT NULL, authorized_users LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_1D3B660EF47645AE (url), INDEX IDX_1D3B660EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(255) DEFAULT NULL, preferred_language VARCHAR(255) DEFAULT NULL, type_of_account VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, avatar_picture VARCHAR(255) DEFAULT NULL, date_of_birth DATETIME DEFAULT NULL, is_banned TINYINT(1) DEFAULT NULL, send_email_after_each_action TINYINT(1) NOT NULL, send_email_if_file_is_infected TINYINT(1) NOT NULL, send_sms_if_file_is_infected TINYINT(1) NOT NULL, post_url_after_action VARCHAR(255) DEFAULT NULL, send_post_to_url_after_each_action TINYINT(1) DEFAULT NULL, send_post_to_url_if_file_is_infected TINYINT(1) DEFAULT NULL, email_confirmed TINYINT(1) DEFAULT 0, total_space_used_mo DOUBLE PRECISION DEFAULT NULL, authorized_size_mo DOUBLE PRECISION DEFAULT \'100\', registration_date DATETIME DEFAULT NULL, last_connexion_date DATETIME DEFAULT NULL, secret_token_for_validation VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649AA08CB10 (login), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE action_requested ADD CONSTRAINT FK_3C5408B29D32F035 FOREIGN KEY (action_id) REFERENCES action (id)');
        $this->addSql('ALTER TABLE action_requested ADD CONSTRAINT FK_3C5408B2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE hosted_file ADD CONSTRAINT FK_1D3B660EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action_requested DROP FOREIGN KEY FK_3C5408B29D32F035');
        $this->addSql('ALTER TABLE action_requested DROP FOREIGN KEY FK_3C5408B2A76ED395');
        $this->addSql('ALTER TABLE hosted_file DROP FOREIGN KEY FK_1D3B660EA76ED395');
        $this->addSql('DROP TABLE action');
        $this->addSql('DROP TABLE action_requested');
        $this->addSql('DROP TABLE banned_email');
        $this->addSql('DROP TABLE flood');
        $this->addSql('DROP TABLE hosted_file');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
