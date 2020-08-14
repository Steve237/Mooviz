<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200606084026 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA643259BA67D1');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, categoryname VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE videocategories');
        $this->addSql('DROP INDEX IDX_29AA643259BA67D1 ON videos');
        $this->addSql('ALTER TABLE videos ADD category_id INT DEFAULT NULL, DROP videocategories_id, CHANGE parutiondate parutiondate TIME NOT NULL');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA643212469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_29AA643212469DE2 ON videos (category_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA643212469DE2');
        $this->addSql('CREATE TABLE videocategories (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP INDEX IDX_29AA643212469DE2 ON videos');
        $this->addSql('ALTER TABLE videos ADD videocategories_id INT NOT NULL, DROP category_id, CHANGE parutiondate parutiondate DATETIME NOT NULL');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA643259BA67D1 FOREIGN KEY (videocategories_id) REFERENCES videocategories (id)');
        $this->addSql('CREATE INDEX IDX_29AA643259BA67D1 ON videos (videocategories_id)');
    }
}
