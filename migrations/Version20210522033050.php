<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210522033050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnements ADD CONSTRAINT FK_4788B767C325A696 FOREIGN KEY (abonne_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE abonnements ADD CONSTRAINT FK_4788B767633E2BBF FOREIGN KEY (abonnements_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE avatar ADD CONSTRAINT FK_1677722FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnements DROP FOREIGN KEY FK_4788B767C325A696');
        $this->addSql('ALTER TABLE abonnements DROP FOREIGN KEY FK_4788B767633E2BBF');
        $this->addSql('ALTER TABLE avatar DROP FOREIGN KEY FK_1677722FA76ED395');
    }
}
