<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190621072800 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE horse_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE horse_by_race_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE race_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE horse (id INT NOT NULL, nick_name VARCHAR(255) NOT NULL, speed DOUBLE PRECISION NOT NULL, strength DOUBLE PRECISION NOT NULL, endurance DOUBLE PRECISION NOT NULL, autonomy DOUBLE PRECISION NOT NULL, slowdown DOUBLE PRECISION NOT NULL, best_speed DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE horse_by_race (id INT NOT NULL, horse_id INT NOT NULL, race_id INT NOT NULL, current_distance DOUBLE PRECISION NOT NULL, spent_time DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_51EC718C76B275AD ON horse_by_race (horse_id)');
        $this->addSql('CREATE INDEX IDX_51EC718C6E59D40D ON horse_by_race (race_id)');
        $this->addSql('CREATE TABLE race (id INT NOT NULL, active INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, max_distance DOUBLE PRECISION NOT NULL, completed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, duration DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE horse_by_race ADD CONSTRAINT FK_51EC718C76B275AD FOREIGN KEY (horse_id) REFERENCES horse (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE horse_by_race ADD CONSTRAINT FK_51EC718C6E59D40D FOREIGN KEY (race_id) REFERENCES race (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE horse_by_race DROP CONSTRAINT FK_51EC718C76B275AD');
        $this->addSql('ALTER TABLE horse_by_race DROP CONSTRAINT FK_51EC718C6E59D40D');
        $this->addSql('DROP SEQUENCE horse_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE horse_by_race_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE race_id_seq CASCADE');
        $this->addSql('DROP TABLE horse');
        $this->addSql('DROP TABLE horse_by_race');
        $this->addSql('DROP TABLE race');
    }
}
