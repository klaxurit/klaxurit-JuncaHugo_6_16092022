<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221123091636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick ADD cover_image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EE5A0E336 FOREIGN KEY (cover_image_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8F0A91EE5A0E336 ON trick (cover_image_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91EE5A0E336');
        $this->addSql('DROP INDEX UNIQ_D8F0A91EE5A0E336 ON trick');
        $this->addSql('ALTER TABLE trick DROP cover_image_id');
    }
}
