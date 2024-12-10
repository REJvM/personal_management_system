<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241210164224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create File upload table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file_upload (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, created_on DATETIME NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_AFAAC0A0B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE file_upload ADD CONSTRAINT FK_AFAAC0A0B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file_upload DROP FOREIGN KEY FK_AFAAC0A0B03A8386');
        $this->addSql('DROP TABLE file_upload');
    }
}
