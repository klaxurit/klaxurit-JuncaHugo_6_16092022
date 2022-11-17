<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221114095032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_message ADD trick_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_message ADD CONSTRAINT FK_EEB02E75B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('CREATE INDEX IDX_EEB02E75B281BE2E ON user_message (trick_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_message DROP FOREIGN KEY FK_EEB02E75B281BE2E');
        $this->addSql('DROP INDEX IDX_EEB02E75B281BE2E ON user_message');
        $this->addSql('ALTER TABLE user_message DROP trick_id');
    }
}
