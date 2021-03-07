<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210305210238 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, stade_id INT NOT NULL, content LONGTEXT NOT NULL, date DATETIME NOT NULL, INDEX IDX_5F9E962AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, stade_id INT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, verified TINYINT(1) NOT NULL, INDEX IDX_E52FFDEEA76ED395 (user_id), INDEX IDX_E52FFDEE6538AB43 (stade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reviews (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, stade_id INT NOT NULL, rating INT NOT NULL, INDEX IDX_6970EB0FA76ED395 (user_id), INDEX IDX_6970EB0F6538AB43 (stade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stade (id INT AUTO_INCREMENT NOT NULL, stade_name VARCHAR(255) NOT NULL, stade_description VARCHAR(2000) NOT NULL, stade_phone VARCHAR(255) NOT NULL, stade_owner VARCHAR(255) NOT NULL, stade_location VARCHAR(255) NOT NULL, stade_rating INT DEFAULT NULL, stade_date DATE NOT NULL, brochure_filename VARCHAR(255) NOT NULL, brochure_filename2 VARCHAR(255) NOT NULL, brochure_filename3 VARCHAR(255) NOT NULL, stade_available TINYINT(1) DEFAULT NULL, x DOUBLE PRECISION NOT NULL, y DOUBLE PRECISION NOT NULL, featured TINYINT(1) NOT NULL, superficie INT NOT NULL, adresse VARCHAR(255) NOT NULL, supplements VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, user_name VARCHAR(255) NOT NULL, user_pass VARCHAR(255) NOT NULL, user_email VARCHAR(255) NOT NULL, user_phone INT NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', is_verified TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE6538AB43 FOREIGN KEY (stade_id) REFERENCES stade (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F6538AB43 FOREIGN KEY (stade_id) REFERENCES stade (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE6538AB43');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0F6538AB43');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AA76ED395');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEA76ED395');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0FA76ED395');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE reviews');
        $this->addSql('DROP TABLE stade');
        $this->addSql('DROP TABLE user');
    }
}
