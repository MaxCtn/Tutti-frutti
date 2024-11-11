<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241107181920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE discogs_artist (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, genre VARCHAR(100) DEFAULT NULL, country VARCHAR(100) DEFAULT NULL, profile TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE discogs_master (id SERIAL NOT NULL, artist_id INT NOT NULL, title VARCHAR(255) NOT NULL, year VARCHAR(4) DEFAULT NULL, genre VARCHAR(100) DEFAULT NULL, label VARCHAR(100) DEFAULT NULL, format VARCHAR(50) DEFAULT NULL, fruit_keyword VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2429B4B6B7970CF8 ON discogs_master (artist_id)');
        $this->addSql('CREATE TABLE discogs_track (id SERIAL NOT NULL, master_id INT NOT NULL, title VARCHAR(255) NOT NULL, duration VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_40B8B3AA13B3DB11 ON discogs_track (master_id)');
        $this->addSql('CREATE TABLE discogs_video (id SERIAL NOT NULL, master_id INT NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EA9C912013B3DB11 ON discogs_video (master_id)');
        $this->addSql('CREATE TABLE favorite (id SERIAL NOT NULL, user_id INT NOT NULL, track_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_68C58ED9A76ED395 ON favorite (user_id)');
        $this->addSql('CREATE INDEX IDX_68C58ED95ED23C43 ON favorite (track_id)');
        $this->addSql('ALTER TABLE discogs_master ADD CONSTRAINT FK_2429B4B6B7970CF8 FOREIGN KEY (artist_id) REFERENCES discogs_artist (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE discogs_track ADD CONSTRAINT FK_40B8B3AA13B3DB11 FOREIGN KEY (master_id) REFERENCES discogs_master (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE discogs_video ADD CONSTRAINT FK_EA9C912013B3DB11 FOREIGN KEY (master_id) REFERENCES discogs_master (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED9A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED95ED23C43 FOREIGN KEY (track_id) REFERENCES discogs_track (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX uniq_identifier_email');
        $this->addSql('ALTER TABLE "user" ADD username VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE "user" DROP roles');
        $this->addSql('ALTER TABLE "user" ALTER email TYPE VARCHAR(100)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE discogs_master DROP CONSTRAINT FK_2429B4B6B7970CF8');
        $this->addSql('ALTER TABLE discogs_track DROP CONSTRAINT FK_40B8B3AA13B3DB11');
        $this->addSql('ALTER TABLE discogs_video DROP CONSTRAINT FK_EA9C912013B3DB11');
        $this->addSql('ALTER TABLE favorite DROP CONSTRAINT FK_68C58ED9A76ED395');
        $this->addSql('ALTER TABLE favorite DROP CONSTRAINT FK_68C58ED95ED23C43');
        $this->addSql('DROP TABLE discogs_artist');
        $this->addSql('DROP TABLE discogs_master');
        $this->addSql('DROP TABLE discogs_track');
        $this->addSql('DROP TABLE discogs_video');
        $this->addSql('DROP TABLE favorite');
        $this->addSql('ALTER TABLE "user" ADD roles JSON NOT NULL');
        $this->addSql('ALTER TABLE "user" DROP username');
        $this->addSql('ALTER TABLE "user" ALTER email TYPE VARCHAR(180)');
        $this->addSql('CREATE UNIQUE INDEX uniq_identifier_email ON "user" (email)');
    }
}
