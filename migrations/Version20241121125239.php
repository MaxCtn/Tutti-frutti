<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241121125239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute les contraintes étrangères sur la table favorite_album avec vérification préalable.';
    }

    public function up(Schema $schema): void
    {
        // Supprimer les contraintes existantes si elles existent
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY IF EXISTS FK_8F104567A76ED395');
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY IF EXISTS FK_8F10456733B92F39');
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY IF EXISTS FK_8F1045674296D31F');
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY IF EXISTS FK_8F104567D629F605');

        // Supprimer l'index unique sur discogs_id s'il existe
        $this->addSql('DROP INDEX IF EXISTS UNIQ_8F10456761FD3E0A ON favorite_album');

        // Recréer les contraintes étrangères
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F104567A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F10456733B92F39 FOREIGN KEY (label_id) REFERENCES label (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F1045674296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F104567D629F605 FOREIGN KEY (format_id) REFERENCES format (id) ON DELETE SET NULL');

        // Recréer l'index unique sur discogs_id
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8F10456761FD3E0A ON favorite_album (discogs_id)');
    }

    public function down(Schema $schema): void
    {
        // Supprimer les contraintes étrangères ajoutées
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY IF EXISTS FK_8F104567A76ED395');
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY IF EXISTS FK_8F10456733B92F39');
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY IF EXISTS FK_8F1045674296D31F');
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY IF EXISTS FK_8F104567D629F605');

        // Supprimer l'index unique sur discogs_id
        $this->addSql('DROP INDEX IF EXISTS UNIQ_8F10456761FD3E0A ON favorite_album');
    }
}
