<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241121161040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorite_album_fruit (favorite_album_id INT NOT NULL, fruit_id INT NOT NULL, INDEX IDX_15A1DDF2BB60B33E (favorite_album_id), INDEX IDX_15A1DDF2BAC115F0 (fruit_id), PRIMARY KEY(favorite_album_id, fruit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fruit (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_A00BD2975E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favorite_album_fruit ADD CONSTRAINT FK_15A1DDF2BB60B33E FOREIGN KEY (favorite_album_id) REFERENCES favorite_album (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite_album_fruit ADD CONSTRAINT FK_15A1DDF2BAC115F0 FOREIGN KEY (fruit_id) REFERENCES fruit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite_album DROP fruit');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite_album_fruit DROP FOREIGN KEY FK_15A1DDF2BB60B33E');
        $this->addSql('ALTER TABLE favorite_album_fruit DROP FOREIGN KEY FK_15A1DDF2BAC115F0');
        $this->addSql('DROP TABLE favorite_album_fruit');
        $this->addSql('DROP TABLE fruit');
        $this->addSql('ALTER TABLE favorite_album ADD fruit VARCHAR(255) DEFAULT NULL');
    }
}
