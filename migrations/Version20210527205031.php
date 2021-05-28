<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210527205031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE likes_users DROP FOREIGN KEY FK_A87F096F2F23775F');
        $this->addSql('ALTER TABLE likes_videos DROP FOREIGN KEY FK_2DCDBE8B2F23775F');
        $this->addSql('DROP TABLE abonnements');
        $this->addSql('DROP TABLE following');
        $this->addSql('DROP TABLE likes');
        $this->addSql('DROP TABLE likes_users');
        $this->addSql('DROP TABLE likes_videos');
        $this->addSql('DROP TABLE videodislike');
        $this->addSql('ALTER TABLE videobackground CHANGE videoduration videoduration DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abonnements (id INT AUTO_INCREMENT NOT NULL, abonne_id INT NOT NULL, abonnements_id INT NOT NULL, INDEX IDX_4788B767C325A696 (abonne_id), INDEX IDX_4788B767633E2BBF (abonnements_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE following (user_id INT NOT NULL, following_user_id INT NOT NULL, INDEX IDX_71BF8DE31896F387 (following_user_id), INDEX IDX_71BF8DE3A76ED395 (user_id), PRIMARY KEY(user_id, following_user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE likes (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE likes_users (likes_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_A87F096F67B3B43D (users_id), INDEX IDX_A87F096F2F23775F (likes_id), PRIMARY KEY(likes_id, users_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE likes_videos (likes_id INT NOT NULL, videos_id INT NOT NULL, INDEX IDX_2DCDBE8B763C10B2 (videos_id), INDEX IDX_2DCDBE8B2F23775F (likes_id), PRIMARY KEY(likes_id, videos_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE videodislike (id INT AUTO_INCREMENT NOT NULL, video_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_3F5FABE429C1004E (video_id), INDEX IDX_3F5FABE4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE abonnements ADD CONSTRAINT FK_4788B767633E2BBF FOREIGN KEY (abonnements_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE abonnements ADD CONSTRAINT FK_4788B767C325A696 FOREIGN KEY (abonne_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE following ADD CONSTRAINT FK_71BF8DE31896F387 FOREIGN KEY (following_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE following ADD CONSTRAINT FK_71BF8DE3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE likes_users ADD CONSTRAINT FK_A87F096F2F23775F FOREIGN KEY (likes_id) REFERENCES likes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE likes_users ADD CONSTRAINT FK_A87F096F67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE likes_videos ADD CONSTRAINT FK_2DCDBE8B2F23775F FOREIGN KEY (likes_id) REFERENCES likes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE likes_videos ADD CONSTRAINT FK_2DCDBE8B763C10B2 FOREIGN KEY (videos_id) REFERENCES videos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE videodislike ADD CONSTRAINT FK_3F5FABE429C1004E FOREIGN KEY (video_id) REFERENCES videos (id)');
        $this->addSql('ALTER TABLE videodislike ADD CONSTRAINT FK_3F5FABE4A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE videobackground CHANGE videoduration videoduration VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
