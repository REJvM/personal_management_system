<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250306144134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add picture field to user profile';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_profile ADD picture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB405EE45BDBF FOREIGN KEY (picture_id) REFERENCES file_upload (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D95AB405EE45BDBF ON user_profile (picture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB405EE45BDBF');
        $this->addSql('DROP INDEX UNIQ_D95AB405EE45BDBF ON user_profile');
        $this->addSql('ALTER TABLE user_profile DROP picture_id');
    }
}
