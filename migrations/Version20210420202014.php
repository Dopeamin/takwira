<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210420202014 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comments (id SERIAL NOT NULL, user_id INT NOT NULL, stade_id INT NOT NULL, content TEXT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5F9E962AA76ED395 ON comments (user_id)');
        $this->addSql('CREATE TABLE orders (id SERIAL NOT NULL, user_id INT NOT NULL, stade_id INT NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E52FFDEEA76ED395 ON orders (user_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE6538AB43 ON orders (stade_id)');
        $this->addSql('CREATE TABLE reviews (id SERIAL NOT NULL, user_id INT NOT NULL, stade_id INT NOT NULL, rating INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6970EB0FA76ED395 ON reviews (user_id)');
        $this->addSql('CREATE INDEX IDX_6970EB0F6538AB43 ON reviews (stade_id)');
        $this->addSql('CREATE TABLE stade (id SERIAL NOT NULL, stade_name VARCHAR(255) NOT NULL, stade_description VARCHAR(2000) NOT NULL, stade_phone VARCHAR(255) NOT NULL, stade_owner VARCHAR(255) NOT NULL, stade_location VARCHAR(255) NOT NULL, stade_rating INT DEFAULT NULL, stade_date DATE NOT NULL, brochure_filename VARCHAR(255) NOT NULL, brochure_filename2 VARCHAR(255) NOT NULL, brochure_filename3 VARCHAR(255) NOT NULL, stade_available BOOLEAN DEFAULT NULL, x DOUBLE PRECISION NOT NULL, y DOUBLE PRECISION NOT NULL, featured BOOLEAN NOT NULL, superficie INT NOT NULL, adresse VARCHAR(255) NOT NULL, supplements VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, user_name VARCHAR(255) NOT NULL, user_pass VARCHAR(255) NOT NULL, user_email VARCHAR(255) NOT NULL, user_phone INT NOT NULL, roles JSON NOT NULL, is_verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE6538AB43 FOREIGN KEY (stade_id) REFERENCES stade (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0FA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F6538AB43 FOREIGN KEY (stade_id) REFERENCES stade (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE6538AB43');
        $this->addSql('ALTER TABLE reviews DROP CONSTRAINT FK_6970EB0F6538AB43');
        $this->addSql('ALTER TABLE comments DROP CONSTRAINT FK_5F9E962AA76ED395');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEEA76ED395');
        $this->addSql('ALTER TABLE reviews DROP CONSTRAINT FK_6970EB0FA76ED395');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE reviews');
        $this->addSql('DROP TABLE stade');
        $this->addSql('DROP TABLE "user"');
    }
}
