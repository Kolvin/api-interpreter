<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211209180121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attribute (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE facts (id UUID NOT NULL, security_id INT DEFAULT NULL, attribute_id INT DEFAULT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9B040C9B6DBE4214 ON facts (security_id)');
        $this->addSql('CREATE INDEX IDX_9B040C9BB6E62EFA ON facts (attribute_id)');
        $this->addSql('CREATE TABLE security (id INT NOT NULL, symbol VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE facts ADD CONSTRAINT FK_9B040C9B6DBE4214 FOREIGN KEY (security_id) REFERENCES security (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE facts ADD CONSTRAINT FK_9B040C9BB6E62EFA FOREIGN KEY (attribute_id) REFERENCES attribute (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE facts DROP CONSTRAINT FK_9B040C9BB6E62EFA');
        $this->addSql('ALTER TABLE facts DROP CONSTRAINT FK_9B040C9B6DBE4214');
        $this->addSql('DROP TABLE attribute');
        $this->addSql('DROP TABLE facts');
        $this->addSql('DROP TABLE security');
    }
}
