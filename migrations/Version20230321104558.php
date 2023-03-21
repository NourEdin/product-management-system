<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321104558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creating credentials table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE credential (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, client_id VARCHAR(255) NOT NULL, secret VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_57F1D4B19EB6921 (client_id), INDEX IDX_57F1D4BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE credential ADD CONSTRAINT FK_57F1D4BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE credential DROP FOREIGN KEY FK_57F1D4BA76ED395');
        $this->addSql('DROP TABLE credential');
    }
}
