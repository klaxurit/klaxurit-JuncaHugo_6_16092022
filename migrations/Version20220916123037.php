<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220916123037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_message ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_message ADD CONSTRAINT FK_EEB02E75A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_EEB02E75A76ED395 ON user_message (user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_message DROP FOREIGN KEY FK_EEB02E75A76ED395');
        $this->addSql('DROP INDEX IDX_EEB02E75A76ED395 ON user_message');
        $this->addSql('ALTER TABLE user_message DROP user_id');
    }
}
