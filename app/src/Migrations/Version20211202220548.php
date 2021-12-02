<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211202220548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates base domain entities';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attribute (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE fact (id UUID NOT NULL, security_id UUID DEFAULT NULL, attribute_id UUID DEFAULT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6FA45B956DBE4214 ON fact (security_id)');
        $this->addSql('CREATE INDEX IDX_6FA45B95B6E62EFA ON fact (attribute_id)');
        $this->addSql('CREATE TABLE security (id UUID NOT NULL, symbol VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE fact ADD CONSTRAINT FK_6FA45B956DBE4214 FOREIGN KEY (security_id) REFERENCES security (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE fact ADD CONSTRAINT FK_6FA45B95B6E62EFA FOREIGN KEY (attribute_id) REFERENCES attribute (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE fact DROP CONSTRAINT FK_6FA45B95B6E62EFA');
        $this->addSql('ALTER TABLE fact DROP CONSTRAINT FK_6FA45B956DBE4214');
        $this->addSql('DROP TABLE attribute');
        $this->addSql('DROP TABLE fact');
        $this->addSql('DROP TABLE security');
    }
}
