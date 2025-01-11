<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250111180502 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "file" (id INT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(180) NOT NULL, description VARCHAR(180) NOT NULL, type VARCHAR(180) NOT NULL, path VARCHAR(180) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8C9F3610A76ED395 ON "file" (user_id)');
        $this->addSql('ALTER TABLE "file" ADD CONSTRAINT FK_8C9F3610A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "file" DROP CONSTRAINT FK_8C9F3610A76ED395');
        $this->addSql('DROP TABLE "file"');
    }
}
