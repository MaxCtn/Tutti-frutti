<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241121124204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute la colonne discogs_id, met à jour les contraintes, et assure l’intégrité des données.';
    }

    public function up(Schema $schema): void
    {
        // Ajouter la colonne discogs_id si elle n'existe pas
        $this->addSql('ALTER TABLE favorite_album ADD IF NOT EXISTS discogs_id VARCHAR(255) NOT NULL');

        // S'assurer que discogs_id est unique et non nul
        $this->addSql("UPDATE favorite_album SET discogs_id = CONCAT('discogs_', id) WHERE discogs_id IS NULL OR discogs_id = ''");

        // Ajouter la contrainte UNIQUE
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_8F10456761FD3E0A ON favorite_album (discogs_id)');

        // Supprimer et recréer les contraintes étrangères avec les bonnes options
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY IF EXISTS FK_8F104567A76ED395');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F104567A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY IF EXISTS FK_8F10456733B92F39');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F10456733B92F39 FOREIGN KEY (label_id) REFERENCES label (id) ON DELETE SET NULL');

        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY IF EXISTS FK_8F1045674296D31F');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F1045674296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE SET NULL');

        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY IF EXISTS FK_8F104567D629F605');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F104567D629F605 FOREIGN KEY (format_id) REFERENCES format (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // Revenir à l'état précédent
        $this->addSql('ALTER TABLE favorite_album DROP COLUMN IF EXISTS discogs_id');
        $this->addSql('DROP INDEX IF EXISTS UNIQ_8F10456761FD3E0A ON favorite_album');

        // Rétablir les anciennes contraintes
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY IF EXISTS FK_8F104567A76ED395');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F104567A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');

        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY IF EXISTS FK_8F10456733B92F39');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F10456733B92F39 FOREIGN KEY (label_id) REFERENCES label (id)');

        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY IF EXISTS FK_8F1045674296D31F');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F1045674296D31F FOREIGN KEY (genre_id) REFERENCES genre (id)');

        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY IF EXISTS FK_8F104567D629F605');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F104567D629F605 FOREIGN KEY (format_id) REFERENCES format (id)');
    }
}
