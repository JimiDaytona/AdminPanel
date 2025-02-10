<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250207174009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE guests ADD table_in_id INT NOT NULL');
        $this->addSql('ALTER TABLE guests ADD CONSTRAINT FK_4D11BCB21C05B34B FOREIGN KEY (table_in_id) REFERENCES tables (id)');
        $this->addSql('CREATE INDEX IDX_4D11BCB21C05B34B ON guests (table_in_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE guests DROP FOREIGN KEY FK_4D11BCB21C05B34B');
        $this->addSql('DROP INDEX IDX_4D11BCB21C05B34B ON guests');
        $this->addSql('ALTER TABLE guests DROP table_in_id');
    }
}
