<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210426080519 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE likes_users DROP FOREIGN KEY FK_A87F096F2F23775F');
        $this->addSql('ALTER TABLE likes_videos DROP FOREIGN KEY FK_2DCDBE8B2F23775F');
        $this->addSql('DROP TABLE likes');
        $this->addSql('DROP TABLE likes_users');
        $this->addSql('DROP TABLE likes_videos');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE likes (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE likes_users (likes_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_A87F096F67B3B43D (users_id), INDEX IDX_A87F096F2F23775F (likes_id), PRIMARY KEY(likes_id, users_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE likes_videos (likes_id INT NOT NULL, videos_id INT NOT NULL, INDEX IDX_2DCDBE8B763C10B2 (videos_id), INDEX IDX_2DCDBE8B2F23775F (likes_id), PRIMARY KEY(likes_id, videos_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE likes_users ADD CONSTRAINT FK_A87F096F2F23775F FOREIGN KEY (likes_id) REFERENCES likes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE likes_users ADD CONSTRAINT FK_A87F096F67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE likes_videos ADD CONSTRAINT FK_2DCDBE8B2F23775F FOREIGN KEY (likes_id) REFERENCES likes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE likes_videos ADD CONSTRAINT FK_2DCDBE8B763C10B2 FOREIGN KEY (videos_id) REFERENCES videos (id) ON DELETE CASCADE');
    }
}
