<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240829004927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE fund_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE patient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE serum_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE patient (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE serum (id INT NOT NULL, patient_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4296465B6B899279 ON serum (patient_id)');
        $this->addSql('ALTER TABLE serum ADD CONSTRAINT FK_4296465B6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE fund DROP CONSTRAINT fk_dc923e1056265cf9');
        $this->addSql('DROP TABLE fund');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE patient_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE serum_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE fund_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE fund (id INT NOT NULL, duplicate_fund_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, start_year DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_dc923e1056265cf9 ON fund (duplicate_fund_id)');
        $this->addSql('ALTER TABLE fund ADD CONSTRAINT fk_dc923e1056265cf9 FOREIGN KEY (duplicate_fund_id) REFERENCES fund (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE serum DROP CONSTRAINT FK_4296465B6B899279');
        $this->addSql('DROP TABLE patient');
        $this->addSql('DROP TABLE serum');
    }
}
