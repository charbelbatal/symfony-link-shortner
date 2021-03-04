<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210304005753 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE link (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, url VARCHAR(2083) NOT NULL, short_code VARCHAR(9) NOT NULL COLLATE `utf8mb4_bin`, hits INT DEFAULT 0 NOT NULL, ip_address VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_36AC99F117D2FE0D (short_code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_statistic (id INT AUTO_INCREMENT NOT NULL, link_id BIGINT UNSIGNED NOT NULL, ip_address VARCHAR(255) DEFAULT NULL, user_agent VARCHAR(1024) DEFAULT NULL, browser VARCHAR(255) DEFAULT NULL, device VARCHAR(255) DEFAULT NULL, os VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_86EE96E8ADA40271 (link_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE link_statistic ADD CONSTRAINT FK_86EE96E8ADA40271 FOREIGN KEY (link_id) REFERENCES link (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE link_statistic DROP FOREIGN KEY FK_86EE96E8ADA40271');
        $this->addSql('DROP TABLE link');
        $this->addSql('DROP TABLE link_statistic');
    }
}
