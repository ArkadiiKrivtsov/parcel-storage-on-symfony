<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231016084347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dimensions (id INT NOT NULL, weight INT NOT NULL, length INT NOT NULL, height INT NOT NULL, width INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE full_name (id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE parcel_entity (id INT NOT NULL, sender_id INT NOT NULL, receiver_id INT NOT NULL, dimensions_id INT NOT NULL, estimated_cost INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_612E43AAF624B39D ON parcel_entity (sender_id)');
        $this->addSql('CREATE INDEX IDX_612E43AACD53EDB6 ON parcel_entity (receiver_id)');
        $this->addSql('CREATE INDEX IDX_612E43AA4F311658 ON parcel_entity (dimensions_id)');
        $this->addSql('CREATE TABLE recipient (id INT NOT NULL, full_name_id INT NOT NULL, phone VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6804FB49223EC5BA ON recipient (full_name_id)');
        $this->addSql('CREATE TABLE sender (id INT NOT NULL, full_name_id INT NOT NULL, phone VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5F004ACF223EC5BA ON sender (full_name_id)');
        $this->addSql('ALTER TABLE parcel_entity ADD CONSTRAINT FK_612E43AAF624B39D FOREIGN KEY (sender_id) REFERENCES sender (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE parcel_entity ADD CONSTRAINT FK_612E43AACD53EDB6 FOREIGN KEY (receiver_id) REFERENCES recipient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE parcel_entity ADD CONSTRAINT FK_612E43AA4F311658 FOREIGN KEY (dimensions_id) REFERENCES dimensions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE recipient ADD CONSTRAINT FK_6804FB49223EC5BA FOREIGN KEY (full_name_id) REFERENCES full_name (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sender ADD CONSTRAINT FK_5F004ACF223EC5BA FOREIGN KEY (full_name_id) REFERENCES full_name (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE parcel_entity DROP CONSTRAINT FK_612E43AAF624B39D');
        $this->addSql('ALTER TABLE parcel_entity DROP CONSTRAINT FK_612E43AACD53EDB6');
        $this->addSql('ALTER TABLE parcel_entity DROP CONSTRAINT FK_612E43AA4F311658');
        $this->addSql('ALTER TABLE recipient DROP CONSTRAINT FK_6804FB49223EC5BA');
        $this->addSql('ALTER TABLE sender DROP CONSTRAINT FK_5F004ACF223EC5BA');
        $this->addSql('DROP TABLE dimensions');
        $this->addSql('DROP TABLE full_name');
        $this->addSql('DROP TABLE parcel_entity');
        $this->addSql('DROP TABLE recipient');
        $this->addSql('DROP TABLE sender');
    }
}
