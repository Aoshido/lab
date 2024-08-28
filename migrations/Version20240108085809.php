<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240108085809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE alias_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE alias (id INT NOT NULL, fund_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E16C6B9425A38F89 ON alias (fund_id)');
        $this->addSql('ALTER TABLE alias ADD CONSTRAINT FK_E16C6B9425A38F89 FOREIGN KEY (fund_id) REFERENCES fund (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE alias_id_seq CASCADE');
        $this->addSql('ALTER TABLE alias DROP CONSTRAINT FK_E16C6B9425A38F89');
        $this->addSql('DROP TABLE alias');
    }
}
