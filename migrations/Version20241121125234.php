<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241121125234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F10456733B92F39 FOREIGN KEY (label_id) REFERENCES label (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F1045674296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F104567D629F605 FOREIGN KEY (format_id) REFERENCES format (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE favorite_album ADD CONSTRAINT FK_8F104567A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY FK_8F10456733B92F39');
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY FK_8F1045674296D31F');
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY FK_8F104567D629F605');
        $this->addSql('ALTER TABLE favorite_album DROP FOREIGN KEY FK_8F104567A76ED395');
    }
}
