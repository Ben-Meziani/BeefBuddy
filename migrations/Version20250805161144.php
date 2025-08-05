<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250805161144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fighter (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, nickname VARCHAR(255) DEFAULT NULL, weight_class VARCHAR(255) NOT NULL, wins INT NOT NULL, losses INT NOT NULL, draws INT NOT NULL, no_contest INT DEFAULT NULL, country VARCHAR(255) NOT NULL, birthday DATE NOT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fighter_user (fighter_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E2499A2234934341 (fighter_id), INDEX IDX_E2499A22A76ED395 (user_id), PRIMARY KEY(fighter_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_token (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C74F2195A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fighter_user ADD CONSTRAINT FK_E2499A2234934341 FOREIGN KEY (fighter_id) REFERENCES fighter (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fighter_user ADD CONSTRAINT FK_E2499A22A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F2195A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fighter_user DROP FOREIGN KEY FK_E2499A2234934341');
        $this->addSql('ALTER TABLE fighter_user DROP FOREIGN KEY FK_E2499A22A76ED395');
        $this->addSql('ALTER TABLE refresh_token DROP FOREIGN KEY FK_C74F2195A76ED395');
        $this->addSql('DROP TABLE fighter');
        $this->addSql('DROP TABLE fighter_user');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE user');
    }
}
