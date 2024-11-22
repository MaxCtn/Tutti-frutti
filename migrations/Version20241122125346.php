<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241122125346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorite_album (id INT AUTO_INCREMENT NOT NULL, label_id INT DEFAULT NULL, genre_id INT DEFAULT NULL, format_id INT DEFAULT NULL, user_id INT NOT NULL, album_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, year VARCHAR(255) DEFAULT NULL, cover_image LONGTEXT DEFAULT NULL, INDEX IDX_8F10456733B92F39 (label_id), INDEX IDX_8F1045674296D31F (genre_id), INDEX IDX_8F104567D629F605 (format_id), INDEX IDX_8F104567A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favorite_album_fruit (favorite_album_id INT NOT NULL, fruit_id INT NOT NULL, INDEX IDX_15A1DDF2BB60B33E (favorite_album_id), INDEX IDX_15A1DDF2BAC115F0 (fruit_id), PRIMARY KEY(favorite_album_id, fruit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE format (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fruit (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_A00BD2975E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE label (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE track (id INT AUTO_INCREMENT NOT NULL, album_id INT NOT NULL, title VARCHAR(255) NOT NULL, duration VARCHAR(10) DEFAULT NULL, INDEX IDX_D6E3F8A61137ABCF (album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, profile_picture VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F10456733B92F39 FOREIGN KEY (label_id) REFERENCES label (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F1045674296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F104567D629F605 FOREIGN KEY (format_id) REFERENCES format (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F104567A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite_album_fruit ADD CONSTRAINT FK_15A1DDF2BB60B33E FOREIGN KEY (favorite_album_id) REFERENCES favorite_album (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite_album_fruit ADD CONSTRAINT FK_15A1DDF2BAC115F0 FOREIGN KEY (fruit_id) REFERENCES fruit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE track ADD CONSTRAINT FK_D6E3F8A61137ABCF FOREIGN KEY (album_id) REFERENCES favorite_album (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY FK_8F10456733B92F39');
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY FK_8F1045674296D31F');
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY FK_8F104567D629F605');
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY FK_8F104567A76ED395');
        $this->addSql('ALTER TABLE favorite_album_fruit DROP FOREIGN KEY FK_15A1DDF2BB60B33E');
        $this->addSql('ALTER TABLE favorite_album_fruit DROP FOREIGN KEY FK_15A1DDF2BAC115F0');
        $this->addSql('ALTER TABLE track DROP FOREIGN KEY FK_D6E3F8A61137ABCF');
        $this->addSql('DROP TABLE favorite_album');
        $this->addSql('DROP TABLE favorite_album_fruit');
        $this->addSql('DROP TABLE format');
        $this->addSql('DROP TABLE fruit');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE label');
        $this->addSql('DROP TABLE track');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
