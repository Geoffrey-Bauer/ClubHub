<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240709122735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team ADD player_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F99E6F5DF FOREIGN KEY (player_id) REFERENCES `player` (id)');
        $this->addSql('CREATE INDEX IDX_C4E0A61F99E6F5DF ON team (player_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F99E6F5DF');
        $this->addSql('DROP INDEX IDX_C4E0A61F99E6F5DF ON team');
        $this->addSql('ALTER TABLE team DROP player_id');
    }
}
